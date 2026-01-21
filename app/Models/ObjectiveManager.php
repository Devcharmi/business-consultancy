<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObjectiveManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status'
    ];

    public function scopeActiveObjectives($query)
    {
        return $query->where('status', '1');
    }

    // 🔹 Objective → Objective Manager
    public function clientObjectives()
    {
        return $this->hasMany(ClientObjective::class);
    }

    public function scopeFilters($query, $filters = [], $columns = [])
    {
        // if (!empty($filters['date_range'])) {
        //     $explode = explode(' - ', $filters['date_range']);
        //     $from = Carbon::parse($explode[0])->format('Y-m-d H:i:s');
        //     $to = Carbon::parse($explode[1])->format('Y-m-d H:i:s');
        //     $query->whereDate('created_at', '>=', $from);
        //     $query->whereDate('created_at', '<=', $to);
        // }

        if (!empty($filters['search']) or !empty($filters['search']['value'])) {
            $term = is_array($filters['search']) ? $filters['search']['value'] : $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->orWhere('name', 'LIKE', '%' . $term . '%');
            });
        }
        if (!empty($filters['sort'])) {
            $sort = $filters['sort'];

            if ($sort == 'latest') {
                $query->orderBy('id', 'desc');
            }
        }
        if (isset($filters['start']) && !empty($filters['length'])) {
            $query->take($filters['length'])
                ->skip($filters['start']);
        }
        if (!empty($filters['order']) and !empty(head($filters['order']))) {
            $order = head($filters['order']);
            $column = $columns[$order['column']];
            $query->orderBy($column, $order['dir']);
        }
        return $query;
    }
}
