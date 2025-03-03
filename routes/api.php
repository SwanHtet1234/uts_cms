<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\SecurityQuestionController;
use App\Http\Controllers\API\V1\CardController;
use App\Http\Middleware\TrackLoginAttempts;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'verify.pin'])->group(function () {
    // Sensitive operations
    // Route::post('/transfer', [TransactionController::class, 'transfer']);
    // Route::post('/withdraw', [TransactionController::class, 'withdraw']);
});

Route::middleware(['api.key', 'throttle:api'])
    ->prefix('v1')
    ->group(function () {
        // Public routes (no authentication required)
        Route::post('/register', [AuthController::class, 'register']);

        Route::middleware([TrackLoginAttempts::class])->group(function(){
            Route::post('/login', [AuthController::class, 'login']);
        });
        
    
        Route::get('/check-username', [AuthController::class, 'checkUsername']);
        Route::get('/check-email', [AuthController::class, 'checkEmail']);
    
        Route::get('/security-questions', [SecurityQuestionController::class, 'index']);
        Route::post('/security-answers', [SecurityQuestionController::class, 'store']);
    
        Route::post('/verify-username', [AuthController::class, 'verifyUsername']);
        Route::get('/user-security-questions/{user_id}', [SecurityQuestionController::class, 'getUserSecurityQuestions']);
        Route::post('/validate-security-answers', [AuthController::class, 'validateSecurityAnswers']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
        // Authenticated routes (require Sanctum token)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
    
            Route::get('/profile', [UserController::class, 'getProfile']);
            Route::put('/profile', [UserController::class, 'updateProfile'])->middleware('verify.pin');
            Route::delete('/profile', [UserController::class, 'deleteAccount'])->middleware('verify.pin');
    
            Route::post('/set-transaction-pin', [AuthController::class, 'setTransactionPin']);
            Route::post('/unlock-pin', [AuthController::class, 'unlockPin']);
            Route::post('/change-transaction-pin', [AuthController::class, 'changeTransactionPin']);
    
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::get('/user-security-answers', [SecurityQuestionController::class, 'show']);
    
            Route::get('/cards', [CardController::class, 'getUserCards']);
        });
    });




