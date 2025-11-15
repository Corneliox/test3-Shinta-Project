<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Artwork;
use App\Models\ArtistProfile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===================================
// 1. HOMEPAGE ROUTE (Combined)
// ===================================
Route::get('/', function () {
    
    // Load Artworks for "Creative" Section
    $lukisan_artworks = Artwork::where('category', 'Lukisan')
        ->whereHas('user', fn($query) => $query->where('is_artist', true))
        ->latest()->take(10)->get();
        
    $craft_artworks = Artwork::where('category', 'Craft')
        ->whereHas('user', fn($query) => $query->where('is_artist', true))
        ->latest()->take(10)->get();

    // LOAD ARTISTS for "Profile Pelukis" Section
    $artists = User::where('is_artist', true)
        ->with('artistProfile') // Eager load the profile info
        ->get();

    // Pass ALL data to the view
    return view('welcome', [
        'lukisan_artworks' => $lukisan_artworks,
        'craft_artworks' => $craft_artworks,
        'artists' => $artists,
    ]);

})->name('home');


// ===================================
// 2. PUBLIC PAGE ROUTES
// ===================================
Route::get('/event', function () {
    return view('event.main');
})->name('event');

Route::get('/event-details', function () {
    return view('event.details');
})->name('event-details'); 

Route::get('/creative', function () {
   return view('creative');
})->name('creative'); 

Route::get('/contact', function () {
    return view('contact');
})->name('contact');


// ===================================
// 3. ARTIST (PELUKIS) PAGE ROUTE
// ===================================
Route::get('/pelukis/{user}', function (User $user) {
    
    // Make sure the person we're looking up is actually an artist
    if (!$user->is_artist) {
        abort(404);
    }

    // Load the artist's profile and their artworks
    $artistProfile = $user->artistProfile;
    $lukisan = $user->artworks()->where('category', 'Lukisan')->get();
    $crafts = $user->artworks()->where('category', 'Craft')->get();

    return view('pelukis.show', [
        'artist' => $user,
        'profile' => $artistProfile,
        'lukisan' => $lukisan,
        'crafts' => $crafts,
    ]);

})->name('pelukis.show');


// ===================================
// 4. AUTHENTICATED ROUTES
// ===================================

// User Profile (Public Layout)
Route::get('/my-profile', function (Request $request) {
    
    // Get the currently logged-in user
    $user = $request->user();
    
    // Find their artist profile, or create a new empty one if it doesn't exist
    // This makes sure the view always has a $profile object
    $artistProfile = $user->artistProfile ?? new ArtistProfile();

    return view('profile.show', [
        'user' => $user,
        'profile' => $artistProfile // <-- Pass the artist profile to the view
    ]);
})->middleware('auth')->name('profile.user.show');

// Admin Dashboard (Admin Layout)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

// All other Breeze routes (login, register, profile.edit, etc.)
require __DIR__.'/auth.php';