<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\User; // <--- THIS WAS MISSING
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ArtworkController extends Controller
{
    /**
     * Display listing. SUPERADMIN CAN VIEW OTHERS via ?user_id=123
     */
    public function index(Request $request)
    {
        $targetUser = $request->user(); // Default: Logged in user

        // SUPERADMIN OVERRIDE: Check if viewing another user
        if ($request->has('user_id') && ($request->user()->is_superadmin || $request->user()->is_admin)) {
            $targetUser = User::findOrFail($request->user_id);
        }

        // Load artworks for the TARGET user
        $lukisan = $targetUser->artworks()->where('category', 'Lukisan')->latest()->get();
        $crafts = $targetUser->artworks()->where('category', 'Craft')->latest()->get();

        return view('artworks.index', [
            'lukisan' => $lukisan,
            'crafts' => $crafts,
            'is_impersonating' => $targetUser->id !== $request->user()->id, 
            'target_user' => $targetUser 
        ]);
    }

    /**
     * Show create form. Pass target_user_id if impersonating.
     */
    public function create(Request $request)
    {
        $targetUserId = null;

        if ($request->has('user_id') && ($request->user()->is_superadmin || $request->user()->is_admin)) {
            $targetUserId = $request->user_id;
        }

        return view('artworks.create', compact('targetUserId'));
    }

    /**
     * Store new artwork.
     */
    public function store(Request $request)
    {
        // 1. Determine the OWNER ID
        $ownerId = auth()->id();

        // Check if Admin sent a specific User ID to create on behalf of
        if ($request->filled('behalf_user_id') && (auth()->user()->is_superadmin || auth()->user()->is_admin)) {
            $ownerId = $request->behalf_user_id;
        }

        // ... (Keep your existing validation and translation logic here) ...
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Lukisan,Craft',
            'description' => 'nullable|string',
            'image' => 'required_without:image_temp_path|image|max:5120',
            'image_temp_path' => 'required_without:image|nullable|string',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'promo_price' => 'nullable|numeric|lt:price',
        ]);

        $finalPath = '';
        if ($request->hasFile('image')) {
            $finalPath = $request->file('image')->store('artworks', 'public');
        } elseif ($request->filled('image_temp_path')) {
            $tempPath = $request->image_temp_path;
            if (Storage::disk('public')->exists($tempPath)) {
                $newFilename = 'artworks/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newFilename);
                $finalPath = $newFilename;
            }
        }

        // ... (Keep Translation Try/Catch here) ...
        // Quick fallback for translation variables to prevent errors if you removed the try/catch
        $title_en = $validated['title']; $title_id = $validated['title'];
        $desc_en = $validated['description']; $desc_id = $validated['description'];

        // Create the Artwork with the CORRECT ownerId
        Artwork::create([
            'user_id' => $ownerId, // <--- CRITICAL: Uses Lidya's ID
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

        // --- THE FIX IS HERE ---
        // If the Owner ID is NOT the logged-in user, redirect back to THAT user's list
        if ($ownerId != auth()->id()) {
            return redirect()->route('artworks.index', ['user_id' => $ownerId])
                             ->with('status', 'Artwork created for user successfully!');
        }

        return redirect()->route('artworks.index')->with('status', 'Artwork created successfully!');
    }

    /**
     * Show edit form.
     */
    public function edit(Artwork $artwork)
    {
        // Allow Owner OR Admin
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin && !auth()->user()->is_superadmin) {
            abort(403);
        }

        return view('artworks.edit', ['artwork' => $artwork]);
    }

    /**
     * Update artwork.
     */
    public function update(Request $request, Artwork $artwork)
    {
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin && !auth()->user()->is_superadmin) {
            abort(403);
        }

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

        // Translation Update
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

        // Redirect logic
        if (auth()->id() !== $artwork->user_id) {
            return redirect()->route('artworks.index', ['user_id' => $artwork->user_id])->with('status', 'User artwork updated!');
        }

        return redirect()->route('artworks.index')->with('status', 'Artwork updated successfully!');
    }

    /**
     * Delete artwork.
     */
    public function destroy(Request $request, Artwork $artwork)
    {
        if (auth()->id() !== $artwork->user_id && !auth()->user()->is_admin && !auth()->user()->is_superadmin) {
            abort(403);
        }

        if ($artwork->image_path) Storage::disk('public')->delete($artwork->image_path);
        $artwork->delete();

        return back()->with('status', 'artwork-deleted');
    }

    /**
     * Public Show.
     */
    public function show(Artwork $artwork)
    {
        $artwork->load('user.artistProfile');
        return view('artworks.show', ['artwork' => $artwork]);
    }

    /**
     * AJAX Preview.
     */
    public function previewImage(Request $request)
    {
        $request->validate(['url' => 'required|url']);
        $url = $request->url;

        if (str_contains($url, 'drive.google.com')) {
            preg_match('/\/d\/(.*?)\//', $url, $matches);
            if (isset($matches[1])) {
                $url = "https://drive.google.com/uc?export=download&id={$matches[1]}";
            }
        }

        try {
            $response = Http::get($url);
            if ($response->failed()) return response()->json(['error' => 'Failed to download.'], 422);

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