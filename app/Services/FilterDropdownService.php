<?php

namespace App\Services;

use App\Models\User;
use App\Models\Client;
use App\Models\ClientObjective;
use App\Models\StatusManager;
use App\Models\ExpertiseManager;
use App\Models\FocusArea;
use App\Models\FocusAreaManager;
use App\Models\ObjectiveManager;
use App\Models\PriorityManager;

class FilterDropdownService
{

    public function get(): array
    {
        return [
            'staffList' => User::orderBy('name')->get(['id', 'name']),
            'createdByUsers' => User::orderBy('name')->get(['id', 'name']),
            'clients' => Client::select('id', 'client_name')->activeClients()->orderBy('client_name')->get(),
            'objectives' => ObjectiveManager::activeObjectives()->select('id', 'name')->orderBy('name')->get(),
            'statuses' => StatusManager::activeStatus()->get(['id', 'name']),
            'expertiseManagers' => ExpertiseManager::activeExpertise()->select('id', 'name')->get(),
            'focusAreas' => FocusAreaManager::activeFocusArea()->select('id', 'name')->get(),
            'priorities' => PriorityManager::activePriorities()->select('id', 'name')->get(),
        ];
    }
}
