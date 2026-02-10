<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('filterDropdowns')) {
    function filterDropdowns(array $allowedRoutes = [])
    {
        $currentRoute = Route::currentRouteName();

        // If route not allowed, return empty array
        if (!empty($allowedRoutes) && !in_array($currentRoute, $allowedRoutes)) {
            return [
                'created_by' => collect(),
                'staffList' => collect(),
                'clients' => collect(),
                'objectives' => collect(),
                'statuses' => collect(),
                'expertiseManagers' => collect(),
                'focusAreas' => collect(),
                'priorities' => collect(),
                'entities' => collect(),
                'types' => collect(),
            ];
        }

        return app(\App\Services\FilterDropdownService::class)->get();
    }
}
