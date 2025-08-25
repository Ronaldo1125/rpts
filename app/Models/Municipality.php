<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $fillable = [
        'municipality_name',
        'province_id',
        'district_id'
    ];
}
