<?php

namespace App\Http\Controllers;

use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');

        $query = GalleryImage::orderBy('sort_order')->orderByDesc('created_at');

        if ($category) {
            $query->where('category', $category);
        }

        $images     = $query->paginate(40);
        $categories = GalleryImage::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $featured   = GalleryImage::where('featured', true)->orderBy('sort_order')->get();

        return view('gallery.index', compact('images', 'categories', 'featured', 'category'));
    }
}
