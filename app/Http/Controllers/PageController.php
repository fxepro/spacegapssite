<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Profile;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        $profile = Profile::current();
        return view('pages.about', compact('profile'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'message' => 'required|string|max:2000',
        ]);

        // Store or mail — placeholder for now
        return back()->with('success', 'Thank you for your message. I\'ll be in touch soon.');
    }
}
