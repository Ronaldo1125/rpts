<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComponentProject extends Model
{
    protected $fillable = [
        'component_project_title',
    ];

    public function project()
    {
        return $this->hasMany(Project::class, 'component_project_id');
    }
}
