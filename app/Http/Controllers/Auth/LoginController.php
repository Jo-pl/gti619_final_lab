<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $maxAttempts = 5; // Maximum allowed attempts
    protected $lockoutTime = 15; // Lockout duration in minutes

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $request->email]);

        if ($user) {
            // Check if user is locked out
            if ($user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
                $timeLeft = Carbon::now()->diffInMinutes($user->locked_until);
                session()->flash('error', "Account locked. Try again in $timeLeft minutes.");
                return redirect()->back();
            }

            // Increment failed attempts
            $user->increment('failed_attempts');

            // Lock the user if max attempts are reached
            if ($user->failed_attempts >= $this->maxAttempts) {
                $user->update([
                    'locked_until' => Carbon::now()->addMinutes($this->lockoutTime),
                    'failed_attempts' => 0, // Reset attempts after locking
                ]);
                session()->flash('error', 'Account locked due to too many failed attempts. Please try again later or contact admin.');
                return redirect()->back();
            }
        }

        // Default failed login response
        session()->flash('error', 'Invalid credentials.');
        return redirect()->back();
    }

    protected function authenticated(Request $request, $user)
    {
        // Reset failed attempts on successful login
        $user->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);
    }
}
