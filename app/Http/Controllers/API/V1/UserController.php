<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProfile(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . auth()->id(),
            'phone' => 'sometimes|string|unique:users,phone,' . auth()->id(),
        ]);

        // Fetch the authenticated user
        $user = User::findOrFail($request->user_id);

        // Update user data
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        // Save the updated user
        $user->save();

        // Prepare the response data
        $responseData = [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'User data updated successfully.',
            'data' => $responseData,
            'metadata' => null,
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Validate the transaction PIN
        if (!$user->validateTransactionPin($request->transaction_pin)) {
            return response()->json(['message' => 'Invalid transaction PIN'], 401);
        }

        // Delete the user account
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }

    /**
     * Get the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Fetch the authenticated user
        $user = User::findOrFail($request->user_id);

        // Prepare the response data
        $responseData = [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ];

        // Prepare the response
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'User data retrieved successfully.',
            'data' => $responseData,
            'metadata' => null,
        ];

        return response()->json($response, 200);
    }
}
