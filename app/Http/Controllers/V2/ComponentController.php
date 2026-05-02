<?php

namespace App\Http\Controllers\V2;

use App\FundSource;
use App\Http\Requests\StoreComponentRequest;
use App\Http\Requests\UpdateComponentRequest;
use App\Models\Agency;
use App\Models\Chapter;
use App\Models\ComponentProject;
use App\Models\EndorseYear;
use App\Models\Indicator;
use App\Models\Project;
use App\Models\ProjectChapter;
use App\Models\ProjectLocation;
use App\Models\Province;
use App\Models\Sector;
use App\ProjectFundingCategory;
use App\ProjectLocationType;
use App\ProjectStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComponentController extends Controller
{

    public $statuses;
    public $agencies;
    public $funding_categories;
    public $provinces;
    public $chapters;
    public $sectors;
    public $endorse_years;
    public $indicators;
    public $project_location_types;
     public $fund_sources;

    public function __construct()
    {
        $this->middleware('can:project-view')->only(['index']);
        $this->middleware('can:project-create')->only(['create', 'store']);
        $this->middleware('can:project-edit')->only(['edit', 'editSubProject', 'updateSubProject']);
        $this->middleware('can:project-delete')->only(['destroy', 'subProjectDestroy']);

        $this->statuses = ProjectStatus::cases();
        $this->agencies = Agency::pluck('agency_acronym', 'id')->all();
        $this->funding_categories = ProjectFundingCategory::cases();
        $this->provinces = Province::pluck('province_name', 'id')->all();
        $this->chapters = Chapter::pluck('chapter_name', 'id')->all();
        $this->sectors = Sector::pluck('sector_name', 'id')->all();
        $this->endorse_years = EndorseYear::pluck('year', 'id')->all();
        $this->indicators = Indicator::pluck('indicator_name', 'id')->all();
        $this->project_location_types = ProjectLocationType::cases();
        $this->fund_sources = FundSource::cases();
       
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Project::whereNotNull('component_project_id')
                    ->with('component_project', 'agency', 'project_cost_target');

        if (!Auth::user()->hasRole('administrator')) {
            $query->where('user_id', Auth::id());
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('project_title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('component_project', function($cq) use ($search) {
                      $cq->where('component_project_title', 'like', "%{$search}%");
                  });
            });
        }

        $projects = $query->orderBy('component_project_id', 'asc')->paginate($perPage);

        $projects->each(function($p) {
            $p->funding_requirement = ($p->project_cost_target->cost_year_2023 ?? 0) +
                                      ($p->project_cost_target->cost_year_2024 ?? 0) +
                                      ($p->project_cost_target->cost_year_2025 ?? 0) +
                                      ($p->project_cost_target->cost_year_2026 ?? 0) +
                                      ($p->project_cost_target->cost_year_2027 ?? 0) +
                                      ($p->project_cost_target->cost_year_2028 ?? 0) +
                                      ($p->project_cost_target->cost_succeeding_years ?? 0);
        });

        // $projects = Project::whereNotNull('component_project_id')
        //                 ->orderBy('component_project_id', 'asc')
        //                 ->get();

       // dd($projects);

        $title = 'Delete Component Project Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('components.index_v2', compact('projects'));
    }

    public function create($component_id)
    {
        $component_project_id = $component_id;
        $component_project = '';

        //$component_project = ComponentProject::findOrFail($component_project_id);

        if($component_project_id != 0) 
        {
            $component_project = ComponentProject::findOrFail($component_project_id);

            // if($component_project->isEmpty())
            // {
            //     return redirect()->route('components.create', ['component_id' => 0 ]);
            // }
        }

        //dd($component_project);

        //dd($component_projects);
        $title = 'Delete SubProject Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        if ($component_project && $component_project->project) {
            $component_project->project->each(function($p) {
                $p->funding_requirement = ($p->project_cost_target->cost_year_2023 ?? 0) +
                                          ($p->project_cost_target->cost_year_2024 ?? 0) +
                                          ($p->project_cost_target->cost_year_2025 ?? 0) +
                                          ($p->project_cost_target->cost_year_2026 ?? 0) +
                                          ($p->project_cost_target->cost_year_2027 ?? 0) +
                                          ($p->project_cost_target->cost_year_2028 ?? 0) +
                                          ($p->project_cost_target->cost_succeeding_years ?? 0);
            });
        }

        return view('components.manage', [
            'component_project' => $component_project, 
            'statuses' => $this->statuses, 
            'agencies' => $this->agencies, 
            'funding_categories' => $this->funding_categories, 
            'provinces' => $this->provinces, 
            'chapters' => $this->chapters, 
            'sectors' => $this->sectors, 
            'endorse_years' => $this->endorse_years, 
            'indicators' => $this->indicators,
            'project_location_types' => $this->project_location_types,
            'component_project_id' => $component_project_id,
            'fund_sources' => $this->fund_sources
            ]);
    }

    public function store(StoreComponentRequest $request)
    {
        $request->validated();

        //dd($request);
        $component_project_id = $request->component_project_id;

        if($component_project_id == 0) 
        {
            $component_project = ComponentProject::create([
                'component_project_title' => $request->component_project_title,
            ]);

            $component_project_id = $component_project->id;
        } else {
            $component_project = ComponentProject::findOrFail($component_project_id);
            $component_project->update([
                'component_project_title' => $request->component_project_title,
            ]);
        }


        $sub = $this->storeProject($request, $component_project_id);
        
        toast('Component Project was stored successfully!','success');
        
        return redirect()->route('v2.components.showSubProject', [
            'component_id' => $component_project_id,
            'id' => $sub->id
        ]);
        

    }

    public function edit($id)
    {

    }

    public function showSubProject($component_id, $id)
    {
        $component_project_id = $component_id;

        $component_project = ComponentProject::findOrFail($component_project_id);

        $sub_project = Project::where('component_project_id', $component_project_id)
                                ->where('id', $id)
                                ->firstOrFail();

        $arrInterProvinces = [];
        $arrValueSelectedChapters = [];
        $provinceId = '';
        $selectedProvince = '';
        $locationSpecific = '';

        $selectedChapters = ProjectChapter::where('project_id', '=', $id)->get();

        if($sub_project->location == 'inter-province') {
            $selectedInterProvinces = ProjectLocation::where('project_id', $id)
                                                 ->whereNull('district_id')
                                                 ->whereNull('municipality_id')
                                                 ->get();

            if(count($selectedInterProvinces) > 0)
            {
                foreach($selectedInterProvinces as $selectedInterProvince) {
                    $arrInterProvinces[] = $selectedInterProvince['province_id'];
                }  
            }
        }


        $locationSpecific = ProjectLocation::where('project_id', $id)
                                           ->whereNotNull('district_id')
                                           ->first();

        if ($locationSpecific) {
            $provinceId = $locationSpecific->province_id;
            $selectedProvince = $locationSpecific->province->province_name ?? '';
        }

        if ($component_project && $component_project->project) {
            $component_project->project->each(function($p) {
                $p->funding_requirement = ($p->project_cost_target->cost_year_2023 ?? 0) +
                                          ($p->project_cost_target->cost_year_2024 ?? 0) +
                                          ($p->project_cost_target->cost_year_2025 ?? 0) +
                                          ($p->project_cost_target->cost_year_2026 ?? 0) +
                                          ($p->project_cost_target->cost_year_2027 ?? 0) +
                                          ($p->project_cost_target->cost_year_2028 ?? 0) +
                                          ($p->project_cost_target->cost_succeeding_years ?? 0);
            });
        }

        return view('components.manage', [
            'component_project' => $component_project, 
            'sub_project' => $sub_project, 
            'statuses' => $this->statuses, 
            'agencies' => $this->agencies, 
            'funding_categories' => $this->funding_categories, 
            'provinceId' => $provinceId, 
            'provinces' => $this->provinces,
            'selectedProvince' => $selectedProvince,
            'chapters' => $this->chapters, 
            'sectors' => $this->sectors, 
            'endorse_years' => $this->endorse_years, 
            'indicators' => $this->indicators,
            'project_location_types' => $this->project_location_types,
            'component_project_id' => $component_project_id, 
            'arrValueSelectedChapters' => $arrValueSelectedChapters, 
            'arrInterProvinces' => $arrInterProvinces, 
            'locationSpecific' => $locationSpecific,
            'fund_sources' => $this->fund_sources,
            'isView' => true
            ]);
    }

    public function editSubProject($component_id, $id)
    {
        $component_project_id = $component_id;

        $component_project = ComponentProject::findOrFail($component_project_id);

        // if($component_project_id != 0) 
        // {
        //     $component_projects = ComponentProject::where('id', $component_project_id)->get();

        //     if($component_projects->isEmpty())
        //     {
        //         return redirect()->route('components.create', ['component_id' => 0 ]);
        //     }
        // }

      
        $sub_project = Project::where('component_project_id', $component_project_id)
                                ->where('id', $id)
                                ->firstOrFail();

        if(!Auth::user()->hasRole('administrator')) {
            if($sub_project->user->isNot(Auth::user())) 
            {
                abort(403);
            }
        }

                                //dd($component_project);

        // $statuses = ProjectStatus::cases();
        // $agencies = Agency::pluck('agency_acronym', 'id')->all();
        // $funding_categories = ProjectFundingCategory::cases();
        // $provinces = Province::pluck('province_name', 'id')->all();
        // $chapters = Chapter::pluck('chapter_name', 'id')->all();
        // $sectors = Sector::pluck('sector_name', 'id')->all();
        // $endorse_years = EndorseYear::pluck('year', 'id')->all();
        // $indicators = Indicator::pluck('indicator_name', 'id')->all();
        $arrInterProvinces = [];
        $arrValueSelectedChapters = [];
        $provinceId = '';
        $selectedProvince = '';
        $locationSpecific = '';

        $selectedChapters = ProjectChapter::where('project_id', '=', $id)->get();

        if($sub_project->location == 'inter-province') {
            $selectedInterProvinces = ProjectLocation::where('project_id', $id)
                                                 ->whereNull('district_id')
                                                 ->whereNull('municipality_id')
                                                 ->get();

            if(count($selectedInterProvinces) > 0)
            {
                foreach($selectedInterProvinces as $selectedInterProvince) {
                    $arrInterProvinces[] = $selectedInterProvince['province_id'];
                }  
            }
        }


        $locationSpecific = ProjectLocation::where('project_id', $id)
                                           ->whereNotNull('district_id')
                                           ->whereNotNull('municipality_id')
                                           ->first();

        if(!is_null($locationSpecific)) {
            $provinceId = $locationSpecific->province_id;
        }


        if(count($selectedChapters) > 0) {
            foreach($selectedChapters as $selectedChapter) {
                $arrValueSelectedChapters[] = $selectedChapter['chapter_id'];
            }

        }


        if($sub_project->location == 'provincewide') {

             $provinceWide = ProjectLocation::where('project_id', $id)
                                            ->whereNull('district_id')
                                            ->whereNull('municipality_id')
                                            ->first();
            
            $selectedProvince = $provinceWide->province_id;

        }

        $title = 'Delete SubProject Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);



        if ($component_project && $component_project->project) {
            $component_project->project->each(function($p) {
                $p->funding_requirement = ($p->project_cost_target->cost_year_2023 ?? 0) +
                                          ($p->project_cost_target->cost_year_2024 ?? 0) +
                                          ($p->project_cost_target->cost_year_2025 ?? 0) +
                                          ($p->project_cost_target->cost_year_2026 ?? 0) +
                                          ($p->project_cost_target->cost_year_2027 ?? 0) +
                                          ($p->project_cost_target->cost_year_2028 ?? 0) +
                                          ($p->project_cost_target->cost_succeeding_years ?? 0);
            });
        }

        return view('components.manage', [
            'component_project' => $component_project, 
            'sub_project' => $sub_project, 
            'statuses' => $this->statuses, 
            'agencies' => $this->agencies, 
            'funding_categories' => $this->funding_categories, 
            'provinceId' => $provinceId, 
            'provinces' => $this->provinces,
            'selectedProvince' => $selectedProvince,
            'chapters' => $this->chapters, 
            'sectors' => $this->sectors, 
            'endorse_years' => $this->endorse_years, 
            'indicators' => $this->indicators,
            'project_location_types' => $this->project_location_types,
            'component_project_id' => $component_project_id, 
            'arrValueSelectedChapters' => $arrValueSelectedChapters, 
            'arrInterProvinces' => $arrInterProvinces, 
            'locationSpecific' => $locationSpecific,
            'fund_sources' => $this->fund_sources,
            ]);
    }

    public function updateSubProject(UpdateComponentRequest $request, $id)
    {
         $request->validated();

        $sub_project = Project::findOrFail($id);
        if ($sub_project->component_project) {
            $sub_project->component_project->update([
                'component_project_title' => $request->component_project_title,
            ]);
        }
        
        $this->updateProject($request, $id);

        toast('SubProject Data Updated Successfully!','success');

        return redirect()->route('v2.components.showSubProject', [
            'component_id' => $request->component_project_id,
            'id' => $id
        ]);

    }

    public function destroy($id)
    {

       $component = ComponentProject::findOrFail($id);
       $component->delete();

        toast('Component Project Deleted Successfully!','success');

        return redirect()->route('v2.components.index');

    }

    public function subProjectDestroy($component_id, $id)
    {

       $subProject = Project::findOrFail($id);
       $subProject->delete();

        toast('SubProject Data Deleted Successfully!','success');

        return redirect()->route('v2.components.create', ['component_id' => $component_id]);

    }

   
       
}
