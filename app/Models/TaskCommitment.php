<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCommitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'commitment',
        'commitment_date',
        'due_date',
        'staff_manager_id',
        'status',
    ];

    protected $casts = [
        'commitment_date' => 'date',
        'due_date' => 'date',
    ];

    /* ======================
       Relationships
    ====================== */

    public function task() // this is meeting
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_manager_id');
    }

    public function userTask()
    {
        return $this->hasOne(UserTask::class, 'source_id')
            ->where('source_type', 'commitment');
    }
}
