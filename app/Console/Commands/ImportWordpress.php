<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

/**
 * Import content from the WordPress/Next.js export at docs/spacegaps_nextjs_content.
 *
 * Usage:
 *   php artisan content:import-wordpress
 *   php artisan content:import-wordpress --fresh   # truncate posts first
 */
class ImportWordpress extends Command
{
    protected $signature   = 'content:import-wordpress {--fresh : Truncate existing posts first}';
    protected $description = 'Import posts from the WordPress export in docs/spacegaps_nextjs_content';

    private string $base;

    private array $categoryColors = [
        'Christianity'   => '#8b5cf6',
        'World'          => '#3b82f6',
        'Religion'       => '#a855f7',
        'Spirituality'   => '#ec4899',
        'America'        => '#ef4444',
        'People'         => '#f97316',
        'Technology'     => '#6366f1',
        'Business'       => '#10b981',
        'Women'          => '#f59e0b',
        'Satire'         => '#eab308',
        'Uncategorized'  => '#9ca3af',
        'Other'          => '#6b7280',
    ];

    public function handle(): int
    {
        $this->base = base_path('docs/spacegaps_nextjs_content');

        if (! File::isDirectory($this->base)) {
            $this->error("Export directory not found: {$this->base}");
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('--fresh: truncating posts...');
            Post::truncate();
        }

        $this->importCategories();
        $this->importTags();
        $this->importPosts();

        $this->info('Import complete.');
        return self::SUCCESS;
    }

    private function importCategories(): void
    {
        $path = "{$this->base}/data/categories.json";
        if (! File::exists($path)) {
            $this->warn('categories.json not found — skipping.');
            return;
        }

        $items = json_decode(File::get($path), true);
        foreach ($items as $item) {
            $name  = $item['name'];
            $color = $this->categoryColors[$name] ?? '#6366f1';
            Category::firstOrCreate(['name' => $name], ['color' => $color]);
        }

        $this->info('Categories: ' . count($items) . ' processed.');
    }

    private function importTags(): void
    {
        $path = "{$this->base}/data/tags.json";
        if (! File::exists($path)) {
            return;
        }

        $items = json_decode(File::get($path), true);
        foreach ($items as $item) {
            Tag::firstOrCreate(['name' => $item['name']]);
        }

        $this->info('Tags: ' . count($items) . ' processed.');
    }

    private function importPosts(): void
    {
        $indexPath = "{$this->base}/data/posts-index.json";
        if (! File::exists($indexPath)) {
            $this->error('posts-index.json not found.');
            return;
        }

        $posts = json_decode(File::get($indexPath), true);
        $this->info('Importing ' . count($posts) . ' posts...');
        $bar = $this->output->createProgressBar(count($posts));

        foreach ($posts as $meta) {
            if (($meta['status'] ?? '') !== 'publish') {
                $bar->advance();
                continue;
            }

            $content = $this->resolveContent($meta);

            $post = Post::updateOrCreate(
                ['slug' => $meta['slug']],
                [
                    'title'          => $meta['title'],
                    'slug'           => $meta['slug'],
                    'excerpt'        => $this->resolveExcerpt($meta, $content),
                    'content'        => $content,
                    'featured_image' => $meta['featuredImage'] ?? null,
                    'status'         => 'published',
                    'author'         => 'Admin',
                    'featured'       => false,
                    'published_at'   => date('Y-m-d H:i:s', strtotime($meta['date'])),
                ]
            );

            $post->categories()->sync($this->resolveCategories($meta['categories'] ?? []));
            $post->tags()->sync($this->resolveTags($meta['tags'] ?? []));

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function resolveContent(array $meta): string
    {
        // Prefer MDX (clean markdown), fall back to raw HTML
        $mdxPath  = "{$this->base}/{$meta['mdxPath']}";
        $htmlPath = "{$this->base}/{$meta['rawHtmlPath']}";

        if (File::exists($mdxPath)) {
            [, $body] = $this->parseFrontmatter(File::get($mdxPath));
            return $this->renderMarkdown($body);
        }

        if (File::exists($htmlPath)) {
            return $this->cleanFusionHtml(File::get($htmlPath));
        }

        return '';
    }

    private function resolveExcerpt(array $meta, string $htmlContent): ?string
    {
        // MDX sometimes has an empty excerpt field
        if (! empty($meta['excerpt'])) {
            return $meta['excerpt'];
        }

        // Auto-generate from first paragraph of content
        if ($htmlContent) {
            $text = strip_tags($htmlContent);
            $text = preg_replace('/\s+/', ' ', trim($text));
            if (strlen($text) > 20) {
                return mb_substr($text, 0, 280);
            }
        }

        return null;
    }

    private function parseFrontmatter(string $raw): array
    {
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $raw, $m)) {
            return [[], $m[2]];
        }
        return [[], $raw];
    }

    private function renderMarkdown(string $markdown): string
    {
        // Strip MDX JSX components
        $markdown = preg_replace('/<[A-Z][^>]*>.*?<\/[A-Z][^>]*>/s', '', $markdown);
        $markdown = preg_replace('/<[A-Z][^\/]*\/>/s', '', $markdown);

        $environment = Environment::createGfmEnvironment();
        $converter   = new MarkdownConverter($environment);

        return (string) $converter->convert($markdown);
    }

    private function cleanFusionHtml(string $html): string
    {
        // Extract real content from inside [fusion_text]...[/fusion_text] and
        // [fusion_title]...[/fusion_title] blocks, discarding all shortcode tags.
        $out = '';

        // Pull text/title block contents
        preg_match_all('/\[fusion_(?:text|title)[^\]]*\](.*?)\[\/fusion_(?:text|title)\]/s', $html, $matches);
        foreach ($matches[1] as $block) {
            $block = trim($block);
            if ($block !== '') {
                $out .= $block . "\n\n";
            }
        }

        // If no fusion blocks found, strip all shortcodes and return raw
        if ($out === '') {
            $out = preg_replace('/\[[^\]]+\]/s', '', $html);
        }

        // Remove leftover shortcode artifacts
        $out = preg_replace('/\[[^\]]+\]/s', '', $out);

        return trim($out);
    }

    private function resolveCategories(array $names): array
    {
        return array_map(function (string $name) {
            $color = $this->categoryColors[$name] ?? '#6366f1';
            return Category::firstOrCreate(['name' => $name], ['color' => $color])->id;
        }, array_filter($names));
    }

    private function resolveTags(array $names): array
    {
        return array_map(function (string $name) {
            return Tag::firstOrCreate(['name' => $name])->id;
        }, array_filter($names));
    }
}
