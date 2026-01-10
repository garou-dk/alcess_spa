<?php

use App\Http\Middleware\ForceHttps;
use App\Http\Middleware\GuestMiddleware;
use App\Http\Middleware\PreventCachingInDevelopment;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ValidateUserSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        
        //Global middleware for security
        //$middleware->prepend(ForceHttps::class);
        $middleware->append(SecurityHeaders::class);
        
        $middleware->alias([
            'guest-only' => GuestMiddleware::class,
            'role' => RoleMiddleware::class,
            'validate-session' => ValidateUserSession::class,
        ]);
        $middleware->api(append: [
            ValidateUserSession::class,
        ]);
        // Prevent caching in development
        $middleware->web(append: [
            PreventCachingInDevelopment::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Don't report certain exceptions
        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);

        // Custom rendering for production (hide sensitive details)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Resource not found.',
                ], 404);
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $statusCode = $e->getStatusCode();
                $message = match ($statusCode) {
                    401 => $e->getMessage() ?: 'Unauthorized.',
                    403 => 'Access denied.',
                    404 => 'Resource not found.',
                    419 => 'Session expired. Please refresh and try again.',
                    429 => 'Too many requests. Please slow down.',
                    500 => app()->environment('production') 
                        ? 'An unexpected error occurred.' 
                        : $e->getMessage(),
                    503 => 'Service temporarily unavailable.',
                    default => app()->environment('production')
                        ? 'An error occurred.'
                        : $e->getMessage(),
                };

                return response()->json([
                    'message' => $message,
                ], $statusCode);
            }
        });

        // Catch-all for unexpected exceptions in production
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (app()->environment('production') && ($request->is('api/*') || $request->expectsJson())) {
                return response()->json([
                    'message' => 'An unexpected error occurred.',
                ], 500);
            }
        });
    })->create();
