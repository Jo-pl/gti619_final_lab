<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $maxAttempts; // Maximum allowed attempts
    protected $lockoutTime = 15; // Lockout duration in minutes



    public function __construct()
    {
        $this->maxAttempts = config('auth.max_attempts', 3); // Default to 3 if not set
    }

    /**
     * Handle failed login attempts.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Increment failed attempts
            $user->failed_attempts = $user->failed_attempts + 1;
            $user->save();

            // Lock the user if max attempts are reached
            if ($user->failed_attempts >= $this->maxAttempts) {
                $user->update([
                    'locked_until' => Carbon::now()->addMinutes($this->lockoutTime),
                    'failed_attempts' => 0, // Reset failed attempts after locking
                ]);
                Log::debug('User locked:', [
                    'email' => $user->email,
                    'locked_until' => $user->locked_until,
                ]);
            }
        }

        // Flash general invalid credentials error
        session()->flash('error', 'Invalid credentials.');
        return redirect()->back();
    }

    /**
     * Handle successful authentication.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        // Log the authentication status
        Log::info('User authenticated:', ['email' => $user->email]);

        // Regenerate the session
        session()->regenerate();

        // Store the session details in the ActiveSession model
        ActiveSession::updateOrCreate(
            ['session_id' => session()->getId()],
            [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'last_activity' => now(),
            ]
        );

        // Log session information
        Log::info('User session recorded.', [
            'user_id' => $user->id,
            'session_id' => session()->getId(),
        ]);

        // Redirect user to intended page or home
        return redirect()->intended('/home');
    }

    /**
     * Logout the user and invalidate the session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log user logout
        Log::info('User logged out.', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'session_id' => session()->getId(),
        ]);

        // Invalidate the session
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return redirect()->back()->with('error', 'Invalid credentials.');
        }
    
        // Handle account lockout
        if ($user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
            $timeLeft = Carbon::now()->diffInMinutes($user->locked_until);
            return redirect()->back()->with('error', "Account locked. Try again in $timeLeft minutes.");
        }
    
        // Attempt to authenticate
        if (!Auth::attempt($request->only('email', 'password'))) {
            $user->increment('failed_attempts');
    
            if ($user->failed_attempts >= $this->maxAttempts) {
                $user->update([
                    'locked_until' => Carbon::now()->addMinutes($this->lockoutTime),
                    'failed_attempts' => 0,
                ]);
                return redirect()->back()->with('error', "Account locked for {$this->lockoutTime} minutes.");
            }
    
            return redirect()->back()->with('error', 'Invalid credentials.');
        }
    
        // Reset failed attempts after successful login
        $user->update([
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);
    
        return redirect()->intended('/home');
    }
    

}
