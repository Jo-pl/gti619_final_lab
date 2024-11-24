<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class CustomThrottleRequests
{
    protected $maxAttempts = 3;
    protected $decayMinutes = 1;

    public function handle(Request $request, Closure $next, $maxAttempts = null, $decayMinutes = null)
    {
        $maxAttempts = $maxAttempts ?? $this->maxAttempts;
        $decayMinutes = $decayMinutes ?? $this->decayMinutes;

        // Get the key for the rate limiter
        $key = $this->resolveRequestSignature($request);

        // Check the rate limiter for exceeded attempts
        $limiter = app(RateLimiter::class);
        if ($limiter->tooManyAttempts($key, $maxAttempts)) {
            // Optionally, log the failed attempts
            Log::info("Too many login attempts for: {$key}");

            // Return a response for rate-limited requests
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.',
            ], 429);
        }

        // Increment the attempts counter
        $limiter->hit($key, $decayMinutes * 60);

        // Proceed to the next middleware
        return $next($request);
    }

    protected function resolveRequestSignature(Request $request)
    {
        // For login, using the email or IP address (or a combination)
        return $request->ip();
    }
}
