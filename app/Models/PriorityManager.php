<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriorityManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color_name',
        'status'
    ];

    public function scopeActivePriorities($query)
    {
        return $query->where('status', '1');
    }

    public static function mediumId()
    {
        return static::where('name', 'Medium')->value('id');
    }
}
