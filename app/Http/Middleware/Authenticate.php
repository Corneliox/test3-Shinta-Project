<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // This tells Laravel that if a non-logged-in user
        // tries to access a protected page (like /dashboard),
        // it should send them to the 'login' route.
        return $request->expectsJson() ? null : route('login');
    }
}