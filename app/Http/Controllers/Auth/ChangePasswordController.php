<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\Log;



class ChangePasswordController extends Controller
{
    /**
     * Display the password change form.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('auth.passwords.change_password');
    }

    /**
     * Handle the password change request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
        ]);
    
        if ($validator->fails()) {
            Log::warning('Password change failed: Validation error', [
                'user_id' => Auth::id(),
                'errors' => $validator->errors()->toArray(),
            ]);
    
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Check if the current password matches the user's actual password
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            Log::error('Password change failed: Incorrect current password', [
                'user_id' => Auth::id(),
            ]);
    
            return redirect()->back()->with('error', 'Your current password is incorrect.');
        }
    
        // Check password history (last 5 passwords)
        $recentPasswords = PasswordHistory::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    
        foreach ($recentPasswords as $history) {
            if (Hash::check($request->new_password, $history->password)) {
                Log::error('Password change failed: Reused recent password', [
                    'user_id' => Auth::id(),
                ]);
    
                return redirect()->back()->with('error', 'You cannot reuse your recent passwords.');
            }
        }
    
        // Update the user's password
        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);
    
        // Save the new password to password history
        PasswordHistory::create([
            'user_id' => Auth::id(),
            'password' => Auth::user()->password, // Save hashed password
        ]);
    
        // Log the successful password change
        Log::info('Password changed successfully', [
            'user_id' => Auth::id(),
        ]);
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Your password has been changed successfully!');
    }
    
}
