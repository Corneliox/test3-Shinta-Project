<?php

use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\Event;
use App\Models\User;
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

    // NEW: Add these queries for the events
        $pinned_event = Event::where('is_pinned', true)->latest()->first();
        $newest_events = Event::where('is_pinned', false)->latest()->take(3)->get();

    // Pass ALL data to the view
    return view('welcome', [
        'lukisan_artworks' => $lukisan_artworks,
        'craft_artworks' => $craft_artworks,
        'artists' => $artists,
        'pinned_event' => $pinned_event,
        'newest_events' => $newest_events,
    ]);

})->name('home');


// ===================================
// 2. PUBLIC PAGE ROUTES
// ===================================
Route::get('/events', [EventController::class, 'index'])
    ->name('event');

Route::get('/events/{event:slug}', [EventController::class, 'show'])
    ->name('event.details');

Route::get('/creative', function () {

    $artists = App\Models\User::where('is_artist', true)
        ->whereHas('artworks')
        ->with(['artistProfile', 'artworks' => function($query) {
            $query->latest()->take(10);
        }])
        ->withMax('artworks', 'created_at')
        ->orderBy('artworks_max_created_at', 'desc')
        ->get();

   return view('creative', [
        'artists' => $artists
   ]);

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
Route::get('/pelukis/{artist:slug}', function (User $artist) {
    
    if (!$artist->is_artist) {
        abort(404);
    }

    $artistProfile = $artist->artistProfile;
    $lukisan = $artist->artworks()->where('category', 'Lukisan')->get();
    $crafts = $artist->artworks()->where('category', 'Craft')->get();

    return view('pelukis.show', [
        'artist' => $artist,
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
    $artistProfile = $user->artistProfile ?? new ArtistProfile();

    return view('profile.show', [
        'user' => $user,
        'profile' => $artistProfile
    ]);
})->middleware('auth')->name('profile.user.show');

// NEW route for the artist form
Route::patch('/my-profile/artist', [ProfileController::class, 'updateArtistProfile'])
    ->middleware('auth')
    ->name('artist.profile.update');

// Admin Dashboard (Admin Layout)
// !!!!! THIS LINE IS NOW FIXED !!!!!
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

// ARTWORK MANAGEMENT ROUTES
Route::get('/my-artworks', [ArtworkController::class, 'index'])
    ->middleware('auth')
    ->name('artworks.index'); // The page to view and upload

Route::post('/my-artworks', [ArtworkController::class, 'store'])
    ->middleware('auth')
    ->name('artworks.store'); // The action of uploading

Route::delete('/my-artworks/{artwork}', [ArtworkController::class, 'destroy'])
    ->middleware('auth')
    ->name('artworks.destroy'); // The action of deleting

// NEW: Show the edit form
Route::get('/artworks/{artwork:slug}/edit', [ArtworkController::class, 'edit'])
    ->middleware('auth')
    ->name('artworks.edit');

// NEW: Handle the update logic
Route::patch('/artworks/{artwork:slug}', [ArtworkController::class, 'update'])
    ->middleware('auth')
    ->name('artworks.update');

// ===================================
// 5. ADMIN-ONLY ROUTES (NEW)
// ===================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Admin Event Management
    Route::resource('events', AdminEventController::class);

});

// All other Breeze routes (login, register, profile.edit, etc.)
require __DIR__.'/auth.php';