<?php

namespace App\Observers;

use App\Models\PriorityManager;
use App\Models\StatusManager;
use App\Models\TaskCommitment;
use App\Models\UserTask;

class TaskCommitmentObserver
{
    public function created(TaskCommitment $commitment)
    {
        $this->syncUserTask($commitment);
    }

    public function updated(TaskCommitment $commitment)
    {
        $this->syncUserTask($commitment);
    }

    public function deleted(TaskCommitment $commitment)
    {
        UserTask::where('source_type', 'commitment')
            ->where('source_id', $commitment->id)
            ->delete();
    }

    protected function syncUserTask(TaskCommitment $commitment)
    {
        $task = $commitment->task; // meeting

        if (!$task) {
            return;
        }

        UserTask::updateOrCreate(
            [
                'source_type' => 'commitment',
                'source_id'   => $commitment->id,
            ],
            [
                'client_id'        => $task->client_objective->client_id,   // ✅ IMPORTANT
                'task_name'        => $commitment->commitment,
                'task_start_date'  => $commitment->commitment_date,
                'task_due_date'    => $commitment->due_date,
                'priority_manager_id' => PriorityManager::mediumId(),
                'status_manager_id' => StatusManager::pendingId(),
                'staff_manager_id' => auth()->id(),
                'created_by'       => auth()->id(),
            ]
        );
    }
}
