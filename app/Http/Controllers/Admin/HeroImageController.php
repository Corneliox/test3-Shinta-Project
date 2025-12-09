<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroImageController extends Controller
{
    public function index()
    {
        $images = HeroImage::orderBy('created_at', 'desc')->get();
        return view('admin.hero.index', compact('images'));
    }

    public function create()
    {
        return view('admin.hero.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required', // Array is required
            'images.*' => 'image|max:5120', // Each file inside must be an image, max 5MB
        ]);

        if($request->hasFile('images')) {
            // Loop through each uploaded file
            foreach($request->file('images') as $image) {
                $path = $image->store('hero_images', 'public');

                HeroImage::create([
                    'image_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.hero.index')->with('status', 'Images added to carousel!');
    }

    /**
     * Remove the specified hero image from storage.
     * NOTE: We use $id here to avoid Route Model Binding mismatch issues.
     */
    public function destroy($id)
    {
        // 1. Manually find the record. If not found, show error.
        $heroImage = HeroImage::find($id);

        if (!$heroImage) {
            return back()->with('error', 'Image not found or already deleted.');
        }

        // 2. Delete the physical file (Safety check included)
        if (!empty($heroImage->image_path) && Storage::disk('public')->exists($heroImage->image_path)) {
            Storage::disk('public')->delete($heroImage->image_path);
        }

        // 3. Delete the database record
        $heroImage->delete();

        return back()->with('status', 'Image deleted successfully!');
    }
}