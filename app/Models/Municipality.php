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

    public function project_location()
    {
        return $this->hasOne(ProjectLocation::class, 'municipality_id');
    }
}
