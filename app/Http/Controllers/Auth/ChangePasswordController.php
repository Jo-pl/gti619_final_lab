<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\PasswordHistory;

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
     * Handle the password change request for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:6',
                'confirmed',
                'regex:/[0-9]/',
            ],
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $user = Auth::user();
    
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Your current password is incorrect.');
        }
    
        // Check if the new password exists in the password history
        $passwordExists = PasswordHistory::where('user_id', $user->id)
            ->get()
            ->contains(function ($history) use ($request) {
                return Hash::check($request->new_password, $history->password);
            });
    
        if ($passwordExists) {
            return redirect()->back()->with('error', 'You cannot reuse an old password.');
        }
    
        // Update the user's password
        $user->update([
            'password' => Hash::make($request->new_password),
            'locked_until' => null, // Unlock the account
            'failed_attempts' => 0, // Reset failed attempts
        ]);
    
        // Store the new password in the password history
        PasswordHistory::create([
            'user_id' => $user->id,
            'password' => $user->password, // Store the hashed password
        ]);
    
        // Optional: Limit the password history to the last 5 entries
        PasswordHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->skip(5)
            ->take(PHP_INT_MAX)
            ->delete();
    
        return redirect()->back()->with('success', 'Your password has been changed successfully, and your account is unlocked!');
    }
    

    /**
     * Allow admin to change the password for any user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeUserPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'new_password' => [
                'required',
                'min:6',
                'confirmed',
                'regex:/[0-9]/',
            ],
        ]);
    
        if ($validator->fails()) {
            Log::warning('Admin password change failed: Validation error', [
                'admin_id' => Auth::id(),
                'errors' => $validator->errors()->toArray(),
            ]);
    
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        // Find the user by ID
        $user = User::findOrFail($request->user_id);
    
        // Check if the new password is in the user's password history
        $passwordExists = PasswordHistory::where('user_id', $user->id)
            ->get()
            ->contains(function ($history) use ($request) {
                return Hash::check($request->new_password, $history->password);
            });
    
        if ($passwordExists) {
            return redirect()->back()->with('error', 'The user cannot reuse an old password.');
        }
    
        // Record current password in the password history
        PasswordHistory::create([
            'user_id' => $user->id,
            'password' => $user->password, // Save hashed password
        ]);
    
        // Update the user's password and unlock the account
        $user->update([
            'password' => Hash::make($request->new_password),
            'locked_until' => null, // Unlock the account
            'failed_attempts' => 0, // Reset failed attempts
        ]);
    
        // Optional: Limit the password history to the last 5 entries
        PasswordHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->skip(5)
            ->take(PHP_INT_MAX)
            ->delete();
    
        Log::info('Admin changed user password successfully', [
            'admin_id' => Auth::id(),
            'user_id' => $user->id,
        ]);
    
        return redirect()->back()->with('success', 'User password has been changed successfully, and the account is unlocked!');
    }
    
}
