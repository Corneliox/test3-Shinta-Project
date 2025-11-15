<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ArtistProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Validation\Rules\File;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's artist profile information.
     */
    public function updateArtistProfile(Request $request): \Illuminate\Http\RedirectResponse
    {
        // 1. You must be an artist to do this
        if (!$request->user()->is_artist) {
            abort(403);
        }

        // 2. Validate the incoming data
        $validated = $request->validate([
            'about' => 'nullable|string|max:5000',
            'profile_picture' => [
                'nullable',
                File::image() // Use advanced file validation
                    ->max(2048) // 2MB Max
                    ->dimensions(Rule::dimensions()->minWidth(200)->minHeight(200)),
            ],
        ]);

        // 3. Find the profile or create a new one
        $profile = $request->user()->artistProfile()->firstOrNew([
            'user_id' => $request->user()->id,
        ]);

        // 4. Handle the file upload
        if ($request->hasFile('profile_picture')) {
            // Delete the old picture if it exists
            if ($profile->profile_picture) {
                Storage::disk('public')->delete($profile->profile_picture);
            }

            // Store the new one and get its path
            // This will store it in 'storage/app/public/artist_pics'
            $path = $request->file('profile_picture')->store('artist_pics', 'public');
            $profile->profile_picture = $path;
        }

        // 5. Save the "About" text
        $profile->about = $validated['about'];
        $profile->save();

        // 6. Redirect back with a success message
        return redirect()->route('profile.user.show')
                        ->with('status', 'artist-profile-updated');
    }
}
