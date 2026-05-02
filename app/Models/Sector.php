<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Searchable;

class Sector extends Model
{
    use Searchable;

    protected $searchable = ['sector_name', 'sector_acronym'];

    protected $fillable = [
        'sector_name',
        'sector_acronym'
    ];

    public function project_sector()
    {
        return $this->hasOne(ProjectSector::class, 'sector_id');
    }
}
