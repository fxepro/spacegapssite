<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Import posts from a WordPress WXR (XML) export file.
 *
 * Usage:
 *   php artisan import:wordpress path/to/export.xml
 *   php artisan import:wordpress path/to/export.xml --fresh
 */
class ImportWordpress extends Command
{
    protected $signature   = 'import:wordpress {file : Path to the WordPress XML export file} {--fresh : Truncate posts before importing}';
    protected $description = 'Import posts from a WordPress WXR XML export';

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Truncating existing posts...');
            Post::truncate();
        }

        $this->info("Parsing {$file}...");

        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file);
        libxml_clear_errors();

        if (!$xml) {
            $this->error('Failed to parse XML file.');
            return self::FAILURE;
        }

        $xml->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
        $xml->registerXPathNamespace('excerpt', 'http://wordpress.org/export/1.2/excerpt/');
        $xml->registerXPathNamespace('wp',      'http://wordpress.org/export/1.2/');
        $xml->registerXPathNamespace('dc',      'http://purl.org/dc/elements/1.1/');

        $items = $xml->xpath('//item');
        $posts = array_filter($items, function ($item) {
            $item->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
            $type   = (string) ($item->xpath('wp:post_type')[0] ?? '');
            $status = (string) ($item->xpath('wp:status')[0] ?? '');
            return $type === 'post' && in_array($status, ['publish', 'draft']);
        });

        $total = count($posts);
        $this->info("Found {$total} posts.");
        $bar = $this->output->createProgressBar($total);

        $imported = 0;
        $skipped  = 0;

        foreach ($posts as $item) {
            $item->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
            $item->registerXPathNamespace('excerpt', 'http://wordpress.org/export/1.2/excerpt/');
            $item->registerXPathNamespace('wp',      'http://wordpress.org/export/1.2/');
            $item->registerXPathNamespace('dc',      'http://purl.org/dc/elements/1.1/');

            $title   = (string) $item->title;
            $slug    = (string) ($item->xpath('wp:post_name')[0] ?? '');
            $slug    = $slug ?: Str::slug($title);
            $status  = (string) ($item->xpath('wp:status')[0] ?? 'draft');
            $author  = (string) ($item->xpath('dc:creator')[0] ?? 'Admin');
            $date    = (string) ($item->xpath('wp:post_date')[0] ?? null);
            $rawBody = (string) ($item->xpath('content:encoded')[0] ?? '');
            $excerpt = trim((string) ($item->xpath('excerpt:encoded')[0] ?? ''));

            $content = $this->stripShortcodes($rawBody);
            $content = $this->cleanHtml($content);

            // Skip posts with no meaningful content
            if (strlen(trim(strip_tags($content))) < 30) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $featuredImage = $this->extractFeaturedImage($item, $xml);

            // Categories & tags
            $catIds = [];
            $tagIds = [];
            foreach ($item->category as $cat) {
                $domain = (string) $cat->attributes()['domain'];
                $name   = trim((string) $cat);
                if (!$name || strtolower($name) === 'uncategorized') continue;

                if ($domain === 'category') {
                    $catIds[] = Category::firstOrCreate(
                        ['name' => $name],
                        ['slug' => Str::slug($name), 'color' => '#6366f1']
                    )->id;
                } elseif ($domain === 'post_tag') {
                    $tagIds[] = Tag::firstOrCreate(
                        ['name' => $name],
                        ['slug' => Str::slug($name)]
                    )->id;
                }
            }

            $post = Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'          => $title,
                    'slug'           => $slug,
                    'excerpt'        => $excerpt ?: null,
                    'content'        => $content,
                    'featured_image' => $featuredImage,
                    'status'         => $status === 'publish' ? 'published' : 'draft',
                    'author'         => $author,
                    'featured'       => false,
                    'published_at'   => $date ? date('Y-m-d H:i:s', strtotime($date)) : now(),
                ]
            );

            $post->categories()->sync($catIds);
            $post->tags()->sync($tagIds);

            $imported++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Imported: {$imported}, Skipped (empty content): {$skipped}");

        return self::SUCCESS;
    }

    private function stripShortcodes(string $content): string
    {
        // Self-closing: [shortcode /]
        $content = preg_replace('/\[\w[\w_-]*[^\]]*\/\]/s', '', $content);
        // Opening/closing tags — multiple passes for nesting: [fusion_text]...[/fusion_text]
        for ($i = 0; $i < 8; $i++) {
            $content = preg_replace('/\[\/?\w[\w_-]*[^\]]*\]/s', '', $content);
        }
        return $content;
    }

    private function cleanHtml(string $html): string
    {
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = preg_replace('/(\r?\n){3,}/', "\n\n", $html);
        $html = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $html);
        return trim($html);
    }

    private function extractFeaturedImage(\SimpleXMLElement $item, \SimpleXMLElement $xml): ?string
    {
        $item->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');

        $thumbnailId = null;
        foreach ($item->xpath('wp:postmeta') as $meta) {
            $meta->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
            if ((string) ($meta->xpath('wp:meta_key')[0] ?? '') === '_thumbnail_id') {
                $thumbnailId = (string) ($meta->xpath('wp:meta_value')[0] ?? '');
                break;
            }
        }

        if (!$thumbnailId) return null;

        $xml->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
        foreach ($xml->xpath('//item') as $att) {
            $att->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
            if ((string) ($att->xpath('wp:post_id')[0] ?? '')   === $thumbnailId &&
                (string) ($att->xpath('wp:post_type')[0] ?? '') === 'attachment') {
                $url = (string) ($att->xpath('wp:attachment_url')[0] ?? $att->guid ?? '');
                return $url ?: null;
            }
        }

        return null;
    }
}
