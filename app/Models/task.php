<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'client_id',
        'objective_manager_id',
        'expertise_manager_id',
        'title',
        'content',
        'task_start_date',
        'task_due_date',
        'type',
        'status_manager_id',
        'created_by',
        'updated_by',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function objective()
    {
        return $this->belongsTo(ObjectiveManager::class, 'objective_manager_id');
    }

    public function expertise()
    {
        return $this->belongsTo(ExpertiseManager::class, 'expertise_manager_id');
    }

    public function status()
    {
        return $this->belongsTo(StatusManager::class, 'status_manager_id');
    }

    public function commitments()
    {
        return $this->hasMany(TaskCommitment::class);
    }

    public function deliverables()
    {
        return $this->hasMany(TaskDeliverable::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}

