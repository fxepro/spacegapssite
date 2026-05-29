<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Category;
use App\Models\Paper;
use App\Models\PortfolioItem;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Import WordPress avada_portfolio items into books / papers / portfolio_items
 * based on the portfolio_category taxonomy.
 *
 * Routing rules (first match wins):
 *   has "Books"  category → books + one chapter
 *   has "Papers" category → papers
 *   everything else       → portfolio_items
 *
 * The routing category itself ("Books", "Papers", "Portfolio") is stripped
 * before assigning categories to the record.
 *
 * Usage:
 *   php artisan import:wordpress-portfolio docs/spacegaps.WordPress.2026-05-29.xml
 *   php artisan import:wordpress-portfolio docs/spacegaps.WordPress.2026-05-29.xml --fresh
 */
class ImportWordpressPortfolio extends Command
{
    protected $signature = 'import:wordpress-portfolio
                            {file : Path to WordPress WXR XML export file}
                            {--fresh : Truncate books, papers and portfolio_items before importing}';

    protected $description = 'Import WP avada_portfolio items → books / papers / portfolio_items by category';

    // Categories used only for routing — never saved to the imported record
    private const ROUTING_CATS = ['Books', 'Papers', 'Portfolio'];

    public function handle(): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Truncating books, book_chapters, papers, portfolio_items…');
            BookChapter::truncate();
            Book::truncate();
            Paper::truncate();
            PortfolioItem::truncate();
        }

        $this->info("Parsing {$file}…");
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($file);
        libxml_clear_errors();

        if (!$xml) {
            $this->error('Failed to parse XML.');
            return self::FAILURE;
        }

        $xml->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
        $xml->registerXPathNamespace('excerpt', 'http://wordpress.org/export/1.2/excerpt/');
        $xml->registerXPathNamespace('wp',      'http://wordpress.org/export/1.2/');
        $xml->registerXPathNamespace('dc',      'http://purl.org/dc/elements/1.1/');

        // Filter to avada_portfolio items only
        $items = array_filter($xml->xpath('//item'), function ($item) {
            $item->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
            $type   = (string)($item->xpath('wp:post_type')[0] ?? '');
            $status = (string)($item->xpath('wp:status')[0] ?? '');
            return $type === 'avada_portfolio' && in_array($status, ['publish', 'draft']);
        });

        $total = count($items);
        $this->info("Found {$total} portfolio items. Routing to tables…");
        $bar = $this->output->createProgressBar($total);

        $counts = ['books' => 0, 'papers' => 0, 'portfolio' => 0, 'skipped' => 0];

        foreach ($items as $item) {
            $item->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
            $item->registerXPathNamespace('excerpt', 'http://wordpress.org/export/1.2/excerpt/');
            $item->registerXPathNamespace('wp',      'http://wordpress.org/export/1.2/');
            $item->registerXPathNamespace('dc',      'http://purl.org/dc/elements/1.1/');

            $title   = (string)$item->title;
            $slug    = (string)($item->xpath('wp:post_name')[0] ?? '') ?: Str::slug($title);
            $status  = (string)($item->xpath('wp:status')[0]    ?? 'draft');
            $date    = (string)($item->xpath('wp:post_date')[0] ?? null);
            $rawBody = (string)($item->xpath('content:encoded')[0] ?? '');
            $excerpt = trim((string)($item->xpath('excerpt:encoded')[0] ?? ''));

            $content       = $this->cleanHtml($this->stripShortcodes($rawBody));
            $featuredImage = $this->extractFeaturedImage($item, $xml);
            $dbStatus      = $status === 'publish' ? 'published' : 'draft';
            $publishedAt   = $date ? date('Y-m-d H:i:s', strtotime($date)) : null;

            // Collect all portfolio categories
            $rawCats = [];
            foreach ($item->category as $cat) {
                if ((string)$cat->attributes()['domain'] === 'portfolio_category') {
                    $rawCats[] = trim((string)$cat);
                }
            }

            // Determine destination
            $destination = 'portfolio';
            if (in_array('Books',  $rawCats)) $destination = 'books';
            elseif (in_array('Papers', $rawCats)) $destination = 'papers';

            // Content categories = all cats minus routing labels
            $contentCats = array_filter($rawCats, fn($c) => !in_array($c, self::ROUTING_CATS));
            $catIds = $this->resolveCategoryIds($contentCats);

            // ── Route ───────────────────────────────────────────────────────
            match ($destination) {
                'books'     => $this->importBook($title, $slug, $excerpt, $content, $featuredImage, $dbStatus, $publishedAt, $catIds),
                'papers'    => $this->importPaper($title, $slug, $excerpt, $content, $featuredImage, $dbStatus, $publishedAt, $catIds),
                'portfolio' => $this->importPortfolio($title, $slug, $excerpt, $content, $featuredImage, $dbStatus, $publishedAt, $catIds),
            };

            $counts[$destination]++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done.");
        $this->table(
            ['Table', 'Imported'],
            [
                ['books',          $counts['books']],
                ['papers',         $counts['papers']],
                ['portfolio_items',$counts['portfolio']],
            ]
        );

        return self::SUCCESS;
    }

    // ── Importers ────────────────────────────────────────────────────────────

    private function importBook(string $title, string $slug, string $excerpt, string $content, ?string $image, string $status, ?string $date, array $catIds): void
    {
        $book = Book::updateOrCreate(
            ['slug' => $slug],
            [
                'title'          => $title,
                'slug'           => $slug,
                'excerpt'        => $excerpt ?: null,
                'cover_image'    => $image,
                'featured_image' => $image,
                'author'         => 'Admin',
                'status'         => $status,
                'featured'       => false,
                'published_at'   => $date,
            ]
        );

        $book->categories()->sync($catIds);

        // Store full WP content as a single chapter — split manually later
        if ($content) {
            $chapterSlug = $slug . '-full-content';
            BookChapter::updateOrCreate(
                ['slug' => $chapterSlug],
                [
                    'book_id'    => $book->id,
                    'title'      => 'Full Content',
                    'subtitle'   => null,
                    'slug'       => $chapterSlug,
                    'content'    => $content,
                    'sort_order' => 0,
                    'status'     => $status,
                ]
            );
        }
    }

    private function importPaper(string $title, string $slug, string $excerpt, string $content, ?string $image, string $status, ?string $date, array $catIds): void
    {
        $paper = Paper::updateOrCreate(
            ['slug' => $slug],
            [
                'title'          => $title,
                'slug'           => $slug,
                'excerpt'        => $excerpt ?: null,
                'content'        => $content ?: null,
                'featured_image' => $image,
                'author'         => 'Admin',
                'status'         => $status,
                'featured'       => false,
                'published_at'   => $date,
            ]
        );

        $paper->categories()->sync($catIds);
    }

    private function importPortfolio(string $title, string $slug, string $excerpt, string $content, ?string $image, string $status, ?string $date, array $catIds): void
    {
        $item = PortfolioItem::updateOrCreate(
            ['slug' => $slug],
            [
                'title'          => $title,
                'slug'           => $slug,
                'excerpt'        => $excerpt ?: null,
                'content'        => $content ?: null,
                'featured_image' => $image,
                'author'         => 'Admin',
                'status'         => $status,
                'featured'       => false,
                'project_date'   => $date ? date('Y-m-d', strtotime($date)) : null,
                'sort_order'     => 0,
            ]
        );

        $item->categories()->sync($catIds);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function resolveCategoryIds(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {
            $name = trim($name);
            if (!$name) continue;
            $ids[] = Category::firstOrCreate(
                ['name' => $name],
                ['color' => $this->colorForCategory($name)]
            )->id;
        }
        return $ids;
    }

    private function stripShortcodes(string $content): string
    {
        $content = preg_replace('/\[\w[\w_-]*[^\]]*\/\]/s', '', $content);
        for ($i = 0; $i < 8; $i++) {
            $content = preg_replace('/\[\/?\w[\w_-]*[^\]]*\]/s', '', $content);
        }
        return $content;
    }

    private function cleanHtml(string $html): string
    {
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html = preg_replace('/\s+data-(?:start|end)="\d+"/', '', $html);
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
            if ((string)($meta->xpath('wp:meta_key')[0] ?? '') === '_thumbnail_id') {
                $thumbnailId = (string)($meta->xpath('wp:meta_value')[0] ?? '');
                break;
            }
        }
        if (!$thumbnailId) return null;

        $xml->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
        foreach ($xml->xpath('//item') as $att) {
            $att->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
            if ((string)($att->xpath('wp:post_id')[0]   ?? '') === $thumbnailId &&
                (string)($att->xpath('wp:post_type')[0] ?? '') === 'attachment') {
                $url = (string)($att->xpath('wp:attachment_url')[0] ?? $att->guid ?? '');
                return $url ?: null;
            }
        }
        return null;
    }

    private function colorForCategory(string $name): string
    {
        return [
            'SAAS'           => '#6366f1',
            'Books'          => '#8b5cf6',
            'Start Ups'      => '#f59e0b',
            'Papers'         => '#10b981',
            'Corporate'      => '#64748b',
            'America'        => '#ef4444',
            'Christian'      => '#14b8a6',
            'Food'           => '#f97316',
            'Not For Profit' => '#ec4899',
            'Technical'      => '#0ea5e9',
            'Policy'         => '#6366f1',
            'Defense'        => '#374151',
            'Theology'       => '#7c3aed',
            'Data Center'    => '#0891b2',
            'Crypto'         => '#f59e0b',
            'Blockchain'     => '#8b5cf6',
            'Fintech'        => '#10b981',
            'EdTech'         => '#06b6d4',
            'Products'       => '#f97316',
            'Philosophy'     => '#8b5cf6',
        ][$name] ?? '#6366f1';
    }
}
