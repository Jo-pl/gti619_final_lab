<?php

namespace App\Http\Middleware;

use Closure;

class SessionIntegrity
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');

        if (session('ip') === null) {
            session(['ip' => $ip, 'user_agent' => $userAgent]);
        }

        if (session('ip') !== $ip || session('user_agent') !== $userAgent) {
            auth()->logout();
            session()->invalidate();
            return redirect('/login')->with('error', 'Session terminated for security reasons.');
        }

        return $next($request);
    }
}

