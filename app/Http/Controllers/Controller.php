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
            'status' => $request->status,
            'funding_requirement' => str_replace(',', '', $request->funding_requirement),
            'funding_category' => $request->funding_category,
            'fund_source' => $request->fund_source,
            'other_fund_source' => $request->other_fund_source,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude,
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
        if($request->location == 'inter-province') {
            $interprovince = [];
            foreach($request->provinces as $province) {
                $interprovince['province_id'] = $province;

                $project->project_location()->create($interprovince);
            }
        }

        if($request->location == 'provincewide') {
             $project->project_location_specific()->create([
                'province_id' => $request->province,
            ]);

        }

        //Save Project Indicators Data
        if(!empty($request->indicators)) {
            foreach($request->indicators as $indicator_id) {
                $project->project_indicator()->create([
                    'indicator_id' => $indicator_id,
                ]);
            }
        } elseif ($request->indicator_id) {
            $project->project_indicator()->create([
                'indicator_id' => $request->indicator_id,
            ]);
        }

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

       $tc['target_year_2023'] = (empty($request->target_year_2023)) ? 0 : $request->target_year_2023;
        $tc['target_year_2024'] = (empty($request->target_year_2024)) ? 0 : $request->target_year_2024;
        $tc['target_year_2025'] = (empty($request->target_year_2025)) ? 0 : $request->target_year_2025;
        $tc['target_year_2026'] = (empty($request->target_year_2026)) ? 0 : $request->target_year_2026;
        $tc['target_year_2027'] = (empty($request->target_year_2027)) ? 0 : $request->target_year_2027;
        $tc['target_year_2028'] = (empty($request->target_year_2028)) ? 0 : $request->target_year_2028;
        $tc['target_succeeding_years'] = (empty($request->target_succeeding_years)) ? 0 : $request->target_succeeding_years;

        $tc['cost_year_2023'] = (empty($request->cost_year_2023)) ? 0 : $request->cost_year_2023;
        $tc['cost_year_2024'] = (empty($request->cost_year_2024)) ? 0 : $request->cost_year_2024;
        $tc['cost_year_2025'] = (empty($request->cost_year_2025)) ? 0 : $request->cost_year_2025;
        $tc['cost_year_2026'] = (empty($request->cost_year_2026)) ? 0 : $request->cost_year_2026;
        $tc['cost_year_2027'] = (empty($request->cost_year_2027)) ? 0 : $request->cost_year_2027;
        $tc['cost_year_2028'] = (empty($request->cost_year_2028)) ? 0 : $request->cost_year_2028;
        $tc['cost_succeeding_years'] = (empty($request->cost_succeeding_years)) ? 0 : $request->cost_succeeding_years;

        $project->project_cost_target()->create($tc);

        // Save rdp_chapters
        if(!empty($request->rdp_chapters)) {
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
        return $project;
    }

    public function updateProject($request, $id)
    {
        $project = $id instanceof Project ? $id : Project::find($id);

        $project->update([
            'project_title' => $request->project_title,
            'description' => $request->description,
             'agency_id' => $request->agency_id,
            'status' => $request->status,
            'funding_requirement' => str_replace(',', '', $request->funding_requirement),
            'funding_category' => $request->funding_category,
             'fund_source' => $request->fund_source,
            'other_fund_source' => $request->other_fund_source,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longtitude' => $request->longtitude,
            'remarks' => $request->remarks,
            //'user_id' => Auth::id(),
        ]);

            $project->project_location()->delete();

            if($request->location == 'locationspecific') {
                //$project->project_location_specific()->delete();
                $project->project_location_specific()->create([
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'municipality_id' => $request->municipality_id,
                ]);
            } 

            if($request->location == 'inter-province') {
                $interprovince = [];
            
                foreach($request->provinces as $province) 
                {
                    $interprovince['province_id'] = $province;

                    $project->project_location()->create($interprovince);
                }
            }

            if($request->location == 'provincewide') {
             $project->project_location_specific()->create([
                'province_id' => $request->province,
             ]);

            }
        

        //Save Project Indicators Data
        $project->project_indicator()->delete();
        if(!empty($request->indicators)) {
            foreach($request->indicators as $indicator_id) {
                $project->project_indicator()->create([
                    'indicator_id' => $indicator_id,
                ]);
            }
        }

        //Save Project Sector Data
        $project->project_sector()->updateOrCreate(
            ['project_id' => $project->id],
            [
                'sector_id' => $request->sector_id,
                'sub_sector_id' => $request->sub_sector_id,
            ]
        );

        //Save Project Endorsement Data
        $project->project_endorsement()->updateOrCreate(
            ['project_id' => $project->id],
            [
                'endorse_year_id' => $request->endorse_year_id,
                'rdc_endorsement_number' => $request->rdc_endorsement_number,
            ]
        );

        // Save project_cost_target data to the database

        $tc = [];
        $tc['target_year_2023'] = (empty($request->target_year_2023)) ? 0 : $request->target_year_2023;
        $tc['target_year_2024'] = (empty($request->target_year_2024)) ? 0 : $request->target_year_2024;
        $tc['target_year_2025'] = (empty($request->target_year_2025)) ? 0 : $request->target_year_2025;
        $tc['target_year_2026'] = (empty($request->target_year_2026)) ? 0 : $request->target_year_2026;
        $tc['target_year_2027'] = (empty($request->target_year_2027)) ? 0 : $request->target_year_2027;
        $tc['target_year_2028'] = (empty($request->target_year_2028)) ? 0 : $request->target_year_2028;
        $tc['target_succeeding_years'] = (empty($request->target_succeeding_years)) ? 0 : $request->target_succeeding_years;

        $tc['cost_year_2023'] = (empty($request->cost_year_2023)) ? 0 : $request->cost_year_2023;
        $tc['cost_year_2024'] = (empty($request->cost_year_2024)) ? 0 : $request->cost_year_2024;
        $tc['cost_year_2025'] = (empty($request->cost_year_2025)) ? 0 : $request->cost_year_2025;
        $tc['cost_year_2026'] = (empty($request->cost_year_2026)) ? 0 : $request->cost_year_2026;
        $tc['cost_year_2027'] = (empty($request->cost_year_2027)) ? 0 : $request->cost_year_2027;
        $tc['cost_year_2028'] = (empty($request->cost_year_2028)) ? 0 : $request->cost_year_2028;
        $tc['cost_succeeding_years'] = (empty($request->cost_succeeding_years)) ? 0 : $request->cost_succeeding_years;

        if(empty($project->project_cost_target)) {
           $project->project_cost_target()->create($tc);
        } else {
           $project->project_cost_target()->update($tc);
        }
        
        if(!empty($project->project_chapter())) 
        {
            $project->project_chapter()->delete();
        }

        //Save rdp_chapters
        if(!empty($request->rdp_chapters)) {
            $chapter = [];

            foreach($request->rdp_chapters as $rdc_chapter) {
            
                $chapter['chapter_id'] = $rdc_chapter;

                $project->project_chapter()->create($chapter);
            }
        }

        // Manage attachments: Delete removed ones, add new ones
        $inputDocuments = $request->input('document', []);
        
        if (count($project->getMedia('document')) > 0 || !empty($inputDocuments)) {
            // Delete media that is no longer in the request
            foreach ($project->getMedia('document') as $media) {
                if (!in_array($media->file_name, $inputDocuments)) {
                    $media->delete();
                }
            }

            // Add new media
            $existingMedia = $project->getMedia('document')->pluck('file_name')->toArray();

            foreach ($inputDocuments as $file) {
                if (!in_array($file, $existingMedia)) {
                    // Try to attach the new file, ignore if it fails (e.g., file already moved/deleted)
                    try {
                        $project->addMedia(storage_path('app/media/' . $file))->toMediaCollection('document');
                    } catch (\Exception $e) {
                        // Handle exception if needed
                    }
                }
            }
        }
    }
}
