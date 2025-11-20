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
     * SUPERADMIN LOGIC: Hide superadmins UNLESS "Reveal Mode" is active.
     */
    public function index()
    {
        // Check if the "Reveal Mode" session exists and is still valid (not older than 5 mins)
        $revealUntil = session('superadmin_reveal_until');
        $isRevealActive = $revealUntil && now()->lessThan($revealUntil);

        $query = User::query();

        // If Reveal Mode is NOT active, apply the hiding filters
        if (!$isRevealActive) {
            $query->where('id', '!=', auth()->id())
                  ->where('is_superadmin', false);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users', 'isRevealActive'));
    }

    /**
     * NEW SECRET: Reveal Superadmins for 5 minutes.
     */
    public function revealSuperAdmins()
    {
        if (!auth()->user()->is_superadmin) {
            abort(403);
        }

        // Set a session variable that expires 5 minutes from now
        session(['superadmin_reveal_until' => now()->addMinutes(5)]);

        return response()->json(['message' => 'God Mode Activated: Superadmins visible for 5 minutes.']);
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
        // 1. Validate
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Update validation to allow 'superadmin'
            'role' => ['required', 'in:user,artist,admin,superadmin'], 
        ]);

        // 2. Security Check: Prevent regular admins from creating superadmins
        if ($request->role === 'superadmin' && !auth()->user()->is_superadmin) {
            abort(403, 'You are not authorized to create Superadmins.');
        }

        // 3. Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_artist' => $request->role === 'artist',
            'is_admin' => $request->role === 'admin',
            'is_superadmin' => $request->role === 'superadmin', // Handle the new role
        ]);

        // 4. Create Artist Profile if needed
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
     * Toggle Superadmin Status (Demote/Promote manually).
     */
    public function toggleSuperAdmin(User $user)
    {
        // 1. Security Check: Only a Superadmin can do this
        if (!auth()->user()->is_superadmin) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Safety Check: Prevent demoting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot demote yourself!');
        }

        // 3. Toggle the status
        $user->is_superadmin = !$user->is_superadmin;
        $user->save();

        return back()->with('status', 'Superadmin status updated!');
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