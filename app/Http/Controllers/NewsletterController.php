<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);

        NewsletterSubscriber::firstOrCreate(['email' => $request->email]);

        return back()->with('newsletter_success', 'You\'re subscribed! Welcome to SpaceGaps.');
    }
}
