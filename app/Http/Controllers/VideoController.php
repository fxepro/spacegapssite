<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');

        $query = Video::orderBy('sort_order')->orderByDesc('created_at');
        if ($category) $query->where('category', $category);

        $videos     = $query->paginate(24);
        $categories = Video::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $featured   = Video::where('featured', true)->orderBy('sort_order')->get();

        return view('videos.index', compact('videos', 'categories', 'featured', 'category'));
    }
}
