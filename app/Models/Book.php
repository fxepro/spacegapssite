<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Book extends Model
{
    use HasSlug;

    protected $fillable = [
        'title', 'subtitle', 'slug', 'excerpt', 'description',
        'cover_image', 'featured_image', 'author',
        'status', 'featured', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured'     => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string { return 'slug'; }

    // ── Relationships ──────────────────────────────────────────────

    public function chapters()
    {
        return $this->hasMany(BookChapter::class)->orderBy('sort_order');
    }

    public function publishedChapters()
    {
        return $this->hasMany(BookChapter::class)
            ->where('status', 'published')
            ->orderBy('sort_order');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'book_tag');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }
}
