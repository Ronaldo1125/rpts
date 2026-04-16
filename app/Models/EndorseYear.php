<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class EndorseYear extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'year',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['year'])
        ->useLogName('endorse_year')
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }
}
