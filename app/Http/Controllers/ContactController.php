<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission; // <-- ADD THIS
use Illuminate\Http\Request;

class ContactController extends Controller
{
/**
     * Store a new contact form submission.
     */
    public function store(Request $request)
    {
        // 1. Validate 'message' from your form
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string', // Changed from 'feedback'
        ]);

        ContactSubmission::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'feedback' => $validated['message'], // 2. Save 'message' to 'feedback' column
        ]);

        return back()->with('status', 'Thank you! Your feedback has been sent successfully.');
    }
}