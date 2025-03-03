<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackLoginAttempts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip(); // Get the client's IP address
        $cacheKey = "login_attempts:$ip"; // Cache key for tracking attempts
        $blockedKey = $cacheKey . '_blocked'; // Cache key for block status

        // Check if the IP is blocked
        if (Cache::has($blockedKey)) {
            $retryAfter = Cache::get($blockedKey . '_retry_after'); // Get the remaining lockout time
            return response()->json([
                'status' => 'error',
                'code' => 429,
                'message' => 'Too many login attempts. Please try again after 5 minutes.',
                'data' => null,
                'metadata' => [
                    'retry_after' => $retryAfter, // Remaining time in seconds
                ],
            ], 429);
        }

        // Proceed with the request
        return $next($request);
    }

    /**
     * Increment the login attempt count for the IP.
     *
     * @param  string  $ip
     * @return void
     */
    public static function incrementAttempts(string $ip): void
    {
        $cacheKey = "login_attempts:$ip";
        $blockedKey = $cacheKey . '_blocked';
        $attempts = Cache::get($cacheKey, 0) + 1;

        // Store the attempt count for 5 minutes
        Cache::put($cacheKey, $attempts, now()->addMinutes(5));

        // Block the IP if attempts exceed 3
        if ($attempts >= 3) {
            $lockoutDuration = 300; // 5 minutes in seconds
            Cache::put($blockedKey, true, now()->addSeconds($lockoutDuration));
            Cache::put($blockedKey . '_retry_after', $lockoutDuration, now()->addSeconds($lockoutDuration));
        }
    }

    /**
     * Reset the login attempt count for the IP.
     *
     * @param  string  $ip
     * @return void
     */
    public static function resetAttempts(string $ip): void
    {
        $cacheKey = "login_attempts:$ip";
        Cache::forget($cacheKey);
        Cache::forget($cacheKey . '_blocked');
        Cache::forget($cacheKey . '_retry_after');
    }
}
