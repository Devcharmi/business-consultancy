<?php

namespace App\Services;

use App\Models\LeadFollowUp;
use App\Models\UserTask;
use App\Models\StatusManager;
use Carbon\Carbon;

class StatusUpdateService
{
    public static function update(string $type, int $id, string $status): bool
    {
        switch ($type) {

            case 'followup':
                $followup = LeadFollowUp::findOrFail($id);

                $followup->update([
                    'status'       => $status, // pending | completed
                    'completed_at' => $status === 'completed' ? now() : null,
                ]);
                return true;

            case 'task':
                $task = UserTask::findOrFail($id);

                // 🔥 map logical status → actual StatusManager name
                $statusName = self::mapTaskStatusName($status);

                $statusId = StatusManager::where('name', $statusName)->value('id');

                if (!$statusId) {
                    return false;
                }

                $task->update([
                    'status_manager_id' => $statusId,
                    'completed_at'      => $statusName === 'Done' ? now() : null,
                ]);

                return true;
        }

        return false;
    }

    protected static function mapTaskStatusName(string $status): string
    {
        return match (strtolower($status)) {
            'completed', 'done' => 'Done',
            'in_progress'       => 'In Progress',
            default             => 'Pending',
        };
    }
}

