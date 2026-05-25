<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectAssessmentReport;
use App\Models\CppSubmission;
use App\Models\Referral;
use Illuminate\Support\Facades\Auth;

class ProjectAssessmentReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $user = Auth::user();

        $query = ProjectAssessmentReport::with([
            'submission.user.agency',
            'assessor',
        ]);

        // Admins see all PARs; other users see only those assigned to them, created by them, or in their division
        if (! $user->can('admin_management-view')) {
            $isDivisionHead = $user->hasRole('division_chief') || $user->hasRole('chief') || $user->hasRole('division_head');
            
            $query->where(function ($q) use ($user, $isDivisionHead) {
                // 1. Where the user is the creator of the submission
                $q->whereHas('submission', function ($sq) use ($user) {
                    $sq->where('user_id', $user->id);
                });

                // 2. Where the user is assigned as an actor in the PAR
                $q->orWhere('assessor_id', $user->id)
                  ->orWhere('evaluator_id', $user->id)
                  ->orWhere('checker_id', $user->id)
                  ->orWhere('concluder_id', $user->id);

                // 3. Where the user is the target of a referral for this submission
                $q->orWhereHas('submission.referrals', function ($rq) use ($user, $isDivisionHead) {
                    $rq->where('to_user_id', $user->id);
                    if ($isDivisionHead && $user->division_id) {
                        $rq->orWhere('to_division_id', $user->division_id);
                    }
                });

                // 4. For Division Chiefs: Where the assessor is in their division
                if ($isDivisionHead && $user->division_id) {
                    $q->orWhereHas('assessor', function ($aq) use ($user) {
                        $aq->where('division_id', $user->division_id);
                    });
                }
            });
        }

        if ($search) {
            $query->whereHas('submission', function ($q) use ($search) {
                $q->where('project_title', 'LIKE', "%{$search}%");
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate($perPage)->onEachSide(1);
        $reports->appends(['per_page' => $perPage, 'search' => $search]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $reports]);
        }

        return view('project_assessment_report.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $submissionId = $request->query('submission_id');
        
        $submission = null;
        if ($submissionId) {
            $submission = CppSubmission::with([
                'user.agency', 
                'locations.province', 
                'locations.district', 
                'locations.municipality', 
                'locations.barangay',
                'implementation_schedules'
            ])->find($submissionId);
        }

        return view('project_assessment_report.project-assessment-report-form', compact('submission'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        \Illuminate\Support\Facades\Log::info('PAR Store Request Data:', $request->all());
        \Illuminate\Support\Facades\Log::info('PAR Store Files:', $request->allFiles());

        $data = $request->validate([
            'cpp_submission_id' => 'required|exists:cpp_submissions,id',
            'report_status' => 'required|string',
            'doc_request' => 'nullable|string',
            'doc_cpp_fs' => 'nullable|string',
            'doc_endorsements' => 'nullable|string',
            'typology_checks' => 'nullable|array',
            'responsiveness_checks' => 'nullable|array',
            'readiness_status' => 'nullable|string',
            'par_background' => 'nullable|string',
            'par_components' => 'nullable|string',
            'par_spatial' => 'nullable|string',
            'par_qualitative' => 'nullable|string',
            'par_recommendations' => 'nullable|string',
            'par_final_recs' => 'nullable|string',
            'annex_desc' => 'nullable|string',
            'annex_total' => 'nullable|numeric',
            'prepared_by' => 'nullable|string',
            'prepared_by_pos' => 'nullable|string',
            'reviewed_by' => 'nullable|string',
            'reviewed_by_pos' => 'nullable|string',
            'approved_by' => 'nullable|string',
            'approved_by_pos' => 'nullable|string',
            'recommendations' => 'nullable|array',
            'is_sectoral' => 'nullable|boolean',
            'endorsement_checks' => 'nullable|array',
            'readiness_checks' => 'nullable|array',
            'endo_sp_res_text' => 'nullable|string',
            'endo_other_text' => 'nullable|string',
            'final_par_file' => 'required_if:report_status,Final|file|mimes:pdf,doc,docx|max:10240',
            'finalization_notes' => 'nullable|string'
        ]);

        $report = new ProjectAssessmentReport();
        $report->cpp_submission_id = $data['cpp_submission_id'];
        $report->status = $data['report_status'];
        
        // Workflow Assignment
        $report->assessor_id = $user->id;
        if ($report->status === 'Evaluated') $report->evaluator_id = $user->id;
        if ($report->status === 'Reviewed') $report->checker_id = $user->id;
        if ($report->status === 'Final') $report->concluder_id = $user->id;

        // Sections
        $report->doc_request = $request->has('doc_request');
        $report->doc_cpp_fs = $request->has('doc_cpp_fs');
        $report->doc_endorsements = $request->has('doc_endorsements');
        
        $report->typology_data = $data['typology_checks'] ?? [];
        $report->responsiveness_data = $data['responsiveness_checks'] ?? [];
        $report->readiness_level = $data['readiness_status'] ?? null;
        
        $report->par_background = $data['par_background'] ?? '';
        $report->par_components = $data['par_components'] ?? '';
        $report->par_spatial = $data['par_spatial'] ?? '';
        $report->par_qualitative = $data['par_qualitative'] ?? '';
        $report->par_recommendations = $data['par_recommendations'] ?? '';
        
        $report->final_recommendation = $data['par_final_recs'] ?? null;
        $report->annex_description = $data['annex_desc'] ?? null;
        
        // Budget Breakdown
        $report->budget_breakdown = [
            '2023' => $request->annex_2023 ?? 0,
            '2024' => $request->annex_2024 ?? 0,
            '2025' => $request->annex_2025 ?? 0,
            '2026' => $request->annex_2026 ?? 0,
            '2027' => $request->annex_2027 ?? 0,
            '2028' => $request->annex_2028 ?? 0
        ];
        $report->total_project_cost = str_replace(',', '', $data['annex_total'] ?? 0);
        
        // Signatories
        $report->prepared_by = $data['prepared_by'] ?? null;
        $report->prepared_by_pos = $data['prepared_by_pos'] ?? null;
        $report->reviewed_by = $data['reviewed_by'] ?? null;
        $report->reviewed_by_pos = $data['reviewed_by_pos'] ?? null;
        $report->approved_by = $data['approved_by'] ?? null;
        $report->approved_by_pos = $data['approved_by_pos'] ?? null;
        
        $report->is_sectoral = $request->has('is_sectoral');

        // New persistent data
        $report->endorsement_data = [
            'checks' => $data['endorsement_checks'] ?? [],
            'sp_res_text' => $data['endo_sp_res_text'] ?? '',
            'other_text' => $data['endo_other_text'] ?? ''
        ];
        $report->readiness_data = $data['readiness_checks'] ?? [];
        $report->finalization_notes = $data['finalization_notes'] ?? null;
        
        $report->save();

        if ($request->hasFile('final_par_file')) {
            $report->clearMediaCollection('final_technical_reports');
            $report->addMediaFromRequest('final_par_file')
                   ->toMediaCollection('final_technical_reports');
        }

        // Link the referral to this PAR
        \App\Models\Referral::where('cipg_submission_id', $report->cpp_submission_id)
            ->latest()
            ->first()
            ?->update(['par_id' => $report->id]);

        if (!empty($data['findings'])) {
            foreach ($data['findings'] as $index => $findingText) {
                if (!empty($findingText)) {
                    \App\Models\CommentAndRecommendation::create([
                        'cpp_submission_id' => $report->cpp_submission_id,
                        'user_id' => $user->id,
                        'finding' => $findingText,
                        'recommendation' => $data['recommendations'][$index] ?? '',
                        'stage' => $report->submission->stage,
                        'status' => 'Draft'
                    ]);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project Assessment Report saved successfully.', 'id' => $report->id]);
        }

        return redirect()->route('staff.dashboard')->with('success', 'Project Assessment Report saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $report = ProjectAssessmentReport::with([
            'submission.user.agency', 
            'submission.locations.province', 
            'submission.locations.district', 
            'submission.locations.municipality', 
            'submission.locations.barangay',
            'submission.implementation_schedules',
            'assessor'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $report = ProjectAssessmentReport::with([
            'submission.user.agency', 
            'submission.locations.province', 
            'submission.locations.district', 
            'submission.locations.municipality', 
            'submission.locations.barangay',
            'submission.implementation_schedules',
            'assessor'
        ])->findOrFail($id);
        
        $submission = $report->submission;
        
        // Load comments linked to this project
        $comments = \App\Models\CommentAndRecommendation::where('cpp_submission_id', $submission->id)->get();

        return view('project_assessment_report.project-assessment-report-form', compact('report', 'submission', 'comments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        \Illuminate\Support\Facades\Log::info('Updating PAR: ' . $id, $request->all());
        $report = ProjectAssessmentReport::with('submission')->findOrFail($id);
        $user = Auth::user();

        // ── Resolve the referral linked to this PAR or its submission ──────────
        $referral = \App\Models\Referral::where('par_id', $report->id)
            ->orWhere(function($query) use ($report) {
                $query->where('cipg_submission_id', $report->cpp_submission_id)
                      ->where('stage', 'Project Appraisal');
            })
            ->latest()
            ->first();
        
        \Illuminate\Support\Facades\Log::info('PAR Update Request Data:', $request->all());
        \Illuminate\Support\Facades\Log::info('PAR Update Files:', $request->allFiles());
        
        try {
            $data = $request->validate([
                'report_status' => 'required|string',
                'doc_request' => 'nullable|string', // Checkboxes send 'on' or nothing
                'doc_cpp_fs' => 'nullable|string',
                'doc_endorsements' => 'nullable|string',
                'typology_checks' => 'nullable|array',
                'responsiveness_checks' => 'nullable|array',
                'readiness_status' => 'nullable|string',
                'par_background' => 'nullable|string',
                'par_components' => 'nullable|string',
                'par_spatial' => 'nullable|string',
                'par_qualitative' => 'nullable|string',
                'par_recommendations' => 'nullable|string',
                'par_final_recs' => 'nullable|string',
                'annex_desc' => 'nullable|string',
                'annex_total' => 'nullable',
                'prepared_by' => 'nullable|string',
                'prepared_by_pos' => 'nullable|string',
                'reviewed_by' => 'nullable|string',
                'reviewed_by_pos' => 'nullable|string',
                'approved_by' => 'nullable|string',
                'approved_by_pos' => 'nullable|string',
                'findings' => 'nullable|array',
                'recommendations' => 'nullable|array',
                'endorsement_checks' => 'nullable|array',
                'readiness_checks' => 'nullable|array',
                'endo_sp_res_text' => 'nullable|string',
                'endo_other_text' => 'nullable|string',
                'final_par_file' => 'required_if:report_status,Final|file|mimes:pdf,doc,docx|max:10240',
                'finalization_notes' => 'nullable|string'
            ]);
            \Illuminate\Support\Facades\Log::info('Validation passed for PAR update');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('Validation failed for PAR update: ', $e->errors());
            throw $e;
        }

        $report->status = $data['report_status'];
        $report->doc_request = $request->has('doc_request');
        $report->doc_cpp_fs = $request->has('doc_cpp_fs');
        $report->doc_endorsements = $request->has('doc_endorsements');
        $report->typology_data = $data['typology_checks'] ?? [];
        $report->responsiveness_data = $data['responsiveness_checks'] ?? [];
        $report->readiness_level = $data['readiness_status'] ?? 'Potential';
        
        $report->par_background = $data['par_background'] ?? '';
        $report->par_components = $data['par_components'] ?? '';
        $report->par_spatial = $data['par_spatial'] ?? '';
        $report->par_qualitative = $data['par_qualitative'] ?? '';
        $report->par_recommendations = $data['par_recommendations'] ?? '';
        
        $report->final_recommendation = $data['par_final_recs'] ?? '';
        $report->annex_description = $data['annex_desc'] ?? '';
        
        $report->budget_breakdown = [
            '2023' => $request->input('annex_2023', 0),
            '2024' => $request->input('annex_2024', 0),
            '2025' => $request->input('annex_2025', 0),
            '2026' => $request->input('annex_2026', 0),
            '2027' => $request->input('annex_2027', 0),
            '2028' => $request->input('annex_2028', 0),
        ];
        
        $report->total_project_cost = str_replace(',', '', $data['annex_total'] ?? 0);

        // ── Per-status authorization checks ─────────────────────────────────────
        $requestedStatus = $data['report_status'];

        if ($requestedStatus === 'Assessed') {
            // Only the staff member explicitly assigned via the referral may mark as Assessed.
            if (!$referral || (int) $referral->to_user_id !== (int) $user->id) {
                return back()->withErrors(['report_status' =>
                    'You are not the assigned staff for this PAR. Only the staff member assigned by the division head may save this report as Assessed.'
                ]);
            }
        }

        if ($requestedStatus === 'Evaluated') {
            // Only a division head/chief whose division matches the referral's target division may evaluate.
            $isDivisionHead = $user->hasAnyRole(['division_chief', 'chief', 'division_head']);
            $divisionMatches = $referral && (int) $user->division_id === (int) $referral->to_division_id;

            if (!$isDivisionHead || !$divisionMatches) {
                return back()->withErrors(['report_status' =>
                    'Only the division head of the referred division may save this report as Evaluated.'
                ]);
            }
        }

        if ($requestedStatus === 'Reviewed') {
            // Only a staff member in the PDIPBD division may mark as Reviewed.
            $isPdipbdStaff = $user->hasRole('staff')
                && optional($user->division)->name === 'PDIPBD';

            if (!$isPdipbdStaff) {
                return back()->withErrors(['report_status' =>
                    'Only PDIPBD staff may save this report as Reviewed.'
                ]);
            }
        }

        // ── Workflow actor tracking ──────────────────────────────────────────────
        if ($requestedStatus === 'Assessed' && empty($report->assessor_id)) {
            $report->assessor_id = $user->id;
        } elseif ($requestedStatus === 'Evaluated' && empty($report->evaluator_id)) {
            $report->evaluator_id = $user->id;
        } elseif ($requestedStatus === 'Reviewed' && empty($report->checker_id)) {
            $report->checker_id = $user->id;
        } elseif ($requestedStatus === 'Final' && empty($report->concluder_id)) {
            $report->concluder_id = $user->id;
        }

        $report->prepared_by = $data['prepared_by'] ?? $report->prepared_by;
        $report->prepared_by_pos = $data['prepared_by_pos'] ?? $report->prepared_by_pos;
        $report->reviewed_by = $data['reviewed_by'] ?? $report->reviewed_by;
        $report->reviewed_by_pos = $data['reviewed_by_pos'] ?? $report->reviewed_by_pos;
        $report->approved_by = $data['approved_by'] ?? $report->approved_by;
        $report->approved_by_pos = $data['approved_by_pos'] ?? $report->approved_by_pos;
        
        $report->is_sectoral = $request->has('is_sectoral');

        // New persistent data
        $report->endorsement_data = [
            'checks' => $data['endorsement_checks'] ?? [],
            'sp_res_text' => $data['endo_sp_res_text'] ?? '',
            'other_text' => $data['endo_other_text'] ?? ''
        ];
        $report->readiness_data = $data['readiness_checks'] ?? [];
        $report->finalization_notes = $data['finalization_notes'] ?? $report->finalization_notes;
        
        $report->save();

        if ($request->hasFile('final_par_file')) {
            $report->clearMediaCollection('final_technical_reports');
            $report->addMediaFromRequest('final_par_file')
                   ->toMediaCollection('final_technical_reports');
        }

        // ── Sectoral/Submission stage updates ────────────────────────────────────
        if ($requestedStatus === 'Final') {
            if ($request->has('is_sectoral')) {
                // Toggled: For Sectoral Presentation
                if ($report->submission) {
                    $report->submission->update([
                        'stage' => 'Sectoral Committee',
                        'status' => 'Sectoral Presentation'
                    ]);
                }
            } else {
                // Not Toggled: For Revision
                if ($report->submission) {
                    $report->submission->update([
                        'status' => 'For Revision'
                    ]);
                }
            }
        }

        // Keep the referral in sync with this report
        if ($referral) {
            $referralUpdate = [];
            if (!$referral->par_id) {
                $referralUpdate['par_id'] = $report->id;
            }
            if ($requestedStatus === 'Reviewed') {
                $referralUpdate['status'] = 'Reviewed';
                $referralUpdate['resolved_at'] = now();
            }
            // If finalizing, resolve the referral as well
            if ($requestedStatus === 'Final') {
                $referralUpdate['status'] = 'Resolved';
                $referralUpdate['resolved_at'] = now();
            }
            if (!empty($referralUpdate)) {
                $referral->update($referralUpdate);
            }
        }

        // Update Findings & Recommendations
        \Illuminate\Support\Facades\Log::info('Processing findings sync', ['findings' => $request->input('findings')]);
        
        // Always delete and re-sync findings if the submission ID is present
        if ($report->cpp_submission_id) {
            \App\Models\CommentAndRecommendation::where('cpp_submission_id', $report->cpp_submission_id)->delete();
            
            $findings = $request->input('findings', []);
            $recs = $request->input('recommendations', []);
            
            // Determine status for findings based on PAR finalization
            $findingStatus = 'Draft';
            if ($requestedStatus === 'Final' && !$request->has('is_sectoral')) {
                $findingStatus = 'Submitted';
            }

            foreach ($findings as $index => $findingText) {
                if (!empty($findingText)) {
                    \App\Models\CommentAndRecommendation::create([
                        'cpp_submission_id' => $report->cpp_submission_id,
                        'user_id' => $user->id,
                        'finding' => $findingText,
                        'recommendation' => $recs[$index] ?? '',
                        'stage' => 'Project Appraisal',
                        'status' => $findingStatus
                    ]);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Project Assessment Report updated successfully.']);
        }

        $redirectRoute = in_array($requestedStatus, ['Final', 'Assessed', 'Reviewed', 'Evaluated']) ? 'home' : 'project-assessment-reports.index';
        return redirect()->route($redirectRoute)->with('success', 'Project Assessment Report updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Logic for deleting a report
    }
}
