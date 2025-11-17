<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
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
            'image' => 'required|image|max:5120',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'is_pinned' => 'nullable|boolean',
        ]);

        $validated['image_path'] = $request->file('image')->store('events', 'public');
        $validated['is_pinned'] = $request->has('is_pinned');

        Event::create($validated);

        return redirect()->route('admin.events.index')->with('status', 'Event created successfully.');
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
            'image' => 'nullable|image|max:5120', // Image is optional on update
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'is_pinned' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            Storage::disk('public')->delete($event->image_path);
            // Store new one
            $validated['image_path'] = $request->file('image')->store('events', 'public');
        }

        $validated['is_pinned'] = $request->has('is_pinned');
        
        $event->update($validated);

        return redirect()->route('admin.events.index')->with('status', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        Storage::disk('public')->delete($event->image_path);
        $event->delete();

        return back()->with('status', 'Event deleted successfully.');
    }
}