<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\CppSubmission;
use App\Models\ProjectAssessmentReport;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();

    if ($user->hasRole('administrator')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('staff')) {
        if ($user->division && strtoupper($user->division->name) === 'PDIPBD') {
            return redirect()->route('staff.dashboard');
        }
        return redirect()->route('staff.dashboard');
    }

    if ($user->hasRole('implementing_agency') || $user->hasRole('agency')) {
        return redirect()->route('agency.dashboard');
    }

    if ($user->hasRole('division_chief') || $user->hasRole('chief')) {
        return redirect()->route('chief.dashboard');
    }

    if ($user->hasRole('pdipbd_staff')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect('/');
    }

    public function admin()
    {
        $dashboardPdipbStaff = User::with('division')
            ->whereHas('roles', function ($query) {
                $query->where('name', 'staff');
            })
            ->whereHas('division', function ($query) {
                $query->whereRaw('UPPER(name) = ?', ['PDIPBD']);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'staff',
                    'division' => optional($user->division)->name,
                ];
            });

        $totalMasterProjects = Project::count();
        $totalMasterInvestmentRaw = Project::sum('funding_requirement');
        
        $totalMasterInvestment = '₱0.00';
        if ($totalMasterInvestmentRaw >= 1000) {
            // >= 1000 Millions = Billions
            $totalMasterInvestment = '₱' . number_format($totalMasterInvestmentRaw / 1000, 2) . 'B';
        } elseif ($totalMasterInvestmentRaw > 0) {
            // < 1000 Millions = Millions
            $totalMasterInvestment = '₱' . number_format($totalMasterInvestmentRaw, 2) . 'M';
        }

        $ongoingMasterProjects = Project::where('status', 'ongoing')->count();
        $completedMasterProjects = Project::where('status', 'completed')->count();

        $dashboardSubmissions = CppSubmission::with(['user.agency', 'sector', 'sub_sector', 'referrals'])
            ->latest()
            ->get()
            ->map(function ($submission) {
                $latestReferral = $submission->referrals->last();
                return [
                    'id' => $submission->id,
                    'title' => $submission->project_title,
                    'agency' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                    'sector' => optional($submission->sector)->sector_name ?? '—',
                    'sub_sector' => optional($submission->sub_sector)->sub_sector_name ?? '—',
                    'status' => $submission->status,
                    'stage' => $submission->stage,
                    'total_cost' => $submission->total_cost ?? 0,
                    'submittedAt' => optional($submission->submitted_at)->toIso8601String(),
                    'referredToPdipb' => $submission->referrals->where('resolved_at', null)
                        ->whereIn('status', ['For Revision Review', 'For Validation', 'For Appraisal'])
                        ->isNotEmpty(),
                    'assigned_to_user_id' => optional($latestReferral)->to_user_id,
                    'referrals' => $submission->referrals->map(fn($r) => [
                        'id'          => $r->id,
                        'status'      => $r->status,
                        'stage'       => $r->stage,
                        'notes'       => $r->notes,
                        'resolved_at' => optional($r->resolved_at)->toIso8601String(),
                        'to_user_id'  => $r->to_user_id,
                        'to_user_name' => optional($r->toUser)->name,
                    ])->values(),
                ];
            });

        $dashboardInitialCount = $dashboardSubmissions->filter(function ($submission) {
            $status = strtolower(trim($submission['status'] ?? ''));
            $stage = strtolower(trim($submission['stage'] ?? ''));
            $referredToPdipb = (bool) ($submission['referredToPdipb'] ?? false);

            if ($status === 'submitted' && !$referredToPdipb) {
                return true;
            }

            if ($status === 'resubmitted' && $stage === 'completeness test and validation' && !$referredToPdipb) {
                return true;
            }

            return false;
        })->count();

        $dashboardReferredCount = $dashboardSubmissions->filter(function ($submission) {
            $status = strtolower(trim($submission['status'] ?? ''));
            $stage = strtolower(trim($submission['stage'] ?? ''));

            if ($status !== 'validated') return false;

            $hasActiveDivRef = false;
            $hasRejectedDivRef = false;

            foreach ($submission['referrals'] ?? [] as $r) {
                $rStage = strtolower(trim($r['stage'] ?? ''));
                $rStatus = strtolower(trim($r['status'] ?? ''));
                
                if ($rStage === 'project appraisal' || $rStatus === 'referred to division' || $rStatus === 'referred') {
                    if ($rStatus === 'rejected') {
                        $hasRejectedDivRef = true;
                    } else {
                        $hasActiveDivRef = true;
                    }
                }
            }
            
            if ($hasActiveDivRef) return false;
            if ($hasRejectedDivRef) return true;
            if ($stage === 'project appraisal') return false;

            return true;
        })->count();

        $mapParForDashboard = function ($par) {
            $submission = $par->submission;
            $latestReferral = $par->referrals->sortByDesc('created_at')->first();

            $division = optional(optional($par->assessor)->division)->name
                        ?? optional(optional($latestReferral)->toDivision)->name
                        ?? '—';

            $isReferredToPdipbd = $par->referrals->contains(function ($ref) {
                $divName = optional($ref->toDivision)->name ?? '';
                return in_array(strtoupper(trim($divName)), ['PDIPBD', 'PDIPB']) && $ref->status !== 'Rejected';
            }) || $par->status === 'Referred to PDIPBD' || $par->status === 'Reviewed';

            return [
                'id' => $par->id,
                'parId' => $par->id,
                'title' => optional($submission)->project_title ?? '—',
                'projectTitle' => optional($submission)->project_title ?? '—',
                'agency' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                'proponent' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                'status' => $par->status,
                'connectedCteId' => optional($submission)->id,
                'submissionId' => optional($submission)->id,
                'referredToDivision' => optional($latestReferral)->to_division_id ? 'Yes' : 'No',
                'division' => $division,
                'evaluatingDivision' => '—',
                'isReferredToPdipbd' => $isReferredToPdipbd,
                    'referrals' => $par->referrals->map(fn($r) => [
                        'id'          => $r->id,
                        'status'      => $r->status,
                        'stage'       => $r->stage,
                        'notes'       => $r->notes,
                        'resolved_at' => optional($r->resolved_at)->toIso8601String(),
                        'to_user_id'  => $r->to_user_id,
                        'to_user_name' => optional($r->toUser)->name,
                    ])->values(),
            ];
        };

        // Fetch PAR rows for both the evaluated and finalization tabs
        $dashboardEvaluatedPars = ProjectAssessmentReport::with(['submission.user.agency', 'referrals.toDivision', 'assessor.division'])
            ->where('status', 'Evaluated')
            ->latest()
            ->get()
            ->map($mapParForDashboard);

        $dashboardReviewedPars = ProjectAssessmentReport::with(['submission.user.agency', 'referrals.toDivision', 'assessor.division'])
            ->where('status', 'Reviewed')
            ->latest()
            ->get()
            ->map($mapParForDashboard);

        $dashboardEvaluationCount = $dashboardEvaluatedPars->count() + $dashboardReviewedPars->count();

        $seccomAnalytics = $this->getSecComAnalytics();
        $divisionWorkload = $this->getDivisionAssessmentWorkload();

        return view('home.dashboards', compact(
            'dashboardSubmissions',
            'dashboardPdipbStaff',
            'dashboardEvaluatedPars',
            'dashboardReviewedPars',
            'dashboardInitialCount',
            'dashboardReferredCount',
            'dashboardEvaluationCount',
            'seccomAnalytics',
            'divisionWorkload',
            'totalMasterProjects',
            'totalMasterInvestment',
            'ongoingMasterProjects',
            'completedMasterProjects'
        ));
    }

    public function agency()
    {
        return view('home.agency-dashboard');
    }

    public function staff()
    {
        $user = auth()->user();
        $dashboardPdipbStaff = collect();
        $dashboardSubmissions = collect();

        if ($user->division && strtoupper($user->division->name) === 'PDIPBD') {
            $dashboardPdipbStaff = User::with('division')
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'staff');
                })
                ->whereHas('division', function ($query) {
                    $query->whereRaw('UPPER(name) = ?', ['PDIPBD']);
                })
                ->orderBy('name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => 'staff',
                        'division' => optional($user->division)->name,
                    ];
                });

            $dashboardSubmissions = CppSubmission::with(['user.agency', 'sector', 'sub_sector', 'referrals'])
                ->latest()
                ->get()
                ->map(function ($submission) {
                    $latestReferral = $submission->referrals->sortByDesc('created_at')->first();
                    return [
                        'id' => $submission->id,
                        'title' => $submission->project_title,
                        'agency' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                        'sector' => optional($submission->sector)->sector_name ?? '—',
                        'sub_sector' => optional($submission->sub_sector)->sub_sector_name ?? '—',
                        'status' => $submission->status,
                        'stage' => $submission->stage,
                        'submittedAt' => optional($submission->submitted_at)->toIso8601String(),
                        'referredToPdipb' => $submission->referrals->isNotEmpty(),
                        'assigned_to_user_id' => optional($latestReferral)->to_user_id,
                        'referrals' => $submission->referrals,
                    ];
                });
        }

        $myAssignments = \App\Models\Referral::with(['submission.assessment_report', 'fromUser', 'fromDivision'])
            ->where('to_user_id', $user->id)
            ->whereNotIn('status', ['Resolved', 'Completed', 'Rejected'])
            ->whereHas('submission', function($q) {
                $q->whereDoesntHave('assessment_report');
            })
            ->latest()
            ->take(10)
            ->get();

        // Only show PARs that were created through a referral (referral has par_id set)
        $evaluatedReports = ProjectAssessmentReport::with(['submission.user.agency', 'referrals.toDivision', 'assessor.division'])
            ->where('status', 'Evaluated')
            ->whereHas('referral')
            ->latest()
            ->get()
            ->map(function ($par) {
                $submission = $par->submission;
                $latestReferral = $par->referrals->sortByDesc('created_at')->first();
                
                $division = optional(optional($par->assessor)->division)->name 
                            ?? optional(optional($latestReferral)->toDivision)->name 
                            ?? '—';

                $isReferredToPdipbd = $par->referrals->contains(function($ref) {
                    $divName = optional($ref->toDivision)->name ?? '';
                    return in_array(strtoupper(trim($divName)), ['PDIPBD', 'PDIPB']) && $ref->status !== 'Rejected';
                }) || $par->status === 'Referred to PDIPBD' || $par->status === 'Reviewed';

                return [
                    'id' => $par->id,
                    'parId' => $par->id,
                    'title' => optional($submission)->project_title ?? '—',
                    'projectTitle' => optional($submission)->project_title ?? '—',
                    'agency' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                    'proponent' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                    'status' => $par->status,
                    'connectedCteId' => optional($submission)->id,
                    'submissionId' => optional($submission)->id,
                    'referredToDivision' => optional($latestReferral)->to_division_id ? 'Yes' : 'No',
                    'division' => $division,
                    'isReferredToPdipbd' => $isReferredToPdipbd,
                    'referrals' => $par->referrals,
                ];
            });

        $reviewedReports = ProjectAssessmentReport::with(['submission.user.agency', 'referrals.toDivision', 'assessor.division'])
            ->where('status', 'Reviewed')
            ->whereHas('referral')
            ->latest()
            ->get()
            ->map(function ($par) {
                $submission = $par->submission;
                $latestReferral = $par->referrals->sortByDesc('created_at')->first();
                
                $division = optional(optional($par->assessor)->division)->name 
                            ?? optional(optional($latestReferral)->toDivision)->name 
                            ?? '—';

                $isReferredToPdipbd = $par->referrals->contains(function($ref) {
                    $divName = optional($ref->toDivision)->name ?? '';
                    return in_array(strtoupper(trim($divName)), ['PDIPBD', 'PDIPB']);
                }) || $par->status === 'Referred to PDIPBD' || $par->status === 'Reviewed';

                return [
                    'id' => $par->id,
                    'parId' => $par->id,
                    'title' => optional($submission)->project_title ?? '—',
                    'projectTitle' => optional($submission)->project_title ?? '—',
                    'agency' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                    'proponent' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                    'status' => $par->status,
                    'connectedCteId' => optional($submission)->id,
                    'submissionId' => optional($submission)->id,
                    'referredToDivision' => optional($latestReferral)->to_division_id ? 'Yes' : 'No',
                    'division' => $division,
                    'isReferredToPdipbd' => $isReferredToPdipbd,
                ];
            });

        $seccomAnalytics = $this->getSecComAnalytics();
        
        $totalMasterProjects = Project::count();
        $totalSubmissions = CppSubmission::count();
        
        $totalMasterInvestmentRaw = Project::sum('funding_requirement');
        
        $totalMasterInvestment = '₱0.00';
        if ($totalMasterInvestmentRaw >= 1000) {
            $totalMasterInvestment = '₱' . number_format($totalMasterInvestmentRaw / 1000, 2) . 'B';
        } elseif ($totalMasterInvestmentRaw > 0) {
            $totalMasterInvestment = '₱' . number_format($totalMasterInvestmentRaw, 2) . 'M';
        }

        $ongoingMasterProjects = Project::where('status', 'ongoing')->count();
        $completedMasterProjects = Project::where('status', 'completed')->count();
        
        $totalStaffPars = ProjectAssessmentReport::where('assessor_id', $user->id)->count();
            
        $masterProjectStatuses = [
            'ongoing' => Project::where('status', 'ongoing')->count(),
            'proposed' => Project::where('status', 'proposed')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'terminated' => Project::where('status', 'terminated')->count(),
            'suspended' => Project::where('status', 'suspended')->count(),
            'dropped' => Project::where('status', 'dropped')->count(),
        ];
        
        $submissionPipelineCounts = [
            'submitted' => CppSubmission::where('status', 'Submitted')->count(),
            'for_revision' => CppSubmission::where('status', 'For Revision')->count(),
            'incomplete' => CppSubmission::where('status', 'Incomplete')->count(),
            'revised' => CppSubmission::where('status', 'Revised')->count(),
            'resubmitted' => CppSubmission::where('status', 'Resubmitted')->count(),
            'validated' => CppSubmission::where('status', 'Validated')->count(),
            'seccom' => CppSubmission::whereIn('status', ['Sectoral Presentation', 'SecCom Presentation'])->count(),
            'rdc' => CppSubmission::where('status', 'RDC Presentation')->count(),
            'approved' => CppSubmission::whereIn('status', ['Approved', 'RDC Approved'])->count(),
        ];

        return view('home.staff-dashboard', compact('dashboardSubmissions', 'dashboardPdipbStaff', 'myAssignments', 'evaluatedReports', 'reviewedReports', 'seccomAnalytics', 'totalMasterProjects', 'totalSubmissions', 'totalStaffPars', 'masterProjectStatuses', 'submissionPipelineCounts', 'totalMasterInvestment', 'ongoingMasterProjects', 'completedMasterProjects'));
    }

    public function chief()
    {
        $user = auth()->user();
        $division = $user->division;

        $dashboardSubmissions = CppSubmission::with(['user.agency', 'sector', 'sub_sector', 'referrals.toDivision'])
            ->latest()
            ->get()
            ->map(function ($submission) {
                $latestReferral = $submission->referrals->last();
                return [
                    'id' => $submission->id,
                    'title' => $submission->project_title,
                    'agency' => optional(optional($submission->user)->agency)->agency_name ?? '—',
                    'sector' => optional($submission->sector)->sector_name ?? '—',
                    'status' => $submission->status,
                    'stage' => $submission->stage,
                    'submittedAt' => optional($submission->submitted_at)->toIso8601String(),
                    'assigned_to_user_id' => optional($latestReferral)->to_user_id,
                ];
            });

        $divisionReferrals = \App\Models\Referral::with(['submission', 'fromUser', 'fromDivision'])
            ->where('to_division_id', $division->id)
            ->whereNull('to_user_id')
            ->whereNotIn('status', ['Resolved', 'Completed', 'Rejected'])
            ->latest()
            ->take(10)
            ->get();
            
        $totalDivisionReferrals = \App\Models\Referral::where('to_division_id', $division->id)
            ->whereNull('to_user_id')
            ->whereNotIn('status', ['Resolved', 'Completed', 'Rejected'])
            ->count();

        $assessedReports = ProjectAssessmentReport::with(['submission.user.agency', 'assessor'])
            ->where('status', 'Assessed')
            ->whereHas('assessor', function($q) use ($division) {
                $q->where('division_id', $division->id);
            })
            ->latest()
            ->get();
            
        $totalDivisionPars = ProjectAssessmentReport::whereHas('assessor', function($q) use ($division) {
            $q->where('division_id', $division->id);
        })->count();

        $totalMasterProjects = Project::count();
        $totalSubmissions = CppSubmission::count();
        
        $masterProjectStatuses = [
            'ongoing' => Project::where('status', 'ongoing')->count(),
            'proposed' => Project::where('status', 'proposed')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'terminated' => Project::where('status', 'terminated')->count(),
            'suspended' => Project::where('status', 'suspended')->count(),
            'dropped' => Project::where('status', 'dropped')->count(),
        ];
        
        $submissionPipelineCounts = [
            'submitted' => CppSubmission::where('status', 'Submitted')->count(),
            'for_revision' => CppSubmission::where('status', 'For Revision')->count(),
            'incomplete' => CppSubmission::where('status', 'Incomplete')->count(),
            'revised' => CppSubmission::where('status', 'Revised')->count(),
            'resubmitted' => CppSubmission::where('status', 'Resubmitted')->count(),
            'validated' => CppSubmission::where('status', 'Validated')->count(),
            'seccom' => CppSubmission::whereIn('status', ['Sectoral Presentation', 'SecCom Presentation'])->count(),
            'rdc' => CppSubmission::where('status', 'RDC Presentation')->count(),
            'approved' => CppSubmission::whereIn('status', ['Approved', 'RDC Approved'])->count(),
        ];

        return view('home.division-head-dashboard', compact('dashboardSubmissions', 'divisionReferrals', 'totalDivisionReferrals', 'assessedReports', 'totalDivisionPars', 'totalMasterProjects', 'totalSubmissions', 'masterProjectStatuses', 'submissionPipelineCounts'));
    }

    private function getSecComAnalytics()
    {
        $seccomAnalytics = [
            'edc' => 0, // economic
            'idd' => 0, // infrastructure
            'dac' => 0, // development administration
            'sdc' => 0, // social
        ];

        $submissions = CppSubmission::with('sector')
            ->where(function($query) {
                $query->where('stage', 'like', '%sectoral%')
                      ->orWhere('status', 'like', '%sectoral%');
            })
            ->whereNotIn('status', ['For Revision', 'Revised', 'Resubmitted'])
            ->get();

        foreach ($submissions as $sub) {
            $sectorName = strtolower(optional($sub->sector)->sector_name ?? '');
            if (strpos($sectorName, 'economic') !== false || strpos($sectorName, 'environment') !== false) {
                $seccomAnalytics['edc']++;
            } elseif (strpos($sectorName, 'infra') !== false || strpos($sectorName, 'physical') !== false) {
                $seccomAnalytics['idd']++;
            } elseif (strpos($sectorName, 'devt') !== false || strpos($sectorName, 'admin') !== false || strpos($sectorName, 'insti') !== false) {
                $seccomAnalytics['dac']++;
            } elseif (strpos($sectorName, 'social') !== false) {
                $seccomAnalytics['sdc']++;
            }
        }

        return $seccomAnalytics;
    }

    private function getDivisionAssessmentWorkload()
    {
        $divisions = \App\Models\Division::whereIn('name', ['PFPD', 'PMED', 'DRD'])->get();
        
        $workload = [
            'PFPD' => ['total' => 0, 'completed' => 0, 'pending' => 0, 'percentage' => 0, 'color' => '#154A9A', 'textClass' => 'text-primary'],
            'PMED' => ['total' => 0, 'completed' => 0, 'pending' => 0, 'percentage' => 0, 'color' => '#60a5fa', 'textClass' => 'text-info'],
            'DRD'  => ['total' => 0, 'completed' => 0, 'pending' => 0, 'percentage' => 0, 'color' => '#34d399', 'textClass' => 'text-success']
        ];
        
        foreach ($divisions as $div) {
            $name = strtoupper($div->name);
            if (!isset($workload[$name])) continue;
            
            // Count pending referrals for appraisal assigned to this division
            $pendingReferrals = \App\Models\Referral::where('to_division_id', $div->id)
                ->where('stage', 'Project Appraisal')
                ->whereNull('resolved_at')
                ->count();
                
            // Count completed PARs by assessors in this division
            $completedPars = ProjectAssessmentReport::whereHas('assessor', function($q) use ($div) {
                $q->where('division_id', $div->id);
            })->count();
            
            $total = $pendingReferrals + $completedPars;
            $completed = $completedPars;
            
            $workload[$name]['total'] = $total;
            $workload[$name]['completed'] = $completed;
            $workload[$name]['pending'] = $pendingReferrals;
            $workload[$name]['percentage'] = $total > 0 ? round(($completed / $total) * 100) : 0;
        }
        
        return $workload;
    }
}

