<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSector extends Model
{
    protected $fillable = [
        'project_id',
        'sector_id',
        'sub_sector_id',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function sub_sector()
    {
        return $this->belongsTo(SubSector::class);
    }
}
