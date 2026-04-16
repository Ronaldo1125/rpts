<?php

namespace App\Models;

use App\Models\User;
use App\Models\Agency;
use App\Models\Status;
use App\Models\MainProject;
use Spatie\Image\Enums\Fit;
use App\Models\ProjectSector;
use App\Models\ProjectIndicator;
use App\Models\ProjectCostTarget;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
    use InteractsWithMedia, LogsActivity;


    protected $fillable = [
        'project_title',
        'description',
        'component_project_id',
        'agency_id',
        'status',
        'funding_category',
        'fund_source',
        'other_fund_source',
        'latitude',
        'longtitude',
        'location',        
        'remarks',
        'user_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['project_title', 'description', 'status', 'funding_category', 'fund_source', 'latitude', 'longtitude'])
        ->useLogName('project')
        ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }

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
        return $this->hasMany(ProjectLocation::class, 'project_id');
    }

    public function project_indicator()
    {
        return $this->hasOne(ProjectIndicator::class, 'project_id');
    }

    public function project_sector()
    {
        return $this->hasOne(ProjectSector::class, 'project_id');
    }

    public function project_endorsement()
    {
        return $this->hasOne(ProjectEndorsement::class, 'project_id');
    }

    public function project_chapter()
    {
        return $this->hasMany(ProjectChapter::class, 'project_id');
    }

    public function project_location_specific()
    {
        return $this->hasOne(ProjectLocation::class, 'project_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function component_project()
    {
        return $this->belongsTo(ComponentProject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
