<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $fillable = [
        'agency_name',
        'agency_acronym',
        'sector_id',
    ];
}
