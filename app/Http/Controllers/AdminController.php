<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function settings()
    {
        return view('admin.settings');
    }

    public function saveSettings(Request $request)
    {
        // Handle the form submission logic here
        $request->validate([
            'securityParam' => 'required|string|max:255',
        ]);

        // Save the security parameter to the database or configuration file
        // Example:
        // config(['security.param' => $request->securityParam]);

        return redirect()->route('admin.settings')->with('success', 'Settings saved successfully!');
    }
}


