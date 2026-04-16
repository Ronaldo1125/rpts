<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Chapter extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'chapter_name'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['chapter_name'])
        ->useLogName('rdc_chapter')
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }
}
