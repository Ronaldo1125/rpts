<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Sector;
use App\Models\Status;
use App\Models\Chapter;
use App\Models\Project;
use App\Models\Province;
use App\Models\Indicator;
use App\Models\EndorseYear;
use Illuminate\Http\Request;
use App\Models\ProjectSector;
use App\Models\ProjectChapter;
use App\Models\FundingCategory;
use App\Models\ProjectLocation;
use App\Models\ComponentProject;
use App\Models\ProjectIndicator;
use App\Models\ProjectCostTarget;
use App\Models\ProjectEndorsement;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreComponentRequest;
use App\Http\Requests\UpdateComponentRequest;

class ComponentController extends Controller
{
    public function index()
    {
        $projects = Project::whereNotNull('component_project_id')
                        ->orderBy('component_project_id', 'asc')
                        ->get();

       // dd($projects);

        $title = 'Delete Component Project Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('components.index', compact('projects'));
    }

    public function create($component_id)
    {
        $component_project_id = $component_id;
        $component_projects = '';
        if($component_project_id != 0) 
        {
            $component_projects = ComponentProject::where('id', $component_project_id)->get();

            if($component_projects->isEmpty())
            {
                return redirect()->route('components.create', ['component_id' => 0 ]);
            }
        }

        $statuses = Status::pluck('status_name', 'id')->all();
        $agencies = Agency::pluck('agency_acronym', 'id')->all();
        $funding_categories = FundingCategory::pluck('category_name', 'id')->all();
        $provinces = Province::pluck('province_name', 'id')->all();
        $chapters = Chapter::pluck('chapter_name', 'id')->all();
        $sectors = Sector::pluck('sector_name', 'id')->all();
        $endorse_years = EndorseYear::pluck('year', 'id')->all();
        $indicators = Indicator::pluck('indicator_name', 'id')->all();

        //dd($component_projects);
        $title = 'Delete SubProject Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);

        return view('components.create', compact('component_projects', 'statuses', 'agencies', 'funding_categories', 'provinces', 'chapters', 'sectors', 'endorse_years', 'indicators', 'component_project_id'));
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
        }


        $this->storeProject($request, $component_project_id);

        
        toast('Component Project was stored successfully!','success');
        
        return redirect()->route('components.create', ['component_id' => $component_project_id ]);
        

    }

    public function edit($id)
    {

    }

    public function editSubProject($component_id, $id)
    {
        $component_project_id = $component_id;
        $component_projects = '';
        if($component_project_id != 0) 
        {
            $component_projects = ComponentProject::where('id', $component_project_id)->get();

            if($component_projects->isEmpty())
            {
                return redirect()->route('components.create', ['component_id' => 0 ]);
            }
        }

      
        $sub_project = Project::findOrFail($id);

        $statuses = Status::pluck('status_name', 'id')->all();
        $agencies = Agency::pluck('agency_acronym', 'id')->all();
        $funding_categories = FundingCategory::pluck('category_name', 'id')->all();
        $provinces = Province::pluck('province_name', 'id')->all();
        $chapters = Chapter::pluck('chapter_name', 'id')->all();
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

        $title = 'Delete SubProject Record!';
        $text = "Are you sure? This will be deleted permanently.";
        confirmDelete($title, $text);



        return view('components.editSubProject', compact('component_projects', 'sub_project', 'statuses', 'agencies', 'funding_categories', 'provinceId', 'provinces', 'chapters', 'sectors', 'endorse_years', 'indicators', 'component_project_id', 'arrValueSelectedChapters', 'arrInterProvinces', 'locationSpecific'));
    }

    public function updateSubProject(UpdateComponentRequest $request, $id)
    {
         $request->validated();

        //dd($request);
        
        $this->updateProject($request, $id);

        toast('SubProject Data Updated Successfully!','success');

        return redirect()->route('components.index');

    }

    public function destroy($id)
    {

       $component = ComponentProject::findOrFail($id);
       $component->delete();

        toast('Component Project Deleted Successfully!','success');

        return redirect()->route('components.index');

    }

    public function subProjectDestroy($component_id, $id)
    {

       $subProject = Project::findOrFail($id);
       $subProject->delete();

        toast('SubProject Data Deleted Successfully!','success');

        return redirect()->route('components.create', ['component_id' => $component_id]);

    }

   
       
}
