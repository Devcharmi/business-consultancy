<?php

use Illuminate\Support\Facades\Auth;

function canAccess($permission)
{
    $user = Auth::user();

    if (!$user) {
        return false;
    }

    // Super Admin always allowed
    if ($user->hasRole('Super Admin')) {
        return true;
    }

    // Normal permission check
    return $user->can($permission);
}
