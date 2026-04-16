<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Indicator extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'indicator_name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['indicator_name'])
        ->useLogName('indicator')
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }



    public function project_indicator()
    {
       return $this->hasOne(ProjectIndicator::class, 'indicator_id');
    }
}
