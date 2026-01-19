<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Allow Super Admin to bypass all permission checks
        Gate::before(function ($user, $ability) {
            return $user && $user->hasRole('Super Admin') ? true : null;
        });
    }
}