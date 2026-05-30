<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $profile = Profile::current();
        return view('admin.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'         => 'nullable|string|max:255',
            'tagline'      => 'nullable|string|max:255',
            'intro'        => 'nullable|string|max:1000',
            'summary'      => 'nullable|string',
            'photo_url'    => 'nullable|string|max:1000',
            'video_url'    => 'nullable|string|max:1000',
            'location'     => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'social_json'  => 'nullable|string',
        ]);

        // Parse social links JSON from Alpine builder
        $data['social_links'] = $this->parseSocialLinks($request->input('social_json'));
        unset($data['social_json']);

        // Handle photo upload to R2
        if ($request->hasFile('photo_file')) {
            $file = $request->file('photo_file');
            $path = $file->storeAs('profile', \Illuminate\Support\Str::uuid() . '.' . $file->getClientOriginalExtension(), 'r2');
            $data['photo_url'] = Storage::disk('r2')->url($path);
        }

        Profile::current()->update($data);

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated.');
    }

    private function parseSocialLinks(?string $json): array
    {
        $items = json_decode($json ?? '', true);
        if (!is_array($items)) return [];
        return array_values(array_filter($items, fn($s) => !empty(trim($s['url'] ?? ''))));
    }
}
