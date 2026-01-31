<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\UserTask;

class TaskObserver
{
    public function deleting(Task $task)
    {
        // commitments → user_tasks
        UserTask::where('source_type', 'commitment')
            ->whereIn(
                'source_id',
                $task->commitments()->pluck('id')
            )
            ->delete();

        // deliverables → user_tasks
        UserTask::where('source_type', 'deliverable')
            ->whereIn(
                'source_id',
                $task->deliverables()->pluck('id')
            )
            ->delete();
    }
}
