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
        if ($artwork->isSoldOut()) return back()->with('error', 'Sold out.');

        // Reserve logic
        $artwork->decrement('stock');
        $artwork->increment('reserved_stock');
        $artwork->update(['reserved_until' => now()->addHours(6)]);

        // WhatsApp logic
        $phone = $artwork->user->artistProfile->phone ?? '';
        if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
        
        $message = "Hello " . $artwork->user->name . ", I want to buy *" . $artwork->title . "*. Is it available?";
        return redirect()->away("https://wa.me/{$phone}?text=" . urlencode($message));
    }
}