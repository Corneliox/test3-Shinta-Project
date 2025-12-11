<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SecurityQuizController extends Controller
{
    public function show()
    {
        return view('admin.security.quiz');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'answer' => 'required|string'
        ]);

        // The Secret Answer Logic
        $correct = "5 April 2018";

        if (trim($request->answer) === $correct) {
            // Success! Queue a cookie for 5 years (2628000 minutes)
            Cookie::queue('admin_device_trusted', 'true', 2628000);

            return redirect()->route('dashboard')->with('status', 'Device Verified!');
        }

        return back()->withErrors(['answer' => 'Wrong answer. Access Denied. Maybe Capitalization matters?'])->withInput();
    }
}