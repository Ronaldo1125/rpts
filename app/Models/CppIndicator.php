<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CppIndicator extends Model
{
    protected $fillable = [
        'cpp_impl_schedule_id',
        'indicator_id',
        'indicator_quantity',
    ];

    public function cpp_impl_schedule()
    {
        return $this->belongsTo(CppImplSchedule::class);
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
}
