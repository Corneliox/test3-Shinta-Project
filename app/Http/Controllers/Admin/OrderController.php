<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Show Pending Orders (Items currently in "Reserved" state)
     */
    public function index()
    {
        // Fetch artworks where reserved_stock > 0
        $pendingOrders = Artwork::where('reserved_stock', '>', 0)
                                ->with('user') // Load artist info
                                ->orderBy('reserved_until', 'asc')
                                ->get();

        return view('admin.orders.index', compact('pendingOrders'));
    }

    /**
     * Confirm Sale (Admin received money)
     */
    public function confirm(Artwork $artwork)
    {
        // Logic: 
        // 1. Remove from Reserved (Stock was already removed when user clicked Buy)
        // 2. (Optional) Log the sale or increment a 'sold_count'
        
        if($artwork->reserved_stock > 0) {
            $artwork->decrement('reserved_stock');
            // Keep 'reserved_until' or clear it? Clearing it is safer.
            $artwork->update(['reserved_until' => null]);
        }

        return back()->with('status', 'Order confirmed! Stock updated permanently.');
    }

    /**
     * Reject/Cancel Sale (User didn't pay or 6 hours passed)
     */
    public function reject(Artwork $artwork)
    {
        if($artwork->reserved_stock > 0) {
            // Return the item to Stock
            $artwork->decrement('reserved_stock');
            $artwork->increment('stock');
            
            // Clear timer
            $artwork->update(['reserved_until' => null]);
        }

        return back()->with('status', 'Order cancelled. Item returned to stock.');
    }
}