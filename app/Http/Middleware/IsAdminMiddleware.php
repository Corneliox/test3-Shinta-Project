<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // <-- Make sure this is the only "use Auth" line

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This is line 19 (or near it)
        // It needs the Facade to find the check() method
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // If not, redirect them to the homepage
        return redirect('/');
    }
}