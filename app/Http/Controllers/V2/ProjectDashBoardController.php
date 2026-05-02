<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectDashBoardController extends Controller
{
    /**public function __construct()
    {
        $this->middleware('can:project-view');
    }*/

    public function index_v2()
    {
        $projects = Project::with(['project_cost_target', 'agency', 'project_sector.sector', 'project_location.province', 'project_location.municipality', 'project_chapter'])->get();

        $stats = $this->calculateStats($projects);

        $agencies = \App\Models\Agency::orderBy('agency_acronym')->get();
        $sectors = \App\Models\Sector::orderBy('sector_name')->get();
        $provinces = \App\Models\Province::orderBy('province_name')->pluck('province_name', 'id')->all();

        return view('projectDashboard.index_v2', compact('projects', 'stats', 'agencies', 'sectors', 'provinces'));
    }

    public function fetchData(Request $request)
    {
        $query = Project::query()->with(['project_cost_target', 'agency', 'project_location.province', 'project_location.municipality', 'project_sector.sector', 'component_project']);

        if ($request->search) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('project_title', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%");
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->funding_category && $request->funding_category !== 'all') {
            $query->where('funding_category', $request->funding_category);
        }

        if ($request->agency && $request->agency !== 'all') {
            $a = $request->agency;
            $query->whereHas('agency', function($q) use ($a) {
                $q->where('agency_acronym', $a)->orWhere('agency_name', $a);
            });
        }

        if ($request->location && $request->location !== 'all') {
            $query->where('location', $request->location);
        }

        // Location Drilldown (IDs)
        if ($request->province_id && $request->province_id !== 'all' && $request->province_id !== '') {
            $query->whereHas('project_location', function($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }
        if ($request->district_id && $request->district_id !== 'all' && $request->district_id !== '') {
            $query->whereHas('project_location', function($q) use ($request) {
                $q->where('district_id', $request->district_id);
            });
        }
        if ($request->municipality_id && $request->municipality_id !== 'all' && $request->municipality_id !== '') {
            $query->whereHas('project_location', function($q) use ($request) {
                $q->where('municipality_id', $request->municipality_id);
            });
        }

        if ($request->province && $request->province !== 'All' && $request->province !== '' && !$request->province_id) {
            $p = $request->province;
            $query->whereHas('project_location.province', function($q) use ($p) {
                $q->where('province_name', 'like', "%$p%");
            });
        }

        if ($request->sector && $request->sector !== 'all') {
            $sec = $request->sector;
            $query->whereHas('project_sector.sector', function($q) use ($sec) {
                $q->where('sector_name', 'like', "%$sec%");
            });
        }

        // Calculate stats for the filtered dataset BEFORE pagination
        $filteredProjects = $query->get();
        $stats = $this->calculateStats($filteredProjects);

        $paginator = $query->latest()->paginate(25);

        $data = collect($paginator->items())->map(function($project) {
            $pCost = ($project->project_cost_target->cost_year_2023 ?? 0) +
                     ($project->project_cost_target->cost_year_2024 ?? 0) +
                     ($project->project_cost_target->cost_year_2025 ?? 0) +
                     ($project->project_cost_target->cost_year_2026 ?? 0) +
                     ($project->project_cost_target->cost_year_2027 ?? 0) +
                     ($project->project_cost_target->cost_year_2028 ?? 0) +
                     ($project->project_cost_target->cost_succeeding_years ?? 0);
            
            return [
                'title' => $project->project_title,
                'component_title' => $project->component_project->component_project_title ?? null,
                'description' => $project->description ?: 'No description provided.',
                'cost' => number_format($pCost, 2),
                'location' => $project->location,
                'agency' => $project->agency->agency_acronym ?? ($project->agency->agency_code ?? 'N/A'),
                'agency_full' => $project->agency->agency_name ?? 'N/A',
                'status' => $project->status,
                'status_lower' => strtolower($project->getRawOriginal('status') ?: $project->status),
                'funding_category' => $project->funding_category,
                'fund_source' => $project->fund_source
            ];
        });

        return response()->json([
            'projects' => $data,
            'stats' => $stats,
            'hasMore' => $paginator->hasMorePages(),
            'current_page' => $paginator->currentPage(),
            'total' => $paginator->total()
        ]);
    }

    private function calculateStats($projects)
    {
        $stats = [
            'total_count' => $projects->count(),
            'total_cost' => 0,
            'status' => [],
            'funding_category' => [],
            'fund_source' => [],
            'agency' => [],
            'sector' => [],
            'province' => [
                'Albay' => ['count' => 0, 'cost' => 0],
                'Cam Norte' => ['count' => 0, 'cost' => 0],
                'Cam Sur' => ['count' => 0, 'cost' => 0],
                'Catanduanes' => ['count' => 0, 'cost' => 0],
                'Masbate' => ['count' => 0, 'cost' => 0],
                'Sorsogon' => ['count' => 0, 'cost' => 0],
            ],
            'spatial' => [],
            'trends' => [
                '2022' => ['count' => 0, 'cost' => 0],
                '2023' => ['count' => 0, 'cost' => 0],
                '2024' => ['count' => 0, 'cost' => 0],
                '2025' => ['count' => 0, 'cost' => 0],
                '2026' => ['count' => 0, 'cost' => 0],
                '2027' => ['count' => 0, 'cost' => 0],
                '2028' => ['count' => 0, 'cost' => 0],
                '2029' => ['count' => 0, 'cost' => 0],
            ],
            'chapters' => [],
            'municipality' => []
        ];

        foreach ($projects as $project) {
            $costTarget = $project->project_cost_target;
            $projectCost = 0;
            if ($costTarget) {
                $projectCost = ($costTarget->cost_year_2023 ?? 0) +
                               ($costTarget->cost_year_2024 ?? 0) +
                               ($costTarget->cost_year_2025 ?? 0) +
                               ($costTarget->cost_year_2026 ?? 0) +
                               ($costTarget->cost_year_2027 ?? 0) +
                               ($costTarget->cost_year_2028 ?? 0) +
                               ($costTarget->cost_succeeding_years ?? 0);
                
                if (($costTarget->cost_year_2023 ?? 0) > 0) $stats['trends']['2023']['count']++;
                if (($costTarget->cost_year_2024 ?? 0) > 0) $stats['trends']['2024']['count']++;
                if (($costTarget->cost_year_2025 ?? 0) > 0) $stats['trends']['2025']['count']++;
                if (($costTarget->cost_year_2026 ?? 0) > 0) $stats['trends']['2026']['count']++;
                if (($costTarget->cost_year_2027 ?? 0) > 0) $stats['trends']['2027']['count']++;
                if (($costTarget->cost_year_2028 ?? 0) > 0) $stats['trends']['2028']['count']++;

                $stats['trends']['2023']['cost'] += ($costTarget->cost_year_2023 ?? 0);
                $stats['trends']['2024']['cost'] += ($costTarget->cost_year_2024 ?? 0);
                $stats['trends']['2025']['cost'] += ($costTarget->cost_year_2025 ?? 0);
                $stats['trends']['2026']['cost'] += ($costTarget->cost_year_2026 ?? 0);
                $stats['trends']['2027']['cost'] += ($costTarget->cost_year_2027 ?? 0);
                $stats['trends']['2028']['cost'] += ($costTarget->cost_year_2028 ?? 0);
            }
            $stats['total_cost'] += $projectCost;

            // Status
            $status = $project->status ?: 'Unknown';
            if (!isset($stats['status'][$status])) $stats['status'][$status] = ['count' => 0, 'cost' => 0];
            $stats['status'][$status]['count']++;
            $stats['status'][$status]['cost'] += $projectCost;

            // Funding Category
            $fc = $project->funding_category ?: 'Unknown';
            if (!isset($stats['funding_category'][$fc])) $stats['funding_category'][$fc] = ['count' => 0, 'cost' => 0];
            $stats['funding_category'][$fc]['count']++;
            $stats['funding_category'][$fc]['cost'] += $projectCost;

            // Fund Source
            $fs = $project->fund_source ?: 'Unknown';
            if (!isset($stats['fund_source'][$fs])) $stats['fund_source'][$fs] = ['count' => 0, 'cost' => 0];
            $stats['fund_source'][$fs]['count']++;
            $stats['fund_source'][$fs]['cost'] += $projectCost;

            // Agency
            $agency = ($project->agency && $project->agency->agency_acronym) ? $project->agency->agency_acronym : ($project->agency->agency_name ?? 'Unknown');
            if (!isset($stats['agency'][$agency])) $stats['agency'][$agency] = ['count' => 0, 'cost' => 0];
            $stats['agency'][$agency]['count']++;
            $stats['agency'][$agency]['cost'] += $projectCost;

            // Sector
            $sector = $project->project_sector->sector->sector_name ?? 'Unknown';
            if (!isset($stats['sector'][$sector])) $stats['sector'][$sector] = ['count' => 0, 'cost' => 0];
            $stats['sector'][$sector]['count']++;
            $stats['sector'][$sector]['cost'] += $projectCost;

            // Spatial
            $spatialRaw = $project->getRawOriginal('location') ?: 'Unknown';
            if (stripos($spatialRaw, 'Specific') !== false) {
                $spatial = 'Location Specific';
            } else {
                $spatial = ucwords(strtolower(str_replace(['_', '-'], ' ', $spatialRaw)));
            }
            
            if (!isset($stats['spatial'][$spatial])) $stats['spatial'][$spatial] = ['count' => 0, 'cost' => 0];
            $stats['spatial'][$spatial]['count']++;
            $stats['spatial'][$spatial]['cost'] += $projectCost;

            // Province and Municipality
            if ($project->project_location->count() > 0) {
                $locationCount = $project->project_location->count();
                $apportionedCost = $projectCost / $locationCount;
                foreach ($project->project_location as $loc) {
                    // Province
                    $pRaw = $loc->province->province_name ?? null;
                    if ($pRaw) {
                        $pName = str_replace('Camarines', 'Cam', $pRaw);
                        if (isset($stats['province'][$pName])) {
                            $stats['province'][$pName]['count']++;
                            $stats['province'][$pName]['cost'] += $apportionedCost;
                        }
                    }
                    // Municipality
                    $mRaw = isset($loc->municipality->municipality_name) ? $loc->municipality->municipality_name : null;
                    if ($mRaw) {
                        // Aggressive Normalization:
                        // 1. Uppercase and trim
                        $mName = strtoupper(trim($mRaw));
                        // 2. Remove "CITY OF " prefix if exists
                        $mName = str_replace('CITY OF ', '', $mName);
                        // 3. Remove " CITY" or " MUNICIPALITY" suffixes
                        $mName = str_replace([' CITY', ' MUNICIPALITY'], '', $mName);
                        
                        if (!isset($stats['municipality'][$mName])) {
                            $stats['municipality'][$mName] = ['count' => 0, 'cost' => 0];
                        }
                        $stats['municipality'][$mName]['count']++;
                        $stats['municipality'][$mName]['cost'] += $apportionedCost;
                    }
                }
            }

            // Chapters
            if ($project->project_chapter->count() > 0) {
                foreach ($project->project_chapter as $chap) {
                    $cName = "Chapter " . ($chap->chapter_id ?? 'Unknown');
                    if (!isset($stats['chapters'][$cName])) $stats['chapters'][$cName] = ['count' => 0, 'cost' => 0];
                    $stats['chapters'][$cName]['count']++;
                }
            }
        }

        return $stats;
    }
}
