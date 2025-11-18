<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a paginated list of all events.
     * This is for your "event/main.blade.php" page.
     */
    public function index()
    {
        // Get the main paginated list
        $events = Event::latest()->paginate(9); 
        
        // Get the pinned event *separately* to show at the top
        $pinned_event = Event::where('is_pinned', true)->latest()->first();

        return view('event.main', [
            'events' => $events,
            'pinned_event' => $pinned_event // <-- This is the variable that was missing
        ]);
    }

    /**
     * Display a single event.
     * This is for your "event/details.blade.php" page.
     */
    public function show(Event $event)
    {
        return view('event.details', [
            'event' => $event
        ]);
    }
}