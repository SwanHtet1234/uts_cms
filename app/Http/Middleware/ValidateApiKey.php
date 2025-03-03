<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the API key from the request header
        $apiKey = $request->header('X-API-KEY');

        // Get the valid API key from the environment
        $validApiKey = env('MOBILE_APP_API_KEY');

        // Validate the API key
        if ($apiKey !== $validApiKey) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthorized. Invalid API key.',
                'data' => null,
                'metadata' => null,
            ], 401);
        }

        return $next($request);
    }
}
