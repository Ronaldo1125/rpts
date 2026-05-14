<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentAndRecommendation extends Model
{
    use HasFactory;

    protected $table = 'comments_and_recommendations';

    protected $fillable = [
        'cpp_submission_id',
        'user_id',
        'finding',
        'recommendation',
        'stage',
        'status'
    ];

    public function submission()
    {
        return $this->belongsTo(CppSubmission::class, 'cpp_submission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
