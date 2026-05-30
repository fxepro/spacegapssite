<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title', 'description', 'video_url', 'thumbnail_url',
        'category', 'sort_order', 'featured',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    /** Convert a YouTube / Vimeo watch URL to an embed URL. */
    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) return null;

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $this->video_url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }
        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }
        return $this->video_url;
    }

    /** Best available thumbnail — stored override or auto-derived from YouTube. */
    public function getThumbAttribute(): ?string
    {
        if ($this->thumbnail_url) return $this->thumbnail_url;

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $this->video_url ?? '', $m)) {
            return "https://img.youtube.com/vi/{$m[1]}/hqdefault.jpg";
        }
        return null;
    }

    /** Lightbox-compatible array for $store.lb.show(). */
    public function toLightbox(): array
    {
        return [
            'type'    => 'video',
            'url'     => $this->embed_url,
            'title'   => $this->title,
            'caption' => $this->description ?? '',
        ];
    }
}
