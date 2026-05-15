<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProjectAssessmentReport extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'cpp_submission_id',
        'assessor_id',
        'evaluator_id',
        'checker_id',
        'concluder_id',
        'prepared_by',
        'prepared_by_pos',
        'reviewed_by',
        'reviewed_by_pos',
        'approved_by',
        'approved_by_pos',
        'doc_request',
        'doc_cpp_fs',
        'doc_endorsements',
        'typology_data',
        'responsiveness_data',
        'readiness_level',
        'par_background',
        'par_components',
        'par_spatial',
        'par_qualitative',
        'par_recommendations',
        'final_recommendation',
        'annex_description',
        'budget_breakdown',
        'total_project_cost',
        'status',
        'is_sectoral',
        'readiness_data',
        'endorsement_data',
        'finalization_notes'
    ];

    protected $casts = [
        'doc_request' => 'boolean',
        'doc_cpp_fs' => 'boolean',
        'doc_endorsements' => 'boolean',
        'typology_data' => 'array',
        'responsiveness_data' => 'array',
        'budget_breakdown' => 'array',
        'is_sectoral' => 'boolean',
        'total_project_cost' => 'decimal:2',
        'readiness_data' => 'array',
        'endorsement_data' => 'array'
    ];

    public function submission()
    {
        return $this->belongsTo(CppSubmission::class, 'cpp_submission_id');
    }

    public function referral()
    {
        return $this->hasOne(\App\Models\Referral::class, 'par_id')->latestOfMany();
    }

    public function referrals()
    {
        return $this->hasMany(\App\Models\Referral::class, 'par_id');
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id');
    }

    public function concluder()
    {
        return $this->belongsTo(User::class, 'concluder_id');
    }
}
