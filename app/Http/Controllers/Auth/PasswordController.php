<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            // 🚀 FORCE SCROLL ON FAILURE
            return redirect()
                ->route('profile.user.show')
                ->withFragment('update-password-information')
                ->withErrors($e->errors(), 'updatePassword')
                ->withInput();
        }

        // SUCCESS
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('profile.user.show', [], 303)
            ->with('status', 'password-updated')
            ->withFragment('update-password-information');
    }

}
