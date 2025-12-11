<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; // Required for Link Pull
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ArtworkController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $lukisan = $user->artworks()->where('category', 'Lukisan')->latest()->get();
        $crafts = $user->artworks()->where('category', 'Craft')->latest()->get();

        return view('artworks.index', [
            'lukisan' => $lukisan,
            'crafts' => $crafts,
        ]);
    }

    public function create()
    {
        return view('artworks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Lukisan,Craft',
            'description' => 'nullable|string',
            
            // Allow either File OR Temp Path
            'image' => 'required_without:image_temp_path|image|max:5120',
            'image_temp_path' => 'required_without:image|nullable|string',
            
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'promo_price' => 'nullable|numeric|lt:price',
        ]);

        $finalPath = '';

        // 1. Handle File Upload
        if ($request->hasFile('image')) {
            $finalPath = $request->file('image')->store('artworks', 'public');
        } 
        // 2. Handle Link Pull (Move Temp File)
        elseif ($request->filled('image_temp_path')) {
            $tempPath = $request->image_temp_path;
            if (Storage::disk('public')->exists($tempPath)) {
                $newFilename = 'artworks/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newFilename);
                $finalPath = $newFilename;
            } else {
                return back()->withErrors(['image' => 'Image link expired. Please pull again.'])->withInput();
            }
        }

        // --- TRANSLATION ---
        try {
            $tr = new GoogleTranslate(); 
            $title_en = $tr->setTarget('en')->translate($validated['title']);
            $title_id = $tr->setTarget('id')->translate($validated['title']);
            $desc_en = $validated['description'] ? $tr->setTarget('en')->translate($validated['description']) : null;
            $desc_id = $validated['description'] ? $tr->setTarget('id')->translate($validated['description']) : null;
        } catch (\Exception $e) {
            $title_en = $validated['title'];
            $title_id = $validated['title'];
            $desc_en = $validated['description'];
            $desc_id = $validated['description'];
        }

        Artwork::create([
            'user_id' => auth()->id(),
            'title' => $title_en,
            'title_id' => $title_id,
            'category' => $validated['category'],
            'description' => $desc_en,
            'description_id' => $desc_id,
            'image_path' => $finalPath,
            'price' => $validated['price'],
            'stock' => $validated['stock'] ?? 0,
            'is_promo' => $request->has('is_promo'),
            'promo_price' => $request->input('promo_price'),
        ]);

        return redirect()->route('artworks.index')->with('status', 'Artwork created successfully!');
    }

    public function edit(Artwork $artwork)
    {
        if (auth()->id() !== $artwork->user_id) abort(403);
        return view('artworks.edit', ['artwork' => $artwork]);
    }

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
            if ($artwork->image_path) Storage::disk('public')->delete($artwork->image_path); 
            $artwork->image_path = $request->file('image')->store('artworks', 'public');
        }

        try {
            $tr = new GoogleTranslate(); 
            $artwork->title = $tr->setTarget('en')->translate($validated['title']);
            $artwork->title_id = $tr->setTarget('id')->translate($validated['title']);
            if($validated['description']) {
                $artwork->description = $tr->setTarget('en')->translate($validated['description']);
                $artwork->description_id = $tr->setTarget('id')->translate($validated['description']);
            }
        } catch (\Exception $e) {
            $artwork->title = $validated['title'];
            if($validated['description']) $artwork->description = $validated['description'];
        }

        $artwork->category = $validated['category'];
        $artwork->price = $validated['price'];
        $artwork->stock = $validated['stock'] ?? 0;
        $artwork->is_promo = $request->has('is_promo');
        $artwork->promo_price = $request->input('promo_price');

        $artwork->save();

        return redirect()->route('artworks.index')->with('status', 'Artwork updated successfully!');
    }

    public function destroy(Request $request, Artwork $artwork)
    {
        if ($request->user()->id !== $artwork->user_id) abort(403);
        if ($artwork->image_path) Storage::disk('public')->delete($artwork->image_path);
        $artwork->delete();
        return back()->with('status', 'artwork-deleted');
    }

    public function show(Artwork $artwork)
    {
        $artwork->load('user.artistProfile');
        return view('artworks.show', ['artwork' => $artwork]);
    }

    /**
     * AJAX Handler for Image Pull
     */
    public function previewImage(Request $request)
    {
        $request->validate(['url' => 'required|url']);
        $url = $request->url;

        // Convert Google Drive Link
        if (str_contains($url, 'drive.google.com')) {
            preg_match('/\/d\/(.*?)\//', $url, $matches);
            if (isset($matches[1])) {
                $url = "https://drive.google.com/uc?export=download&id={$matches[1]}";
            }
        }

        try {
            $response = Http::get($url);
            if ($response->failed()) return response()->json(['error' => 'Failed to download image.'], 422);

            $filename = 'temp_' . uniqid() . '.jpg';
            $tempPath = 'artworks/temp/' . $filename;
            Storage::disk('public')->put($tempPath, $response->body());

            return response()->json([
                'success' => true,
                'preview_url' => Storage::url($tempPath),
                'temp_path' => $tempPath
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}