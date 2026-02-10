<?php

namespace App\Models;

use App\Traits\FiltersByExpertiseManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Consulting extends Model
{
    use FiltersByExpertiseManager;

    protected $fillable = [
        // 'client_id',
        // 'objective_manager_id',
        'client_objective_id',
        'expertise_manager_id',
        'focus_area_manager_id',
        'consulting_datetime',
        'created_by',
        'updated_by'
    ];

    // public function client()
    // {
    //     return $this->belongsTo(Client::class);
    // }

    // public function objective_manager()
    // {
    //     return $this->belongsTo(ObjectiveManager::class);
    // }

    public function client_objective()
    {
        return $this->belongsTo(ClientObjective::class);
    }

    public function expertise_manager()
    {
        return $this->belongsTo(ExpertiseManager::class);
    }

    public function focus_area_manager()
    {
        return $this->belongsTo(FocusAreaManager::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
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

        if (!empty($filters['search']) or !empty($filters['search']['value'])) {
            $term = is_array($filters['search']) ? $filters['search']['value'] : $filters['search'];
            $query->where(function ($q) use ($term) {

                // Client name
                $q->whereHas('client_objective.client', function ($qc) use ($term) {
                    $qc->where('client_name', 'LIKE', "%{$term}%");
                })

                    // OR Objective name
                    ->orWhereHas('client_objective.objective_manager', function ($qo) use ($term) {
                        $qo->where('name', 'LIKE', "%{$term}%");
                    })
                    // OR expertise name
                    ->orWhereHas('expertise_manager', function ($qo) use ($term) {
                        $qo->where('name', 'LIKE', "%{$term}%");
                    })
                    // OR focus_area name
                    ->orWhereHas('focus_area_manager', function ($qo) use ($term) {
                        $qo->where('name', 'LIKE', "%{$term}%");
                    });
                // // OR client name
                // ->orWhereHas('client', function ($qo) use ($term) {
                //     $qo->where('client_name', 'LIKE', "%{$term}%");
                // })
                // // OR objective name
                // ->orWhereHas('objective_manager', function ($qo) use ($term) {
                //     $qo->where('name', 'LIKE', "%{$term}%");
                // });
            });
        }

        // 🔹 Project filter
        if (!empty($filters['filterClient'])) {
            $query->whereHas('client_objective', function ($qc) use ($filters) {
                $qc->where('client_id', $filters['filterClient']);
            });
        }

        // 🔹 Project filter
        if (!empty($filters['filterObjective'])) {
            $query->whereHas('client_objective', function ($qc) use ($filters) {
                $qc->where('objective_manager_id', $filters['filterObjective']);
            });
        }

        // 🔹 Status filter
        if (!empty($filters['filterExpertise'])) {
            $query->where('expertise_manager_id', $filters['filterExpertise']);
        }

        // 🔹 Status filter
        if (!empty($filters['filterFocusArea'])) {
            $query->where('focus_area_manager_id', $filters['filterFocusArea']);
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
