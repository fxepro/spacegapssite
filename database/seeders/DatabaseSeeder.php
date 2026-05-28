<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@spacegaps.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        $catDefs = [
            ['name' => 'Technology', 'color' => '#6366f1'],
            ['name' => 'Society',    'color' => '#f59e0b'],
            ['name' => 'Research',   'color' => '#10b981'],
            ['name' => 'Philosophy', 'color' => '#8b5cf6'],
            ['name' => 'Politics',   'color' => '#ef4444'],
            ['name' => 'Creative',   'color' => '#ec4899'],
            ['name' => 'Science',    'color' => '#14b8a6'],
            ['name' => 'Design',     'color' => '#f97316'],
        ];
        $categories = [];
        foreach ($catDefs as $c) {
            $categories[$c['name']] = Category::firstOrCreate(['name' => $c['name']], $c);
        }

        $tagNames = ['ai', 'writing', 'future', 'history', 'economics', 'culture', 'infrastructure', 'democracy'];
        foreach ($tagNames as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}
