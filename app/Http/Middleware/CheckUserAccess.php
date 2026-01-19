<?php

namespace App\Http\Middleware;

use App\Mail\VendorVerifyEmail;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        /*
        |--------------------------------------------------------------------------
        | BLOCKED USER (GLOBAL)
        |--------------------------------------------------------------------------
        */
        if ($user->status === 'blocked') {
            Auth::logout();

            return redirect()->route('login')
                ->withErrors([
                    'username' => 'Your account has been blocked. Please contact support.',
                ]);
        }

        return $next($request);
    }
}
