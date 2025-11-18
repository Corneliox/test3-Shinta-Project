<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a list of all contact submissions (For the Admin Sidebar).
     */
    public function index()
    {
        $submissions = ContactSubmission::latest()->paginate(20);
        
        return view('admin.contact.index', [
            'submissions' => $submissions
        ]);
    }

    /**
     * Mark a submission as seen (For the Green Tick).
     */
    public function update(ContactSubmission $submission)
    {
        $submission->update(['is_seen' => true]);
        
        return back()->with('status', 'Message marked as seen.');
    }
}