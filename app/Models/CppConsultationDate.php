<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CppConsultationDate extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'cpp_submission_id',
        'consultation_date'
    ];

    protected $casts = [
        'consultation_date' => 'date'
    ];

    public function cpp_submission()
    {
        return $this->belongsTo(CppSubmission::class);
    }
}
