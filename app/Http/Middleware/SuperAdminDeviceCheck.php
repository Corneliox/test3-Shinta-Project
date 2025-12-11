<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminDeviceCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Only run this check for Super Admins
        if ($user && $user->is_super_admin) {
            
            // 2. Check if this device has the "Trusted" cookie
            if (!$request->hasCookie('admin_device_trusted')) {
                
                // 3. If not trusted, and not currently ON the quiz page, redirect to quiz
                if (!$request->is('admin/security-quiz') && !$request->is('admin/security-quiz/verify')) {
                    return redirect()->route('admin.security.quiz');
                }
            }
        }

        return $next($request);
    }
}