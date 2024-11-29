<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReauthenticateController extends Controller
{
    /**
     * Show the reauthentication form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        return view('auth.reauthenticate');
    }

    /**
     * Handle the reauthentication request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reauthenticate(Request $request)
    {
        // Validate the password input
        $request->validate([
            'password' => 'required',
        ]);

        // Check if the password matches the current user's password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return redirect()->back()->withErrors([
                'password' => 'The provided password does not match our records.',
            ]);
        }

        // Update the session with the reauthentication timestamp
        session(['reauthenticated_at' => now()]);

        // Redirect the user back to their previous action
        return redirect(route('settings'))->with('success', 'Reauthentication successful.');

    }
}

