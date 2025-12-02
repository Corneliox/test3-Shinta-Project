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
        $request->validate([
            'keyword' => [
                'nullable', 
                'string', 
                'max:100', 
                // REGEX EXPLANATION:
                // ^ = Start of string
                // a-zA-Z0-9 = Alphanumeric
                // \s = Spaces
                // \-\.\' = Dashes, Dots, Single Quotes (for names)
                // $ = End of string
                'regex:/^[a-zA-Z0-9\s\-\.\']+$/' 
            ],
        ], [
            // Custom Error Message
            'keyword.regex' => 'Search term contains invalid characters. Only letters, numbers, spaces, dots, and dashes are allowed.'
        ]);

        $keyword = $request->input('keyword');

        if (empty($keyword)) {
            return redirect()->route('home');
        }

        // ... (Rest of your query logic remains the same) ...
        $artists = User::where('is_artist', true)
                        ->where('name', 'LIKE', "%{$keyword}%")
                        ->with('artistProfile')
                        ->get();

        $events = Event::where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%")
                        ->get();

        $artworks = Artwork::where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%")
                        ->with('user')
                        ->get();

        return view('search.results', [
            'keyword' => $keyword,
            'artists' => $artists,
            'events' => $events,
            'artworks' => $artworks,
        ]);
        
    }
    
}