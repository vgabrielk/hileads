<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware
        $middleware->append(\App\Http\Middleware\CheckRequestSize::class);
        $middleware->append(\App\Http\Middleware\SanitizeInputMiddleware::class);
        $middleware->append(\App\Http\Middleware\PerformanceHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\OptimizeQueries::class);
        
        // Middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'token.auth' => \App\Http\Middleware\TokenAuthMiddleware::class,
            'subscription.required' => \App\Http\Middleware\AdminAccessMiddleware::class,
            'subscription.security' => \App\Http\Middleware\SubscriptionSecurityMiddleware::class,
            'bestfy.webhook' => \App\Http\Middleware\BestfyWebhookMiddleware::class,
            'subscription.access' => \App\Http\Middleware\CheckSubscriptionAccess::class,
            'rate.limit' => \App\Http\Middleware\RateLimitMiddleware::class,
            'response.cache' => \App\Http\Middleware\ResponseCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
