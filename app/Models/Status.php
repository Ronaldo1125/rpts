<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public $fillable = [
        'status_name',
    ];

    public function project()
    {
        return $this->hasOne(Project::class, 'status_id');
    }
}
