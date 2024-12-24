<?php

use Barn2App\Http\Middleware\HandleInertiaRequests;
use Barn2App\Http\Middleware\IframeProtection;
use Barn2App\Http\Middleware\ShopifyVerify;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('shopifyProtection', [
            ShopifyVerify::class,
            IframeProtection::class,
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
