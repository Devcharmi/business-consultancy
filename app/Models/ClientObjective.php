<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientObjective extends Model
{
    protected $fillable = [
        'client_id',
        'objective_manager_id',
        'status',
        'note',
        'created_by',
        'updated_by'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function objective_manager()
    {
        return $this->belongsTo(ObjectiveManager::class);
    }
}
