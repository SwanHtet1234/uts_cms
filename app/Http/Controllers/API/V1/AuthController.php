<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\TrackLoginAttempts;

class AuthController extends Controller
{
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required|string|unique:users',
    //         'name' => 'required|string',
    //         'email' => 'nullable|email|unique:users',
    //         'phone' => 'required|string|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     $user = User::create([
    //         'username' => $request->username,
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'phone' => $request->phone,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return response()->json([
    //         'user_id' => $user->id,
    //         'message' => 'User registered successfully',
    //     ], 201);
    // } 
    
    public function register(Request $request)
    {
        // Validate the request
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'name' => 'required|string',
            'email' => 'nullable|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            ],
            'phone' => 'required|string|unique:users,phone',
        ], [
            'password.regex' => 'The password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.',
        ]);

        // Hash the password
        $hashedPassword = Hash::make($request->password);

        // Create the user
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => $hashedPassword,
            'phone' => $request->phone,
        ]);

        // Return a success response
        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => 'Registration successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ],
            'metadata' => null,
        ], 201);
    }
    
    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function login(Request $request)
    // {
    //     // Validate the request
    //     $request->validate([
    //         'username' => 'required|string',
    //         'password' => 'required|string',
    //     ]);

    //     // Attempt to authenticate the user
    //     if (Auth::attempt($request->only('username', 'password'))) {
    //         // Reset login attempts on successful login
    //         TrackLoginAttempts::resetAttempts($request->ip());

    //         // Authentication successful
    //         $user = Auth::user();
    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         return response()->json([
    //             'status' => 'success',
    //             'code' => 200,
    //             'message' => 'Login successful.',
    //             'data' => [
    //                 'user_id' => $user->id,
    //                 'token' => $token,
    //                 'expires_in' => 1440, // 1 day in minutes
    //             ],
    //             'metadata' => null,
    //         ]);
    //     }

    //     // Increment login attempts on failed login
    //     TrackLoginAttempts::incrementAttempts($request->ip());

    //     // Authentication failed
    //     throw ValidationException::withMessages([
    //         'wrong_credentials' => ['The provided credentials are incorrect.'],
    //     ]);
    // }

    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('username', 'password'))) {
            // Reset login attempts on successful login
            TrackLoginAttempts::resetAttempts($request->ip());

            // Authentication successful
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            // Check if it's the user's first login
            $hasSecurityQuestions = $user->securityAnswers()->count() >= 3;
            $hasTransactionPin = !is_null($user->transaction_pin);
            $isFirstLogin = !$hasSecurityQuestions || !$hasTransactionPin;

            // Prepare the response data
            $responseData = [
                'user_id' => $user->id,
                'token' => $token,
                'expires_in' => 1440, // 1 day in minutes
            ];

            // if ($isFirstLogin) {
            //     $responseData['first_login'] = true;
            //     $responseMessage = 'Please complete your security questions and set a transaction pin.';
            // } else {
            //     $responseData['first_login'] = false;
            //     $responseMessage = 'Login successful.';
            // }

            $responseData['security_flag'] = $hasSecurityQuestions ? true : false;
            $responseData['set_pin_flag'] = $hasTransactionPin ? true : false;
            if ($isFirstLogin){
                $responseMessage = 'Please complete your security questions and set a transaction pin.';
            } elseif (!$hasSecurityQuestions){
                $responseMessage = 'Please complete your security questions.';
            } elseif (!$hasTransactionPin){
                $responseMessage = 'Please set a Transaction Pin.';
            } else {
                $responseMessage = "Login successful.";
            }

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $responseMessage,
                'data' => $responseData,
                'metadata' => null,
            ]);
        }

        // Increment login attempts on failed login
        TrackLoginAttempts::incrementAttempts($request->ip());

        // Authentication failed
        throw ValidationException::withMessages([
            'wrong_credentials' => ['The provided credentials are incorrect.'],
        ]);
    }


    public function setTransactionPin(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_pin' => 'required|string|digits:6|numeric',
        ]);

        // Get the authenticated user
        $authenticatedUser = User::findOrFail($request->user_id);

        // Check if the user has already set a transaction pin
        if ($authenticatedUser->transaction_pin !== null) {
            return response()->json([
                'status' => 'error',
                'code' => 400,
                'message' => 'Transaction pin has already been set.',
                'data' => null,
                'metadata' => null,
            ], 400);
        }

        // Update the user's transaction pin
        $authenticatedUser->update([
            'transaction_pin' => $request->transaction_pin,
        ]);

        // Return a success response
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Transaction pin set successfully.',
            'data' => null,
            'metadata' => null,
        ]);
    }

    public function logout(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'transaction_pin' => 'required|string|size:6',
        ]);

        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'Logged out successfully.',
            'data' => null,
            'metadata' => null,
        ];

        return response()->json($response, 200);
    }

    public function unlockPin(Request $request)
    {
        $user = $request->user();

        // Add logic to verify user (e.g., OTP via email/phone)
        $user->update([
            'is_pin_locked' => false,
            'pin_attempt' => 0,
        ]);

        return response()->json(['message' => 'Transaction PIN unlocked successfully'], 200);
    }

    /**
     * Check if a username is already taken.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $usernameExists = User::where('username', $request->username)->exists();

        return response()->json([
            'username' => $request->username,
            'exists' => $usernameExists,
        ]);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $emailExists = User::where('email', $request->email)->exists();

        return response()->json([
            'email' => $request->email,
            'exists' => $emailExists,
        ]);
    }

    public function changeTransactionPin(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_transaction_pin' => 'required|string|min:4|max:4',
        ]);

        $user = $request->user();

        // Verify the main password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        // Update the transaction PIN
        $user->transaction_pin = Hash::make($request->new_transaction_pin);
        $user->save();

        return response()->json(['message' => 'Transaction PIN changed successfully']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        // Verify the current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Invalid current password'], 401);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function verifyUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json(['message' => 'Username not found'], 404);
        }

        // Check if the user has answered security questions
        $hasSecurityAnswers = $user->securityAnswers()->exists();

        return response()->json([
            'user_id' => $user->id,
            'has_security_answers' => $hasSecurityAnswers,
        ]);
    }

    public function validateSecurityAnswers(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:security_questions,id',
            'answers.*.answer' => 'required|string',
        ]);

        $user = User::findOrFail($request->user_id);

        // Validate security answers
        foreach ($request->answers as $answer) {
            $userAnswer = $user->securityAnswers()
                ->where('question_id', $answer['question_id'])
                ->first();

            if (!$userAnswer || !Hash::check($answer['answer'], $userAnswer->answer)) {
                return response()->json(['message' => 'Invalid security answers'], 401);
            }
        }

        return response()->json(['message' => 'Security answers validated successfully']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($request->user_id);

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
