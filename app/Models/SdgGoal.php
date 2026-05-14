<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SdgGoal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'number',
        'title',
        'label',
    ];

    public function submissions()
    {
        return $this->belongsToMany(CppSubmission::class, 'cpp_sdg_alignments', 'sdg_goal_id', 'cpp_submission_id');
    }
}
