<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Sector;
use App\Models\Status;
use App\Models\Chapter;
use App\Models\Project;
use App\Models\Province;
use App\Models\Indicator;
use App\Models\SubSector;
use App\Models\Attachment;
use App\Models\Endorsement;
use App\Models\EndorseYear;
use App\Models\MainProject;
use Illuminate\Http\Request;
use App\Models\ProjectSector;
use App\Models\ProjectChapter;
use App\Models\FundingCategory;
use App\Models\ProjectLocation;
use Illuminate\Validation\Rule;
use App\Models\ProjectIndicator;
use App\Models\ProjectCostTarget;
use App\Models\ProjectEndorsement;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        //$path = request()->path();
        $projects = Project::whereNull('component_project_id')->get();

        //dd($mainProjects);

        $title = 'Delete Project Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = Status::pluck('status_name', 'id')->all();
        $agencies = Agency::pluck('agency_acronym', 'id')->all();
        $funding_categories = FundingCategory::pluck('category_name', 'id')->all();
        $provinces = Province::pluck('province_name', 'id')->all();
        $chapters = Chapter::pluck('chapter_name', 'id')->all();
        $sectors = Sector::pluck('sector_name', 'id')->all();
        $endorse_years = EndorseYear::pluck('year', 'id')->all();
        $indicators = Indicator::pluck('indicator_name', 'id')->all();

        return view('projects.create', compact('statuses', 'agencies', 'funding_categories', 'provinces' , 'chapters', 'sectors', 'endorse_years', 'indicators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        
        $request->validated();

        $this->storeProject($request);


        toast('Project Data Stored Successfully!','success');
        
        return redirect()->route('projects.index');

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
        $project = Project::where('id', '=', $id)->first();
        $statuses = Status::pluck('status_name', 'id')->all();
        $agencies = Agency::pluck('agency_acronym', 'id')->all();
        $funding_categories = FundingCategory::pluck('category_name', 'id')->all();
        $chapters = Chapter::pluck('chapter_name', 'id')->all();
        $provinces = Province::pluck('province_name', 'id')->all();
        $sectors = Sector::pluck('sector_name', 'id')->all();
        $endorse_years = EndorseYear::pluck('year', 'id')->all();
        $indicators = Indicator::pluck('indicator_name', 'id')->all();
        $arrInterProvinces = [];
        $provinceId = '';

        $selectedChapters = ProjectChapter::where('project_id', '=', $id)->get();
        $selectedInterProvinces = ProjectLocation::where('project_id', $id)
                                                 ->whereNull('district_id')
                                                 ->whereNull('municipality_id')
                                                 ->get();
        $locationSpecific = ProjectLocation::where('project_id', $id)
                                           ->whereNotNull('district_id')
                                           ->whereNotNull('municipality_id')
                                           ->first();

        if($locationSpecific != null) {
            $provinceId = $locationSpecific->province_id;
        }

        if(count($selectedInterProvinces) > 0)
        {
            foreach($selectedInterProvinces as $selectedInterProvince) {
                $arrInterProvinces[] = $selectedInterProvince['province_id'];
            }  
        }

        foreach($selectedChapters as $selectedChapter) {
            $arrValueSelectedChapters[] = $selectedChapter['chapter_id'];
        }

        //dd($arrValueSelectedChapters);

        return view('projects.edit', compact('project', 'agencies', 'statuses', 'funding_categories', 'chapters', 'provinceId', 'provinces', 'sectors','endorse_years', 'indicators', 'arrValueSelectedChapters', 'arrInterProvinces', 'locationSpecific'));
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
        
        return redirect()->route('projects.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

       $project = Project::findOrFail($id);
       $project->delete();

        toast('Project data deleted successfully!', 'success');

        return redirect()->route('projects.index');
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
