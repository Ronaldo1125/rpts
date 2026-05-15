<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CppSubmission extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'agency_id',
        'sector_id',
        'sub_sector_id',
        'project_title',
        'project_type',
        'components',
        'project_coverage',
        'geo_start_lat',
        'geo_start_lng',
        'geo_end_lat',
        'geo_end_lng',
        'project_status',
        'prep_status',
        'background',
        'goal',
        'purpose',
        'outputs',
        'activities',
        'linkages',
        'total_cost',
        'funding_source',
        'counterpart_funding',
        'agencies_involved',
        'impl_arrangement',
        'env_clearance_desc',
        'social_accept',
        'consultation_status',
        'consult_planned_date',
        'consult_highlights',
        'hgdg',
        'prepared_by_name',
        'prepared_by_position',
        'prepared_date',
        'noted_by_name',
        'noted_by_position',
        'noted_date',
        'status',
        'stage',
        'submitted_at',
        'project_id'
    ];

    protected $casts = [
        'prep_status' => 'array',
        'project_type' => 'array',
        'submitted_at' => 'datetime',
        'prepared_date' => 'date',
        'noted_date' => 'date',
        'consult_planned_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function implementation_schedules()
    {
        return $this->hasMany(CppImplSchedule::class, 'cpp_submission_id');
    }

    public function consultation_dates()
    {
        return $this->hasMany(CppConsultationDate::class, 'cpp_submission_id');
    }

    public function locations()
    {
        return $this->hasMany(CppLocation::class, 'cpp_submission_id');
    }

    public function logframe()
    {
        return $this->hasOne(CppLogframe::class, 'cpp_submission_id');
    }

    public function endorsement()
    {
        return $this->hasOne(CppEndorsement::class, 'cpp_submission_id');
    }

    public function benefits_costs()
    {
        return $this->hasOne(CppBenefitsCosts::class, 'cpp_submission_id');
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'sector_id');
    }

    public function sub_sector()
    {
        return $this->belongsTo(SubSector::class, 'sub_sector_id');
    }

    public function sdg_alignments()
    {
        return $this->belongsToMany(SdgGoal::class, 'cpp_sdg_alignments', 'cpp_submission_id', 'sdg_goal_id');
    }

    public function rdp_alignments()
    {
        return $this->belongsToMany(Chapter::class, 'cpp_rdp_alignments', 'cpp_submission_id', 'chapter_id');
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'cipg_submission_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(SubmissionFeedback::class, 'cpp_submission_id');
    }

    public function assessment_report()
    {
        return $this->hasOne(ProjectAssessmentReport::class, 'cpp_submission_id');
    }

    public function comments_and_recommendations()
    {
        return $this->hasMany(CommentAndRecommendation::class, 'cpp_submission_id');
    }
}
