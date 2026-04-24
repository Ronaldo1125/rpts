<?php

namespace App\Http\Controllers;

use App\FundSource;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Agency;
use App\Models\Chapter;
use App\Models\EndorseYear;
use App\Models\Indicator;
use App\Models\Project;
use App\Models\ProjectChapter;
use App\Models\ProjectLocation;
use App\Models\Province;
use App\Models\Sector;
use App\Models\SubSector;
use App\ProjectFundingCategory;
use App\ProjectLocationType;
use App\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Facades\DataTables;

class ProjectController_v2 extends Controller
{
    public $statuses;
    public $indicators;
    public $agencies;
    public $sectors;
    public $funding_categories;
    public $endorse_years;
    public $chapters;
    public $project_location_types;
    public $provinces;
    public $fund_sources;

    public function __construct()
    {
        //$this->authorizeResource(Project::class);
        $this->statuses = ProjectStatus::cases();
        $this->indicators = Indicator::orderBy('indicator_name')->pluck('indicator_name', 'id')->all();
        $this->agencies = Agency::pluck('agency_acronym', 'id')->all();
        $this->sectors = Sector::pluck('sector_name', 'id')->all();
        $this->funding_categories = ProjectFundingCategory::cases();
        $this->endorse_years = EndorseYear::pluck('year', 'id')->all();
        $this->chapters = Chapter::pluck('chapter_name', 'id')->all();
        $this->project_location_types = ProjectLocationType::cases();
        $this->provinces = Province::pluck('province_name', 'id')->all();
        $this->fund_sources = FundSource::cases();
       
       
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Server-Side Datatables Implementation
        // if ($request->ajax()) {
        //     $projects = Project::query();
        //     if(!Auth::user()->hasRole('administrator')) {
        //         $projects->where('user_id', auth()->id());
        //     }
        //     $projects->whereNull('component_project_id');

        //     return DataTables::eloquent($projects)
            
        //     ->addColumn('created_at', function($projects){
        //         return Carbon::parse($projects->created_at)->format('Y-m-d H:i:s');
        //     })
            
        //     ->addColumn('action', function($projects){
        //         return '<div class="dropdown">
        //                     <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
        //                       <i class="icon-base bx bx-dots-vertical-rounded"></i>
        //                     </button>
        //                     <div class="dropdown-menu"><a class="dropdown-item" href="' . route('projects.edit', $projects->id) . '"
        //                         ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a>
        //                         <a class="dropdown-item" href="'. route('projects.destroy', $projects->id) . '" data-confirm-delete="true"
        //                         ><i class="icon-base bx bx-trash me-1"></i> Delete</a
        //                       >
        //                       </div>
        //                   </div>';
        //     })
        //     ->toJson();
        // }

        // return view('projects.index');

        // Client-Side Datatables Implementation
        
        $perPage = $request->input('per_page', 10);
        $projects = (Auth::user()->hasRole('administrator')) 
                    ? Project::with('agency')->whereNull('component_project_id')->paginate($perPage)->onEachSide(1)
                    : Project::with('agency')->where('user_id', auth()->id())->whereNull('component_project_id')->paginate($perPage)->onEachSide(1);

        $projects->appends(['per_page' => $perPage]);

       
        foreach($projects as $key => $project) {
            $projects[$key]['funding_requirement'] = $project->project_cost_target?->cost_year_2023 + $project->project_cost_target?->cost_year_2024 
                                                   + $project->project_cost_target?->cost_year_2025 + $project->project_cost_target?->cost_year_2026 
                                                   + $project->project_cost_target?->cost_year_2027 + $project->project_cost_target?->cost_year_2028 
                                                   +  $project->project_cost_target?->cost_succeeding_years;
        }

        $title = 'Delete Project Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('projects.index_v2', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$statuses = ProjectStatus::cases();
        //$project_location_types = ProjectLocationType::cases();
        
        
       //$provinces = Province::pluck('province_name', 'id')->all();
        
        
        //$endorse_years = EndorseYear::pluck('year', 'id')->all();
        //$indicators = Indicator::pluck('indicator_name', 'id')->all();

        return view('projects.create', [
            'statuses' => $this->statuses, 
            'agencies' => $this->agencies, 
            'funding_categories' => $this->funding_categories, 
            'provinces' => $this->provinces, 
            'chapters' => $this->chapters, 
            'sectors' => $this->sectors, 
            'endorse_years' => $this->endorse_years, 
            'indicators' => $this->indicators, 
            'project_location_types' => $this->project_location_types,
            'fund_sources' => $this->fund_sources
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        
        $request->validated();

        $this->storeProject($request);

        //activity()->log('Look mum, I logged something');


        toast('Project Data Stored Successfully!','success');
        
        return redirect()->route('projects.index_v2');

    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //$this->authorize('edit', $id);

         $project = Project::where('id', '=', $id)->first();

        if(!Auth::user()->hasRole('administrator')) {
            if($project->user->isNot(Auth::user())) 
            {
                abort(403);
            }
        }
       
        $arrInterProvinces = [];
        $arrValueSelectedChapters = [];
        $provinceId = '';
        $selectedProvince = '';
        $locationSpecific = '';

        $selectedChapters = ProjectChapter::where('project_id', '=', $id)->get();

        if($project->location == 'inter-province') {
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

        if($project->location == 'provincewide') {

             $provinceWide = ProjectLocation::where('project_id', $id)
                                            ->whereNull('district_id')
                                            ->whereNull('municipality_id')
                                            ->first();
            
            $selectedProvince = $provinceWide->province_id;

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
        
        return view('projects.edit', [
                                        'project' => $project, 
                                        'agencies' => $this->agencies, 
                                        'statuses' => $this->statuses, 
                                        'funding_categories' => $this->funding_categories, 
                                        'fund_sources' => $this->fund_sources,
                                        'chapters' => $this->chapters, 
                                        'provinceId' => $provinceId, 
                                        'provinces' => $this->provinces,
                                        'selectedProvince' => $selectedProvince, 
                                        'sectors' => $this->sectors,
                                        'endorse_years' => $this->endorse_years, 
                                        'indicators' => $this->indicators, 
                                        'arrValueSelectedChapters' => $arrValueSelectedChapters, 
                                        'arrInterProvinces' => $arrInterProvinces, 
                                        'locationSpecific' => $locationSpecific, 
                                        'project_location_types' => $this->project_location_types,
                                        
                                        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        
        $request->validated();

        //dd($request);
        
        $this->updateProject($request, $id);

        toast('Project Data Updated Successfully!','success');
        
        //return redirect()->route('projects.index');
        return redirect()->route('projects.edit', ['id' => $id ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

       $project = Project::findOrFail($id);

       if(!Auth::user()->hasRole('administrator')) {
            if($project->user->isNot(Auth::user())) 
            {
                abort(403);
            }
        } 
        
        $project->delete();

        toast('Project data deleted successfully!', 'success');

        return redirect()->route('projects.index_v2');
    }

    public function storeMedia(Request $request){

       $path = storage_path('app/media');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        //$name = time().'.'.$file->getClientOriginalExtension();

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function getSubSectors(Request $request)
    {
        $sector_id = $request->sector_id;

        $sub_sectors = SubSector::where('sector_id', $sector_id)->get();

        return response()->json($sub_sectors);
    }
}
