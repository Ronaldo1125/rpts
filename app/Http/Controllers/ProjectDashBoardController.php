<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectDashBoardController extends Controller
{
    public function index()
    {
    
    $componentProjects = Project::whereNotNull('component_project_id')
                    ->orderBy('component_project_id')->get();

    $projects = Project::whereNull('component_project_id')->get();

    $projectCountStatus = Project::selectRaw('count(status) as status_total, status')
            ->groupBy('status')
            ->pluck('status_total', 'status')
            ->toArray();

    $projectCountFunding = Project::selectRaw('funding_category as name, count(funding_category) as y')
                            ->groupBy('funding_category')
                            ->get();

    // $projectCountFundingCategories = $projectCountFunding->toJson();

           //dd($projectCountStatus);
           //dd($projectCountFundingCategories);
        return view('projectDashboard.index', compact('projectCountStatus', 'projectCountFunding', 'projects', 'componentProjects'));
    }

    public function index_v2()
    {
        return view('projectDashboard.index_v2');
    }
}
