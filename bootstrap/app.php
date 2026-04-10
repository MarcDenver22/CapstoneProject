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
        $middleware->alias([
            'can.access.admin' => \App\Http\Middleware\CanAccessAdmin::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'kiosk.ip.allowlist' => \App\Http\Middleware\KioskIpAllowlist::class,
            'kiosk.pin.unlock' => \App\Http\Middleware\KioskPinUnlock::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
