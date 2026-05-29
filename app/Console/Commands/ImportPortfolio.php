<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\PortfolioItem;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Import portfolio items from MDX files.
 *
 * Usage:
 *   php artisan import:portfolio path/to/content/portfolio
 *   php artisan import:portfolio path/to/content/portfolio --drafts=path/to/content/portfolio-drafts
 *   php artisan import:portfolio path/to/content/portfolio --fresh
 */
class ImportPortfolio extends Command
{
    protected $signature = 'import:portfolio
                            {dir : Path to the published portfolio MDX directory}
                            {--drafts= : Optional path to drafts directory}
                            {--fresh : Truncate portfolio_items before importing}';

    protected $description = 'Import portfolio items from MDX files';

    public function handle(): int
    {
        $dir = rtrim($this->argument('dir'), '/\\');

        if (!is_dir($dir)) {
            $this->error("Directory not found: {$dir}");
            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Truncating existing portfolio items...');
            PortfolioItem::truncate();
        }

        $files = $this->collectFiles($dir);

        if ($draftsDir = $this->option('drafts')) {
            $draftsDir = rtrim($draftsDir, '/\\');
            if (is_dir($draftsDir)) {
                $files = array_merge($files, $this->collectFiles($draftsDir));
                $this->info("Also scanning drafts: {$draftsDir}");
            }
        }

        $total = count($files);
        $this->info("Found {$total} MDX files. Importing...");
        $bar = $this->output->createProgressBar($total);

        $imported = 0;
        $skipped  = 0;

        foreach ($files as $file) {
            [$frontmatter, $content] = $this->parseMdx($file);

            if (empty($frontmatter['title'])) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $title  = $frontmatter['title'];
            $slug   = $frontmatter['slug'] ?? Str::slug($title);
            $status = ($frontmatter['status'] ?? 'publish') === 'publish' ? 'published' : 'draft';
            $date   = $frontmatter['date'] ?? null;

            // Categories
            $catIds = [];
            $rawCats = $frontmatter['categories'] ?? [];
            foreach ($rawCats as $name) {
                $name = trim($name);
                if (!$name || strtolower($name) === 'portfolio') continue; // skip the generic "Portfolio" tag
                $catIds[] = Category::firstOrCreate(
                    ['name' => $name],
                    ['color' => $this->colorForCategory($name)]
                )->id;
            }

            $item = PortfolioItem::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'          => $title,
                    'slug'           => $slug,
                    'excerpt'        => $frontmatter['excerpt'] ?? null,
                    'content'        => $this->cleanHtml($content),
                    'featured_image' => $frontmatter['featuredImage'] ?? null,
                    'status'         => $status,
                    'author'         => 'Admin',
                    'project_date'   => $date ? date('Y-m-d', strtotime($date)) : null,
                    'featured'       => false,
                    'sort_order'     => 0,
                ]
            );

            $item->categories()->sync($catIds);

            $imported++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Imported: {$imported}, Skipped (no title): {$skipped}");

        return self::SUCCESS;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function collectFiles(string $dir): array
    {
        $files = glob($dir . DIRECTORY_SEPARATOR . '*.mdx');
        return $files ?: [];
    }

    /**
     * Parse an MDX file into [frontmatter array, content string].
     */
    private function parseMdx(string $file): array
    {
        $raw = file_get_contents($file);

        // Must start with ---
        if (!str_starts_with(ltrim($raw), '---')) {
            return [[], $raw];
        }

        // Find closing ---
        $raw    = ltrim($raw);
        $rest   = substr($raw, 3);          // strip opening ---
        $end    = strpos($rest, "\n---");

        if ($end === false) {
            return [[], $raw];
        }

        $yamlBlock = substr($rest, 0, $end);
        $content   = ltrim(substr($rest, $end + 4)); // +4 for "\n---"

        $frontmatter = $this->parseYaml($yamlBlock);

        return [$frontmatter, $content];
    }

    /**
     * Minimal YAML parser for our known frontmatter fields.
     * Handles: string, quoted string, array (bracket or block).
     */
    private function parseYaml(string $yaml): array
    {
        $data  = [];
        $lines = explode("\n", $yaml);
        $i     = 0;
        $n     = count($lines);

        while ($i < $n) {
            $line = $lines[$i];

            // Skip empty / comment lines
            if (trim($line) === '' || str_starts_with(trim($line), '#')) {
                $i++;
                continue;
            }

            // key: value
            if (preg_match('/^(\w[\w_-]*):\s*(.*)$/', $line, $m)) {
                $key = $m[1];
                $val = trim($m[2]);

                // Inline array  [a, b, c]
                if (str_starts_with($val, '[')) {
                    $data[$key] = $this->parseInlineArray($val);
                }
                // Block list (value is empty, next lines start with "  - ")
                elseif ($val === '') {
                    $items = [];
                    $i++;
                    while ($i < $n && preg_match('/^\s+-\s+(.+)$/', $lines[$i], $lm)) {
                        $items[] = trim($lm[1], '"\'');
                        $i++;
                    }
                    $data[$key] = $items;
                    continue; // already incremented
                }
                // Quoted string
                elseif (preg_match('/^"(.*)"$/', $val, $qm)) {
                    $data[$key] = $qm[1];
                }
                elseif (preg_match("/^'(.*)'$/", $val, $qm)) {
                    $data[$key] = $qm[1];
                }
                // Plain value
                else {
                    $data[$key] = $val;
                }
            }

            $i++;
        }

        return $data;
    }

    private function parseInlineArray(string $val): array
    {
        // Strip [ and ]
        $inner = preg_replace('/^\[|\]$/', '', $val);
        $items = [];
        foreach (explode(',', $inner) as $item) {
            $item = trim($item, ' "\'');
            if ($item !== '') $items[] = $item;
        }
        return $items;
    }

    private function cleanHtml(string $html): string
    {
        if (trim($html) === '') return '';
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Remove data-start / data-end attributes added by WordPress block editor
        $html = preg_replace('/\s+data-(?:start|end)="\d+"/', '', $html);
        $html = preg_replace('/(\r?\n){3,}/', "\n\n", $html);
        return trim($html);
    }

    private function colorForCategory(string $name): string
    {
        $map = [
            'SAAS'         => '#6366f1',
            'Books'        => '#8b5cf6',
            'Start Ups'    => '#f59e0b',
            'Papers'       => '#10b981',
            'Corporate'    => '#64748b',
            'America'      => '#ef4444',
            'Christian'    => '#14b8a6',
            'Food'         => '#f97316',
            'Not For Profit' => '#ec4899',
            'Technical'    => '#0ea5e9',
            'Policy'       => '#6366f1',
            'Defense'      => '#374151',
            'Theology'     => '#7c3aed',
            'Data Center'  => '#0891b2',
            'Crypto'       => '#f59e0b',
            'Blockchain'   => '#8b5cf6',
            'Fintech'      => '#10b981',
            'EdTech'       => '#06b6d4',
            'Products'     => '#f97316',
            'Philosophy'   => '#8b5cf6',
        ];
        return $map[$name] ?? '#6366f1';
    }
}
