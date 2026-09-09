<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RememberCommunityUrl;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
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
        $middleware->alias([
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'admin' => AdminMiddleware::class,
        ]);
         $middleware->web(append: [
            RememberCommunityUrl::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();