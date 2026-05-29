<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    protected $fillable = [
        'title', 'caption', 'image_url', 'category', 'sort_order', 'featured',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    /** Return a lightbox-ready array for use with $store.lb.show() */
    public function toLightbox(): array
    {
        return [
            'url'     => $this->image_url,
            'title'   => $this->title ?? '',
            'caption' => $this->caption ?? '',
        ];
    }
}
