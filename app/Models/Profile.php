<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Profile extends Model
{
    use LogsActivity;
    
    protected $fillable = [
        'user_id',
        'mobile_number',
        'address',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['user_id', 'mobile_number', 'address'])
        ->useLogName('profile')
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
