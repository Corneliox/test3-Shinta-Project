<?php

use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController; // <-- Public Contact
use App\Http\Controllers\SearchController;  // <-- Search
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ContactController as AdminContactController; // <-- Admin Contact
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\Event;
use App\Models\User;
use App\Models\ArtistProfile;
use App\Models\ContactSubmission; // <-- THIS WAS MISSING

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===================================
// 1. HOMEPAGE ROUTE
// ===================================
Route::get('/', function () {
    
    // Load Artworks
    $lukisan_artworks = Artwork::where('category', 'Lukisan')
        ->whereHas('user', fn($query) => $query->where('is_artist', true))
        ->latest()->take(10)->get();
        
    $craft_artworks = Artwork::where('category', 'Craft')
        ->whereHas('user', fn($query) => $query->where('is_artist', true))
        ->latest()->take(10)->get();

    // Load Artists
    $artists = User::where('is_artist', true)
        ->with('artistProfile')
        ->get();

    // Load Events
    $pinned_event = Event::where('is_pinned', true)->latest()->first();
    $newest_events = Event::where('is_pinned', false)->latest()->take(3)->get();

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

// Events
Route::get('/events', [EventController::class, 'index'])->name('event');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('event.details');

// Creative
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

// Contact Form (GET & POST)
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Artwork Details
Route::get('/artworks/{artwork:slug}', [ArtworkController::class, 'show'])->name('artworks.show');

Route::get('/about', function () {
    return view('about');
})->name('about');

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

// User Profile
Route::get('/my-profile', function (Request $request) {
    $user = $request->user();
    $artistProfile = $user->artistProfile ?? new ArtistProfile();
    return view('profile.show', [
        'user' => $user,
        'profile' => $artistProfile
    ]);
})->middleware('auth')->name('profile.user.show');

Route::patch('/my-profile/artist', [ProfileController::class, 'updateArtistProfile'])
    ->middleware('auth')
    ->name('artist.profile.update');

// Dashboard (With Contact Submissions)
Route::get('/dashboard', function () {
    // Fetch unseen contact submissions
    $unseenSubmissions = ContactSubmission::where('is_seen', false)->latest()->get();
    
    return view('dashboard', [
        'unseenSubmissions' => $unseenSubmissions
    ]);
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');

// Artwork Management
Route::get('/my-artworks', [ArtworkController::class, 'index'])
    ->middleware('auth')->name('artworks.index');

Route::post('/my-artworks', [ArtworkController::class, 'store'])
    ->middleware('auth')->name('artworks.store');

Route::delete('/my-artworks/{artwork}', [ArtworkController::class, 'destroy'])
    ->middleware('auth')->name('artworks.destroy');

Route::get('/artworks/{artwork:slug}/edit', [ArtworkController::class, 'edit'])
    ->middleware('auth')->name('artworks.edit');

Route::patch('/artworks/{artwork:slug}', [ArtworkController::class, 'update'])
    ->middleware('auth')->name('artworks.update');


// ===================================
// 5. ADMIN-ONLY ROUTES
// ===================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    // 1. Create New User (Manual Add)
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // 2. Existing Artist Toggle
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    
    // 3. Toggle Admin Status (Only for Superadmin)
    Route::patch('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');

    // 4. NEW: Toggle Superadmin Status (For demoting other superadmins)
    Route::patch('/users/{user}/toggle-super', [UserController::class, 'toggleSuperAdmin'])->name('users.toggle-super');

    // 5. SECRET ROUTE: Promote to Superadmin (10 clicks)
    Route::post('/users/{user}/promote-super', [UserController::class, 'promoteToSuperAdmin'])->name('users.promote-super');

    // 6. NEW SECRET: Reveal All Superadmins (5 clicks on header)
    Route::post('/users/reveal-super', [UserController::class, 'revealSuperAdmins'])->name('users.reveal-super');
    
    // 7. Delete User
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Events
    Route::resource('events', AdminEventController::class);

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Contact Submissions
    Route::get('/contact-submissions', [AdminContactController::class, 'index'])->name('contact.index');
    Route::patch('/contact-submissions/{submission}', [AdminContactController::class, 'update'])->name('contact.update');

});

require __DIR__.'/auth.php';