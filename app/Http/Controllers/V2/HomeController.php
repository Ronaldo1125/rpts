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
                    'submittedAt' => optional($submission->submitted_at)->toIso8601String(),
                    'referredToPdipb' => $submission->referrals->where('resolved_at', null)
                        ->whereIn('status', ['For Revision Review', 'For Validation', 'For Appraisal'])
                        ->isNotEmpty(),
                    'assigned_to_user_id' => optional($latestReferral)->to_user_id,
                    'referrals' => $submission->referrals,
                ];
            });

        $dashboardInitialCount = $dashboardSubmissions->filter(function ($submission) {
            $status = strtolower(trim($submission['status'] ?? ''));
            $stage = strtolower(trim($submission['stage'] ?? ''));
            $referredToPdipb = (bool) ($submission['referredToPdipb'] ?? false);

            if ($status === 'submitted' && !$referredToPdipb) {
                return true;
            }

            if ($status === 'resubmitted' && $stage === 'completeness test and validation') {
                return true;
            }

            return false;
        })->count();

        $mapParForDashboard = function ($par) {
            $submission = $par->submission;
            $latestReferral = $par->referrals->sortByDesc('created_at')->first();

            $division = optional(optional($par->assessor)->division)->name
                        ?? optional(optional($latestReferral)->toDivision)->name
                        ?? '—';

            $isReferredToPdipbd = $par->referrals->contains(function ($ref) {
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
                'evaluatingDivision' => '—',
                'isReferredToPdipbd' => $isReferredToPdipbd,
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

        return view('home.dashboards', compact(
            'dashboardSubmissions',
            'dashboardPdipbStaff',
            'dashboardEvaluatedPars',
            'dashboardReviewedPars',
            'dashboardInitialCount',
            'dashboardEvaluationCount',
            'seccomAnalytics'
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
            ->whereNotIn('status', ['Resolved', 'Completed'])
            ->whereHas('submission', function($q) {
                $q->whereDoesntHave('assessment_report');
            })
            ->latest()
            ->take(10)
            ->get();

        // Only show PARs that were created through a referral (referral has par_id set)
        $evaluatedReports = \App\Models\ProjectAssessmentReport::with(['submission.user.agency', 'referrals.toDivision', 'assessor.division'])
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

        $reviewedReports = \App\Models\ProjectAssessmentReport::with(['submission.user.agency', 'referrals.toDivision', 'assessor.division'])
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

        return view('home.staff-dashboard', compact('dashboardSubmissions', 'dashboardPdipbStaff', 'myAssignments', 'evaluatedReports', 'reviewedReports', 'seccomAnalytics'));
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
            ->whereNotIn('status', ['Resolved', 'Completed'])
            ->latest()
            ->take(10)
            ->get();

        $assessedReports = \App\Models\ProjectAssessmentReport::with(['submission.user.agency', 'assessor'])
            ->where('status', 'Assessed')
            ->whereHas('assessor', function($q) use ($division) {
                $q->where('division_id', $division->id);
            })
            ->latest()
            ->get();

        return view('home.division-head-dashboard', compact('dashboardSubmissions', 'divisionReferrals', 'assessedReports'));
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
}

