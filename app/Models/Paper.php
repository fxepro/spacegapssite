<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Paper extends Model
{
    use HasSlug;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'abstract', 'references',
        'citations', 'gallery',
        'featured_image', 'pdf_url', 'status', 'author',
        'reading_time', 'featured', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured'     => 'boolean',
        'citations'    => 'array',
        'gallery'      => 'array',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getReadingTimeAttribute($value): string
    {
        if ($value) return $value . ' min read';
        if ($this->content) {
            $words = str_word_count(strip_tags($this->content));
            $minutes = max(1, (int) ceil($words / 200));
            return $minutes . ' min read';
        }
        return '1 min read';
    }
}
