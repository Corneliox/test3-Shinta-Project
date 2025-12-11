<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;

class ArtworkController extends Controller
{
    public function index()
    {
        // Show ALL artworks, with pagination
        $artworks = Artwork::with('user')->latest()->paginate(20);
        return view('admin.artworks.index', compact('artworks'));
    }
}