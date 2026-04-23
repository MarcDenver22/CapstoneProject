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
        // Global middleware (applied to all requests)
        $middleware->append(\App\Http\Middleware\ForceHttps::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Middleware aliases
        $middleware->alias([
            'can.access.admin' => \App\Http\Middleware\CanAccessAdmin::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'kiosk.ip.allowlist' => \App\Http\Middleware\KioskIpAllowlist::class,
            'kiosk.pin.unlock' => \App\Http\Middleware\KioskPinUnlock::class,
        ]);

        // Trust proxies for HTTPS detection behind load balancers
        // Configure TRUSTED_PROXIES in .env for your specific setup
        $trustedProxies = array_filter(explode(',', env('TRUSTED_PROXIES', '')));
        if (!empty($trustedProxies)) {
            $middleware->trustProxies(at: $trustedProxies);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
