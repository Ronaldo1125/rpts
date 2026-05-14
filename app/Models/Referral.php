<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $table = 'referrals';

    protected $fillable = [
        'cipg_submission_id',
        'par_id',
        'from_user_id',
        'from_division_id',
        'to_user_id',
        'to_division_id',
        'stage',
        'status',
        'notes',
        'referred_at',
        'resolved_at',
    ];

    protected $casts = [
        'referred_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(CppSubmission::class, 'cipg_submission_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function fromDivision()
    {
        return $this->belongsTo(Division::class, 'from_division_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function toDivision()
    {
        return $this->belongsTo(Division::class, 'to_division_id');
    }

    public function par()
    {
        return $this->belongsTo(\App\Models\ProjectAssessmentReport::class, 'par_id');
    }

    public function getProjectTitleAttribute()
    {
        return $this->submission?->project_title ?? 'N/A';
    }

    public function getAgencyAttribute()
    {
        return $this->submission?->user?->agency?->agency_name ?? 'N/A';
    }
}
