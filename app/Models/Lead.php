<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
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

    public function followUps()
    {
        return $this->hasMany(LeadFollowUp::class);
    }

    public function scopeFilters($query, $filters = [], $columns = [])
    {
        if (!empty($filters['date_range'])) {
            $explode = explode(' - ', $filters['date_range']);
            $from = Carbon::parse($explode[0])->format('Y-m-d H:i:s');
            $to = Carbon::parse($explode[1])->format('Y-m-d H:i:s');
            $query->whereDate('created_at', '>=', $from);
            $query->whereDate('created_at', '<=', $to);
        }

        // SEARCH
        if (!empty($filters['search']) || !empty($filters['search']['value'])) {

            $term = is_array($filters['search'])
                ? $filters['search']['value']
                : $filters['search'];

            $query->where(function ($q) use ($term) {
                $q->orWhere('name', 'LIKE', '%' . $term . '%');
                $q->orWhere('email', 'LIKE', '%' . $term . '%');
                $q->orWhere('phone', 'LIKE', '%' . $term . '%');
                $q->orWhere('status', 'LIKE', '%' . $term . '%');
            });
        }
        // 🔹 Created by filter
        if (!empty($filters['filterCreatedBy'])) {
            $query->where('user_id', $filters['filterCreatedBy']);
        }

        // 🔹 Client filter
        if (!empty($filters['filterClient'])) {
            $query->where('client_id', $filters['filterClient']);
        }

        if (!empty($filters['sort'])) {
            $sort = $filters['sort'];

            if ($sort == 'latest') {
                $query->orderBy('id', 'desc');
            }
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
