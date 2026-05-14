<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CppBenefitsCosts extends Model
{
    public $timestamps = false;
    protected $table = 'cpp_benefits_costs';

    protected $fillable = [
        'cpp_submission_id',
        'beneficiaries',
        'social_benefits',
        'economic_benefits',
        'social_costs',
        'economic_costs',
    ];

    public function submission()
    {
        return $this->belongsTo(CppSubmission::class, 'cpp_submission_id');
    }
}
