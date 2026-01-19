<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'objective_manager_id',
        'name',
        'phone',
        'email',
        'status',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function objective_manager()
    {
        return $this->belongsTo(ObjectiveManager::class, 'objective_manager_id');
    }

    public function followUps()
    {
        return $this->hasMany(LeadFollowUp::class);
    }

    public function scopeFilters($query, $filters = [], $columns = [])
    {
        // SEARCH
        if (!empty($filters['search']) || !empty($filters['search']['value'])) {

            $term = is_array($filters['search'])
                ? $filters['search']['value']
                : $filters['search'];

            $query->where(function ($q) use ($term) {
                $q->orWhere('name', 'LIKE', '%' . $term . '%');
                $q->orWhere('email', 'LIKE', '%' . $term . '%');
                $q->orWhere('phone', 'LIKE', '%' . $term . '%');
            });
        }

        // ORDER
        if (!empty($filters['order']) && !empty(head($filters['order']))) {

            $order  = head($filters['order']);
            $column = $columns[$order['column']] ?? 'id';

            $query->orderBy($column, $order['dir']);
        }

        // PAGINATION
        if (isset($filters['start']) && !empty($filters['length'])) {
            $query->skip($filters['start'])->take($filters['length']);
        }

        return $query;
    }
}
