<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEndorsement extends Model
{
    protected $fillable = [
        'project_id',
        'endorse_year_id',
        'rdc_endorsement_number'
    ];

     public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
