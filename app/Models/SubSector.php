<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubSector extends Model
{
    protected $fillable = [
        'subsector_name',
        'sector_id',
    ];

    public function project_sector()
    {
        return $this->hasOne(ProjectSector::class, 'sub_sector_id');
    }
}
