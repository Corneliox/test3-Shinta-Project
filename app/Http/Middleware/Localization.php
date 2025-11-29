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
            // Jika belum, deteksi browser user (Auto-detect)
            // Ambil 2 huruf pertama (misal: 'id-ID' jadi 'id')
            $browserLang = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
            
            if (in_array($browserLang, ['id', 'en'])) {
                App::setLocale($browserLang);
            } else {
                App::setLocale('id'); // Default ke Indonesia
            }
        }

        return $next($request);
    }
}