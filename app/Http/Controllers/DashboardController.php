<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        // Log::info(auth()->user());
        // Optional: ensure user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // // Check for User role
        // if ($user->hasRole('User')) {
        //     // Log::info($user->roles->first()?->name);
        //     return view('user.dashboard');
        // }

        // Default dashboard for super admin n others roles
        return view('admin.dashboard');
    }
}
