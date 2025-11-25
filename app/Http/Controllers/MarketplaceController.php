<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * This method loads your 'marketplace.blade.php' file.
     */
    public function index(Request $request)
    {
        $query = Artwork::query()
            ->with('user.artistProfile')
            ->whereNotNull('price') // Only items for sale
            ->where('price', '>', 0); // Exclude display-only

        // 1. Filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('artist')) {
            $query->where('user_id', $request->artist);
        }

        // 2. Sort
        if ($request->sort === 'oldest') {
            $query->oldest();
        } elseif ($request->sort === 'alphabetical') {
            $query->orderBy('title', 'asc');
        } else {
            $query->latest();
        }

        // 3. Get Data
        $artworks = $query->get()->sortBy(function($art) {
            return $art->isSoldOut() ? 1 : 0; // Move sold out to bottom
        });

        // 4. Get Promos
        $promos = Artwork::where('is_promo', true)
                         ->where('stock', '>', 0)
                         ->with('user')
                         ->take(3)->get();

        // 5. Get Artists list for sidebar
        $artists = User::where('is_artist', true)->orderBy('name')->get();

        // === THIS LINE LOADS YOUR BLADE FILE ===
        return view('marketplace', compact('artworks', 'promos', 'artists'));
    }

    public function buy(Artwork $artwork)
    {
        if ($artwork->isSoldOut()) {
            return back()->with('error', 'Sorry, this item is sold out.');
        }

        // 1. RESERVATION LOGIC
        // Decrement actual stock, increment reserved stock
        $artwork->decrement('stock');
        $artwork->increment('reserved_stock');
        
        // Set timer for 6 hours from now
        $artwork->update(['reserved_until' => now()->addHours(6)]);

        // 2. WHATSAPP LOGIC (TO ADMIN)
        // Get Admin number from .env, default to a placeholder if missing
        $adminPhone = env('ADMIN_WA_NUMBER', '628123456789'); 
        
        // Construct Message
        $message = "Halo Admin WOPANCO, Saya ingin membeli karya: *" . $artwork->title . "* " .
                   "karya dari artist: *" . $artwork->user->name . "*. " .
                   "Apakah masih tersedia?";
                   
        $url = "https://wa.me/{$adminPhone}?text=" . urlencode($message);

        // 3. Redirect
        return redirect()->away($url);
    }
}