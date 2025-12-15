<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectIndicator extends Model
{
    protected $fillable = [
        'project_id',
        'indicator_id',
        'indicator_quantity',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
}
