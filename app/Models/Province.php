<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'province_name',
    ];

    public function project_location()
    {
        return $this->hasMany(ProjectLocation::class, 'province_id');
    }
}
