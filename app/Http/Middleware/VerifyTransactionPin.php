<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class VerifyTransactionPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the authenticated user
        $user = User::findOrFail($request->user_id);

        // Check if the user has a transaction pin
        if (empty($user->transaction_pin)) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Transaction pin not set.',
                'data' => null,
                'metadata' => null,
            ], 403);
        }

        // Validate the transaction pin
        $request->validate([
            'transaction_pin' => 'required|string|size:6',
        ]);

        // Check if the provided pin matches the user's transaction pin
        if ($request->transaction_pin !== $user->transaction_pin) {
            return response()->json([
                'status' => 'error',
                'code' => 401,
                'message' => 'Invalid transaction pin.',
                'data' => null,
                'metadata' => null,
            ], 401);
        }

        // Proceed to the next middleware or controller
        return $next($request);
    }
}
