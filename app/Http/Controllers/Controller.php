<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function storeProject($request, $component_project_id = null)
    {
        $project = Project::create([
            'project_title' => $request->project_title,
            'description' => $request->description,
            'component_project_id' => $component_project_id,
            'agency_id' => $request->agency_id,
            'status_id' => $request->status_id,
            'funding_category_id' => $request->funding_category_id,
            'funding_requirement' => $request->funding_requirement,
            'location' => $request->location,
            'remarks' => $request->remarks,
            'user_id' => Auth::id(),
        ]);

        //Project Location selected locationspecific
        if($request->location == 'locationspecific') {
            $project->project_location_specific()->create([
            'province_id' => $request->province_id,
            'district_id' => $request->district_id,
            'municipality_id' => $request->municipality_id,
            ]);
        }

        //Project Location selected interprovince
        if($request->location == 'interprovince') {
            $interprovince = [];
            foreach($request->provinces as $province) {
                $interprovince['province_id'] = $province;

                $project->project_location()->create($interprovince);
            }
        }

        //Save Project Indicators Data
        $project->project_indicator()->create([
            'indicator_id' => $request->indicator_id,
            'indicator_quantity' => $request->indicator_quantity,
        ]);

        //Save Project Sector Data
        $project->project_sector()->create([
            'sector_id' => $request->sector_id,
            'sub_sector_id' => $request->sub_sector_id,
        ]);

        //Save Project Endorsement Data
        $project->project_endorsement()->create([
            'endorse_year_id' => $request->endorse_year_id,
            'rdc_endorsement_number' => $request->rdc_endorsement_number,
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

        $project->project_cost_target()->create($tc);

        // Save rdp_chapters
        if($request->rdp_chapters) {
            $chapter = [];
            foreach($request->rdp_chapters as $rdc_chapter) {
            
                $chapter['chapter_id'] = $rdc_chapter;

                $project->project_chapter()->create($chapter);
            }
        }

        // Save Image on the Attachments Table
        if($request->document) {
            foreach ($request->input('document', []) as $file) {
                $project->addMedia(storage_path('app/media/' . $file))->toMediaCollection('document');
            }
        }
    }

    public function updateProject($request, $id)
    {
        $project = Project::find($id);

        $project->update([
            'project_title' => $request->project_title,
            'description' => $request->description,
             'agency_id' => $request->agency_id,
            'status_id' => $request->status_id,
            'funding_category_id' => $request->funding_category_id,
            'funding_requirement' => $request->funding_requirement,
            'location' => $request->location,
            'remarks' => $request->remarks,
            //'user_id' => Auth::id(),
        ]);

        if($request->location !== 'regionwide') {
            $project->project_location()->delete();

            if($request->location == 'locationspecific') {
                $project->project_location()->create([
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'municipality_id' => $request->municipality_id,
                ]);
            } else {
                $interprovince = [];
            
                foreach($request->provinces as $province) 
                {
                    $interprovince['province_id'] = $province;

                    $project->project_location()->create($interprovince);
                }
            }
        }

        //Save Project Indicators Data
        $project->project_indicator()->update([
            'indicator_id' => $request->indicator_id,
            'indicator_quantity' => $request->indicator_quantity,
        ]);

        //Save Project Sector Data
        $project->project_sector()->update([
            'sector_id' => $request->sector_id,
            'sub_sector_id' => $request->sub_sector_id,
        ]);

        //Save Project Endorsement Data
        $project->project_endorsement()->update([
            'endorse_year_id' => $request->endorse_year_id,
            'rdc_endorsement_number' => $request->rdc_endorsement_number,
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

        $project->project_cost_target()->update($tc);

        //Save rdp_chapters
        if($request->rdp_chapters) {
            $chapter = [];
            $project->project_chapter()->delete();

            foreach($request->rdp_chapters as $rdc_chapter) {
            
                $chapter['chapter_id'] = $rdc_chapter;

                $project->project_chapter()->create($chapter);
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
    }
}
