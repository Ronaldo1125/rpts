<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

use App\Traits\Searchable;

class EndorseYear extends Model
{
    use LogsActivity, Searchable;

    protected $searchable = ['year'];
    
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
