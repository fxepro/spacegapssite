<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class BookChapter extends Model
{
    use HasSlug;

    protected $fillable = [
        'book_id', 'title', 'subtitle', 'slug',
        'content', 'sort_order', 'status',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string { return 'slug'; }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
