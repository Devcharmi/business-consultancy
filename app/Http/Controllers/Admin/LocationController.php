<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function states($countryId)
    {
        return State::where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function cities($stateId)
    {
        return City::where('state_id', $stateId)
            ->orderBy('city')
            ->get(['id', 'city']);
    }
}
