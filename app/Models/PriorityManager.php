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

    
    public static function mediumId()
    {
        return static::where('name', 'Medium')->value('id');
    }
}
