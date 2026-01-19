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
        $user = $request->user();

        // Check if user is Super Admin or Admin
        $isAdmin = $user->hasRole(['Super Admin', 'Admin']);

        // Validation rules
        $rules = [
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];

        // Add current_password only for Admin/Super Admin
        if ($isAdmin) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
            'original_password' => $validated['password'],
        ]);

        return back()->with('status', 'password-updated');
    }

    // public function update(Request $request): RedirectResponse
    // {
    //     $validated = $request->validate([
    //         'current_password' => ['required', 'current_password'],
    //         'password' => ['required', Password::defaults(), 'confirmed'],
    //     ]);

    //     $request->user()->update([
    //         'password' => Hash::make($validated['password']),
    //         'original_password' => $validated['password'],
    //     ]);

    //     return back()->with('status', 'password-updated');
    // }
}
