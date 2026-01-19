<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'gst_number',
        'status',
        'created_by',
        'updated_by',
    ];

    // Relations
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scope for filtering/searching
    public function scopeFilters($query, $filters = [], $columns = [])
    {
        if (!empty($filters['search']) || !empty($filters['search']['value'])) {
            $term = is_array($filters['search']) ? $filters['search']['value'] : $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->orWhere('client_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('contact_person', 'LIKE', '%' . $term . '%')
                    ->orWhere('email', 'LIKE', '%' . $term . '%')
                    ->orWhere('phone', 'LIKE', '%' . $term . '%');
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

        if (!empty($filters['order']) && !empty(head($filters['order']))) {
            $order = head($filters['order']);
            $column = $columns[$order['column']] ?? 'id';
            $query->orderBy($column, $order['dir']);
        }

        return $query;
    }
}
