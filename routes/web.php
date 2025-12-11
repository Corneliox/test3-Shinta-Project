<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Artwork;
use App\Models\Event;
use App\Models\User;
use App\Models\ArtistProfile;
use App\Models\ContactSubmission;

// --- Controllers ---
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\EventController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\ArtistDashboardController;

// --- Admin Controllers ---
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\HeroImageController;
use App\Http\Controllers\Admin\SecurityQuizController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;

// --- Middleware ---
use App\Http\Middleware\SuperAdminDeviceCheck;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===================================
// 1. PUBLIC ROUTES (GUEST)
// ===================================

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Events
Route::get('/events', [EventController::class, 'index'])->name('event');
Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('event.details');

// Creative (Old Page - Artist List)
Route::get('/creative', function () {
    $artists = User::where('is_artist', true)
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

// Contact, Search, About
Route::get('/contact', function () { return view('contact'); })->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/about', function () { return view('about'); })->name('about');

// Artwork Details (Public)
Route::get('/artworks/{artwork:slug}', [ArtworkController::class, 'show'])->name('artworks.show');

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


// ===================================
// 2. AUTHENTICATED ROUTES (LOGGED IN)
// ===================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // --- User Profile ---
    Route::get('/my-profile', function (Request $request) {
        $user = $request->user();
        $artistProfile = $user->artistProfile ?? new ArtistProfile();
        return view('profile.show', ['user' => $user, 'profile' => $artistProfile]);
    })->name('profile.user.show');

    // --- Profile Settings (Breeze Standard) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Artist Profile Settings ---
    Route::patch('/my-profile/artist', [ProfileController::class, 'updateArtistProfile'])->name('artist.profile.update');
    Route::post('/profile/set-gate', [ProfileController::class, 'setAsGate'])->name('profile.set-gate'); // Admin Helper

    // --- ARTWORK MANAGEMENT (The Missing Piece) ---
    Route::prefix('my-artworks')->name('artworks.')->group(function () {
        Route::get('/', [ArtworkController::class, 'index'])->name('index');      // List
        Route::get('/create', [ArtworkController::class, 'create'])->name('create'); // Form
        Route::post('/', [ArtworkController::class, 'store'])->name('store');     // Save
        
        // Note: Using {artwork} implies ID. If your controller uses slug, change to {artwork:slug}
        Route::get('/{artwork}/edit', [ArtworkController::class, 'edit'])->name('edit');     // Edit Form
        Route::patch('/{artwork}', [ArtworkController::class, 'update'])->name('update');    // Update
        Route::delete('/{artwork}', [ArtworkController::class, 'destroy'])->name('destroy'); // Delete
        
        // NEW: Route to handle the "Pull Image" AJAX request
        Route::post('/preview-from-url', [ArtworkController::class, 'previewImage'])->name('preview');

        // Buy Action
        Route::get('/{artwork}/buy', [MarketplaceController::class, 'buy'])->name('buy'); 
    });

    // --- Artist Dashboard (Legacy / Notifications) ---
    Route::get('/artist/dashboard', [ArtistDashboardController::class, 'index'])->name('artist.dashboard');
    Route::post('/artworks/{artwork}/confirm', [ArtistDashboardController::class, 'confirmSale'])->name('artworks.confirm');
    Route::post('/artworks/{artwork}/reject', [ArtistDashboardController::class, 'rejectSale'])->name('artworks.reject');
});


// ===================================
// 3. ADMIN DASHBOARD
// ===================================
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
        'pendingOrders' => $pendingOrders
    ]);
})->middleware(['auth', 'verified', 'admin'])->name('dashboard');


// ===================================
// 4. ADMIN-ONLY ROUTES
// ===================================

// A. Security Quiz Routes (Must be accessible BEFORE the check)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function() {
    Route::get('/security-quiz', [SecurityQuizController::class, 'show'])->name('security.quiz');
    Route::post('/security-quiz/verify', [SecurityQuizController::class, 'verify'])->name('security.verify');
});

// B. Protected Admin Routes (Wrapped with SuperAdminDeviceCheck)
Route::middleware(['auth', 'admin', SuperAdminDeviceCheck::class]) // <--- The Check is applied here
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    
    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    
    // User Editing & Actions
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit'); // NEW
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update'); // NEW
    
    Route::patch('/users/{user}/toggle-admin', [UserController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::patch('/users/{user}/toggle-super', [UserController::class, 'toggleSuperAdmin'])->name('users.toggle-super');
    Route::patch('/users/{user}/toggle-artist', [UserController::class, 'toggleArtist'])->name('users.toggle-artist');
    Route::post('/users/{user}/promote-super', [UserController::class, 'promoteToSuperAdmin'])->name('users.promote-super');
    Route::post('/users/reveal-super', [UserController::class, 'revealSuperAdmins'])->name('users.reveal-super');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    
    // Admin Order Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{artwork}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/orders/{artwork}/reject', [OrderController::class, 'reject'])->name('orders.reject');

    // Events & Activities
    Route::resource('events', AdminEventController::class);
    Route::get('/admin/events/{id}/download', [AdminEventController::class, 'downloadPhotos'])->name('events.download');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Contact Submissions
    Route::get('/contact-submissions', [AdminContactController::class, 'index'])->name('contact.index');
    Route::patch('/contact-submissions/{submission}', [AdminContactController::class, 'update'])->name('contact.update');

    // Hero Carousel
    Route::resource('hero', HeroImageController::class)->except(['show', 'edit', 'update']);
    
    // NEW: Superadmin Global Artwork Management
    // (Assuming you created the Admin/ArtworkController I mentioned in the previous step)
    Route::get('/all-artworks', [App\Http\Controllers\Admin\ArtworkController::class, 'index'])->name('artworks.index');
});


// ===================================
// 5. UTILITIES (LANG & DEBUG)
// ===================================

// Route for Language Switch
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]); 
    }
    return redirect()->back();
})->name('lang.switch');


require __DIR__.'/auth.php';