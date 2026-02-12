<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTaskActivity extends Model
{
    use HasFactory;
   
    protected $fillable = [
        'user_task_id',
        'activity_type',
        'description',
        'meta',
        'performed_by'
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(UserTask::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
