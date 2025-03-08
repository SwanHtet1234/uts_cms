<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class ValidateSanctumToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the Bearer token from the request header
        $token = $request->bearerToken();

        // Check if the token exists
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthorized. No token provided.',
                'data' => null,
                'metadata' => null,
            ], 401);
        }

        // Validate the token
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Unauthorized. Invalid token.',
                'data' => null,
                'metadata' => null,
            ], 401);
        }

        // Get the authenticated user from the token
        $user = $accessToken->tokenable;

        // Validate the user_id parameter
        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Bad Request. user_id is required.',
                'data' => null,
                'metadata' => null,
            ], 400);
        }

        if ($userId != $user->id) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Forbidden. You are not authorized to access this resource.',
                'data' => null,
                'metadata' => null,
            ], 403);
        }

        // Attach the authenticated user to the request
        $request->merge(['user' => $user]);

        return $next($request);
    }
}
