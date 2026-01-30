<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FiltersByExpertiseManager
{
    public function scopeAccessibleBy(Builder $query, $user)
    {
        // Super Admin → full access
        if (!$user || $user->hasRole('Super Admin')) {
            return $query;
        }

        static $expertiseIds = null;

        if ($expertiseIds === null) {
            $expertiseIds = $user->expertiseManagers()
                ->pluck('expertise_managers.id')
                ->toArray();
        }

        $query->whereIn(
            $this->getTable() . '.expertise_manager_id',
            $expertiseIds
        );

        return $query;
    }
}
