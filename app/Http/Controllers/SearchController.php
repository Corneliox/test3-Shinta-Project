<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\Artwork;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        if (empty($keyword)) {
            return redirect()->route('home');
        }

        // Search Artists (with their profile)
        $artists = User::where('is_artist', true)
                        ->where('name', 'LIKE', "%{$keyword}%")
                        ->with('artistProfile') // <-- THIS IS THE FIX
                        ->get();

        // Search Events
        $events = Event::where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%")
                        ->get();

        // Search Artworks (with their user)
        $artworks = Artwork::where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%")
                        ->with('user') // <-- This is also important
                        ->get();

        return view('search.results', [
            'keyword' => $keyword,
            'artists' => $artists,
            'events' => $events,
            'artworks' => $artworks,
        ]);
    }
}