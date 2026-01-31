<?php

namespace App\Observers;

use App\Models\PriorityManager;
use App\Models\StatusManager;
use App\Models\TaskDeliverable;
use App\Models\UserTask;

class TaskDeliverableObserver
{
    public function created(TaskDeliverable $deliverable)
    {
        $this->syncUserTask($deliverable);
    }

    public function updated(TaskDeliverable $deliverable)
    {
        $this->syncUserTask($deliverable);
    }

    public function deleted(TaskDeliverable $deliverable)
    {
        UserTask::where('source_type', 'deliverable')
            ->where('source_id', $deliverable->id)
            ->delete();
    }

    protected function syncUserTask(TaskDeliverable $deliverable)
    {
        $task = $deliverable->task; // meeting

        if (!$task) {
            return;
        }

        UserTask::updateOrCreate(
            [
                'source_type' => 'deliverable',
                'source_id'   => $deliverable->id,
            ],
            [
                'client_id'        => $task->client_objective->client_id,   // ✅ IMPORTANT
                'task_name'        => $deliverable->deliverable,
                'task_start_date'  => $deliverable->deliverable_date,
                'task_due_date'    => $deliverable->expected_date,
                'priority_manager_id' => PriorityManager::mediumId(),
                'status_manager_id' => StatusManager::pendingId(),
                'staff_manager_id' => auth()->id(),
                'created_by'       => auth()->id(),
            ]
        );
    }
}
