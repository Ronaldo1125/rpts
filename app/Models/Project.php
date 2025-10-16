<?php

namespace App\Models;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
    use InteractsWithMedia;


    protected $fillable = [
        'project_title',
        'description',
        'status_id',
        'endorsement_id',
        'funding_requirement',
        'remarks',
        'user_id',
    ];

    public static function last() 
    {
        return static::all()->last();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }

    public function project_cost_target() 
    {
        return $this->hasOne(ProjectCostTarget::class, 'project_id');
    }

    public function project_location()
    {
        return $this->hasOne(ProjectLocation::class, 'project_id');
    }
}
