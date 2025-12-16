<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventImage;
use App\Models\Event;
use App\Traits\ImageUploadTrait; // <--- 1. IMPORT TRAIT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Stichoza\GoogleTranslate\GoogleTranslate; 
use ZipArchive; 

class EventController extends Controller
{
    use ImageUploadTrait; // <--- 2. USE TRAIT

    /**
     * Display a listing of the events (in the ADMIN panel).
     */
    public function index()
    {
        $events = Event::latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a new event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|max:5120', // Main Image
            'gallery.*' => 'nullable|image|max:5120', 
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'is_pinned' => 'nullable|boolean',
        ]);

        // 1. Handle Main Image (With Rotation & Optimization)
        $rotation = $request->input('rotation', 0);
        $validated['image_path'] = $this->uploadImage($request->file('image'), 'events', $rotation);
        
        $validated['is_pinned'] = $request->has('is_pinned');

        // 2. Handle Google Translation
        try {
            $tr = new GoogleTranslate(); 
            $validated['title'] = $tr->setTarget('en')->translate($request->title);
            $validated['title_id'] = $tr->setTarget('id')->translate($request->title);

            if($request->description) {
                $validated['description'] = $tr->setTarget('en')->translate($request->description);
                $validated['description_id'] = $tr->setTarget('id')->translate($request->description);
            }
        } catch (\Exception $e) {
            // Fallback if translation fails
            $validated['title_id'] = $validated['title'];
            if($request->description) $validated['description_id'] = $validated['description'];
        }

        // 3. Create Event
        $data = $validated;
        unset($data['image']); 
        if(isset($data['gallery'])) unset($data['gallery']);

        $event = Event::create($data);

        // 4. Handle Gallery Uploads
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $photo) {
                // Optimization for gallery images too (No rotation for bulk yet)
                $path = $this->uploadImage($photo, 'event_gallery');
                EventImage::create([
                    'event_id' => $event->id,
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('admin.events.index')->with('status', 'Event created, translated & gallery uploaded successfully.');
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120', 
            'gallery.*' => 'nullable|image|max:5120', 
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'is_pinned' => 'nullable|boolean',
        ]);

        $rotation = $request->input('rotation', 0);

        // 1. Handle Main Image Update
        if ($request->hasFile('image')) {
            if($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            // Upload with Rotation
            $validated['image_path'] = $this->uploadImage($request->file('image'), 'events', $rotation);
        }
        // Handle Rotation Only (Rotate existing image)
        elseif ($rotation != 0 && $event->image_path) {
             // Not implemented for events explicitly here to keep simple, 
             // but if you want it, you'd use the same logic as ArtworkController's update method.
        }
        
        unset($validated['image']); 

        // 2. Handle Translation Updates
        try {
            $tr = new GoogleTranslate();
            $validated['title'] = $tr->setTarget('en')->translate($request->title);
            $validated['title_id'] = $tr->setTarget('id')->translate($request->title);

            if ($request->filled('description')) {
                $validated['description'] = $tr->setTarget('en')->translate($request->description);
                $validated['description_id'] = $tr->setTarget('id')->translate($request->description);
            }
        } catch (\Exception $e) {}

        $validated['is_pinned'] = $request->has('is_pinned');

        // 3. Update Event Record
        $data = $validated;
        if(isset($data['gallery'])) unset($data['gallery']);

        $event->update($data);

        // 4. Handle NEW Gallery Uploads
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $photo) {
                $path = $this->uploadImage($photo, 'event_gallery');
                EventImage::create([
                    'event_id' => $event->id,
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->route('admin.events.index')->with('status', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        if($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        foreach($event->images as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }

        $event->delete();

        return back()->with('status', 'Event deleted successfully.');
    }

    /**
     * Download All Gallery Photos as ZIP
     */
    public function downloadPhotos($id)
    {
        $event = Event::with('images')->findOrFail($id);

        if ($event->images->isEmpty()) {
            return back()->with('error', 'No images to download.');
        }

        $zip = new ZipArchive;
        $fileName = 'event-' . $event->id . '-photos.zip';
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($event->images as $img) {
                $absolutePath = storage_path('app/public/' . $img->image_path);
                if (file_exists($absolutePath)) {
                    $zip->addFile($absolutePath, basename($img->image_path));
                }
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}