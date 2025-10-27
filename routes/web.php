<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// 1. Your public homepage route (Breeze already made this)
// We'll use your 'welcome' view.
Route::get('/', function () {
    return view('welcome');
})->name('home'); // Give it a name 'home'

// // 2. NEW User Profile Page Route (This is the fix)
// // This route points to a view that uses your PUBLIC 'main' layout
// Route::get('/my-profile', function () {
//     return view('profile.show'); // We'll re-use this view name
// })->middleware('auth')->name('profile.user.show'); // A new, unique name

Route::get('/my-profile', function (Request $request) { // <-- 1. Inject Request
    return view('profile.show', [
        'user' => $request->user() // <-- 2. Pass the user object
    ]);
})->middleware('auth')->name('profile.user.show');

// 3. The "Admin Dashboard" route
// This was made by Breeze, but we'll add our 'admin' protection
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

// 4. Breeze's built-in auth routes (login, logout, etc.)
require __DIR__.'/auth.php';