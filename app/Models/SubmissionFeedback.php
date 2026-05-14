<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFeedback extends Model
{
    protected $table = 'submission_feedbacks';

    protected $fillable = [
        'cpp_submission_id',
        'referral_id',
        'staff_id',
        'notes',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(CppSubmission::class, 'cpp_submission_id');
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
