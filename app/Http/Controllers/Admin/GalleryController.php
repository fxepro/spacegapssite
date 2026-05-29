<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $images     = GalleryImage::orderBy('sort_order')->orderByDesc('created_at')->paginate(40);
        $categories = GalleryImage::whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        return view('admin.gallery.index', compact('images', 'categories'));
    }

    /**
     * Handle multi-file drag-and-drop upload (XHR / fetch).
     * POST /admin/gallery/upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files'   => 'required|array|max:50',
            'files.*' => 'required|file|image|max:20480', // 20 MB per file
        ]);

        $created = [];

        foreach ($request->file('files', []) as $file) {
            $dir  = 'gallery/' . now()->format('Y/m');
            $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($dir, $name, 'r2');

            $image = GalleryImage::create([
                'image_url'  => Storage::disk('r2')->url($path),
                'title'      => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'sort_order' => 0,
            ]);

            $created[] = [
                'id'       => $image->id,
                'url'      => $image->image_url,
                'title'    => $image->title,
                'edit_url' => route('admin.gallery.edit', $image),
            ];
        }

        return response()->json(['images' => $created]);
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
        // Delete from R2 if it's an R2-hosted file
        $r2Url = rtrim(config('filesystems.disks.r2.url'), '/');
        if ($r2Url && str_starts_with($gallery->image_url, $r2Url)) {
            $path = ltrim(str_replace($r2Url, '', $gallery->image_url), '/');
            Storage::disk('r2')->delete($path);
        }

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
