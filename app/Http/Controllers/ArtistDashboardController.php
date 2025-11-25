<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artwork;

class ArtistDashboardController extends Controller
{
    /**
     * Display the Artist Dashboard (Pending Sales).
     */
    public function index()
    {
        $user = auth()->user();

        if (!$user->is_artist) {
            abort(403, 'Only artists can access this dashboard.');
        }
        
        // Get items with reserved stock (Pending Sales)
        $pendingSales = $user->artworks()
                             ->where('reserved_stock', '>', 0)
                             ->orderBy('reserved_until', 'asc')
                             ->get();

        return view('profile.artist-dashboard', compact('pendingSales'));
    }

    public function confirmSale(Artwork $artwork)
    {
        if ($artwork->user_id !== auth()->id()) abort(403);
        $artwork->decrement('reserved_stock');
        return back()->with('status', 'Sale confirmed! Item marked as sold.');
    }

    public function rejectSale(Artwork $artwork)
    {
        if ($artwork->user_id !== auth()->id()) abort(403);
        $artwork->decrement('reserved_stock');
        $artwork->increment('stock'); 
        $artwork->update(['reserved_until' => null]);
        return back()->with('status', 'Reservation cancelled.');
    }
}