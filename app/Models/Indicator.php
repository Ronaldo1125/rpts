<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    protected $fillable = [
        'indicator_name',
    ];

    public function project_indicator()
    {
       return $this->hasOne(ProjectIndicator::class, 'indicator_id');
    }
}
