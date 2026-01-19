<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
  protected $fillable = [
        'name',
        'country_code',
        'dial_code',
        'currency_name',
        'currency_code',
        'currency_symbol',
        'status'
    ];
}
