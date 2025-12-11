<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule; // Import Rule for unique checks

class UserController extends Controller
{
    /**
     * Display list of users.
     */
    public function index()
    {
        $revealUntil = session('superadmin_reveal_until');
        $isRevealActive = $revealUntil && now()->lessThan($revealUntil);

        $query = User::query();

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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,artist,admin,superadmin'], 
        ]);

        if ($request->role === 'superadmin' && !auth()->user()->is_superadmin) {
            abort(403, 'You are not authorized to create Superadmins.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_artist' => $request->role === 'artist',
            'is_admin' => $request->role === 'admin',
            'is_superadmin' => $request->role === 'superadmin',
        ]);

        if ($user->is_artist) {
            $user->artistProfile()->create();
        }

        return redirect()->route('admin.users.index')->with('status', 'New user created successfully!');
    }

    /**
     * Show the edit form for a user.
     * (Previously missing)
     */
    public function edit(User $user)
    {
        // Only Superadmins can edit other Admins/Superadmins
        if ($user->is_admin || $user->is_superadmin) {
            if (!auth()->user()->is_superadmin) {
                abort(403, 'Only Superadmins can edit other administrators.');
            }
        }

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage (Full Profile Update).
     * (Completely rewritten to handle Name, Email, Password)
     */
    public function update(Request $request, User $user)
    {
        // 1. Authorization Check
        if (!auth()->user()->is_superadmin && ($user->is_admin || $user->is_superadmin)) {
            abort(403, 'Unauthorized.');
        }

        // 2. Validation
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // Optional
            'role' => ['nullable', 'in:user,artist,admin,superadmin'], // Optional role change
        ]);

        // 3. Update Basic Info
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // 4. Update Password (only if provided)
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // 5. Update Roles (Optional, logic depends on your UI)
        // If you added a role dropdown in edit.blade.php
        if ($request->has('role')) {
            // Reset roles first
            $user->is_artist = false;
            $user->is_admin = false;
            
            // Only Superadmin can set Superadmin
            if (auth()->user()->is_superadmin) {
                $user->is_superadmin = false; 
            }

            switch ($request->role) {
                case 'artist':
                    $user->is_artist = true;
                    if (!$user->artistProfile) $user->artistProfile()->create();
                    break;
                case 'admin':
                    $user->is_admin = true;
                    break;
                case 'superadmin':
                    if (auth()->user()->is_superadmin) $user->is_superadmin = true;
                    break;
            }
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User profile updated successfully.');
    }

    /**
     * Toggle Superadmin Status.
     */
    public function toggleSuperAdmin(User $user)
    {
        if (!auth()->user()->is_superadmin) abort(403, 'Unauthorized action.');
        if ($user->id === auth()->id()) return back()->with('error', 'You cannot demote yourself!');

        $user->is_superadmin = !$user->is_superadmin;
        $user->save();

        return back()->with('status', 'Superadmin status updated!');
    }

    /**
     * Toggle Admin Status.
     */
    public function toggleAdmin(User $user)
    {
        if (!auth()->user()->is_superadmin) abort(403, 'Unauthorized action.');

        $user->is_admin = !$user->is_admin;
        $user->save();

        return back()->with('status', 'User admin privileges updated!');
    }

    /**
     * Secret Promote.
     */
    public function promoteToSuperAdmin(User $user)
    {
        if (!auth()->user()->is_superadmin) abort(403, 'Nice try.');

        $user->is_superadmin = true;
        $user->save();

        return response()->json(['message' => 'User is now a hidden Superadmin!']);
    }

    /**
     * Delete User.
     */
    public function destroy(User $user)
    {
        if ($user->is_superadmin) return back()->with('error', 'Cannot delete a Superadmin.');

        // Clean up artworks and profile
        if ($user->is_artist) {
            $user->artworks()->each(function($artwork) {
                if($artwork->image_path) Storage::disk('public')->delete($artwork->image_path);
                $artwork->delete();
            });
            if($user->artistProfile) $user->artistProfile()->delete();
        }
        
        $user->delete();

        return back()->with('status', 'User deleted successfully.');
    }
}