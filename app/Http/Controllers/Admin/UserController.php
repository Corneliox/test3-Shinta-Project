<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display list of users.
     * SUPERADMIN LOGIC: Hide all superadmins from the list.
     */
    public function index()
    {
        // 1. Hide the current user (yourself)
        // 2. Hide ALL superadmins (Invisibility Mode)
        $users = User::where('id', '!=', auth()->id())
                     ->where('is_superadmin', false) 
                     ->latest()
                     ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form to create a new user manually.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store the manually created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,artist,admin'], // Simple role selection
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_artist' => $request->role === 'artist',
            'is_admin' => $request->role === 'admin',
        ]);

        if ($user->is_artist) {
            $user->artistProfile()->create();
        }

        return redirect()->route('admin.users.index')->with('status', 'New user created successfully!');
    }

    /**
     * Toggle Artist Status (Existing function).
     */
    public function update(Request $request, User $user)
    {
        $user->is_artist = !$user->is_artist;
        $user->save();

        if ($user->is_artist && !$user->artistProfile) {
            $user->artistProfile()->create();
        }

        return back()->with('status', 'User artist status updated!');
    }

    /**
     * SUPERADMIN ONLY: Toggle Admin Status.
     */
    public function toggleAdmin(User $user)
    {
        // Only allow if current user is Superadmin
        if (!auth()->user()->is_superadmin) {
            abort(403, 'Unauthorized action.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('status', 'User admin privileges updated!');
    }

    /**
     * SECRET EASTER EGG: Promote to Superadmin.
     * Triggered by 10 clicks on the name.
     */
    public function promoteToSuperAdmin(User $user)
    {
        // Only a Superadmin can promote another (even via the secret click)
        if (!auth()->user()->is_superadmin) {
            abort(403, 'Nice try, but you are not a Superadmin.');
        }

        $user->is_superadmin = true;
        $user->save();

        return response()->json(['message' => 'User is now a hidden Superadmin!']);
    }

    /**
     * Delete User.
     */
    public function destroy(User $user)
    {
        if ($user->is_superadmin) {
            return back()->with('error', 'Cannot delete a Superadmin.');
        }

        if ($user->is_artist) {
            $user->artworks()->each(function($artwork) {
                Storage::disk('public')->delete($artwork->image_path);
                $artwork->delete();
            });
            $user->artistProfile()->delete();
        }
        
        $user->delete();

        return back()->with('status', 'User deleted successfully.');
    }
}