<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CppLocation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'cpp_submission_id',
        'province_id',
        'district_id',
        'municipality_id',
        'barangay_id',
    ];

    public function submission()
    {
        return $this->belongsTo(CppSubmission::class, 'cpp_submission_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'barangay_id');
    }
}
