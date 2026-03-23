<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\DashboardMiddleware;
use App\Http\Middleware\RoleAuthMiddleware;
use App\Http\Middleware\AdvancedRateLimit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => Authenticate::class,
            'role.check'=> RoleAuthMiddleware::class,
            'dashboard.check'=> DashboardMiddleware::class,
            'advanced.rate.limit' => AdvancedRateLimit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle rate limiting exceptions
        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.',
                    'retry_after' => (int) $retryAfter,
                ], 429, [
                    'Retry-After' => $retryAfter,
                    'X-RateLimit-Limit' => $e->getHeaders()['X-RateLimit-Limit'] ?? null,
                    'X-RateLimit-Remaining' => $e->getHeaders()['X-RateLimit-Remaining'] ?? null,
                    'X-RateLimit-Reset' => $e->getHeaders()['X-RateLimit-Reset'] ?? null,
                ]);
            }
        });
    })->create();
