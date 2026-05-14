<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CppLogframe extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'cpp_submission_id',
        'lf_goal_narrative',
        'lf_goal_indicators',
        'lf_goal_verification',
        'lf_goal_assumptions',
        'lf_purpose_narrative',
        'lf_purpose_indicators',
        'lf_purpose_verification',
        'lf_purpose_assumptions',
        'lf_outputs_narrative',
        'lf_outputs_indicators',
        'lf_outputs_verification',
        'lf_outputs_assumptions',
        'lf_inputs_narrative',
        'lf_inputs_indicators',
        'lf_inputs_verification',
        'lf_inputs_assumptions',
    ];

    public function submission()
    {
        return $this->belongsTo(CppSubmission::class, 'cpp_submission_id');
    }
}
