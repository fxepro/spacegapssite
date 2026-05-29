<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\GalleryImage;
use App\Models\Paper;
use App\Models\PortfolioItem;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Download all external images referenced in the DB and re-upload to R2.
 * Updates every URL in-place so the app immediately serves from R2.
 *
 * Usage:
 *   php artisan images:migrate-to-r2
 *   php artisan images:migrate-to-r2 --dry-run   ← just lists what would be migrated
 */
class MigrateImagesToR2 extends Command
{
    protected $signature   = 'images:migrate-to-r2 {--dry-run : List images without uploading}';
    protected $description = 'Download external images and re-upload to Cloudflare R2, updating all DB references';

    /** URL → R2 URL cache so we don't upload the same image twice */
    private array $cache = [];

    private int $uploaded = 0;
    private int $skipped  = 0;
    private int $failed   = 0;

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $r2  = config('filesystems.disks.r2.url');

        if (!$r2 && !$dry) {
            $this->error('R2_URL is not set. Add it to your .env / Render env vars.');
            return self::FAILURE;
        }

        $this->info($dry ? '── DRY RUN — no files will be uploaded ──' : "── Migrating images → R2 ({$r2}) ──");
        $this->newLine();

        // ── Collect all image sources ────────────────────────────────────────

        // posts: featured_image
        $this->info('Posts…');
        foreach (Post::whereNotNull('featured_image')->get() as $post) {
            $new = $this->migrate($post->featured_image, 'posts', $dry);
            if ($new && $new !== $post->featured_image) {
                $post->updateQuietly(['featured_image' => $new]);
            }
        }

        // portfolio_items: featured_image + gallery[]
        $this->info('Portfolio…');
        foreach (PortfolioItem::whereNotNull('featured_image')->get() as $item) {
            $new = $this->migrate($item->featured_image, 'portfolio', $dry);
            if ($new && $new !== $item->featured_image) {
                $item->updateQuietly(['featured_image' => $new]);
            }
        }
        foreach (PortfolioItem::whereNotNull('gallery')->get() as $item) {
            $gallery = $item->gallery ?? [];
            $updated = false;
            foreach ($gallery as &$url) {
                $new = $this->migrate($url, 'portfolio/gallery', $dry);
                if ($new && $new !== $url) { $url = $new; $updated = true; }
            }
            unset($url);
            if ($updated) $item->updateQuietly(['gallery' => $gallery]);
        }

        // papers: featured_image + gallery[]
        $this->info('Papers…');
        foreach (Paper::whereNotNull('featured_image')->get() as $paper) {
            $new = $this->migrate($paper->featured_image, 'papers', $dry);
            if ($new && $new !== $paper->featured_image) {
                $paper->updateQuietly(['featured_image' => $new]);
            }
        }
        foreach (Paper::whereNotNull('gallery')->get() as $paper) {
            $gallery = $paper->gallery ?? [];
            $updated = false;
            foreach ($gallery as &$url) {
                $new = $this->migrate($url, 'papers/gallery', $dry);
                if ($new && $new !== $url) { $url = $new; $updated = true; }
            }
            unset($url);
            if ($updated) $paper->updateQuietly(['gallery' => $gallery]);
        }

        // books: cover_image + featured_image
        $this->info('Books…');
        foreach (Book::get() as $book) {
            $changed = [];
            foreach (['cover_image', 'featured_image'] as $col) {
                if (!$book->$col) continue;
                $new = $this->migrate($book->$col, 'books', $dry);
                if ($new && $new !== $book->$col) $changed[$col] = $new;
            }
            if ($changed) $book->updateQuietly($changed);
        }

        // gallery_images: image_url
        $this->info('Gallery…');
        foreach (GalleryImage::all() as $img) {
            $new = $this->migrate($img->image_url, 'gallery', $dry);
            if ($new && $new !== $img->image_url) {
                $img->updateQuietly(['image_url' => $new]);
            }
        }

        // ── Summary ──────────────────────────────────────────────────────────
        $this->newLine();
        $this->table(
            ['Uploaded', 'Skipped (already R2)', 'Failed'],
            [[$this->uploaded, $this->skipped, $this->failed]]
        );

        return self::SUCCESS;
    }

    /**
     * Download $url and upload to R2 under $folder.
     * Returns the new R2 URL, the original URL (if already on R2 / skipped), or null on failure.
     */
    private function migrate(string $url, string $folder, bool $dry): ?string
    {
        if (!$url) return null;

        $r2Base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        // Already on R2 — nothing to do
        if ($r2Base && str_starts_with($url, $r2Base)) {
            $this->skipped++;
            $this->line("  <fg=gray>SKIP</> {$url}");
            return $url;
        }

        // Already processed this URL in this run
        if (isset($this->cache[$url])) {
            return $this->cache[$url];
        }

        if ($dry) {
            $this->line("  <fg=yellow>WOULD UPLOAD</> {$url}");
            $this->uploaded++;
            return $url;
        }

        // Download
        try {
            $response = Http::timeout(30)->get($url);
            if (!$response->successful()) {
                throw new \RuntimeException("HTTP {$response->status()}");
            }
        } catch (\Throwable $e) {
            $this->failed++;
            $this->line("  <fg=red>FAIL</> {$url} — {$e->getMessage()}");
            $this->cache[$url] = null;
            return null;
        }

        // Determine extension from URL or Content-Type
        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        if (!$ext) {
            $ct  = $response->header('Content-Type');
            $ext = match (true) {
                str_contains($ct, 'jpeg'), str_contains($ct, 'jpg') => 'jpg',
                str_contains($ct, 'png')  => 'png',
                str_contains($ct, 'gif')  => 'gif',
                str_contains($ct, 'webp') => 'webp',
                default                   => 'jpg',
            };
        }

        $path   = "{$folder}/" . Str::uuid() . ".{$ext}";
        $stored = Storage::disk('r2')->put($path, $response->body());

        if (!$stored) {
            $this->failed++;
            $this->line("  <fg=red>FAIL (R2 write)</> {$url}");
            $this->cache[$url] = null;
            return null;
        }

        $r2Url = rtrim($r2Base, '/') . '/' . ltrim($path, '/');
        $this->uploaded++;
        $this->line("  <fg=green>OK</>   {$url}");
        $this->line("       → {$r2Url}");

        $this->cache[$url] = $r2Url;
        return $r2Url;
    }
}
