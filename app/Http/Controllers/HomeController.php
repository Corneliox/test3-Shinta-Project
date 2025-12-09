<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\Artwork;
use App\Models\HeroImage; // <--- The new model

class HomeController extends Controller
{
    public function index()
    {
        // 1. Fetch Artworks (Lukisan) - Exact logic from your route
        $lukisan_artworks = Artwork::where('category', 'Lukisan')
            ->whereHas('user', fn($query) => $query->where('is_artist', true))
            ->latest()
            ->take(10)
            ->get();
            
        // 2. Fetch Artworks (Craft)
        $craft_artworks = Artwork::where('category', 'Craft')
            ->whereHas('user', fn($query) => $query->where('is_artist', true))
            ->latest()
            ->take(10)
            ->get();

        // 3. Fetch Artists
        $artists = User::where('is_artist', true)
            ->with('artistProfile')
            ->orderBy('name', 'asc')
            ->get();

        // 4. Fetch Events
        $pinned_event = Event::where('is_pinned', true)->latest()->first();
        $newest_events = Event::where('is_pinned', false)->latest()->take(3)->get();

        // 5. Fetch Hero Images (UPGRADED to Database)
        // Instead of scanning a folder, we get them from the Admin uploads
        $hero_images = HeroImage::orderBy('created_at', 'desc')->get();

        // 6. Return View
        return view('welcome', [
            'lukisan_artworks' => $lukisan_artworks,
            'craft_artworks' => $craft_artworks,
            'artists' => $artists,
            'pinned_event' => $pinned_event,
            'newest_events' => $newest_events,
            'hero_images' => $hero_images, // Now passes database objects, not strings
        ]);
    }
}