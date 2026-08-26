<?php

namespace App\Http\Controllers;

use App\Models\CppSubmission;
use App\Models\CommentAndRecommendation;
use App\Models\Referral;
use App\Models\SubmissionFeedback;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        // If API request, return JSON data
        if ($request->wantsJson() || $request->expectsJson()) {
            $user = $request->user();
            $query = Referral::with(['submission', 'referrer.roles', 'fromDivision', 'assignedStaff.roles', 'toDivision']);

            if ($user && !$user->hasRole('administrator')) {
                $isDivisionHead = $user->hasRole('division_chief') || $user->hasRole('chief') || $user->hasRole('division_head');
                $isStaff = $user->hasRole('staff');

                if ($isDivisionHead && $user->division_id) {
                    $query->where('to_division_id', $user->division_id);
                } elseif ($isStaff) {
                    $query->where('to_user_id', $user->id);
                } else {
                    $query->whereHas('submission', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                }
            }

            $items = $query->latest('referred_at')->latest('id')->get()->map(function ($ref) {
                return [
                    'id' => (string) $ref->id,
                    'submissionId' => (string) ($ref->cipg_submission_id ?? ''),
                    'submissionTitle' => $ref->submission?->project_title ?? 'N/A',
                    'stage' => $ref->stage,
                    'fromUserName' => $ref->fromUser?->name ?? 'System User',
                    'fromDivisionName' => $ref->fromDivision?->name ?? 'N/A',
                    'toDivisionName' => $ref->toDivision?->name ?? 'N/A',
                    'toUserName' => $ref->toUser?->name ?? 'Unassigned',
                    'toUserId' => (string) ($ref->to_user_id ?? ''),
                    'referredAt' => optional($ref->referred_at)->toIso8601String() ?? optional($ref->created_at)->toIso8601String(),
                    'status' => $ref->status,
                    'notes' => $ref->notes,
                    'resolvedAt' => optional($ref->resolved_at)?->toIso8601String() ?? null,
                ];
            });

            return response()->json(['data' => $items]);
        }

        // Otherwise render the view with referrals data
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $user = $request->user();
        $query = Referral::with(['submission', 'referrer.roles', 'fromDivision', 'assignedStaff.roles', 'toDivision']);

        if ($user && !$user->hasRole('administrator')) {
            $isDivisionHead = $user->hasRole('division_chief') || $user->hasRole('chief') || $user->hasRole('division_head');
            $isStaff = $user->hasRole('staff');

            if ($isDivisionHead && $user->division_id) {
                $query->where('to_division_id', $user->division_id);
            } elseif ($isStaff) {
                $query->where('to_user_id', $user->id);
            } else {
                $query->whereHas('submission', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        if ($search) {
            $query->whereHas('submission', function ($q) use ($search) {
                $q->where('project_title', 'LIKE', "%{$search}%");
            });
        }

        $referrals = $query->latest('referred_at')->latest('id')->paginate($perPage)->onEachSide(1);
        $referrals->appends(['per_page' => $perPage, 'search' => $search]);

        return view('referrals.index', ['referrals' => $referrals]);
    }

    public function validatedProjects()
    {
        $rows = CppSubmission::query()
            ->whereIn('status', ['Validated', 'Final'])
            ->latest('updated_at')
            ->latest('id')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => (string) $s->id,
                    'title' => $s->project_title,
                    'agency' => $s->agency,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function staffByDivision(Request $request)
    {
        $divisionId = $request->query('division_id');
        if (!$divisionId) {
            return response()->json(['data' => []]);
        }

        $rows = User::query()
            ->whereHas('roles', function ($q) {
                $q->where('name', 'staff');
            })
            ->where('division_id', $divisionId)
            ->orderBy('name')
            ->get()
            ->map(function ($u) {
                return [
                    'id' => (string) $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cipg_submission_id' => ['required', 'exists:cpp_submissions,id'],
            'to_division_id' => ['nullable', 'exists:divisions,id'],
            'division_name' => ['nullable', 'string'],
            'to_user_id' => ['nullable', 'exists:users,id'],
            'stage' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $submission = CppSubmission::findOrFail($validated['cipg_submission_id']);
        $user = $request->user();
        
        $to_user_id = $validated['to_user_id'] ?? null;
        $to_division_id = $validated['to_division_id'] ?? null;

        if (!$to_division_id && !empty($validated['division_name'])) {
            $div = \App\Models\Division::where('name', $validated['division_name'])->first();
            if ($div) {
                $to_division_id = $div->id;
            }
        }

        if ($to_user_id && !$to_division_id) {
            $toUserObj = User::find($to_user_id);
            if ($toUserObj) {
                $to_division_id = $toUserObj->division_id;
            }
        }

        $toUser = $to_user_id ? User::find($to_user_id) : null;

        $currentStatus = strtolower(trim($submission->status));
        $currentStage = strtolower(trim($submission->stage));
        
        $isAdmin = $user->hasRole('administrator') || $user->hasRole('admin');
        $isDivisionHead = $user->hasRole('division_chief') || $user->hasRole('chief') || $user->hasRole('division_head');
        
        $isToPdipbdStaff = $toUser && $toUser->hasRole('staff') && $toUser->division && in_array(strtoupper(trim($toUser->division->name)), ['PDIPBD', 'PDIPB']);
        $isToStaff = $toUser && $toUser->hasRole('staff');

        // Rule 1: Initial Referral: Admin -> PDIPBD Staff
        if (in_array($currentStatus, ['submitted', 'resubmitted', 'incomplete']) && in_array($currentStage, ['submission', 'completeness test and validation']) && $isAdmin && $isToPdipbdStaff) {
            $referral = Referral::create([
                'cipg_submission_id' => $submission->id,
                'from_user_id' => $user->id,
                'from_division_id' => $user->division_id,
                'to_user_id' => $to_user_id,
                'to_division_id' => $to_division_id,
                'stage' => 'Completeness Test and Validation',
                'status' => 'For Validation',
                'notes' => $validated['notes'] ?? null,
                'referred_at' => now(),
            ]);

            $submission->stage = 'Completeness Test and Validation';
            $submission->save();

            // Send notification to the assigned PDIPBD staff (Added on: 8/25/2026)
            if ($toUser) {
                $details = [
                    'subject' => 'New Referral: "' . $submission->project_title . '"',
                    'body' => 'You have been assigned a new referral for the project "' . $submission->project_title . '". Please review it at your earliest convenience.',
                    'actionText' => 'View Referral',
                    'actionURL' => url('/admin/home#cipgTableBody'),
                ];
                $toUser->notify(new \App\Notifications\CppNotification($details));
            }

            return response()->json(['success' => true, 'id' => $referral->id]);
        }

        // Rule 2: Referral After Validation: Admin -> Division
        if (in_array($currentStage, ['completeness test and evaluation', 'completeness test and validation', 'project appraisal']) && $currentStatus === 'validated' && $isAdmin && $to_division_id && !$to_user_id) {
            $referral = Referral::create([
                'cipg_submission_id' => $submission->id,
                'from_user_id' => $user->id,
                'from_division_id' => $user->division_id,
                'to_user_id' => null, 
                'to_division_id' => $to_division_id,
                'stage' => 'Project Appraisal',
                'status' => 'Referred to Division',
                'notes' => $validated['notes'] ?? null,
                'referred_at' => now(),
            ]);

            $submission->stage = 'Project Appraisal';
            $submission->save();

            return response()->json(['success' => true, 'id' => $referral->id]);
        }

        // Rule 3: Division Head Assignment: Division Head -> Division Staff
        if ($isDivisionHead && $isToStaff && $toUser->division_id === $user->division_id) {
            $referral = Referral::where('cipg_submission_id', $submission->id)
                ->where('to_division_id', $user->division_id)
                ->whereNull('to_user_id')
                ->latest()
                ->first();

            if ($referral) {
                $referral->update([
                    'to_user_id' => $to_user_id,
                    'status' => 'For Appraisal',
                    'notes' => $validated['notes'] ? ($referral->notes ? $referral->notes . "\n" . $validated['notes'] : $validated['notes']) : $referral->notes,
                ]);

                return response()->json(['success' => true, 'id' => $referral->id]);
            }
        }

        // Rule 4: Revision Referral After Project Appraisal: Admin -> PDIPBD Staff
        if ($currentStage === 'project appraisal' && $currentStatus === 'revised' && $isAdmin && $isToPdipbdStaff) {
            $referral = Referral::create([
                'cipg_submission_id' => $submission->id,
                'from_user_id' => $user->id,
                'from_division_id' => $user->division_id,
                'to_user_id' => $to_user_id,
                'to_division_id' => $to_division_id,
                'stage' => 'Project Appraisal',
                'status' => 'For Revision Review',
                'notes' => $validated['notes'] ?? null,
                'referred_at' => now(),
            ]);

            $submission->save();

            return response()->json(['success' => true, 'id' => $referral->id]);
        }

        // Rule 5: Sectoral Presentation Referral: Admin -> PDIPBD Staff
        if (str_contains($currentStage, 'sectoral') && in_array($currentStatus, ['sectoral presentation', 'seccom presentation']) && $isAdmin && $isToPdipbdStaff) {
            $referral = Referral::create([
                'cipg_submission_id' => $submission->id,
                'from_user_id' => $user->id,
                'from_division_id' => $user->division_id,
                'to_user_id' => $to_user_id,
                'to_division_id' => $to_division_id,
                'stage' => $submission->stage,
                'status' => 'Sectoral Presentation Review',
                'notes' => $validated['notes'] ?? null,
                'referred_at' => now(),
            ]);
            $submission->save();

            return response()->json(['success' => true, 'id' => $referral->id]);
        }

        // Rule 6: Revision Referral After Sectoral Committee Presentation: Admin -> PDIPBD Staff
        if (str_contains($currentStage, 'sectoral') && $currentStatus === 'revised' && $isAdmin && $isToPdipbdStaff) {
            $referral = Referral::create([
                'cipg_submission_id' => $submission->id,
                'from_user_id' => $user->id,
                'from_division_id' => $user->division_id,
                'to_user_id' => $to_user_id,
                'to_division_id' => $to_division_id,
                'stage' => $submission->stage,
                'status' => 'For Revision Review',
                'notes' => $validated['notes'] ?? null,
                'referred_at' => now(),
            ]);
            $submission->save();

            return response()->json(['success' => true, 'id' => $referral->id]);
        }

        // Rule 7a: RDC Presentation Referral: Admin -> PDIPBD Staff (project in RDC stage, status RDC Presentation)
        if ($currentStage === 'rdc' && $currentStatus === 'rdc presentation' && $isAdmin && $isToPdipbdStaff) {
            $referral = Referral::create([
                'cipg_submission_id' => $submission->id,
                'from_user_id' => $user->id,
                'from_division_id' => $user->division_id,
                'to_user_id' => $to_user_id,
                'to_division_id' => $to_division_id,
                'stage' => 'RDC',
                'status' => 'RDC Presentation Review',
                'notes' => $validated['notes'] ?? null,
                'referred_at' => now(),
            ]);
            $submission->save();

            return response()->json(['success' => true, 'id' => $referral->id]);
        }

        // Rule 7b: Revision Referral After RDC stage: Admin -> PDIPBD Staff
        if ($currentStage === 'rdc' && $currentStatus === 'revised' && $isAdmin && $isToPdipbdStaff) {
            $referral = Referral::create([
                'cipg_submission_id' => $submission->id,
                'from_user_id' => $user->id,
                'from_division_id' => $user->division_id,
                'to_user_id' => $to_user_id,
                'to_division_id' => $to_division_id,
                'stage' => 'RDC',
                'status' => 'For Revision Review',
                'notes' => $validated['notes'] ?? null,
                'referred_at' => now(),
            ]);
            $submission->save();

            return response()->json(['success' => true, 'id' => $referral->id]);
        }

        // Rule 8: Refer Evaluated PAR to PDIPBD: Admin -> PDIPBD Staff (PAR-based referral)
        if ($isAdmin && $isToPdipbdStaff) {
            $evaluatedPar = \App\Models\ProjectAssessmentReport::where('cpp_submission_id', $submission->id)
                ->where('status', 'Evaluated')
                ->first();

            if ($evaluatedPar) {
                // Check if this PAR has already been referred to PDIPBD
                $existingReferral = Referral::where('par_id', $evaluatedPar->id)
                    ->where('stage', 'Project Appraisal')
                    ->where('to_division_id', $to_division_id)
                    ->where('status', '!=', 'Rejected')
                    ->first();

                if ($existingReferral) {
                    return response()->json(['success' => false, 'message' => 'This PAR has already been referred to PDIPBD staff.'], 422);
                }

                $referral = Referral::create([
                    'cipg_submission_id' => $submission->id,
                    'par_id' => $evaluatedPar->id,
                    'from_user_id' => $user->id,
                    'from_division_id' => $user->division_id,
                    'to_user_id' => $to_user_id,
                    'to_division_id' => $to_division_id,
                    'stage' => 'Project Appraisal',
                    'status' => 'For Review',
                    'notes' => $validated['notes'] ?? null,
                    'referred_at' => now(),
                ]);

                return response()->json(['success' => true, 'id' => $referral->id]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Action not authorized or does not meet the current workflow conditions.'], 403);
    }

    public function assignStaff(Request $request, Referral $referral)
    {
        $validated = $request->validate([
            'to_user_id' => ['required', 'exists:users,id'],
        ]);

        $user = $request->user();
        $isDivisionHead = $user->hasRole('division_chief') || $user->hasRole('chief') || $user->hasRole('division_head');
        $staff = User::findOrFail($validated['to_user_id']);

        if (!$isDivisionHead || $staff->division_id !== $user->division_id) {
            return response()->json(['success' => false, 'message' => 'Action not authorized or staff is not in your division.'], 403);
        }

        $referral->to_user_id = $staff->id;
        $referral->status = 'Referred to Staff';
        $referral->save();

        return response()->json(['success' => true]);
    }

    public function destroy(Referral $referral)
    {
        $referral->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Handle PDIPBD staff Complete / Feedback actions from the CPP view page.
     */
    public function staffAction(Request $request, $submissionId)
    {
        $submission = CppSubmission::findOrFail($submissionId);
        $action = $request->input('action');
        $validStatuses = ['Review', 'Submitted', 'Resubmitted'];

        if ($action === 'complete') {
            if ($submission->stage !== 'Completeness Test and Validation' || !in_array($submission->status, $validStatuses)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'This submission is not eligible for completion at this stage.'], 400);
                }
                return redirect()->back()->with('error', 'This submission is not eligible for completion at this stage.');
            }

            $submission->stage = 'Completeness Test and Evaluation';
            $submission->status = 'Validated';
            $submission->save();

            $referral = Referral::where('cipg_submission_id', $submission->id)->latest()->first();
            if ($referral) {
                $referral->status = 'Validated';
                $referral->save();
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Submission marked as complete.']);
            }
            return redirect()->route('staff.dashboard')->with('success', "\"" . $submission->project_title . "\" has been marked as complete and forwarded for Test & Evaluation.");
        }

        if ($action === 'feedback') {
            if ($submission->stage !== 'Completeness Test and Validation' || !in_array($submission->status, $validStatuses)) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'This submission is not eligible for feedback at this stage.'], 400);
                }
                return redirect()->back()->with('error', 'This submission is not eligible for feedback at this stage.');
            }

            $request->validate(['notes' => 'required|string']);

            $referral = Referral::where('cipg_submission_id', $submission->id)->latest()->first();

            SubmissionFeedback::create([
                'cpp_submission_id' => $submission->id,
                'staff_id'          => $request->user()?->id,
                'notes'             => $request->input('notes'),
            ]);

            $submission->status = 'Incomplete';
            $submission->save();

            if ($referral) {
                $referral->status = 'Returned';
                $referral->save();
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Feedback sent for "' . $submission->project_title . '".']);
            }
            return redirect()->route('staff.dashboard')->with('success', "Feedback sent for \"" . $submission->project_title . "\".");
        }

        if ($action === 'sectoral') {
            $currentReferral = Referral::where('cipg_submission_id', $submission->id)
                ->whereIn('status', ['For Revision Review', 'For Sectoral Presentation'])
                ->latest()
                ->first();

            if (!$currentReferral) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'No sectoral referral is available for this submission.'], 400);
                }
                return redirect()->back()->with('error', 'No sectoral referral is available for this submission.');
            }

            $submission->update([
                'stage' => 'Sectoral Committee',
                'status' => 'Sectoral Presentation',
            ]);

            $currentReferral->update([
                'status' => 'For Sectoral Presentation',
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Submission moved to sectoral presentation.']);
            }

            return redirect()->route('staff.dashboard')->with('success', 'Submission moved to sectoral presentation.');
        }

        if ($action === 'rdc_presentation') {
            $currentReferral = Referral::where('cipg_submission_id', $submission->id)
                ->whereIn('status', ['For Revision Review', 'For Sectoral Presentation', 'Sectoral Presentation Review'])
                ->latest()
                ->first();

            if (!$currentReferral) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'No active sectoral referral found for this submission.'], 400);
                }
                return redirect()->back()->with('error', 'No active sectoral referral found for this submission.');
            }

            $submission->update([
                'stage' => 'RDC',
                'status' => 'RDC Presentation',
            ]);

            $currentReferral->update([
                'status' => 'For RDC Presentation',
                'resolved_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Submission moved to RDC presentation.']);
            }

            return redirect()->route('staff.dashboard')->with('success', 'Submission moved to RDC presentation.');
        }

        if ($action === 'rdc_approved') {
            $currentReferral = Referral::where('cipg_submission_id', $submission->id)
                ->whereIn('status', ['For RDC Presentation', 'RDC Presentation Review'])
                ->latest()
                ->first();

            if (!$currentReferral) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'No active RDC presentation referral found for this submission.'], 400);
                }
                return redirect()->back()->with('error', 'No active RDC presentation referral found for this submission.');
            }

            $submission->update([
                'stage' => 'RDC',
                'status' => 'RDC Approved',
            ]);

            $currentReferral->update([
                'status' => 'RDC Approved',
                'stage' => 'RDC',
                'resolved_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Submission marked as RDC Approved.']);
            }

            return redirect()->route('staff.dashboard')->with('success', 'Submission marked as RDC Approved.');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'Unknown action.'], 400);
        }
        return redirect()->back()->with('error', 'Unknown action.');
    }
    /**
     * Save comments/recommendations from the PDIPBD staff cpp-view (Sectoral & other stages).
     * Creates CommentAndRecommendation records and updates submission status to 'For Revision'.
     */
    public function saveComments(Request $request, $submissionId)
    {
        $submission = CppSubmission::findOrFail($submissionId);
        $user = $request->user();

        $request->validate([
            'findings'        => 'required|array|min:1',
            'findings.*'      => 'required|string',
            'recommendations' => 'required|array|min:1',
            'recommendations.*' => 'nullable|string',
        ]);

        $findings        = $request->input('findings', []);
        $recommendations = $request->input('recommendations', []);

        // Determine comment stage from submission (default to Sectoral Committee)
        $commentStage = $submission->stage ?: 'Sectoral Committee';

        foreach ($findings as $index => $findingText) {
            if (!empty(trim($findingText))) {
                CommentAndRecommendation::create([
                    'cpp_submission_id' => $submission->id,
                    'user_id'           => $user->id,
                    'finding'           => $findingText,
                    'recommendation'    => $recommendations[$index] ?? '',
                    'stage'             => $commentStage,
                    'status'            => 'Submitted',
                ]);
            }
        }

        // Update submission status to For Revision
        $submission->status = 'For Revision';
        $submission->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Comments saved. Submission returned for revision.']);
        }

        return redirect()->route('staff.dashboard')
            ->with('success', 'Comments submitted. "' . $submission->project_title . '" has been returned for revision.');
    }

    /**
     * PDIPBD Staff — Reject a referral from any workflow stage.
     * Contexts: par | revised | sectoral | rdc
     */
    public function reject(Request $request, $submissionId)
    {
        $request->validate([
            'context' => 'required|string|in:par,revised,sectoral,rdc,cte,division',
            'notes'   => 'nullable|string|max:1000',
        ]);

        $submission = CppSubmission::findOrFail($submissionId);
        $context    = $request->input('context');
        $notes      = $request->input('notes', '');

        // Find the active referral for the given context
        $baseQuery = Referral::where('cipg_submission_id', $submissionId)->whereNull('resolved_at')->latest();

        switch ($context) {
            case 'par':
                $referral = (clone $baseQuery)->where('stage', 'Project Appraisal')->first();
                break;
            case 'revised':
                $referral = (clone $baseQuery)->where('status', 'For Revision Review')->first();
                break;
            case 'sectoral':
                $referral = (clone $baseQuery)->where('status', 'Sectoral Presentation Review')->first();
                break;
            case 'rdc':
                $referral = (clone $baseQuery)->where('status', 'RDC Presentation Review')->first();
                break;
            case 'cte':
                $referral = (clone $baseQuery)->whereIn('stage', ['Completeness Test and Validation', 'Completeness Test'])->first();
                break;
            case 'division':
                $referral = (clone $baseQuery)->where('status', 'Referred to Division')->first();
                break;
            default:
                $referral = null;
        }

        if ($referral) {
            $rejecterName = auth()->check() ? auth()->user()->name : 'Staff';
            $existingNotes = $referral->notes ? $referral->notes . "\n" : '';
            $referral->update([
                'status'      => 'Rejected',
                'resolved_at' => now(),
                'notes'       => trim($existingNotes . '[Rejected by ' . $rejecterName . '] ' . $notes),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Referral rejected. \"{$submission->project_title}\" has been returned for revision.",
        ]);
    }
}
