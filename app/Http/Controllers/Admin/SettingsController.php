<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function security()
    {
        $settings = Settings::current();
        return view('admin.settings.security', compact('settings'));
    }

    public function updateSecurity(Request $request)
    {
        $data = [];
        foreach (Settings::current()->getFillable() as $field) {
            $data[$field] = $request->boolean($field);
        }

        Settings::current()->update($data);

        return redirect()->route('admin.settings.security')->with('success', 'Visibility settings saved.');
    }
}
