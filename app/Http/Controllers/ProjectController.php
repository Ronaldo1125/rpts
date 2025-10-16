<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Chapter;
use App\Models\Project;
use App\Models\Province;
use App\Models\Attachment;
use App\Models\Endorsement;
use Illuminate\Http\Request;
use App\Models\ChapterProject;
use App\Models\ProjectLocation;
use App\Models\ProjectCostTarget;
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
        $projects = Project::all();

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
        $endorsements = Endorsement::pluck('rdc_resolution', 'id')->all();
        $provinces = Province::pluck('province_name', 'id')->all();
        $chapters = Chapter::pluck('chapter_name', 'id')->all();

        return view('projects.create', compact('statuses', 'endorsements', 'provinces' , 'chapters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        
        $request->validated();

        //dd($request->all());

        // Save Project Data
        $project = Project::create([
            'project_title' => $request->project_title,
            'description' => $request->description,
            'status_id' => $request->status_id,
            'endorsement_id' => $request->endorsement_id,
            'funding_requirement' => $request->funding_requirement,
            'remarks' => $request->remarks,
            'user_id' => Auth::id(),
        ]);

        //Save project_location data to the database
        ProjectLocation::create([
            'project_id' => $project->id,
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'municipality_id' => $request->municipality_id,
        ]);

        // Save project_cost_target data to the database
        $tc = [];

        $tc['project_id'] = $project->id;
        $tc['target_year_2023'] = (is_null($request->target_year_2023)) ? 0 : $request->target_year_2023;
        $tc['target_year_2024'] = (is_null($request->target_year_2024)) ? 0 : $request->target_year_2024;
        $tc['target_year_2025'] = (is_null($request->target_year_2025)) ? 0 : $request->target_year_2025;
        $tc['target_year_2026'] = (is_null($request->target_year_2026)) ? 0 : $request->target_year_2026;
        $tc['target_year_2027'] = (is_null($request->target_year_2027)) ? 0 : $request->target_year_2027;
        $tc['target_year_2028'] = (is_null($request->target_year_2028)) ? 0 : $request->target_year_2028;
        $tc['target_succeeding_years'] = (is_null($request->target_succeeding_years)) ? 0 : $request->target_succeeding_years;

        $tc['cost_year_2023'] = (is_null($request->cost_year_2023)) ? 0 : $request->cost_year_2023;
        $tc['cost_year_2024'] = (is_null($request->cost_year_2024)) ? 0 : $request->cost_year_2024;
        $tc['cost_year_2025'] = (is_null($request->cost_year_2025)) ? 0 : $request->cost_year_2025;
        $tc['cost_year_2026'] = (is_null($request->cost_year_2026)) ? 0 : $request->cost_year_2026;
        $tc['cost_year_2027'] = (is_null($request->cost_year_2027)) ? 0 : $request->cost_year_2027;
        $tc['cost_year_2028'] = (is_null($request->cost_year_2028)) ? 0 : $request->cost_year_2028;
        $tc['cost_succeeding_years'] = (is_null($request->cost_succeeding_years)) ? 0 : $request->cost_succeeding_years;

        ProjectCostTarget::create($tc);

        // Save rdp_chapters
        if($request->rdp_chapters) {
            $chapter = [];
            foreach($request->rdp_chapters as $rdc_chapter) {
            
                $chapter['project_id'] = $project->id;
                $chapter['chapter_id'] = $rdc_chapter;

                ChapterProject::create($chapter);
            }
        }

        // Save Image on the Attachments Table
        if($request->document) {
            foreach ($request->input('document', []) as $file) {
                $project->addMedia(storage_path('app/media/' . $file))->toMediaCollection('document');
            }
        }
        

        // if($request->file('attachments')) {
        //     $response = [];
        //     $file = [];
        //     foreach($request->file('attachments') as $attachment) {
        //         $filename = time().'.'.$attachment->getClientOriginalExtension();
        //         $response[] = $attachment->storeAs('attachments', $filename);

        //         $file['project_id'] = $project->id;
        //         $file['attachment_filename'] = $filename;

        //         Attachment::create($file);
        //     }
        // }

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
        $endorsements = Endorsement::pluck('rdc_resolution', 'id')->all();
        $chapters = Chapter::pluck('chapter_name', 'id')->all();
        $provinces = Province::pluck('province_name', 'id')->all();
        $selectedChapters = ChapterProject::where('project_id', '=', $id)->get();

        foreach($selectedChapters as $selectedChapter) {
            $arrValueSelectedChapters[] = $selectedChapter['chapter_id'];
        }

        //dd($arrValueSelectedChapters);

        return view('projects.edit', compact('project', 'statuses', 'endorsements', 'chapters', 'provinces', 'arrValueSelectedChapters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //$request->validated();
        $project = Project::find($id);

        $project->update([
                    'project_title' => $request->project_title,
                    'description' => $request->description,
                    'status_id' => $request->status_id,
                    'endorsement_id' => $request->endorsement_id,
                    'funding_requirement' => $request->funding_requirement,
                    'remarks' => $request->remarks,
                    //'user_id' => Auth::id(),
                ]);

        //update the project_location data to the database
        ProjectLocation::where('project_id', $id)->update([
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'municipality_id' => $request->municipality_id,
        ]);

        // Save project_cost_target data to the database
        $tc = [];

        $tc['target_year_2023'] = (is_null($request->target_year_2023)) ? 0 : $request->target_year_2023;
        $tc['target_year_2024'] = (is_null($request->target_year_2024)) ? 0 : $request->target_year_2024;
        $tc['target_year_2025'] = (is_null($request->target_year_2025)) ? 0 : $request->target_year_2025;
        $tc['target_year_2026'] = (is_null($request->target_year_2026)) ? 0 : $request->target_year_2026;
        $tc['target_year_2027'] = (is_null($request->target_year_2027)) ? 0 : $request->target_year_2027;
        $tc['target_year_2028'] = (is_null($request->target_year_2028)) ? 0 : $request->target_year_2028;
        $tc['target_succeeding_years'] = (is_null($request->target_succeeding_years)) ? 0 : $request->target_succeeding_years;

        $tc['cost_year_2023'] = (is_null($request->cost_year_2023)) ? 0 : $request->cost_year_2023;
        $tc['cost_year_2024'] = (is_null($request->cost_year_2024)) ? 0 : $request->cost_year_2024;
        $tc['cost_year_2025'] = (is_null($request->cost_year_2025)) ? 0 : $request->cost_year_2025;
        $tc['cost_year_2026'] = (is_null($request->cost_year_2026)) ? 0 : $request->cost_year_2026;
        $tc['cost_year_2027'] = (is_null($request->cost_year_2027)) ? 0 : $request->cost_year_2027;
        $tc['cost_year_2028'] = (is_null($request->cost_year_2028)) ? 0 : $request->cost_year_2028;
        $tc['cost_succeeding_years'] = (is_null($request->cost_succeeding_years)) ? 0 : $request->cost_succeeding_years;

        ProjectCostTarget::where('project_id', $id)->update($tc);

        //Save rdp_chapters
        if($request->rdp_chapters) {
            $chapter = [];
            $rdcChapter = ChapterProject::where('project_id', $id);
            $rdcChapter->delete();

            foreach($request->rdp_chapters as $rdc_chapter) {
            
                $chapter['project_id'] = $id;
                $chapter['chapter_id'] = $rdc_chapter;

                ChapterProject::create($chapter);
            }
        }

        if($request->document) {     
            if (count($project->getMedia('document')) > 0) {
                // With error on this
                foreach ($project->getMedia('document') as $media) {
                    if (!in_array($media->file_name, $request->input('document', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $project->getMedia('document')->pluck('file_name')->toArray();

            foreach ($request->input('document', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $project->addMedia(storage_path('app/media/' . $file))->toMediaCollection('document');
                }
            }
        }

        toast('Project Data Updated Successfully!','success');
        
        return redirect()->route('projects.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

       $project = Project::find($id);
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
}
