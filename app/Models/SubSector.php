<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Searchable;

class SubSector extends Model
{
    use Searchable;

    protected $searchable = ['subsector_name'];

    protected $fillable = [
        'subsector_name',
        'sector_id',
    ];

    public function project_sector()
    {
        return $this->hasOne(ProjectSector::class, 'sub_sector_id');
    }
}
