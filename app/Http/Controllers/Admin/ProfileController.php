<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        if ($request->validated()) {
            $user->fill($request->all());
        }
        // Log::info($user);
        // Handle Profile Image
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image');
            // Log::info('profileImage - ' . $profileImage);
            if ($user->profile_image) {
                ImageHelper::deleteImage('uploads/users/profile/' . $user->profile_image);
            }
            $imagePath = ImageHelper::uploadImageSimple($profileImage, 'uploads/users/profile');
            $user->profile_image = $imagePath;
        }

        // Handle Signature Image
        if ($request->hasFile('signature_image')) {
            $signatureImage = $request->file('signature_image');
            if ($user->signature_image) {
                ImageHelper::deleteImage('uploads/users/signature/' . $user->signature_image);
            }
            $imagePath = ImageHelper::uploadImageSimple($signatureImage, 'uploads/users/signature');
            $user->signature_image = $imagePath;
        }

        // Remove images if flagged
        if ($request->remove_profile_image) {
            if ($user->profile_image) {
                ImageHelper::deleteImage('uploads/users/profile/' . $user->profile_image);
            }
            $user->profile_image = null;
        }

        if ($request->remove_signature_image) {
            if ($user->signature_image) {
                ImageHelper::deleteImage('uploads/users/signature/' . $user->signature_image);
            }
            $user->signature_image = null;
        }

        $user->save();

        return Redirect::route('admin.profile.edit')->with('status', 'profile-updated');
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
}
