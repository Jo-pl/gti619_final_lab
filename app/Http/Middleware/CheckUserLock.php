<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckUserLock
{
    public function handle($request, Closure $next)
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $request->email]);

        if ($user && $user->locked_until && Carbon::now()->lessThan($user->locked_until)) {
            $timeLeft = Carbon::now()->diffInMinutes($user->locked_until);
            return redirect()->back()->withErrors([
                'error' => "Account locked. Try again in $timeLeft minutes."
            ]);
        }

        return $next($request);
    }
}
