<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CppImplSchedule extends Model
{
    protected $table = 'cpp_impl_schedule';

    protected $fillable = [
        'cpp_submission_id',
        'year',
        'physical_target',
        'indicator',
        'amount',
        'sort_order'
    ];

    public function cpp_submission()
    {
        return $this->belongsTo(CppSubmission::class);
    }
}
