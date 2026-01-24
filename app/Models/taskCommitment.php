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
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    /* ======================
       Relationships
    ====================== */

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
