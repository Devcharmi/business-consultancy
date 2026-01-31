<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadFollowUp extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'remark',
        'next_follow_up_at',
        'status',  //   pending/completed
        'completed_at'
    ];

    protected $casts = [
        'next_follow_up_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }


    /* ======================
       Scopes
    ====================== */

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }

    public function scopeCompleted($q)
    {
        return $q->where('status', 'completed');
    }

    /* ======================
       Helpers
    ====================== */

    public function markCompleted()
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markPending()
    {
        $this->update([
            'status'       => 'pending',
            'completed_at' => null,
        ]);
    }
}
