<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\VerifyTransactionPin;
use App\Http\Middleware\ValidateApiKey;
use App\Http\Middleware\TrackLoginAttempts;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\RateLimiter;
// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'verify.pin' => VerifyTransactionPin::class,
        ]);
        $middleware->alias([
            'api.key' => ValidateApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (TooManyRequestsHttpException $exception, Request $request) {
            return response()->json([
                'status' => 'error',
                'code' => 429,
                'message' => 'Too many requests. Please try again later.',
                'data' => null,
                'metadata' => [
                    'retry_after' => $exception->getHeaders()['Retry-After'] ?? 60, // Default to 60 seconds if missing
                ],
            ], 429);
        });

        // Handle validation errors
        $exceptions->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => 'The given data was invalid.',
                'data' => [
                    'errors' => $e->errors(),
                ],
                'metadata' => null,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        // Handle any other unexpected exceptions
        $exceptions->renderable(function (Throwable $exception, Request $request) {
            return response()->json([
                'status' => 'error',
                'code' => 500,
                'message' => 'Something went wrong on the server.',
                'data' => null,
            ], 500);
        });

        
    })->create();
