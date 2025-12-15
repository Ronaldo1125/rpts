<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundingCategory extends Model
{
    protected $fillable = [
        'category_name'
    ];

     public function project()
    {
        return $this->hasOne(Project::class, 'funding_category_id');
    }
}
