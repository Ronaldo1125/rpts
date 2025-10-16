<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectLocation extends Model
{
    protected $table = 'project_location';

    protected $fillable = [
        'project_id',
        'province_id',
        'district_id',
        'municipality_id'

    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
