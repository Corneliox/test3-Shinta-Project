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

// Event Routes
Route::get('/event', function () {
    return view('event.main');
})->name('event');

Route::get('/event-details', function () {
    return view('event.details');
})->name('event-details'); 

//-- End Of Event  Routes

Route::get('/creative', function () {
   return view('creative');
})->name('creative'); 

Route::get('/contact', function () {
    return view('contact');
})->name('contact'); // <-- Give this one a name too

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

// -- Artowrks on Homepage Started Here

// routes/web.php
use App\Models\Artwork; // <-- Add this at the top

// Find your homepage route
Route::get('/', function () {
    
    // Query for Lukisan
    $lukisan_artworks = Artwork::where('category', 'Lukisan')
        ->whereHas('user', fn($query) => $query->where('is_artist', true)) // Only from artists
        ->latest() // Get newest first
        ->take(10) // Limit to 10
        ->get();

    // Query for Craft
    $craft_artworks = Artwork::where('category', 'Craft')
        ->whereHas('user', fn($query) => $query->where('is_artist', true))
        ->latest()
        ->take(10)
        ->get();

    // Pass the data to the view
    return view('welcome', [
        'lukisan_artworks' => $lukisan_artworks,
        'craft_artworks' => $craft_artworks,
    ]);

})->name('home');

// -- Added End of Artworks on Homepage

// 4. Breeze's built-in auth routes (login, logout, etc.)
require __DIR__.'/auth.php';