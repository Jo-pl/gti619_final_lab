<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $maxAttempts = 5; // Maximum allowed attempts
    protected $lockoutTime = 15; // Lockout duration in minutes

    /**
     * Handle failed login attempts.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $request->email]);

        if ($user) {
            // Increment failed attempts
            $user->increment('failed_attempts');

            // Lock the user if max attempts are reached
            if ($user->failed_attempts >= $this->maxAttempts) {
                $user->update([
                    'locked_until' => Carbon::now()->addMinutes($this->lockoutTime),
                    'failed_attempts' => 0, // Reset failed attempts after locking
                ]);
                Log::debug('User locked:', ['email' => $user->email, 'locked_until' => $user->locked_until]);
            }
            if ($user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
                // Calculate the time left until unlock
                $timeLeft = now()->diffInMinutes($user->locked_until);
            
                // Use session()->put() to keep the message in session across requests
                session()->put('error', "Account locked. Try again in $timeLeft minutes.");
            
                // Debug log to confirm the session error is set correctly
                Log::debug('Flash session error set for locked account', [
                    'error' => session('error'),
                    'email' => $user->email,
                    'locked_until' => $user->locked_until,
                    'time_left' => $timeLeft
                ]);
            
                return redirect()->route('login'); // Redirect to login page
            }
        }

        // Flash general invalid credentials error if the user is not locked
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
        // Validate the incoming login credentials
        $credentials = $request->only('email', 'password');
        
        // Check if the user exists and if the account is locked
        $user = User::where('email', $request->email)->first();
        
        // If user is locked, handle this case
        if ($user && $user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
            // Calculate the remaining time before the account can be unlocked
            $timeLeft = now()->diffInMinutes($user->locked_until);
            
            // Store the error message in session so it persists across requests
            session()->put('error', "Account locked. Try again in $timeLeft minutes.");
            
            // Log the situation for debugging
            Log::debug('User locked out:', [
                'email' => $user->email,
                'locked_until' => $user->locked_until,
                'time_left' => $timeLeft
            ]);
            
            // Redirect the user back to the login page with the error message
            return redirect()->route('login');
        }
        
        // Attempt the login with the provided credentials
        if (Auth::attempt($credentials)) {
            // Log the user in, regenerate the session, and redirect to the intended page
            return redirect()->intended('/home');
        }

        // If credentials are incorrect, show a general invalid credentials error
        session()->flash('error', 'Invalid credentials.');
        return redirect()->back();
    }

}
