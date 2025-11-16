<?php

use App\Http\Controllers\ArtworkController;
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

// NEW: Artwork Show Page Route
Route::get('/artworks/{artwork:slug}', [ArtworkController::class, 'show'])
    ->name('artworks.show');


// ===================================
// 3. ARTIST (PELUKIS) PAGE ROUTE
// ===================================
Route::get('/pelukis/{artist:slug}', function (User $artist) { // <-- CHANGED
    
    if (!$artist->is_artist) { // <-- CHANGED
        abort(404);
    }

    $artistProfile = $artist->artistProfile; // <-- CHANGED
    $lukisan = $artist->artworks()->where('category', 'Lukisan')->get(); // <-- CHANGED
    $crafts = $artist->artworks()->where('category', 'Craft')->get(); // <-- CHANGED

    return view('pelukis.show', [
        'artist' => $artist, // This now matches
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

// NEW route for the artist form
Route::patch('/my-profile/artist', [ProfileController::class, 'updateArtistProfile'])
    ->middleware('auth')
    ->name('artist.profile.update');

// Admin Dashboard (Admin Layout)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

// ARTWORK MANAGEMENT ROUTES (NEW!)
Route::get('/my-artworks', [ArtworkController::class, 'index'])
    ->middleware('auth')
    ->name('artworks.index'); // The page to view and upload

Route::post('/my-artworks', [ArtworkController::class, 'store'])
    ->middleware('auth')
    ->name('artworks.store'); // The action of uploading

Route::delete('/my-artworks/{artwork}', [ArtworkController::class, 'destroy'])
    ->middleware('auth')
    ->name('artworks.destroy'); // The action of deleting

    // ARTWORK MANAGEMENT ROUTES
Route::get('/my-artworks', [ArtworkController::class, 'index'])
    ->middleware('auth')
    ->name('artworks.index');

Route::post('/my-artworks', [ArtworkController::class, 'store'])
    ->middleware('auth')
    ->name('artworks.store');

Route::delete('/my-artworks/{artwork}', [ArtworkController::class, 'destroy'])
    ->middleware('auth')
    ->name('artworks.destroy');

// NEW: Show the edit form
Route::get('/artworks/{artwork:slug}/edit', [ArtworkController::class, 'edit'])
    ->middleware('auth')
    ->name('artworks.edit');

// NEW: Handle the update logic
Route::patch('/artworks/{artwork:slug}', [ArtworkController::class, 'update'])
    ->middleware('auth')
    ->name('artworks.update');
    
// All other Breeze routes (login, register, profile.edit, etc.)
require __DIR__.'/auth.php';