<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class MainProject extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'main_project_title',
        'is_component',
        'user_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['main_project_title'])
        ->useLogName('component_project')
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'main_project_id');
    }

    public function project_component()
    {
        return $this->hasMany(Project::class, 'main_project_id');
    }
}
