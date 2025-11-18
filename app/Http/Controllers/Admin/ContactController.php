<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission; // <-- ADD THIS
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a list of all contact submissions.
     */
    public function index()
    {
        $submissions = ContactSubmission::latest()->paginate(20);
        return view('admin.contact.index', [
            'submissions' => $submissions
        ]);
    }

    /**
     * Mark a submission as seen.
     */
    public function update(ContactSubmission $submission)
    {
        $submission->update(['is_seen' => true]);
        return back()->with('status', 'Submission marked as read.');
    }
}