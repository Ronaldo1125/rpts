<?php

namespace App\Models;

use App\Models\Project;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Agency extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'agency_name',
        'agency_acronym',
        'sector_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['agency_name', 'agency_acronym', 'sector_id'])
        ->useLogName('agency')
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }


    public function user()
    {
        return $this->hasOne(User::class, 'agency_id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'agency_id');
    }
}
