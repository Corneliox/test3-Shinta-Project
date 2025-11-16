<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArtworkController extends Controller
{
    /**
     * Display a listing of the artist's artworks and the upload form.
     */
    public function index(Request $request)
    {
        // Get the logged-in user
        $user = $request->user();

        // Load only the artworks that belong to this user
        $lukisan = $user->artworks()->where('category', 'Lukisan')->latest()->get();
        $crafts = $user->artworks()->where('category', 'Craft')->latest()->get();

        // Return the management view
        return view('artworks.index', [
            'lukisan' => $lukisan,
            'crafts' => $crafts,
        ]);
    }

    /**
     * Store a new artwork.
     */
    public function store(Request $request)
    {
        // 1. Validate the data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'category' => 'required|string|in:Lukisan,Craft', // Must be one of these two
            'image' => 'required|image|max:5120', // 5MB max
        ]);

        // 2. Store the image
        $path = $request->file('image')->store('artworks', 'public');

        // 3. Create the artwork and associate it with the logged-in user
        $request->user()->artworks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'image_path' => $path,
        ]);

        // 4. Redirect back with a success message
        return back()->with('status', 'artwork-uploaded');
    }

    /**
     * Remove the specified artwork from storage.
     */
    public function destroy(Request $request, Artwork $artwork)
    {
        // 1. Authorization Check:
        // Make sure the logged-in user actually owns this artwork
        if ($request->user()->id !== $artwork->user_id) {
            abort(403); // Forbidden
        }

        // 2. Delete the image file from storage
        Storage::disk('public')->delete($artwork->image_path);

        // 3. Delete the artwork record from the database
        $artwork->delete();

        // 4. Redirect back with a success message
        return back()->with('status', 'artwork-deleted');
    }
}