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
use Illuminate\Validation\Rule;
use Stichoza\GoogleTranslate\GoogleTranslate; // <--- IMPORT THIS

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
        // 1. Manually validate 'phone' in addition to the standard ProfileUpdateRequest rules
        // We do this here to ensure it gets saved without needing to edit the Request file.
        $validatedPhone = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $request->user()->fill($request->validated());

        // 2. Assign the phone number
        $request->user()->phone = $validatedPhone['phone'];

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()
            ->route('profile.user.show', [], 303)
            ->with('status', 'profile-updated')
            ->withFragment('update-profile-information');
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
            'about' => 'nullable|string|max:1000',
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

        // 5. Save and Translate "About" text
        if (!empty($validated['about'])) {
            $tr = new GoogleTranslate(); 
            
            // Save English Version
            $profile->about = $tr->setTarget('en')->translate($validated['about']);
            
            // Save Indonesian Version (Assuming you added 'about_id' to database)
            $profile->about_id = $tr->setTarget('id')->translate($validated['about']);
        } else {
            $profile->about = null;
            $profile->about_id = null;
        }

        $profile->save();

        // 6. Redirect back with a success message
        // return redirect()->route('profile.user.show')->with('status', 'artist-profile-updated')->withFragment('artist-profile-form');
        return redirect()
            ->route('profile.user.show', [], 303)
            ->with('status', 'artist-profile-updated')
            ->withFragment('artist-profile-form');

    }

    /**
     * Set the current user as the "One Gate" Shop Contact (Gatekeeper).
     */
    public function setAsGate(Request $request)
    {
        $user = $request->user();

        // 1. Security Check: Only Admins or Superadmins can be the gate
        if (!$user->is_admin && !$user->is_superadmin) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Validation: User MUST have a phone number first
        if (empty($user->phone)) {
            return back()->withErrors(['phone' => 'Please save your Phone Number in the form above first!']);
        }

        // 3. Reset everyone else's status to false (Only one gatekeeper allowed)
        \App\Models\User::query()->update(['is_shop_contact' => false]);

        // 4. Set current user as the Gatekeeper
        $user->is_shop_contact = true;
        $user->save();

        return back()->with('status', 'You are now the Main Shop Contact (Gatekeeper)! All orders will be directed to your WhatsApp.');
    }
}