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
    ];

    protected $casts = [
        'next_follow_up_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
