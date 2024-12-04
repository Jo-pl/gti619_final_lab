<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\PasswordHistory;

class AdminController extends Controller
{
    protected function setEnvVariable($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            file_put_contents($path, preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                file_get_contents($path)
            ));
        }
    }

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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::findOrFail($request->user_id);

        // Check if the new password exists in the password history
        $passwordExists = PasswordHistory::where('user_id', $user->id)
            ->get()
            ->contains(function ($history) use ($request) {
                return Hash::check($request->new_password, $history->password);
            });

        if ($passwordExists) {
            return redirect()->back()->with('error', 'The user cannot reuse an old password.');
        }

        // Record the current password in password history
        PasswordHistory::create([
            'user_id' => $user->id,
            'password' => $user->password,
        ]);

        // Update the user's password and unlock account
        $user->update([
            'password' => Hash::make($request->new_password),
            'locked_until' => null, // Unlock account
            'failed_attempts' => 0, // Reset failed attempts
        ]);

        // Keep only the last 5 password entries in history
        PasswordHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->skip(5)
            ->take(PHP_INT_MAX)
            ->delete();

        return redirect()->back()->with('success', 'User password has been changed successfully!');
    }

    public function updateFailedAttempts(Request $request)
    {
        $request->validate([
            'max_attempts' => 'required|integer|min:1|max:10',
        ]);

        $this->setEnvVariable('AUTH_MAX_ATTEMPTS', $request->input('max_attempts'));

        config(['auth.max_attempts' => $request->input('max_attempts')]);

        return redirect()->back()->with('success', 'Max failed login attempts updated successfully!');
    }
}
