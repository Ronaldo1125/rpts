<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CppEndorsement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'cpp_submission_id',
        'resolution_document',
        'sp_resolution_no',
        'sp_resolution_date',
        'sb_resolution_no',
        'sb_resolution_date',
        'letter_request_ref',
        'letter_transmittal_date',
        'bor_bot_resolution_no',
        'bor_bot_resolution_date',
    ];

    protected $casts = [
        'resolution_document' => 'array',
        'sp_resolution_date' => 'date',
        'sb_resolution_date' => 'date',
        'letter_transmittal_date' => 'date',
        'bor_bot_resolution_date' => 'date',
    ];

    public function submission()
    {
        return $this->belongsTo(CppSubmission::class, 'cpp_submission_id');
    }
}
