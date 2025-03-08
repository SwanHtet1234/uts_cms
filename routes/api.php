<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\SecurityQuestionController;
use App\Http\Controllers\API\V1\CardController;
use App\Http\Controllers\API\V1\HomeController;
use App\Http\Controllers\API\V1\TransactionController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Middleware\TrackLoginAttempts;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('verify.pin')->group(function () {
    // Sensitive operations
    
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
    
        
    
        Route::post('/verify-username', [AuthController::class, 'verifyUsername']);
        Route::get('/user-security-questions/{user_id}', [SecurityQuestionController::class, 'getUserSecurityQuestions']);
        Route::post('/validate-security-answers', [AuthController::class, 'validateSecurityAnswers']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
        // Authenticated routes (require Sanctum token)
        Route::middleware('validate.sanctum.token')->group(function () {

            Route::post('/security-questions', [SecurityQuestionController::class, 'index']);
            Route::post('/security-answers', [SecurityQuestionController::class, 'store']);

            Route::post('/home', [HomeController::class, 'index']);
            Route::delete('/profile', [UserController::class, 'deleteAccount']);
    
            Route::post('/set-transaction-pin', [AuthController::class, 'setTransactionPin']);
            Route::post('/unlock-pin', [AuthController::class, 'unlockPin']);
            Route::post('/change-transaction-pin', [AuthController::class, 'changeTransactionPin']);
            Route::post('/transaction-types/get', [TransactionController::class, 'gettype']);
            
            Route::middleware('verify.pin')->group(function () {
                // Sensitive operations
                Route::post('/cards', [CardController::class, 'store']);
                Route::post('/cards/details', [CardController::class, 'show']);
                Route::post('/cards/change-status', [CardController::class, 'changeStatus']);
                Route::post('/cards/services', [CardController::class, 'getCardServices']);
                Route::post('/cards/update-services', [CardController::class, 'updateCardServices']);
                Route::post('/cards/update-service', [CardController::class, 'updateCardService']);
                Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
                Route::post('/profile', [UserController::class, 'getProfile']);
                Route::post('/profile/update', [UserController::class, 'updateProfile']);
                Route::post('/transactions', [TransactionController::class, 'store']);
            });

            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::get('/user-security-answers', [SecurityQuestionController::class, 'show']);
    
            // Route::get('/cards', [CardController::class, 'getUserCards']);
        });
    });




