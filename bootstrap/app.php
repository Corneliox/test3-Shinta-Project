<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Http\Request; // <-- ADD THIS LINE
use Illuminate\Http\Middleware\TrustProxies;

// bootstrap/app.php

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->trustProxies(
            at: '*', // Trust all proxies for local ngrok development
            headers: Request::HEADER_X_FORWARDED_FOR | 
                        Request::HEADER_X_FORWARDED_HOST | 
                        Request::HEADER_X_FORWARDED_PORT | 
                        Request::HEADER_X_FORWARDED_PROTO | 
                        Request::HEADER_X_FORWARDED_AWS_ELB
        );
    
        $middleware->web(append: [
            \App\Http\Middleware\Localization::class,
        ]);
        
        // Add all of these aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'admin' => \App\Http\Middleware\IsAdminMiddleware::class,
        ]);

        // ...
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();