<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\CppSubmission;
use App\Models\CppImplSchedule;
use App\Models\CppConsultationDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CipgSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        // $admin = \App\Models\User::role('administrator')->first();

        // $details = [
        //             'subject' => 'New CPP Submission Received',
        //             'body' => ' Testing if the notification works.',
        //             'actionText' => 'CPP Submission',
        //             'actionURL' => url('/dashboard/admin'),
        //         ];

        //         $admin->notify(new \App\Notifications\CppSubmitted($details));
        
        $user = Auth::user();
        $query = CppSubmission::query();

        if (!$user->hasRole(['admin', 'administrator', 'pmed_staff', 'chief', 'division_chief', 'pmed_chief'])) {
            $query->where('user_id', $user->id);
        } else {
            // Admins see everything EXCEPT drafts created by others
            $query->where(function($q) use ($user) {
                $q->where('status', '!=', 'Draft')
                  ->orWhere('user_id', $user->id);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('project_title', 'LIKE', "%{$search}%");
            });
        }

        $submissions = $query->with(['sector', 'sub_sector', 'feedbacks', 'assessment_report', 'comments_and_recommendations'])->latest()->paginate($perPage)->onEachSide(1);
        $submissions->appends(['per_page' => $perPage, 'search' => $search]);

        return view('cipg_submissions.index_v2', compact('submissions'));
    }

    public function manage(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $user = Auth::user();
        $query = CppSubmission::with(['sector', 'sub_sector', 'user.agency', 'feedbacks', 'assessment_report', 'comments_and_recommendations'])->latest();
        
        // Admins/staff view — shows ALL submissions EXCEPT other people's drafts
        $query->where(function($q) use ($user) {
            $q->where('status', '!=', 'Draft')
              ->orWhere('user_id', $user->id);
        });

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('project_title', 'LIKE', "%{$search}%")
                  ->orWhereHas('user.agency', function ($aq) use ($search) {
                      $aq->where('agency_acronym', 'LIKE', "%{$search}%")
                         ->orWhere('agency_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $submissions = $query->paginate($perPage)->onEachSide(1);
        $submissions->appends(['per_page' => $perPage, 'search' => $search]);

        return view('cipg_submissions.index_v2', [
            'submissions' => $submissions,
            'isManage'    => true,   // flag used in Blade to show Agency column
        ]);
    }

    public function edit($id)
    {
        $submission = CppSubmission::findOrFail($id);
        
        $user = Auth::user();

        // Ensure that Drafts can only be edited by their creator
        if ($submission->status === 'Draft' && $submission->user_id !== $user->id) {
            abort(403, 'You do not have permission to edit this draft.');
        }

        $agency = $user->agency;
        $agencies = \App\Models\Agency::orderBy('agency_name')->get();
        $sectors = \App\Models\Sector::orderBy('sector_name')->get();
        $provinces = \App\Models\Province::orderBy('province_name')->get();

        $sdg_goals = \App\Models\SdgGoal::orderBy('number')->get();
        $chapters = \App\Models\Chapter::orderBy('id')->get();
        $indicators = \App\Models\Indicator::orderBy('indicator_name')->get();

        return view('cipg_submissions.cpp-form', [
            'agency' => $agency,
            'agencies' => $agencies,
            'sectors' => $sectors,
            'provinces' => $provinces,
            'sdg_goals' => $sdg_goals,
            'chapters' => $chapters,
            'indicators' => $indicators,
            'edit_id' => $id,
            'status' => $submission->status
        ]);
    }

    public function destroy($id)
    {
        $submission = CppSubmission::findOrFail($id);
        if (!Auth::user()->hasRole(['admin', 'administrator', 'pmed_staff', 'chief', 'division_chief', 'pmed_chief']) && $submission->user_id !== Auth::id()) {
            abort(403);
        }
        $submission->delete();
        return back()->with('success', 'Submission deleted successfully.');
    }

    public function create()
    {
        $user = Auth::user();
        $agency = $user->agency;
        $agencies = \App\Models\Agency::orderBy('agency_name')->get();
        $sectors = \App\Models\Sector::orderBy('sector_name')->get();
        $provinces = \App\Models\Province::orderBy('province_name')->get();

        $sdg_goals = \App\Models\SdgGoal::orderBy('number')->get();
        $chapters = \App\Models\Chapter::orderBy('id')->get();
        $indicators = \App\Models\Indicator::orderBy('indicator_name')->get();
        $status = 'Draft'; // Default status for new submissions

        return view('cipg_submissions.cpp-form', compact('agency', 'agencies', 'sectors', 'provinces', 'sdg_goals', 'chapters', 'indicators', 'status'));
    }

    public function fetchData(Request $request)
    {
        $user = Auth::user();
        $query = CppSubmission::query();

        if (!$user->hasRole(['admin', 'administrator', 'pmed_staff', 'chief', 'division_chief', 'pmed_chief'])) {
            $query->where('user_id', $user->id);
        } else {
            // Admins see everything EXCEPT drafts created by others
            $query->where(function($q) use ($user) {
                $q->where('status', '!=', 'Draft')
                  ->orWhere('user_id', $user->id);
            });
        }

        if ($request->search) {
            $s = $request->search;
            $query->where('project_title', 'like', "%$s%");
        }

        $submissions = $query->latest()->get();

        $data = $submissions->map(function ($s) {
            return [
                'id' => $s->id,
                'title' => $s->project_title,
                'agency' => $s->user->agency ?? '—',
                'sector' => $s->sector?? '—',
                'sub_sector' => $s->sub_sector ?? '—',
                'status' => $s->status,
                'stage' => $s->stage,
                'date' => $s->submitted_at ? $s->submitted_at->toIso8601String() : $s->created_at->toIso8601String(),
            ];
        });

        return response()->json($data);
    }
    public function details($id)
    {
        $s = CppSubmission::with([
            'sector', 'sub_sector', 'user.agency', 'locations.province', 
            'implementation_schedules.cpp_indicators.indicator', 'consultation_dates', 
            'logframe', 'endorsement', 'benefits_costs',
            'sdg_alignments', 'rdp_alignments'
        ])->findOrFail($id);

        $loc = $s->locations->first();

        // Map model fields to form field names
        $data = [
            'f-agency' => $s->agency_id,
            'f-sector' => $s->sector_id,
            'f-sub-sector' => $s->sub_sector_id,
            'f-title' => $s->project_title,
            'project-type' => $s->project_type, // Array
            'f-components' => $s->components,
            'project-coverage' => $s->project_coverage,
            'geo-coordinate' => $s->geo_coordinate,
            'f-province' => $loc->province_id ?? '',
            'f-district' => $loc->district_id ?? '',
            'f-municipality' => $loc->municipality_id ?? '',
            'f-barangay' => $loc->barangay_id ?? '',
            'f-geo-start-lat' => $s->geo_start_lat ?? '',
            'f-geo-start-lng' => $s->geo_start_lng ?? '',
            'f-geo-end-lat' => $s->geo_end_lat ?? '',
            'f-geo-end-lng' => $s->geo_end_lng ?? '',
            'f-provinces' => $s->locations->map(fn($l) => $l->province->province_name ?? '')->filter()->implode('||'),
            'f-alignment' => $s->sdg_alignments->map(fn($g) => $g->number . ': ' . $g->title)->toArray() ? implode('||', $s->sdg_alignments->map(fn($g) => $g->number . ': ' . $g->title)->toArray()) : '',
            'f-rdp-alignment' => $s->rdp_alignments->pluck('chapter_name')->toArray() ? implode('||', $s->rdp_alignments->pluck('chapter_name')->toArray()) : '',
            'project-status' => $s->project_status,
            //'consultation-status' => $s->consultation_status,
            'prep-site' => isset(($s->prep_status ?? [])['site']) ? (bool)($s->prep_status ?? [])['site'] : in_array('Site is readily available', $s->prep_status ?? [], true),
            'prep-row' => isset(($s->prep_status ?? [])['row']) ? (bool)($s->prep_status ?? [])['row'] : in_array('No issue on right-of-way acquisition', $s->prep_status ?? [], true),
            'prep-ded' => isset(($s->prep_status ?? [])['ded']) ? (bool)($s->prep_status ?? [])['ded'] : in_array('Detailed Engineering Design was prepared', $s->prep_status ?? [], true),
            
            // Page 2
            'f-background' => $s->background,
            'f-goal' => $s->goal,
            'f-purpose' => $s->purpose,
            'f-outputs' => $s->outputs,
            'f-activities' => $s->activities,
            'f-linkages' => $s->linkages,
            'f-nga-funding' => $s->nga_funding,
            'f-lgu-funding' => $s->lgu_funding,
            'f-oda-funding' => $s->oda_funding,
            'f-others-funding' => $s->others_funding,
            'f-total-cost' => $s->total_cost,
            'f-funding-source' => $s->funding_source,
            'f-counterpart-funding' => $s->counterpart_funding,
            
            // Page 3
            'f-agencies-involved' => $s->agencies_involved,
            'f-impl-arrangement' => $s->impl_arrangement,
            'f-env-clearance-desc' => $s->env_clearance_desc,
            'f-social-accept' => $s->social_accept,
            'f-social-costs' => $s->benefits_costs->social_costs ?? '',
            'f-economic-costs' => $s->benefits_costs->economic_costs ?? '',
            'f-beneficiaries' => $s->benefits_costs->beneficiaries ?? '',
            'f-social-benefits' => $s->benefits_costs->social_benefits ?? '',
            'f-economic-benefits' => $s->benefits_costs->economic_benefits ?? '',
            'consultation-status' => $s->consultation_status,
            'f-consult-planned-date' => $s->consult_planned_date ? $s->consult_planned_date->format('Y-m-d') : '',
            'f-consult-highlights' => $s->consult_highlights,
            
            // Page 5
            'f-hgdg-score' => $s->hgdg,
            'f-prepared-name' => $s->prepared_by_name,
            'f-prepared-pos' => $s->prepared_by_position,
            'f-prepared-date' => $s->prepared_date ? $s->prepared_date->format('Y-m-d') : '',
            'f-noted-name' => $s->noted_by_name,
            'f-noted-pos' => $s->noted_by_position,
            'f-noted-date' => $s->noted_date ? $s->noted_date->format('Y-m-d') : '',

            // Complex Relationships
            'impl_schedule' => $s->implementation_schedules->map(function($row) {
                return [
                    'year' => $row->year,
                    'physical_target' => $row->physical_target,
                    'indicator' => $row->cpp_indicators->map(fn($ci) => [
                        'id' => $ci->indicator_id,
                        'name' => $ci->indicator?->indicator_name ?? '',
                    ])->values()->toArray(),
                    'amount' => $row->amount
                ];
            }),
            'consult_dates' => $s->consultation_dates->map(function($row) {
                return $row->consultation_date->format('Y-m-d');
            }),
            'logframe' => $s->logframe ? [
                'lf-goal-narrative' => $s->logframe->lf_goal_narrative,
                'lf-goal-indicators' => $s->logframe->lf_goal_indicators,
                'lf-goal-verification' => $s->logframe->lf_goal_verification,
                'lf-goal-assumptions' => $s->logframe->lf_goal_assumptions,
                'lf-purpose-narrative' => $s->logframe->lf_purpose_narrative,
                'lf-purpose-indicators' => $s->logframe->lf_purpose_indicators,
                'lf-purpose-verification' => $s->logframe->lf_purpose_verification,
                'lf-purpose-assumptions' => $s->logframe->lf_purpose_assumptions,
                'lf-outputs-narrative' => $s->logframe->lf_outputs_narrative,
                'lf-outputs-indicators' => $s->logframe->lf_outputs_indicators,
                'lf-outputs-verification' => $s->logframe->lf_outputs_verification,
                'lf-outputs-assumptions' => $s->logframe->lf_outputs_assumptions,
                'lf-inputs-narrative' => $s->logframe->lf_inputs_narrative,
                'lf-inputs-indicators' => $s->logframe->lf_inputs_indicators,
                'lf-inputs-verification' => $s->logframe->lf_inputs_verification,
                'lf-inputs-assumptions' => $s->logframe->lf_inputs_assumptions,
            ] : null,
            'endorsement' => $s->endorsement ? [
                'resolution-document' => $s->endorsement->resolution_document, //array
                'f-sp-res'    => $s->endorsement->sp_resolution_no,
                'f-sp-date'   => $s->endorsement->sp_resolution_date ? $s->endorsement->sp_resolution_date->format('Y-m-d') : '',
                'f-sb-res'    => $s->endorsement->sb_resolution_no,
                'f-sb-date'   => $s->endorsement->sb_resolution_date ? $s->endorsement->sb_resolution_date->format('Y-m-d') : '',
                'f-letter-req'  => $s->endorsement->letter_request_ref,
                'f-letter-date' => $s->endorsement->letter_transmittal_date ? $s->endorsement->letter_transmittal_date->format('Y-m-d') : '',
                'f-bor-res'   => $s->endorsement->bor_bot_resolution_no,
                'f-bor-date'  => $s->endorsement->bor_bot_resolution_date ? $s->endorsement->bor_bot_resolution_date->format('Y-m-d') : '',
            ] : null,
        ];

        // Include existing media attachments so the edit form can restore upload zones
        $data['attachments'] = $s->getMedia('attachments')->map(fn($m) => [
            'name'  => $m->file_name,
            'url'   => $m->getUrl(),
            'type'  => $m->getCustomProperty('type'),
            'label' => $m->getCustomProperty('label'),
        ])->values()->toArray();

        return response()->json($data);
    }


    public function show($id)
    {
        $submission = CppSubmission::with([
            'implementation_schedules.cpp_indicators.indicator',
            'consultation_dates',
            'user.agency',
            'locations.province',
            'logframe',
            'endorsement',
            'benefits_costs',
            'sdg_alignments',
            'rdp_alignments',
            'sector',
            'sub_sector'
        ])->findOrFail($id);

        $loc = $submission->locations->first();

        if (!Auth::user()->hasRole(['admin', 'administrator', 'pmed_staff', 'chief', 'division_chief', 'pmed_chief', 'staff', 'division_head']) && $submission->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $data = [
            'f-title' => $submission->project_title,
            'f-agency' => $submission->user->agency->agency_name ?? null,
            'agency_id' => $submission->agency_id,
            'project-coverage' => $submission->project_coverage,
            'geo-coordinate' => $submission->geo_coordinate,
            
            // Location
            'f-province' => $loc->province_id ?? null,
            'f-district' => $loc->district_id ?? null,
            'f-municipality' => $loc->municipality_id ?? null,
            'f-barangay' => $loc->barangay_id ?? null,
            'f-geo-start-lat' => $submission->geo_start_lat ?? null,
            'f-geo-start-lng' => $submission->geo_start_lng ?? null,
            'f-geo-end-lat' => $submission->geo_end_lat ?? null,
            'f-geo-end-lng' => $submission->geo_end_lng ?? null,
            'f-provinces' => $submission->locations->map(fn($l) => $l->province->province_name ?? '')->filter()->implode('||'),

            'project-status' => $submission->project_status,
            'prep-site' => $submission->prep_status['site'] ?? false,
            'prep-row' => $submission->prep_status['row'] ?? false,
            'prep-ded' => $submission->prep_status['ded'] ?? false,
            'f-components' => $submission->components,
            'project-type' => $submission->project_type ?? [],
            
            // Endorsement
            'f-sp-res' => $submission->endorsement->sp_resolution_no ?? null,
            'f-sp-date' => $submission->endorsement->sp_resolution_date ?? null,
            'f-sb-res' => $submission->endorsement->sb_resolution_no ?? null,
            'f-sb-date' => $submission->endorsement->sb_resolution_date ?? null,
            'f-letter-req' => $submission->endorsement->letter_request_ref ?? null,
            'f-letter-date' => $submission->endorsement->letter_transmittal_date ?? null,
            'f-bor-res' => $submission->endorsement->bor_bot_resolution_no ?? null,
            'f-bor-date' => $submission->endorsement->bor_bot_resolution_date ?? null,

            'f-sector' => $submission->sector->sector_name ?? null,
            'f-sub-sector' => $submission->sub_sector->subsector_name ?? null,
            
            // Alignments (Formatted for view)
            'f-alignment' => $submission->sdg_alignments->map(fn($s) => "{$s->number}: {$s->title}")->implode("<br>"),
            'f-rdp-alignment' => $submission->rdp_alignments->pluck('chapter_name')->implode("<br>"),

            // Project Justification (Page 2)
            'f-background' => $submission->background,
            'f-goal' => $submission->goal,
            'f-purpose' => $submission->purpose,
            'f-outputs' => $submission->outputs,
            'f-activities' => $submission->activities,
            'f-linkages' => $submission->linkages,

            // Project Financing (Page 2)
            'f-total-cost' => $submission->total_cost,
            'f-funding-source' => $submission->funding_source,
            'f-counterpart-funding' => $submission->counterpart_funding,
            
            // Benefits & Costs
            'f-beneficiaries' => $submission->benefits_costs->beneficiaries ?? null,
            'f-social-benefits' => $submission->benefits_costs->social_benefits ?? null,
            'f-economic-benefits' => $submission->benefits_costs->economic_benefits ?? null,
            'f-social-costs' => $submission->benefits_costs->social_costs ?? null,
            'f-economic-costs' => $submission->benefits_costs->economic_costs ?? null,
            'f-social-accept' => $submission->social_accept,

            // Project Implementation (Page 3)
            'f-agencies-involved' => $submission->agencies_involved,
            'f-impl-arrangement' => $submission->impl_arrangement,
            'f-env-clearance-desc' => $submission->env_clearance_desc,

            'consultation-status' => $submission->consultation_status,
            'f-consult-planned-date' => $submission->consult_planned_date ? $submission->consult_planned_date->format('Y-m-d') : null,
            'f-consult-highlights' => $submission->consult_highlights,
            'f-hgdg' => $submission->hgdg,

            // Logframe
            'lf-goal-narrative' => $submission->logframe->lf_goal_narrative ?? null,
            'lf-goal-indicators' => $submission->logframe->lf_goal_indicators ?? null,
            'lf-goal-verification' => $submission->logframe->lf_goal_verification ?? null,
            'lf-goal-assumptions' => $submission->logframe->lf_goal_assumptions ?? null,
            'lf-purpose-narrative' => $submission->logframe->lf_purpose_narrative ?? null,
            'lf-purpose-indicators' => $submission->logframe->lf_purpose_indicators ?? null,
            'lf-purpose-verification' => $submission->logframe->lf_purpose_verification ?? null,
            'lf-purpose-assumptions' => $submission->logframe->lf_purpose_assumptions ?? null,
            'lf-outputs-narrative' => $submission->logframe->lf_outputs_narrative ?? null,
            'lf-outputs-indicators' => $submission->logframe->lf_outputs_indicators ?? null,
            'lf-outputs-verification' => $submission->logframe->lf_outputs_verification ?? null,
            'lf-outputs-assumptions' => $submission->logframe->lf_outputs_assumptions ?? null,
            'lf-inputs-narrative' => $submission->logframe->lf_inputs_narrative ?? null,
            'lf-inputs-indicators' => $submission->logframe->lf_inputs_indicators ?? null,
            'lf-inputs-verification' => $submission->logframe->lf_inputs_verification ?? null,
            'lf-inputs-assumptions' => $submission->logframe->lf_inputs_assumptions ?? null,

            'f-prepared-name' => $submission->prepared_by_name,
            'f-prepared-pos' => $submission->prepared_by_position,
            'f-prepared-date' => $submission->prepared_date ? $submission->prepared_date->format('Y-m-d') : null,
            'f-noted-name' => $submission->noted_by_name,
            'f-noted-pos' => $submission->noted_by_position,
            'f-noted-date' => $submission->noted_date ? $submission->noted_date->format('Y-m-d') : null,
            
            'f-consult-done-dates' => $submission->consultation_dates->pluck('consultation_date')->map(fn($d) => $d->format('Y-m-d'))->implode(','),
            '_impl_schedule' => $submission->implementation_schedules->map(fn($row) => [
                'year' => $row->year,
                'target' => $row->physical_target,
                'indicator' => $row->cpp_indicators->pluck('indicator.indicator_name')->implode(', '),
                'amount' => $row->amount,
            ])->toArray(),
            'status' => $submission->status,
            'stage' => $submission->stage
        ];

        // Process attachments and extract specific types for the view
        $attachments = $submission->getMedia('attachments')->map(fn($m) => [
            'name' => $m->file_name,
            'url' => $m->getUrl(),
            'type' => $m->getCustomProperty('type'),
            'label' => $m->getCustomProperty('label')
        ]);

        $data['attachments'] = $attachments;
        $data['sig-prep-data'] = $attachments->firstWhere('type', 'sig_prep')['url'] ?? null;
        $data['sig-noted-data'] = $attachments->firstWhere('type', 'sig_noted')['url'] ?? null;
        $data['geo-photo-url'] = $attachments->firstWhere('type', 'geo_photo')['url'] ?? null;

        return view('cipg_submissions.cpp-view', [
            'submission' => $submission,
            'd' => $data
        ]);
    }

    public function store(Request $request)
    {
        $isDraft = $request->input('is_draft') === true || $request->input('is_draft') === 'true';

        if (!$isDraft) {
            $request->validate([
                'f-title' => 'required|string|max:1000',
                'f-sector' => 'required|exists:sectors,id',
                'f-sub-sector' => 'required|exists:sub_sectors,id',
                'project-type' => 'required|array|min:1',
                'project-coverage' => 'required|in:Regionwide,Inter-Province,Location-Specific',
                'project-status' => 'required|in:Ongoing,Pipeline,Proposed',
                'f-background' => 'required|string',
                'f-goal' => 'required|string',
                'f-purpose' => 'required|string',
                'f-outputs' => 'required|string',
                'f-activities' => 'required|string',
                'f-linkages' => 'required|string',
                'f-total-cost' => 'required|numeric|min:0',
                'f-funding-source' => 'required|string',
                'f-prepared-name' => 'required|string',
                'f-prepared-pos' => 'required|string',
                'f-prepared-date' => 'required|date',
                'f-noted-name' => 'required|string',
                'f-noted-pos' => 'required|string',
                'f-noted-date' => 'required|date',
            ]);
        } else {
            // For a draft, only the title is required so we can identify the record
            $request->validate([
                'f-title' => 'required|string|max:1000',
            ]);
        }

        $data = $request->all();
        $user = Auth::user();

        DB::beginTransaction();

        try {
            if ($request->submission_id) {
                $submission = CppSubmission::findOrFail($request->submission_id);
            } else {
                $submission = new CppSubmission();
                $submission->user_id = $user->id;
                $submission->agency_id = $user->agency_id;
            }

            // Set status/stage
            if (!$submission->exists) {
                if ($isDraft) {
                    $submission->status = 'Draft';
                    $submission->stage = 'Draft';
                } else {
                    $submission->status = 'Submitted';
                    $submission->stage = 'Submission';
                    $submission->submitted_at = Carbon::now();
                }
            } else {
                if (!$isDraft) {
                    if ($submission->status === 'Draft') {
                        $submission->status = 'Submitted';
                    } elseif ($submission->status === 'For Revision') {
                        $submission->status = 'Revised';
                    } else {
                        $submission->status = 'Resubmitted';
                    }

                    if ($submission->stage === 'Draft') $submission->stage = 'Submission';
                    $submission->submitted_at = Carbon::now();
                } else {
                    $submission->status = 'Draft';
                }
            }
            
            // CORE
            $submission->project_title = $data['f-title'] ?? null;
            $submission->sector_id = $data['f-sector'] ?? null;
            $submission->sub_sector_id = $data['f-sub-sector'] ?? null;
            $submission->project_type = $data['project-type'] ?? [];
            $submission->components = $data['f-components'] ?? null;
            $submission->project_coverage = $data['project-coverage'] ?? null;
            $submission->geo_coordinate = $data['geo-coordinate'] ?? null;
            $submission->geo_start_lat = $data['f-geo-start-lat'] ?? null;
            $submission->geo_start_lng = $data['f-geo-start-lng'] ?? null;
            $submission->geo_end_lat = $data['f-geo-end-lat'] ?? null;
            $submission->geo_end_lng = $data['f-geo-end-lng'] ?? null;
            $submission->project_status = $data['project-status'] ?? null;
            $submission->prep_status = $data['prep_status'] ?? [];
            
            $submission->background = $data['f-background'] ?? null;
            $submission->goal = $data['f-goal'] ?? null;
            $submission->purpose = $data['f-purpose'] ?? null;
            $submission->outputs = $data['f-outputs'] ?? null;
            $submission->activities = $data['f-activities'] ?? null;
            $submission->linkages = $data['f-linkages'] ?? null;

            $submission->nga_funding = $data['f-nga-funding'] ?? null;
            $submission->lgu_funding = $data['f-lgu-funding'] ?? null;
            $submission->oda_funding = $data['f-oda-funding'] ?? null;
            $submission->others_funding = $data['f-others-funding'] ?? null;
            $submission->total_cost = $data['f-total-cost'] ?? 0;
            $submission->funding_source = $data['f-funding-source'] ?? null;
            $submission->counterpart_funding = $data['f-counterpart-funding'] ?? null;

            $submission->agencies_involved = $data['f-agencies-involved'] ?? null;
            $submission->impl_arrangement = $data['f-impl-arrangement'] ?? null;
            $submission->env_clearance_desc = $data['f-env-clearance-desc'] ?? null;
            $submission->social_accept = $data['f-social-accept'] ?? null;
            $submission->consultation_status = $data['consultation-status'] ?? null;
            $submission->consult_planned_date = $data['f-consult-planned-date'] ?? null;
            $submission->consult_highlights = $data['f-consult-highlights'] ?? null;
            $submission->hgdg = $data['f-hgdg'] ?? null;

            $submission->prepared_by_name = $data['f-prepared-name'] ?? null;
            $submission->prepared_by_position = $data['f-prepared-pos'] ?? null;
            $submission->prepared_date = $data['f-prepared-date'] ?? null;
            $submission->noted_by_name = $data['f-noted-name'] ?? null;
            $submission->noted_by_position = $data['f-noted-pos'] ?? null;
            $submission->noted_date = $data['f-noted-date'] ?? null;

            $submission->save();

            // 1. Location
            $submission->locations()->delete();
            if (($data['project-coverage'] ?? '') === 'Inter-Province' && !empty($data['f-provinces'])) {
                $provinceNames = explode('||', $data['f-provinces']);
                foreach ($provinceNames as $name) {
                    $province = \App\Models\Province::where('province_name', $name)->first();
                    if ($province) {
                        $submission->locations()->create(['province_id' => $province->id]);
                    }
                }
            } elseif (($data['project-coverage'] ?? '') === 'Location-Specific') {
                $submission->locations()->create([
                    'province_id' => $data['f-province'] ?? null,
                    'district_id' => $data['f-district'] ?? null,
                    'municipality_id' => $data['f-municipality'] ?? null,
                    'barangay_id' => $data['f-barangay'] ?? null,
                ]);
            }

            // 2. Logframe
            $submission->logframe()->updateOrCreate([], [
                'lf_goal_narrative' => $data['lf-goal-narrative'] ?? null,
                'lf_goal_indicators' => $data['lf-goal-indicators'] ?? null,
                'lf_goal_verification' => $data['lf-goal-verification'] ?? null,
                'lf_goal_assumptions' => $data['lf-goal-assumptions'] ?? null,
                'lf_purpose_narrative' => $data['lf-purpose-narrative'] ?? null,
                'lf_purpose_indicators' => $data['lf-purpose-indicators'] ?? null,
                'lf_purpose_verification' => $data['lf-purpose-verification'] ?? null,
                'lf_purpose_assumptions' => $data['lf-purpose-assumptions'] ?? null,
                'lf_outputs_narrative' => $data['lf-outputs-narrative'] ?? null,
                'lf_outputs_indicators' => $data['lf-outputs-indicators'] ?? null,
                'lf_outputs_verification' => $data['lf-outputs-verification'] ?? null,
                'lf_outputs_assumptions' => $data['lf-outputs-assumptions'] ?? null,
                'lf_inputs_narrative' => $data['lf-inputs-narrative'] ?? null,
                'lf_inputs_indicators' => $data['lf-inputs-indicators'] ?? null,
                'lf_inputs_verification' => $data['lf-inputs-verification'] ?? null,
                'lf_inputs_assumptions' => $data['lf-inputs-assumptions'] ?? null,
            ]);

            // 3. Endorsement
            $submission->endorsement()->updateOrCreate([], [
                'resolution_document' => $data['resolution-document'] ?? [],
                'sp_resolution_no' => $data['f-sp-res'] ?? null,
                'sp_resolution_date' => $data['f-sp-date'] ?? null,
                'sb_resolution_no' => $data['f-sb-res'] ?? null,
                'sb_resolution_date' => $data['f-sb-date'] ?? null,
                'letter_request_ref' => $data['f-letter-req'] ?? null,
                'letter_transmittal_date' => $data['f-letter-date'] ?? null,
                'bor_bot_resolution_no' => $data['f-bor-res'] ?? null,
                'bor_bot_resolution_date' => $data['f-bor-date'] ?? null,
            ]);

            // 4. Benefits & Costs
            $submission->benefits_costs()->updateOrCreate([], [
                'beneficiaries' => $data['f-beneficiaries'] ?? null,
                'social_benefits' => $data['f-social-benefits'] ?? null,
                'economic_benefits' => $data['f-economic-benefits'] ?? null,
                'social_costs' => $data['f-social-costs'] ?? null,
                'economic_costs' => $data['f-economic-costs'] ?? null,
            ]);

            // 5. Alignments (Sync Many-to-Many)
            if (isset($data['f-alignment'])) {
                $nums = explode('||', $data['f-alignment']);
                $ids = \App\Models\SdgGoal::whereIn('label', $nums)->pluck('id');
                $submission->sdg_alignments()->sync($ids);
            }
            if (isset($data['f-rdp-alignment'])) {
                $nums = explode('||', $data['f-rdp-alignment']);
                $ids = \App\Models\Chapter::whereIn('chapter_name', $nums)->pluck('id');
                $submission->rdp_alignments()->sync($ids);
            }

            // 6. Implementation Schedule
            $submission->implementation_schedules()->delete();
            if (!empty($data['impl_schedule'])) {
                foreach ($data['impl_schedule'] as $row) {
                    $implSchedule = $submission->implementation_schedules()->create([
                        'year' => $row['year'] ?? '',
                        'physical_target' => $row['physical_target'] ?? '',
                        'amount' => $row['amount'] ?? 0,
                        'sort_order' => $row['sort_order'] ?? 0
                    ]);

                    if (!empty($row['indicator']) && is_array($row['indicator'])) {
                        foreach ($row['indicator'] as $indId) {
                            $implSchedule->cpp_indicators()->create([
                                'indicator_id' => $indId
                            ]);
                        }
                    }
                }
            }

            // 7. Consultation Dates
            $submission->consultation_dates()->delete();
            if (!empty($data['consultation_dates'])) {
                foreach ($data['consultation_dates'] as $cdate) {
                    $submission->consultation_dates()->create([
                        'consultation_date' => $cdate
                    ]);
                }
            }

            // 8. Attachments
            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $att) {
                    // If the client sent a new file (base64), save it.
                    if (!empty($att['data'])) {
                        $type = $att['type'] ?? 'other';

                        // For single-file types, remove old version if it exists
                        $singleFileTypes = ['env', 'sp', 'sb', 'ded', 'geo_photo', 'sig_prep', 'sig_noted', 'bor', 'letter', 'consult', 'spatial-cov', 'hgdg'];
                        if (in_array($type, $singleFileTypes)) {
                            $submission->getMedia('attachments')->filter(function ($media) use ($type) {
                                return $media->getCustomProperty('type') === $type;
                            })->each->delete();
                        }

                        $submission->addMediaFromBase64($att['data'])
                            ->usingFileName($att['name'])
                            ->withCustomProperties([
                                'type'  => $type,
                                'label' => $att['label'] ?? ''
                            ])
                            ->toMediaCollection('attachments');
                    }
                    // existing_url entries are intentionally ignored here — the media record
                    // already exists in the 'attachments' collection for this submission.
                }
            }

            If (!$isDraft) {
                // Send notification to admin to refer this to PDIPBD staff for review (Banny 08-2026)
                $admin = \App\Models\User::role('administrator')->where('email', 'eollaguno@depdev.gov.ph')->first(); // for now, just get the first admin user. You may want to refine this logic later.

                $details = [
                    'subject' => 'New CPP Submission Received',
                    'body' => $user->agency->agency_acronym . ' "' . $data['f-title'] . '"'. ' for validation.',
                    'actionText' => 'CPP Submission',
                    'actionURL' => url('/admin/home#cipgTableBody'),
                ];

                $admin->notify(new \App\Notifications\CppNotification($details));
                //Notification::send($admin, new \App\Notifications\CppNotification($details));
            }


            DB::commit();
            return response()->json(['message' => 'Submission saved successfully', 'id' => $submission->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to save submission: ' . $e->getMessage()], 500);
        }
    }
}
