<?php

namespace App\Http\Controllers\V2;

use App\FundSource;
use App\Http\Controllers\Controller;
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

class ProjectController extends Controller
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
        $this->middleware('can:project-view')->only('index');
        $this->middleware('can:project-create')->only(['create', 'store']);
        $this->middleware('can:project-edit')->only(['edit', 'update']);
        $this->middleware('can:project-delete')->only('destroy');
        
        // Allow storeMedia for both create and edit permissions
        $this->middleware(function ($request, $next) {
            if (auth()->user()->can('project-create') || auth()->user()->can('project-edit')) {
                return $next($request);
            }
            abort(403);
        })->only('storeMedia');

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
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Project::with('agency')
            ->whereNull('component_project_id');

        $user = Auth::user();
        if (!$user->hasRole(['administrator', 'admin', 'staff', 'chief', 'division_chief', 'division_head'])) {
            if ($user->hasRole(['agency', 'implementing_agency'])) {
                $query->where('agency_id', $user->agency_id);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('project_title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('agency', function ($aq) use ($search) {
                        $aq->where('agency_acronym', 'LIKE', "%{$search}%");
                    });
            });
        }

        $projects = $query->paginate($perPage)->onEachSide(1);

        $projects->appends(['per_page' => $perPage, 'search' => $search]);

        foreach ($projects as $key => $project) {
            $projects[$key]['funding_requirement'] = $project->project_cost_target?->cost_year_2023 + $project->project_cost_target?->cost_year_2024
                + $project->project_cost_target?->cost_year_2025 + $project->project_cost_target?->cost_year_2026
                + $project->project_cost_target?->cost_year_2027 + $project->project_cost_target?->cost_year_2028
                + $project->project_cost_target?->cost_succeeding_years;
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

        return view('projects.create_edit', [
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


        toast('Project Data Stored Successfully!', 'success');

        return redirect()->route('v2.projects.index');

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $project = Project::where('id', '=', $id)->first();

        $user = Auth::user();
        if (!$user->hasRole(['administrator', 'admin', 'staff', 'pmed_staff', 'chief', 'division_chief', 'pmed_chief', 'division_head'])) {
            if ($user->hasRole(['agency', 'implementing_agency'])) {
                if ($project->agency_id != $user->agency_id) abort(403);
            } else {
                if ($project->user_id !== $user->id) abort(403);
            }
        }

        return view('projects.create_edit', [
            'project' => $project,
            'agencies' => $this->agencies,
            'statuses' => $this->statuses,
            'funding_categories' => $this->funding_categories,
            'fund_sources' => $this->fund_sources,
            'chapters' => $this->chapters,
            'provinces' => $this->provinces,
            'sectors' => $this->sectors,
            'endorse_years' => $this->endorse_years,
            'indicators' => $this->indicators,
            'project_location_types' => $this->project_location_types,
            'viewMode' => true
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //$this->authorize('edit', $id);

        $project = Project::where('id', '=', $id)->first();

        $user = Auth::user();
        if (!$user->hasRole(['administrator', 'admin', 'staff', 'pmed_staff', 'chief', 'division_chief', 'pmed_chief', 'division_head'])) {
            if ($user->hasRole(['agency', 'implementing_agency'])) {
                if ($project->agency_id != $user->agency_id) abort(403);
            } else {
                if ($project->user_id !== $user->id) abort(403);
            }
        }

        $arrInterProvinces = [];
        $arrValueSelectedChapters = [];
        $provinceId = '';
        $selectedProvince = '';
        $locationSpecific = '';

        $selectedChapters = ProjectChapter::where('project_id', '=', $id)->get();

        if ($project->location == 'inter-province') {
            $selectedInterProvinces = ProjectLocation::where('project_id', $id)
                ->whereNull('district_id')
                ->whereNull('municipality_id')
                ->get();

            if (count($selectedInterProvinces) > 0) {
                foreach ($selectedInterProvinces as $selectedInterProvince) {
                    $arrInterProvinces[] = $selectedInterProvince['province_id'];
                }
            }
        }

        if ($project->location == 'provincewide') {

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

        if (!is_null($locationSpecific)) {
            $provinceId = $locationSpecific->province_id;
        }

        if (count($selectedChapters) > 0) {
            foreach ($selectedChapters as $selectedChapter) {
                $arrValueSelectedChapters[] = $selectedChapter['chapter_id'];
            }

        }

        return view('projects.create_edit', [
            'project' => $project,
            'agencies' => $this->agencies,
            'statuses' => $this->statuses,
            'funding_categories' => $this->funding_categories,
            'fund_sources' => $this->fund_sources,
            'chapters' => $this->chapters,
            'provinces' => $this->provinces,
            'sectors' => $this->sectors,
            'endorse_years' => $this->endorse_years,
            'indicators' => $this->indicators,
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

        toast('Project Data Updated Successfully!', 'success');

        //return redirect()->route('projects.index');
        return redirect()->route('v2.projects.show', $id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $project = Project::findOrFail($id);

        $user = Auth::user();
        if (!$user->hasRole(['administrator', 'admin', 'staff', 'pmed_staff', 'chief', 'division_chief', 'pmed_chief', 'division_head'])) {
            if ($user->hasRole(['agency', 'implementing_agency'])) {
                if ($project->agency_id != $user->agency_id) abort(403);
            } else {
                if ($project->user_id !== $user->id) abort(403);
            }
        }

        $project->delete();

        toast('Project data deleted successfully!', 'success');

        return redirect()->route('projects.index');
    }

    public function storeMedia(Request $request)
    {

        $path = storage_path('app/media');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        //$name = time().'.'.$file->getClientOriginalExtension();

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
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
