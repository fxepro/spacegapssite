<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'nav_home', 'nav_blog', 'nav_portfolio', 'nav_papers',
        'nav_gallery', 'nav_books', 'nav_videos', 'nav_about', 'nav_contact',
    ];

    protected $casts = [
        'nav_home'      => 'boolean',
        'nav_blog'      => 'boolean',
        'nav_portfolio' => 'boolean',
        'nav_papers'    => 'boolean',
        'nav_gallery'   => 'boolean',
        'nav_books'     => 'boolean',
        'nav_videos'    => 'boolean',
        'nav_about'     => 'boolean',
        'nav_contact'   => 'boolean',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'nav_home' => true, 'nav_blog' => true, 'nav_portfolio' => true,
            'nav_papers' => true, 'nav_gallery' => true, 'nav_books' => true,
            'nav_videos' => true, 'nav_about' => true, 'nav_contact' => true,
        ]);
    }

    /** Returns the ordered list of nav links filtered by visibility settings. */
    public function visibleNavLinks(): array
    {
        $all = [
            ['key' => 'home',      'label' => 'Home',      'route' => 'home'],
            ['key' => 'about',     'label' => 'Profile',   'route' => 'about'],
            ['key' => 'blog',      'label' => 'Blog',      'route' => 'blog.index'],
            ['key' => 'portfolio', 'label' => 'Portfolio', 'route' => 'portfolio.index'],
            ['key' => 'papers',    'label' => 'Papers',    'route' => 'papers.index'],
            ['key' => 'gallery',   'label' => 'Gallery',   'route' => 'gallery.index'],
            ['key' => 'books',     'label' => 'Books',     'route' => 'books.index'],
            ['key' => 'videos',    'label' => 'Videos',    'route' => 'videos.index'],
            ['key' => 'contact',   'label' => 'Contact',   'route' => 'contact'],
        ];

        return array_filter($all, fn($link) => (bool) $this->{'nav_' . $link['key']});
    }
}
