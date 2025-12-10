<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\EventImage;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Stichoza\GoogleTranslate\GoogleTranslate; // <--- IMPORT THIS
use ZipArchive; // <--- IMPORT THIS

class EventController extends Controller
{
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
            // 'title_id' generated automatically
            'description' => 'nullable|string',
            // 'description_id' generated automatically
            'image' => 'required|image|max:5120', // Main Image
            'gallery.*' => 'nullable|image|max:5120', // Gallery Images
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'is_pinned' => 'nullable|boolean',
        ]);

        // 1. Handle Main Image
        $validated['image_path'] = $request->file('image')->store('events', 'public');
        $validated['is_pinned'] = $request->has('is_pinned');

        // 2. Handle Google Translation
        $tr = new GoogleTranslate(); 
        
        // Translate Title
        $validated['title'] = $tr->setTarget('en')->translate($request->title); // Ensure EN
        $validated['title_id'] = $tr->setTarget('id')->translate($request->title); // Create ID

        // Translate Description
        if($request->description) {
            $validated['description'] = $tr->setTarget('en')->translate($request->description);
            $validated['description_id'] = $tr->setTarget('id')->translate($request->description);
        }

        // 3. Create Event
        // Remove 'image' and 'gallery' from array before creating, as they aren't columns in 'events' table
        $data = $validated;
        unset($data['image']); 
        if(isset($data['gallery'])) unset($data['gallery']);

        $event = Event::create($data);

        // 4. Handle Gallery Uploads (New Table)
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $photo) {
                $path = $photo->store('event_gallery', 'public');
                EventImage::create([
                    'event_id' => $event->id,
                    'image_path' => $path
                ]);
            }
        }

        // ActivityLog::record('Event Created', 'Created event: ' . $event->title);

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
            'image' => 'nullable|image|max:5120', // Main Image Update
            'gallery.*' => 'nullable|image|max:5120', // Add more to gallery
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'is_pinned' => 'nullable|boolean',
        ]);

        // 1. Handle Main Image Update
        if ($request->hasFile('image')) {
            // Delete old main image
            if($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('events', 'public');
        }
        unset($validated['image']); // Remove from array

        // 2. Handle Translation Updates
        $tr = new GoogleTranslate();
        
        // Update Title & Translation
        $validated['title'] = $tr->setTarget('en')->translate($request->title);
        $validated['title_id'] = $tr->setTarget('id')->translate($request->title);

        // Update Description & Translation
        if ($request->filled('description')) {
            $validated['description'] = $tr->setTarget('en')->translate($request->description);
            $validated['description_id'] = $tr->setTarget('id')->translate($request->description);
        }

        $validated['is_pinned'] = $request->has('is_pinned');

        // 3. Update Event Record
        // Remove gallery from validated array so it doesn't try to update the event table
        $data = $validated;
        if(isset($data['gallery'])) unset($data['gallery']);

        $event->update($data);

        // 4. Handle NEW Gallery Uploads (Appends to existing)
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $photo) {
                $path = $photo->store('event_gallery', 'public');
                EventImage::create([
                    'event_id' => $event->id,
                    'image_path' => $path
                ]);
            }
        }

        // ActivityLog::record('Event Updated', 'Updated event details for: ' . $event->title);

        return redirect()->route('admin.events.index')->with('status', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        // 1. Delete Main Image
        if($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }

        // 2. Delete Gallery Images
        foreach($event->images as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }

        $event->delete();

        // ActivityLog::record('Event Deleted', 'Deleted event: ' . $event->title);

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
        // We save the zip temporarily in storage/app/public
        $zipPath = storage_path('app/public/' . $fileName);

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($event->images as $img) {
                // Get physical path of the image
                $absolutePath = storage_path('app/public/' . $img->image_path);
                
                if (file_exists($absolutePath)) {
                    // Add to zip with its original filename
                    $zip->addFile($absolutePath, basename($img->image_path));
                }
            }
            $zip->close();
        }

        // Return download and delete the zip file afterwards
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}