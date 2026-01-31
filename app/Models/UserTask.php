<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'staff_manager_id',
        'client_id',
        'task_name',
        'priority_manager_id',
        'task_start_date',
        'task_end_date',
        'task_due_date',
        'created_by',
        'status_manager_id',
        'description',
        'last_reminder_sent_at',
    ];

    protected $casts = [
        'last_reminder_sent_at' => 'datetime',
    ];

    public function clients()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_manager_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function priority_manager()
    {
        return $this->belongsTo(PriorityManager::class, 'priority_manager_id');
    }

    public function status_manager()
    {
        return $this->belongsTo(StatusManager::class, 'status_manager_id');
    }

    public function scopeFilters($query, $filters = [], $columns = [])
    {
        // 🟦 Always apply this → show only records from 1 Dec
        $query->whereDate('created_at', '>=', '2025-12-01');

        // if (!empty($filters['date_range'])) {
        //     $explode = explode(' - ', $filters['date_range']);
        //     $from = Carbon::parse($explode[0])->format('Y-m-d H:i:s');
        //     $to = Carbon::parse($explode[1])->format('Y-m-d H:i:s');
        //     $query->whereDate('task_start_date', '>=', $from);
        //     $query->whereDate('task_start_date', '<=', $to);
        // }
        if (!empty($filters['date_range'])) {

            $explode = explode(' - ', $filters['date_range']);
            $from = Carbon::parse($explode[0])->startOfDay();
            $to   = Carbon::parse($explode[1])->endOfDay();
        }

        // 🔹 Search filter
        if (!empty($filters['search']) || !empty($filters['search']['value'])) {
            $term = is_array($filters['search']) ? $filters['search']['value'] : $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->orWhere('task_name', 'LIKE', '%' . $term . '%')
                    ->orWhereHas('priority_manager', fn($q2) => $q2->where('name', 'LIKE', "%{$term}%"))
                    ->orWhereDate('task_start_date', 'LIKE', "%{$term}%");
            });
        }

        // 🔹 Staff filter
        if (!empty($filters['filterStaff'])) {
            $query->where('staff_manager_id', $filters['filterStaff']);
        }

        // 🔹 Project filter
        if (!empty($filters['filterClient'])) {
            $query->where('client_id', $filters['filterClient']);
        }

        // 🔹 Priority filter
        if (!empty($filters['filterPriority'])) {
            $query->where('priority_manager_id', $filters['filterPriority']);
        }

        // 🔹 Restrict staff view for non-admin users
        if (!auth()->user()->hasRole(['Super Admin', 'Admin'])) {
            $query->where('staff_manager_id', auth()->id());
        }

        // ====================================

        // 🔹 Sorting filter
        if (!empty($filters['sort']) && $filters['sort'] === 'latest') {
            $query->orderBy('task_start_date', 'desc');
        }

        // 🔹 Order by specific column (DataTables)
        if (!empty($filters['order']) && !empty(head($filters['order']))) {
            $order = head($filters['order']);
            $column = $columns[$order['column']];
            $query->orderBy($column, $order['dir']);
        }

        // 🔹 Pagination (DataTables)
        if (isset($filters['start']) && !empty($filters['length'])) {
            $query->take($filters['length'])->skip($filters['start']);
        }
        return $query;
    }
}
