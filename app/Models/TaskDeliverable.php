<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDeliverable extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'deliverable',
        'deliverable_date',
        'expected_date',
        'status',
    ];

    protected $casts = [
        'deliverable_date' => 'date',
        'expected_date' => 'date',
    ];

    /* ======================
       Relationships
    ====================== */

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function userTask()
    {
        return $this->hasOne(UserTask::class, 'source_id')
            ->where('source_type', 'deliverable');
    }
}
