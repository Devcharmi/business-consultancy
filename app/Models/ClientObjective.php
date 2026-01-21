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

    protected $appends = ['label'];

    public function getLabelAttribute()
    {
        return $this->client->client_name . ' - ' . $this->objective_manager->name;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function objective_manager()
    {
        return $this->belongsTo(ObjectiveManager::class);
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

                // Client name
                $q->whereHas('client', function ($qc) use ($term) {
                    $qc->where('client_name', 'LIKE', "%{$term}%");
                })

                    // OR Objective name
                    ->orWhereHas('objective_manager', function ($qo) use ($term) {
                        $qo->where('name', 'LIKE', "%{$term}%");
                    });
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
