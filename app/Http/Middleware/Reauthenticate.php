<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Reauthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (!session('reauthenticated_at') || now()->diffInMinutes(session('reauthenticated_at')) > 10) {
            return redirect()->route('reauthenticate.form')->with('error', 'Please reauthenticate to proceed.');
        }

        return $next($request);
    }
}
