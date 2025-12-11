<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('locale')) {
            // Jika user sudah memilih bahasa manual, gunakan itu
            App::setLocale(Session::get('locale'));
        } else {
            // Default selalu ke Indonesia, abaikan bahasa browser
            App::setLocale('id');
        }

        return $next($request);
    }
}