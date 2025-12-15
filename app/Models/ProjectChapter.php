<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectChapter extends Model
{
    protected $fillable = [
        'project_id',
        'chapter_id'
    ];
}
