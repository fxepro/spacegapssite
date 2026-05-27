# SpaceGaps WordPress Export Migration Report

Source file: `spacegaps.WordPress.2026-05-26.xml`

## What was found

- Total XML items: 162
- Published posts extracted: 79
- Attachments referenced in export: 83
- Portfolio items found: 0

## Post types in XML

- `post`: 79
- `attachment`: 83

## Output

- `content/posts/`: one `.mdx` file per WordPress post
- `raw/posts_html/`: original WordPress HTML/shortcode body per post
- `data/posts-index.json`: index for building routes/lists
- `data/categories.json`: category counts
- `data/tags.json`: tag counts

## Notes

The export contains 79 WordPress posts and 83 attachments. It does not contain portfolio/custom post type items.
Avada/Fusion Builder shortcodes were stripped from the `.mdx` versions while preserving the inner article text as much as possible.
The original raw content is preserved separately so nothing is lost during cleanup.
Featured images were mapped from `_thumbnail_id` when present.
