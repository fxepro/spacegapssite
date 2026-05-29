<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $images     = GalleryImage::orderBy('sort_order')->orderByDesc('created_at')->paginate(40);
        $categories = GalleryImage::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.gallery.index', compact('images', 'categories'));
    }

    public function create()
    {
        $categories = GalleryImage::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.gallery.form', compact('categories'));
    }

    public function store(Request $request)
    {
        GalleryImage::create($this->validated($request));
        return redirect()->route('admin.gallery.index')->with('success', 'Image added.');
    }

    public function edit(GalleryImage $gallery)
    {
        $categories = GalleryImage::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.gallery.form', compact('gallery', 'categories'));
    }

    public function update(Request $request, GalleryImage $gallery)
    {
        $gallery->update($this->validated($request, $gallery));
        return redirect()->route('admin.gallery.index')->with('success', 'Image updated.');
    }

    public function destroy(GalleryImage $gallery)
    {
        $gallery->delete();
        return redirect()->route('admin.gallery.index')->with('success', 'Image deleted.');
    }

    private function validated(Request $request, ?GalleryImage $image = null): array
    {
        return $request->validate([
            'image_url'  => 'required|string|max:1000',
            'title'      => 'nullable|string|max:255',
            'caption'    => 'nullable|string|max:1000',
            'category'   => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'featured'   => 'boolean',
        ]);
    }
}
