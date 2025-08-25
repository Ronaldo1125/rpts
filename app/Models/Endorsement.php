<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Endorsement extends Model
{
    protected $fillable = [
        'rdc_resolution',
        'resolution_date',
    ];
}
