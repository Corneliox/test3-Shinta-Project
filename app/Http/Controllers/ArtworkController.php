<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\User; 
use App\Traits\ImageUploadTrait; // <--- 1. Import Trait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ArtworkController extends Controller
{
    use ImageUploadTrait; // <--- 2. Use Trait

    /**
     * Display listing. SUPERADMIN CAN VIEW OTHERS via ?user_id=123
     */
    public function index(Request $request)
    {
        $targetUser = $request->user(); // Default: Logged in user

        // SUPERADMIN OVERRIDE
        if ($request->has('user_id') && ($request->user()->is_superadmin || $request->user()->is_admin)) {
            $targetUser = User::findOrFail($request->user_id);
        }

        // OPTIMIZATION: Fetch ALL artworks in one query
        $all_artworks = $targetUser->artworks()->latest()->get();

        // Filter them in memory (faster than 3 DB queries)
        $lukisan = $all_artworks->where('category', 'Lukisan');
        $crafts = $all_artworks->where('category', 'Craft');

        return view('artworks.index', [
            'all_artworks' => $all_artworks,
            'lukisan' => $lukisan,
            'crafts' => $crafts,
            'is_impersonating' => $targetUser->id !== $request->user()->id, 
            'target_user' => $targetUser 
        ]);
    }

    /**
     * Show create form.
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

        // 2. Validate
        $rules = [
            'title' => 'required|string|max:255',
            'category' => 'required|in:Lukisan,Craft',
            'description' => 'nullable|string',
            
            // Image Validation: File OR Temp Path
            'image' => 'required_without:image_temp_path|image|max:5120',
            'image_temp_path' => 'required_without:image|nullable|string',
            
            // Marketplace Validation
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'promo_price' => 'nullable|numeric|lt:price',
        ];

        // NEW: If Craft, allow up to 2 EXTRA images (Total 3)
        if ($request->category === 'Craft') {
            $rules['extra_images.*'] = 'image|max:5120';
            $rules['extra_images'] = 'max:2'; // Limit array count
        }

        $validated = $request->validate($rules);

        $finalPath = '';

        // --- IMAGE PROCESSING (OPTIMIZED) ---
        // SCENARIO A: Standard File Upload
        if ($request->hasFile('image')) {
            $finalPath = $this->uploadImage($request->file('image'), 'artworks');
        } 
        // SCENARIO B: Google Drive Pull
        elseif ($request->filled('image_temp_path')) {
            $finalPath = $this->processTempImage($request->image_temp_path, 'artworks');
            
            if (!$finalPath) {
                return back()->withErrors(['image' => 'Image link expired. Pull again.'])->withInput();
            }
        }

        // --- HANDLE EXTRA IMAGES (CRAFT ONLY) ---
        $additionalPaths = [];
        if ($request->category === 'Craft' && $request->hasFile('extra_images')) {
            foreach ($request->file('extra_images') as $file) {
                // Reuse optimization trait
                $additionalPaths[] = $this->uploadImage($file, 'artworks/extras');
            }
        }

        // --- TRANSLATION LOGIC ---
        try {
            $tr = new GoogleTranslate(); 
            $title_en = $tr->setTarget('en')->translate($validated['title']);
            $title_id = $tr->setTarget('id')->translate($validated['title']);
            $desc_en = $validated['description'] ? $tr->setTarget('en')->translate($validated['description']) : null;
            $desc_id = $validated['description'] ? $tr->setTarget('id')->translate($validated['description']) : null;
        } catch (\Exception $e) {
            $title_en = $validated['title']; $title_id = $validated['title'];
            $desc_en = $validated['description']; $desc_id = $validated['description'];
        }

        // --- DB CREATION (With Error Handling) ---
        try {
            Artwork::create([
                'user_id' => $ownerId,
                'title' => $title_en,
                'title_id' => $title_id,
                'category' => $validated['category'],
                'description' => $desc_en,
                'description_id' => $desc_id,
                'image_path' => $finalPath,
                'additional_images' => $additionalPaths, // <--- SAVE ARRAY
                'price' => $validated['price'],
                'stock' => $validated['stock'] ?? 0,
                'is_promo' => $request->has('is_promo'),
                'promo_price' => $request->input('promo_price'),
            ]);

            // Success Redirect
            if ($ownerId != auth()->id()) {
                return redirect()->route('artworks.index', ['user_id' => $ownerId])
                                 ->with('status', 'Artwork created for user successfully!');
            }
            return redirect()->route('artworks.index')->with('status', 'Artwork created successfully!');

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle Duplicate Entry
            if ($e->errorInfo[1] == 1062) {
                return back()->withInput()->with('error', 'Error: An artwork with this title already exists.');
            }
            return back()->withInput()->with('error', 'Database Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form.
     */
    public function edit(Artwork $artwork)
    {
        // Authorization: Owner OR Admin
        $isOwner = intval(auth()->id()) === intval($artwork->user_id);
        $isAdmin = auth()->user()->is_admin || auth()->user()->is_superadmin;

        if (!$isOwner && !$isAdmin) {
            abort(403);
        }

        return view('artworks.edit', ['artwork' => $artwork]);
    }

    /**
     * Update artwork.
     */
    public function update(Request $request, Artwork $artwork)
    {
        // Authorization
        $isOwner = intval(auth()->id()) === intval($artwork->user_id);
        $isAdmin = auth()->user()->is_admin || auth()->user()->is_superadmin;

        if (!$isOwner && !$isAdmin) abort(403);

        $rules = [
            'title' => 'required|string|max:255',
            'category' => 'required|in:Lukisan,Craft',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'promo_price' => 'nullable|numeric|lt:price',
        ];

        if ($request->category === 'Craft') {
            $rules['extra_images.*'] = 'image|max:5120';
        }

        $validated = $request->validate($rules);

        // --- 1. IMAGE UPDATE (OPTIMIZED) ---
        if ($request->hasFile('image')) {
            // Delete Old Images
            if ($artwork->image_path) {
                Storage::disk('public')->delete($artwork->image_path); 
                $originalPath = $artwork->getOriginalImagePath(); 
                if(Storage::disk('public')->exists($originalPath)) {
                    Storage::disk('public')->delete($originalPath);
                }
            }
            // Upload New
            $artwork->image_path = $this->uploadImage($request->file('image'), 'artworks');
        }

        // --- 2. HANDLE EXTRA IMAGES ---
        $currentExtras = $artwork->additional_images ?? [];
        
        // If switching to Lukisan, clear extras
        if ($validated['category'] === 'Lukisan') {
            foreach($currentExtras as $path) Storage::disk('public')->delete($path);
            $currentExtras = [];
        } 
        // If Craft, allow adding more
        elseif ($request->hasFile('extra_images')) {
            // Check limits (Current + New <= 2 extras)
            if (count($currentExtras) + count($request->file('extra_images')) > 2) {
                return back()->withErrors(['extra_images' => 'Maximum 3 images total allowed (1 Main + 2 Extras). Please delete some first.']);
            }
            
            foreach ($request->file('extra_images') as $file) {
                $currentExtras[] = $this->uploadImage($file, 'artworks/extras');
            }
        }

        // --- 3. HANDLE DELETING SPECIFIC EXTRAS ---
        if ($request->has('delete_extras')) {
            $filesToDelete = $request->delete_extras;
            $currentExtras = array_values(array_filter($currentExtras, function($path) use ($filesToDelete) {
                if (in_array($path, $filesToDelete)) {
                    Storage::disk('public')->delete($path);
                    return false; // Remove from array
                }
                return true; // Keep
            }));
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

        $artwork->additional_images = $currentExtras; // Save updated array
        $artwork->category = $validated['category'];
        $artwork->price = $validated['price'];
        $artwork->stock = $validated['stock'] ?? 0;
        $artwork->is_promo = $request->has('is_promo');
        $artwork->promo_price = $request->input('promo_price');

        // Wrap save in Try-Catch
        try {
            $artwork->save();

            // Redirect logic
            if ($artwork->user_id != auth()->id()) {
                return redirect()->route('artworks.index', ['user_id' => $artwork->user_id])->with('status', 'User artwork updated!');
            }
            return redirect()->route('artworks.index')->with('status', 'Artwork updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Update Failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete artwork.
     */
    public function destroy(Request $request, Artwork $artwork)
    {
        $isOwner = intval(auth()->id()) === intval($artwork->user_id);
        $isAdmin = auth()->user()->is_admin || auth()->user()->is_superadmin;

        if (!$isOwner && !$isAdmin) abort(403);

        // Delete Main Images
        if ($artwork->image_path) {
            Storage::disk('public')->delete($artwork->image_path);
            $originalPath = $artwork->getOriginalImagePath();
            if(Storage::disk('public')->exists($originalPath)) {
                Storage::disk('public')->delete($originalPath);
            }
        }

        // Delete Extra Images
        if (!empty($artwork->additional_images)) {
            foreach ($artwork->additional_images as $extraPath) {
                Storage::disk('public')->delete($extraPath);
            }
        }
        
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