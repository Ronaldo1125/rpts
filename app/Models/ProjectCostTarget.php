<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCostTarget extends Model
{
    protected $table = 'project_cost_target';

    protected $fillable = [
        'project_id',
        'target_year_2023',
        'target_year_2024',
        'target_year_2025',
        'target_year_2026',
        'target_year_2027',
        'target_year_2028',
        'target_succeeding_years',
        'cost_year_2023',
        'cost_year_2024',
        'cost_year_2025',
        'cost_year_2026',
        'cost_year_2027',
        'cost_year_2028',
        'cost_succeeding_years',
    ];
}
