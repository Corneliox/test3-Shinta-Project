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
        $events = Event::latest()->paginate(9); // Show 9 events per page

        return view('event.main', [
            'events' => $events
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