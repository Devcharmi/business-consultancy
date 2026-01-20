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
        'expected_date',
        'status',
    ];

    protected $casts = [
        'expected_date' => 'date',
    ];

    /* ======================
       Relationships
    ====================== */

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
