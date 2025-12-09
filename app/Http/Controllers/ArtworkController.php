<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Stichoza\GoogleTranslate\GoogleTranslate; // <--- IMPORT THIS

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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            // 'title_id' is generated automatically now
            'category' => 'required|in:Lukisan,Craft',
            'description' => 'nullable|string',
            // 'description_id' is generated automatically now
            'image' => 'required|image|max:5120',
            // New Validation rules
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'promo_price' => 'nullable|numeric|lt:price', // Promo must be less than Price
        ]);

        $path = $request->file('image')->store('artworks', 'public');

        // --- AUTOMATIC TRANSLATION LOGIC ---
        $tr = new GoogleTranslate(); // Auto-detects source language

        // 1. Title Translation
        $title_en = $tr->setTarget('en')->translate($validated['title']);
        $title_id = $tr->setTarget('id')->translate($validated['title']);

        // 2. Description Translation
        $desc_en = $validated['description'] ? $tr->setTarget('en')->translate($validated['description']) : null;
        $desc_id = $validated['description'] ? $tr->setTarget('id')->translate($validated['description']) : null;
        // -----------------------------------

        Artwork::create([
            'user_id' => auth()->id(),
            'title' => $title_en,       // Save English
            'title_id' => $title_id,    // Save Indonesian
            'category' => $validated['category'],
            'description' => $desc_en,    // Save English
            'description_id' => $desc_id, // Save Indonesian
            'image_path' => $path,
            // Save new fields
            'price' => $validated['price'],
            'stock' => $validated['stock'] ?? 0,
            'is_promo' => $request->has('is_promo'), // Checkbox logic
            'promo_price' => $request->input('promo_price'),
        ]);

        return redirect()->route('artworks.index')->with('status', 'Artwork created & translated successfully!');
    }

    /**
     * Show the form for editing the specified artwork.
     */
    public function edit(Artwork $artwork)
    {
        // AUTHORIZATION: Is the logged-in user the owner?
        if (auth()->id() !== $artwork->user_id) {
            abort(403); // Forbidden
        }

        return view('artworks.edit', [
            'artwork' => $artwork
        ]);
    }

    /**
     * Update the specified artwork in storage.
     */
    public function update(Request $request, Artwork $artwork)
    {
        if ($artwork->user_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Lukisan,Craft',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'promo_price' => 'nullable|numeric|lt:price',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            // Storage::disk('public')->delete($artwork->image_path); 
            $path = $request->file('image')->store('artworks', 'public');
            $artwork->image_path = $path;
        }

        // --- AUTOMATIC TRANSLATION LOGIC (UPDATE) ---
        $tr = new GoogleTranslate(); 

        // 1. Title
        $artwork->title = $tr->setTarget('en')->translate($validated['title']);
        $artwork->title_id = $tr->setTarget('id')->translate($validated['title']);

        // 2. Description
        if($validated['description']) {
            $artwork->description = $tr->setTarget('en')->translate($validated['description']);
            $artwork->description_id = $tr->setTarget('id')->translate($validated['description']);
        } else {
            $artwork->description = null;
            $artwork->description_id = null;
        }
        // --------------------------------------------

        $artwork->category = $validated['category'];
        
        // Update Logic
        $artwork->price = $validated['price'];
        $artwork->stock = $validated['stock'] ?? 0;
        $artwork->is_promo = $request->has('is_promo');
        $artwork->promo_price = $request->input('promo_price');

        $artwork->save();

        return redirect()->route('artworks.index')->with('status', 'Artwork updated & translated successfully!');
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

    /**
     * Display the specified artwork.
     */
    public function show(Artwork $artwork)
    {
        // Eager load the artist's info
        $artwork->load('user.artistProfile');

        // Pass the single artwork to a new view
        return view('artworks.show', [
            'artwork' => $artwork
        ]);
    }
}