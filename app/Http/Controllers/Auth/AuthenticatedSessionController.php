<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View; // <-- Make sure this is imported

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // This is our new, custom redirect logic
        if (Auth::user()->is_admin) {
            // Admin goes to the homepage
            return redirect()->intended(route('home'));
        }

        // Regular user goes to their /my-profile page
        return redirect()->intended(route('profile.user.show'));
    }

    /**
     * Destroy an authenticated session.
     * * THIS IS THE MISSING METHOD
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}