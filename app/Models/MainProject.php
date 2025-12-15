<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainProject extends Model
{
    protected $fillable = [
        'main_project_title',
        'is_component',
        'user_id',
    ];

    public function project()
    {
        return $this->hasOne(Project::class, 'main_project_id');
    }

    public function project_component()
    {
        return $this->hasMany(Project::class, 'main_project_id');
    }
}
