<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index()
    {
        $videos     = Video::orderBy('sort_order')->orderByDesc('created_at')->paginate(40);
        $categories = Video::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.videos.index', compact('videos', 'categories'));
    }

    public function create()
    {
        $categories = Video::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.videos.form', compact('categories'));
    }

    public function store(Request $request)
    {
        Video::create($this->validated($request));
        return redirect()->route('admin.videos.index')->with('success', 'Video added.');
    }

    public function edit(Video $video)
    {
        $categories = Video::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.videos.form', compact('video', 'categories'));
    }

    public function update(Request $request, Video $video)
    {
        $video->update($this->validated($request, $video));
        return redirect()->route('admin.videos.index')->with('success', 'Video updated.');
    }

    public function destroy(Video $video)
    {
        $video->delete();
        return redirect()->route('admin.videos.index')->with('success', 'Video deleted.');
    }

    private function validated(Request $request, ?Video $video = null): array
    {
        return $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:2000',
            'video_url'     => 'required|string|max:1000',
            'thumbnail_url' => 'nullable|string|max:1000',
            'category'      => 'nullable|string|max:100',
            'sort_order'    => 'nullable|integer',
            'featured'      => 'boolean',
        ]);
    }
}
