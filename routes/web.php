<?php

use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MarketplaceController; // <--- NEW
use App\Http\Controllers\ArtistDashboardController; // <--- NEW
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\Event;
use App\Models\User;
use App\Models\ArtistProfile;
use App\Models\ContactSubmission;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. HOMEPAGE
Route::get('/', function () {
    $lukisan_artworks = Artwork::where('category', 'Lukisan')
        ->whereHas('user', fn($query) => $query->where('is_artist', true))
        ->latest()->take(10)->get();
        
    $craft_artworks = Artwork::where('category', 'Craft')
        ->whereHas('user', fn($query) => $query->where('is_artist', true))
        ->latest()->take(10)->get();

    $artists = User::where('is_artist', true)
        ->with('artistProfile')
        ->orderBy('name', 'asc')
        ->get();

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

// 2. PUBLIC ROUTES
Route::get('/events', [EventController::class, 'index'])->name('event');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('event.details');

// Creative (Old Page - Artist List)
Route::get('/creative', function () {
    $artists = App\Models\User::where('is_artist', true)
        ->whereHas('artworks')
        ->with(['artistProfile', 'artworks' => function($query) {
            $query->latest()->take(10);
        }])
        ->withMax('artworks', 'created_at')
        ->orderBy('artworks_max_created_at', 'desc')
        ->get();

   return view('creative', ['artists' => $artists]);
})->name('creative');

// Marketplace (New Page - Items)
Route::get('/marketplace', [MarketplaceController::class, 'index'])->name('marketplace.index');

// Contact, Search, About, Artwork Details
Route::get('/contact', function () { return view('contact'); })->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/artworks/{artwork:slug}', [ArtworkController::class, 'show'])->name('artworks.show');
Route::get('/about', function () { return view('about'); })->name('about');

// Artist Profile Page (Public)
Route::get('/pelukis/{artist:slug}', function (User $artist) {
    if (!$artist->is_artist) abort(404);
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

// 3. AUTHENTICATED ROUTES
Route::middleware('auth')->group(function () {
    // User Profile
    Route::get('/my-profile', function (Request $request) {
        $user = $request->user();
        $artistProfile = $user->artistProfile ?? new ArtistProfile();
        return view('profile.show', ['user' => $user, 'profile' => $artistProfile]);
    })->name('profile.user.show');

    Route::patch('/my-profile/artist', [ProfileController::class, 'updateArtistProfile'])->name('artist.profile.update');

    // Artwork Management
    Route::get('/my-artworks', [ArtworkController::class, 'index'])->name('artworks.index');
    Route::post('/my-artworks', [ArtworkController::class, 'store'])->name('artworks.store');
    Route::delete('/my-artworks/{artwork}', [ArtworkController::class, 'destroy'])->name('artworks.destroy');
    Route::get('/artworks/{artwork:slug}/edit', [ArtworkController::class, 'edit'])->name('artworks.edit');
    Route::patch('/artworks/{artwork:slug}', [ArtworkController::class, 'update'])->name('artworks.update');

    // MARKETPLACE ACTIONS (Buy)
    Route::get('/artworks/{artwork}/buy', [MarketplaceController::class, 'buy'])->name('artworks.buy');

    // ARTIST DASHBOARD (Notifications & Confirmations)
    Route::get('/artist/dashboard', [ArtistDashboardController::class, 'index'])->name('artist.dashboard');
    Route::post('/artworks/{artwork}/confirm', [ArtistDashboardController::class, 'confirmSale'])->name('artworks.confirm');
    Route::post('/artworks/{artwork}/reject', [ArtistDashboardController::class, 'rejectSale'])->name('artworks.reject');
});

// Admin Dashboard
Route::get('/dashboard', function () {
    // 1. Fetch Unseen Contact Submissions
    $unseenSubmissions = ContactSubmission::where('is_seen', false)->latest()->get();
    
    // 2. Fetch Pending Orders (Items currently reserved)
    $pendingOrders = Artwork::where('reserved_stock', '>', 0)
                            ->with('user') // Load Artist info
                            ->orderBy('reserved_until', 'asc') // Oldest reservation first (urgent)
                            ->get();

    return view('dashboard', [
        'unseenSubmissions' => $unseenSubmissions,
        'pendingOrders' => $pendingOrders // <--- Pass this to the view
    ]);
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');
// 4. ADMIN-ONLY ROUTES
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::patch('/users/{user}/toggle-super', [UserController::class, 'toggleSuperAdmin'])->name('users.toggle-super');
    Route::post('/users/{user}/promote-super', [UserController::class, 'promoteToSuperAdmin'])->name('users.promote-super');
    Route::post('/users/reveal-super', [UserController::class, 'revealSuperAdmins'])->name('users.reveal-super');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // ADMIN ORDER MANAGEMENT
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{artwork}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/orders/{artwork}/reject', [OrderController::class, 'reject'])->name('orders.reject');

    Route::resource('events', AdminEventController::class);
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/contact-submissions', [AdminContactController::class, 'index'])->name('contact.index');
    Route::patch('/contact-submissions/{submission}', [AdminContactController::class, 'update'])->name('contact.update');
});

require __DIR__.'/auth.php';