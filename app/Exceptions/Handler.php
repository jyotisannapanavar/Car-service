<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        // Handle authentication exceptions
        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            // Check if it's an API request
            $isApi = $request->is('api/*') || $request->is('v1/*') || str_starts_with($request->path(), 'api/');

            if ($isApi || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please provide a valid authentication token.',
                    'data' => null,
                ], 401);
            }
        });

        // Handle validation exceptions
        $this->renderable(function (\Illuminate\Validation\ValidationException $e, \Illuminate\Http\Request $request) {
            $isApi = $request->is('api/*') || $request->is('v1/*') || str_starts_with($request->path(), 'api/');

            if ($isApi || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                    'data' => null,
                ], 422);
            }
        });

        // Handle routing exceptions for API
        $this->renderable(function (\Symfony\Component\Routing\Exception\RouteNotFoundException $e, \Illuminate\Http\Request $request) {
            $isApi = $request->is('api/*') || $request->is('v1/*') || str_starts_with($request->path(), 'api/');

            if ($isApi || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found',
                    'data' => null,
                ], 404);
            }
        });

        // Handle all other exceptions for API routes
        $this->renderable(function (Throwable $e, \Illuminate\Http\Request $request) {
            $isApi = $request->is('api/*') || $request->is('v1/*') || str_starts_with($request->path(), 'api/');

            if ($isApi && !$request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data' => null,
                ], 500);
            }
        });
    }
}
