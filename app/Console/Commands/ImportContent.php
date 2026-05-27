<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Paper;
use App\Models\PortfolioItem;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Import content from /content/{posts,portfolio,papers} MDX/JSON files.
 *
 * MDX frontmatter fields supported:
 *   title, slug, date, excerpt, categories[], tags[],
 *   featuredImage, status, author, type, abstract, references,
 *   pdfUrl, projectDate, client, role, gallery[], externalUrl
 *
 * Usage:
 *   php artisan content:import           # all types
 *   php artisan content:import --type=posts
 *   php artisan content:import --type=portfolio
 *   php artisan content:import --type=papers
 *   php artisan content:import --fresh   # truncate before import
 */
class ImportContent extends Command
{
    protected $signature   = 'content:import {--type= : posts|portfolio|papers} {--fresh : Truncate existing content first}';
    protected $description = 'Import MDX/JSON content files from /content directory';

    public function handle(): int
    {
        $type = $this->option('type');

        if ($this->option('fresh')) {
            $this->warn('Running fresh import — truncating existing content...');
            if (!$type || $type === 'posts')     Post::truncate();
            if (!$type || $type === 'portfolio') PortfolioItem::truncate();
            if (!$type || $type === 'papers')    Paper::truncate();
        }

        $contentBase = base_path('content');

        if (!$type || $type === 'posts') {
            $this->importPosts($contentBase . '/posts');
        }
        if (!$type || $type === 'portfolio') {
            $this->importPortfolio($contentBase . '/portfolio');
        }
        if (!$type || $type === 'papers') {
            $this->importPapers($contentBase . '/papers');
        }

        $this->info('Import complete.');
        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------

    private function importPosts(string $dir): void
    {
        if (!File::isDirectory($dir)) {
            $this->line("  Skipping posts — directory not found: {$dir}");
            return;
        }

        $files = File::files($dir);
        $this->info("Importing posts from {$dir} (" . count($files) . " files)...");
        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            [$front, $body] = $this->parseFrontmatter($file->getContents());

            $slug = $front['slug'] ?? Str::slug($front['title'] ?? $file->getFilenameWithoutExtension());

            Post::updateOrCreate(['slug' => $slug], [
                'title'          => $front['title'] ?? $slug,
                'slug'           => $slug,
                'excerpt'        => $front['excerpt'] ?? null,
                'content'        => $this->renderMarkdown($body),
                'featured_image' => $front['featuredImage'] ?? $front['featured_image'] ?? null,
                'status'         => $front['status'] ?? 'published',
                'author'         => $front['author'] ?? 'Admin',
                'featured'       => filter_var($front['featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'published_at'   => isset($front['date']) ? date('Y-m-d H:i:s', strtotime($front['date'])) : now(),
            ])->tap(function (Post $post) use ($front) {
                $post->categories()->sync($this->resolveCategories($front['categories'] ?? []));
                $post->tags()->sync($this->resolveTags($front['tags'] ?? []));
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function importPortfolio(string $dir): void
    {
        if (!File::isDirectory($dir)) {
            $this->line("  Skipping portfolio — directory not found: {$dir}");
            return;
        }

        $files = File::files($dir);
        $this->info("Importing portfolio from {$dir} (" . count($files) . " files)...");
        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            [$front, $body] = $this->parseFrontmatter($file->getContents());

            $slug = $front['slug'] ?? Str::slug($front['title'] ?? $file->getFilenameWithoutExtension());

            PortfolioItem::updateOrCreate(['slug' => $slug], [
                'title'          => $front['title'] ?? $slug,
                'slug'           => $slug,
                'excerpt'        => $front['excerpt'] ?? null,
                'content'        => $this->renderMarkdown($body),
                'featured_image' => $front['featuredImage'] ?? $front['featured_image'] ?? null,
                'status'         => $front['status'] ?? 'published',
                'author'         => $front['author'] ?? 'Admin',
                'featured'       => filter_var($front['featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'project_date'   => isset($front['projectDate']) ? date('Y-m-d', strtotime($front['projectDate'])) : null,
                'client'         => $front['client'] ?? null,
                'role'           => $front['role'] ?? null,
                'external_url'   => $front['externalUrl'] ?? $front['external_url'] ?? null,
                'gallery'        => isset($front['gallery']) ? (array) $front['gallery'] : null,
                'sort_order'     => (int) ($front['order'] ?? 0),
            ])->tap(function (PortfolioItem $item) use ($front) {
                $item->categories()->sync($this->resolveCategories($front['categories'] ?? []));
                $item->tags()->sync($this->resolveTags($front['tags'] ?? []));
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    private function importPapers(string $dir): void
    {
        if (!File::isDirectory($dir)) {
            $this->line("  Skipping papers — directory not found: {$dir}");
            return;
        }

        $files = File::files($dir);
        $this->info("Importing papers from {$dir} (" . count($files) . " files)...");
        $bar = $this->output->createProgressBar(count($files));

        foreach ($files as $file) {
            [$front, $body] = $this->parseFrontmatter($file->getContents());

            $slug = $front['slug'] ?? Str::slug($front['title'] ?? $file->getFilenameWithoutExtension());

            Paper::updateOrCreate(['slug' => $slug], [
                'title'          => $front['title'] ?? $slug,
                'slug'           => $slug,
                'excerpt'        => $front['excerpt'] ?? null,
                'abstract'       => $front['abstract'] ?? null,
                'content'        => $this->renderMarkdown($body),
                'references'     => $front['references'] ?? null,
                'featured_image' => $front['featuredImage'] ?? $front['featured_image'] ?? null,
                'pdf_url'        => $front['pdfUrl'] ?? $front['pdf_url'] ?? null,
                'status'         => $front['status'] ?? 'published',
                'author'         => $front['author'] ?? 'Admin',
                'featured'       => filter_var($front['featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'published_at'   => isset($front['date']) ? date('Y-m-d H:i:s', strtotime($front['date'])) : now(),
            ])->tap(function (Paper $paper) use ($front) {
                $paper->categories()->sync($this->resolveCategories($front['categories'] ?? []));
                $paper->tags()->sync($this->resolveTags($front['tags'] ?? []));
            });

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    // -------------------------------------------------------------------------

    private function parseFrontmatter(string $raw): array
    {
        $front = [];
        $body  = $raw;

        if (preg_match('/^---\s*\n(.*?)\n---\s*\n(.*)/s', $raw, $m)) {
            $front = $this->parseYaml($m[1]);
            $body  = $m[2];
        }

        return [$front, $body];
    }

    private function parseYaml(string $yaml): array
    {
        $data = [];

        // Handle multiline values and list blocks
        $lines = explode("\n", $yaml);
        $currentKey = null;
        $listMode = false;

        foreach ($lines as $line) {
            // List item
            if ($listMode && preg_match('/^\s+-\s+(.+)$/', $line, $m)) {
                $data[$currentKey][] = trim($m[1], '"\'');
                continue;
            }
            // Key: value
            if (preg_match('/^(\w[\w\s]*):\s*(.*)$/', $line, $m)) {
                $listMode   = false;
                $currentKey = trim($m[1]);
                $val        = trim($m[2], '"\'');

                if ($val === '' || $val === null) {
                    $data[$currentKey] = [];
                    $listMode          = true;
                } elseif (in_array(strtolower($val), ['true', 'yes'])) {
                    $data[$currentKey] = true;
                } elseif (in_array(strtolower($val), ['false', 'no'])) {
                    $data[$currentKey] = false;
                } elseif (preg_match('/^\[(.+)\]$/', $val, $arr)) {
                    $data[$currentKey] = array_map(fn($v) => trim($v, ' "\''), explode(',', $arr[1]));
                } else {
                    $data[$currentKey] = $val;
                }
            }
        }

        return $data;
    }

    private function renderMarkdown(string $markdown): string
    {
        // Strip MDX JSX components before converting
        $markdown = preg_replace('/<[A-Z][^>]*>.*?<\/[A-Z][^>]*>/s', '', $markdown);
        $markdown = preg_replace('/<[A-Z][^\/]*\/>/s', '', $markdown);

        $environment = \League\CommonMark\Environment\Environment::createGfmEnvironment();
        $converter   = new \League\CommonMark\MarkdownConverter($environment);

        return (string) $converter->convert($markdown);
    }

    private function resolveCategories(array $names): array
    {
        return array_map(function (string $name) {
            return Category::firstOrCreate(
                ['name' => $name],
                ['color' => '#6366f1']
            )->id;
        }, array_filter($names));
    }

    private function resolveTags(array $names): array
    {
        return array_map(function (string $name) {
            return Tag::firstOrCreate(['name' => $name])->id;
        }, array_filter($names));
    }
}
