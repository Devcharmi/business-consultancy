<?php

namespace App\Models;

use App\Traits\FiltersByExpertiseManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Consulting extends Model
{
    use FiltersByExpertiseManager;

    protected $fillable = [
        'client_objective_id',
        'expertise_manager_id',
        'focus_area_manager_id',
        'consulting_date',
        'consulting_type_id',
        'start_time',
        'end_time',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'consulting_date' => 'date',
    ];


    // public static function hasTimeOverlap(
    //     $date,
    //     $startTime,
    //     $endTime,
    //     $expertiseManagerId,
    //     $userId,
    //     $ignoreId = null
    // ) {
    //     $query = self::whereDate('consulting_date', $date)
    //         ->where('created_by', $userId)
    //         ->where('expertise_manager_id', $expertiseManagerId)
    //         ->where(function ($q) use ($startTime, $endTime) {
    //             $q->where('start_time', '<', $endTime)
    //                 ->where('end_time', '>', $startTime);
    //         });

    //     if ($ignoreId) {
    //         $query->where('id', '!=', $ignoreId);
    //     }

    //     return $query->exists();
    // }

    public static function hasTimeOverlap(
        $date,
        $startTime,
        $endTime,
        $expertiseManagerId,
        $userId,
        $ignoreId = null
    ) {
        // If any are null, cannot check overlap
        if (!$date || !$startTime || !$endTime) {
            return false;
        }

        $query = self::whereDate('consulting_date', $date)
            ->where('created_by', $userId)
            ->where('expertise_manager_id', $expertiseManagerId);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->where(function ($q) use ($startTime, $endTime) {
            $q->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime);
        })->exists();
    }

    public function consulting_type()
    {
        return $this->belongsTo(ConsultingType::class);
    }

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

    public function scopeAccessibleBy($query, $user)
    {
        if (!$user || $user->hasRole('Super Admin')) {
            return $query;
        }

        return $query->whereIn('expertise_manager_id', function ($q) use ($user) {
            $q->select('expertise_manager_id')
                ->from('users_expertise_manager')
                ->where('user_id', $user->id);
        });
    }

    public function scopeFilters($query, $filters = [], $columns = [])
    {
        if (!empty($filters['date_range'])) {
            $explode = explode(' - ', $filters['date_range']);
            $from = Carbon::parse($explode[0])->format('Y-m-d H:i:s');
            $to = Carbon::parse($explode[1])->format('Y-m-d H:i:s');
            $query->whereDate('consulting_date', '>=', $from);
            $query->whereDate('consulting_date', '<=', $to);
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
                    })
                    // OR type name
                    ->orWhereHas('consulting_type', function ($qo) use ($term) {
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

        if (!empty($filters['filterClient'])) {
            $query->whereHas('client_objective', function ($qc) use ($filters) {
                $qc->where('client_id', $filters['filterClient']);
            });
        }

        if (!empty($filters['filterObjective'])) {
            $query->whereHas('client_objective', function ($qc) use ($filters) {
                $qc->where('objective_manager_id', $filters['filterObjective']);
            });
        }

        if (!empty($filters['filterExpertise'])) {
            $query->where('expertise_manager_id', $filters['filterExpertise']);
        }

        if (!empty($filters['filterFocusArea'])) {
            $query->where('focus_area_manager_id', $filters['filterFocusArea']);
        }

        if (!empty($filters['filterConsultingType'])) {
            $query->where('consulting_type_id', $filters['filterConsultingType']);
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
