<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; // Import HTTP Client
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

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
     * Show the form for creating a new artwork.
     */
    public function create()
    {
        return view('artworks.create');
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
            
            // Updated validation: allow either direct upload OR temp path
            'image' => 'required_without:image_temp_path|image|max:5120',
            'image_temp_path' => 'required_without:image|nullable|string',
            
            // New Validation rules
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'promo_price' => 'nullable|numeric|lt:price', // Promo must be less than Price
        ]);

        $finalPath = '';

        // SCENARIO A: User uploaded a file directly
        if ($request->hasFile('image')) {
            $finalPath = $request->file('image')->store('artworks', 'public');
        } 
        // SCENARIO B: User used "Pull Image" (move temp file to permanent location)
        elseif ($request->filled('image_temp_path')) {
            $tempPath = $request->image_temp_path;
            
            if (Storage::disk('public')->exists($tempPath)) {
                $newFilename = 'artworks/' . basename($tempPath);
                // Move from temp folder to main artworks folder
                Storage::disk('public')->move($tempPath, $newFilename);
                $finalPath = $newFilename;
            } else {
                return back()->withErrors(['image' => 'Temporary image expired or not found. Please upload again.'])->withInput();
            }
        }

        // --- AUTOMATIC TRANSLATION LOGIC ---
        // Wrap in try-catch to prevent crash if internet fails or key is missing
        try {
            $tr = new GoogleTranslate(); // Auto-detects source language

            // 1. Title Translation
            $title_en = $tr->setTarget('en')->translate($validated['title']);
            $title_id = $tr->setTarget('id')->translate($validated['title']);

            // 2. Description Translation
            $desc_en = $validated['description'] ? $tr->setTarget('en')->translate($validated['description']) : null;
            $desc_id = $validated['description'] ? $tr->setTarget('id')->translate($validated['description']) : null;
        } catch (\Exception $e) {
            // Fallback if translation fails
            $title_en = $validated['title'];
            $title_id = $validated['title'];
            $desc_en = $validated['description'];
            $desc_id = $validated['description'];
        }
        // -----------------------------------

        Artwork::create([
            'user_id' => auth()->id(),
            'title' => $title_en,       // Save English
            'title_id' => $title_id,    // Save Indonesian
            'category' => $validated['category'],
            'description' => $desc_en,    // Save English
            'description_id' => $desc_id, // Save Indonesian
            'image_path' => $finalPath,   // Save the determined path
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
            if ($artwork->image_path) {
                 Storage::disk('public')->delete($artwork->image_path); 
            }
            $path = $request->file('image')->store('artworks', 'public');
            $artwork->image_path = $path;
        }

        // --- AUTOMATIC TRANSLATION LOGIC (UPDATE) ---
        try {
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
        } catch (\Exception $e) {
            // Fallback
            $artwork->title = $validated['title'];
            if($validated['description']) {
                $artwork->description = $validated['description'];
            }
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
        if ($artwork->image_path) {
            Storage::disk('public')->delete($artwork->image_path);
        }

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

    /**
     * NEW: AJAX Handler to Pull Image from Link
     */
    public function previewImage(Request $request)
    {
        $request->validate(['url' => 'required|url']);
        $url = $request->url;

        // 1. Convert Google Drive Share Link to Direct Download Link
        // Pattern: https://drive.google.com/file/d/{ID}/view... -> https://drive.google.com/uc?id={ID}&export=download
        if (str_contains($url, 'drive.google.com')) {
            preg_match('/\/d\/(.*?)\//', $url, $matches);
            if (isset($matches[1])) {
                $fileId = $matches[1];
                $url = "https://drive.google.com/uc?export=download&id={$fileId}";
            }
        }

        try {
            // 2. Fetch the Image Content
            $response = Http::get($url);

            if ($response->failed()) {
                return response()->json(['error' => 'Failed to download image. Check the link accessibility.'], 422);
            }

            // 3. Save to a Temporary Folder
            $content = $response->body();
            $filename = 'temp_' . uniqid() . '.jpg'; // Assume JPG for simplicity, or detect mime type more robustly if needed
            $tempPath = 'artworks/temp/' . $filename;
            
            Storage::disk('public')->put($tempPath, $content);

            return response()->json([
                'success' => true,
                'preview_url' => Storage::url($tempPath), // For the <img> tag
                'temp_path' => $tempPath // To send back when submitting form
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error processing link: ' . $e->getMessage()], 500);
        }
    }
}