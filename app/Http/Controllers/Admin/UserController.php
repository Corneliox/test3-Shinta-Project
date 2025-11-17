<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Make sure this is imported

class UserController extends Controller
{
    /**
     * Display a list of all users.
     * THIS IS THE MISSING METHOD
     */
    public function index()
    {
        // Get all users, except for the admin (user ID 1)
        $users = User::where('id', '!=', 1)->paginate(15);

        return view('admin.users.index', [
            'users' => $users
        ]);
    }

    /**
     * Update the user's artist status.
     */
    public function update(Request $request, User $user)
    {
        // Toggle the 'is_artist' status
        $user->is_artist = !$user->is_artist;
        $user->save();

        // Also, create an ArtistProfile if one doesn't exist
        if ($user->is_artist && !$user->artistProfile) {
            $user->artistProfile()->create();
        }

        return back()->with('status', 'User status updated!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the main admin
        if ($user->id === 1 || $user->is_admin) {
            return back()->with('status', 'Cannot delete an admin account.');
        }

        // 1. Delete all their artworks and profile
        if ($user->is_artist) {
            $user->artworks()->each(function($artwork) {
                Storage::disk('public')->delete($artwork->image_path);
                $artwork->delete();
            });
            $user->artistProfile()->delete();
        }
        
        // 2. Delete the user
        $user->delete();

        return back()->with('status', 'User has been deleted successfully.');
    }
}