<?php

namespace App\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    protected $fillable = [
        'agency_name',
        'agency_acronym',
        'sector_id',
    ];


    public function user()
    {
        return $this->hasOne(User::class, 'agency_id');
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'agency_id');
    }
}
