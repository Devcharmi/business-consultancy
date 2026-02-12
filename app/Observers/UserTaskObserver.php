<?php

namespace App\Observers;

use App\Models\UserTask;
use App\Models\UserTaskActivity;
use App\Models\StatusManager;
use Carbon\Carbon;

class UserTaskObserver
{
    /*
    |--------------------------------------------------------------------------
    | BEFORE SAVE
    |--------------------------------------------------------------------------
    */
    public function saving(UserTask $task)
    {
        if ($task->status_manager_id) {

            $status = StatusManager::find($task->status_manager_id);

            if ($status && $status->isCompleted()) {
                $task->completed_at = $task->completed_at ?? now();
            } else {
                $task->completed_at = null;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREATED
    |--------------------------------------------------------------------------
    */
    public function created(UserTask $task)
    {
        UserTaskActivity::create([
            'user_task_id' => $task->id,
            'activity_type' => 'created',
            'description'  => 'Task created',
            'performed_by' => auth()->check() ? auth()->id() : null,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATED
    |--------------------------------------------------------------------------
    */
    public function updated(UserTask $task)
    {
        $original = $task->getOriginal();

        /*
        =========================
        STATUS CHANGED
        =========================
        */
        if ($task->wasChanged('status_manager_id')) {

            $oldStatus = StatusManager::find($original['status_manager_id']);
            $newStatus = StatusManager::find($task->status_manager_id);

            UserTaskActivity::create([
                'user_task_id' => $task->id,
                'activity_type' => 'status_changed',
                'description'  => "Status changed from "
                    . ($oldStatus->name ?? '-') . " to "
                    . ($newStatus->name ?? '-'),
                'meta' => [
                    'old_status' => $oldStatus->name ?? null,
                    'new_status' => $newStatus->name ?? null,
                ],
                'performed_by' => auth()->check() ? auth()->id() : null,
            ]);
        }

        /*
        =========================
        DUE DATE CHANGED (Delay)
        =========================
        */
        if ($task->wasChanged('task_due_date')) {

            $oldDate = Carbon::parse($original['task_due_date'])->format('d-m-Y');
            $newDate = $task->task_due_date->format('d-m-Y');

            if (Carbon::parse($task->task_due_date)
                ->gt(Carbon::parse($original['task_due_date']))
            ) {
                UserTaskActivity::create([
                    'user_task_id' => $task->id,
                    'activity_type' => 'delayed',
                    'description'  => "Due date extended from {$oldDate} to {$newDate}",
                    'meta' => [
                        'old_due_date' => $oldDate,
                        'new_due_date' => $newDate,
                        'delay_days'   => Carbon::parse($task->task_due_date)
                            ->diffInDays(Carbon::parse($original['task_due_date'])),
                    ],
                    'performed_by' => auth()->check() ? auth()->id() : null,
                ]);
            }
        }


        /*
        =========================
        STAFF REASSIGNED
        =========================
        */
        if ($task->wasChanged('staff_manager_id')) {

            UserTaskActivity::create([
                'user_task_id' => $task->id,
                'activity_type' => 'reassigned',
                'description'  => "Task reassigned",
                'meta' => [
                    'old_staff' => $original['staff_manager_id'],
                    'new_staff' => $task->staff_manager_id,
                ],
                'performed_by' => auth()->check() ? auth()->id() : null,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETED
    |--------------------------------------------------------------------------
    */
    public function deleted(UserTask $task)
    {
        UserTaskActivity::create([
            'user_task_id' => $task->id,
            'activity_type' => 'deleted',
            'description'  => 'Task deleted',
            'performed_by' => auth()->check() ? auth()->id() : null,
        ]);
    }
}
