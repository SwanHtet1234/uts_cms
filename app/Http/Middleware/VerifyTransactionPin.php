<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

class VerifyTransactionPin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if the user has set a transaction PIN
        if (!$user->transaction_pin) {
            return response()->json(['message' => 'Transaction PIN not set'], 403);
        }

        // Check if the PIN is locked
        if ($user->is_pin_locked) {
            return response()->json(['message' => 'Transaction PIN is locked'], 403);
        }

        // Verify the transaction PIN
        if (!Hash::check($request->transaction_pin, $user->transaction_pin)) {
            $user->increment('pin_attempt');

            // Lock the PIN after 3 failed attempts
            if ($user->pin_attempt >= 3) {
                $user->update(['is_pin_locked' => true]);
                return response()->json(['message' => 'Too many attempts. PIN locked.'], 403);
            }

            return response()->json(['message' => 'Invalid transaction PIN'], 401);
        }

        // Reset PIN attempts on successful verification
        $user->update(['pin_attempt' => 0]);

        return $next($request);
    }
}
