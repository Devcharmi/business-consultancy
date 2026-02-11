<?php

namespace App\Models;

use App\Traits\FiltersByExpertiseManager;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use FiltersByExpertiseManager;

    protected $fillable = [
        'consulting_id',
        'client_objective_id',
        'expertise_manager_id',
        'title',
        'task_start_date',
        'task_due_date',
        'type',
        'status_manager_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'task_start_date' => 'date',
        'task_due_date'   => 'date',
    ];

    public function consulting()
    {
        return $this->belongsTo(Consulting::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function client_objective()
    {
        return $this->belongsTo(ClientObjective::class);
    }

    public function expertise_manager()
    {
        return $this->belongsTo(ExpertiseManager::class, 'expertise_manager_id');
    }

    public function status_manager()
    {
        return $this->belongsTo(StatusManager::class, 'status_manager_id');
    }

    public function content()
    {
        return $this->hasMany(TaskContent::class);
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
            $from = Carbon::parse($explode[0])->startOfDay();
            $to   = Carbon::parse($explode[1])->endOfDay();
            $query->whereDate('task_start_date', '>=', $from);
            $query->whereDate('task_due_date', '<=', $to);
        }

        /* ================= CONSULTING FILTER ================= */

        if (!empty($filters['consulting_id'])) {
            $query->where('consulting_id', $filters['consulting_id']);
        }

        /* ================= CLIENT OBJECTIVE FILTER ================= */

        if (!empty($filters['client_objective_id'])) {
            $query->where('client_objective_id', $filters['client_objective_id']);
        }

        if (!empty($filters['search']) or !empty($filters['search']['value'])) {
            $term = is_array($filters['search']) ? $filters['search']['value'] : $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('title', 'LIKE', "%{$term}%");
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
                    });
            });
        }

        // 🔹 Created by filter
        if (!empty($filters['filterCreatedBy'])) {
            $query->where('created_by', $filters['filterCreatedBy']);
        }

        // 🔹 Project filter
        if (!empty($filters['filterClient'])) {
            $query->whereHas('client_objective', function ($qc) use ($filters) {
                $qc->where('client_id', $filters['filterClient']);
            });
        }

        // 🔹 Status filter
        if (!empty($filters['filterStatus'])) {
            $query->where('status_manager_id', $filters['filterStatus']);
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
