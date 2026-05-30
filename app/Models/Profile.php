<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profile';

    protected $fillable = [
        'name', 'tagline', 'intro', 'summary',
        'photo_url', 'video_url', 'location', 'email', 'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    /**
     * Always return the single site profile, creating it if needed.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], ['name' => '']);
    }

    /**
     * Convert a YouTube / Vimeo watch URL to an embed URL.
     */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) return null;

        // YouTube
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $this->video_url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return $this->video_url; // already an embed URL
    }
}
