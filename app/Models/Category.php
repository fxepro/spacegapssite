<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasSlug;

    protected $fillable = ['name', 'slug', 'description', 'color'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function portfolioItems()
    {
        return $this->belongsToMany(PortfolioItem::class);
    }

    public function papers()
    {
        return $this->belongsToMany(Paper::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function totalCount(): int
    {
        return $this->posts()->published()->count()
            + $this->portfolioItems()->published()->count()
            + $this->papers()->published()->count();
    }
}
