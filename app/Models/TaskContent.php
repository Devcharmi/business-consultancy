<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'task_content',
        'content_date',
    ];

    /* ======================
       Relationships
    ====================== */

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
