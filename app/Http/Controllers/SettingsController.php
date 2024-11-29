<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index'); // Ensure this view file exists
    }

    public function update(Request $request)
    {
        // Handle settings update logic
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
