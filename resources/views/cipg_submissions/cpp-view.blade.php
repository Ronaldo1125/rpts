@extends('layouts.app_v2')
@section('content')
@php
    // Helper closures to simplify blade rendering, matching the JS 'g' functions
    $g = function($key) use ($d) {
        return (isset($d[$key]) && $d[$key] !== null && $d[$key] !== '') ? (string) $d[$key] : '—';
    };
    $gSig = function($key) use ($d) {
        return (isset($d[$key]) && $d[$key] !== null) ? $d[$key] : '';
    };
    $renderCheckboxGroup = function($options, $activeValues) {
        if (!is_array($activeValues)) {
            $activeValues = $activeValues ? [$activeValues] : [];
        }
        $html = '';
        foreach($options as $opt) {
            $checked = in_array($opt, $activeValues) ? '✓' : '';
            $html .= '<div class="d-flex align-items-center gap-2 mb-1">
                <div style="width:14px; height:14px; border:1px solid #000; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold; background:#fff;">
                    '.$checked.'
                </div>
                <span style="font-size:0.72rem;">'.$opt.'</span>
            </div>';
        }
        return $html;
    };
@endphp

@php
    $isPdipbdStaff = in_array(Auth::user()->roles->first()?->name, ['staff', 'division_head']);
    $isCompletenessTest = $submission->stage === 'Completeness Test and Validation' && !in_array($submission->status, ['Incomplete', 'Validated']);

    // Revision context: project revised/returned BEFORE reaching Sectoral Committee or RDC
    $isRevisionContext = in_array(strtolower($submission->status), ['revised', 'for revision review', 'for revision'])
        && !in_array($submission->stage, ['Sectoral Committee', 'RDC']);

    // Sectoral context: already in Sectoral Committee stage (any status)
    $isSectoralContext = $submission->stage === 'Sectoral Committee'
        && !in_array($submission->status, ['RDC Presentation', 'RDC Approved']);

    // RDC context: project is in the RDC stage (any status including For Revision)
    $isRdcContext = $submission->stage === 'RDC'
        && !in_array($submission->status, ['RDC Approved']);

    // Allow URL override (legacy/testing)
    $isRevisionContext = $isRevisionContext || request()->query('view_context') === 'pdipbd_revision';
    $isSectoralContext = $isSectoralContext || request()->query('view_context') === 'pdipbd_sectoral';
    $isRdcContext      = $isRdcContext      || request()->query('view_context') === 'pdipbd_rdc';

    $hasPdipbdActions = $isPdipbdStaff && ($isCompletenessTest || $isRevisionContext || $isSectoralContext || $isRdcContext);
@endphp

<section id="cpp-view" class="page-content active container-fluid py-4">

    {{-- ── Floating Action Panel (PDIPBD Staff only) ── --}}
    @if($hasPdipbdActions)
    <div class="no-print" id="cppViewStaffActions"
         style="position:fixed;right:32px;bottom:32px;z-index:9999;display:flex;flex-direction:column;gap:12px;">

        @if($submission->stage === 'Completeness Test and Validation')
        <button type="button" data-bs-toggle="modal" data-bs-target="#feedbackModal"
                style="background:#ef4444;color:#fff;border:none;font-size:0.85rem;padding:0.9rem 1.5rem;border-radius:14px;font-weight:700;box-shadow:0 6px 20px rgba(239,68,68,0.3);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);display:flex;align-items:center;gap:0.5rem;cursor:pointer;white-space:nowrap;width:100%;"
                onmouseover="this.style.transform='translateY(-3px)';this.style.filter='brightness(1.1)'"
                onmouseout="this.style.transform='';this.style.filter=''">
            <i data-lucide="message-circle" width="16"></i> Send Feedback
        </button>

        <form method="POST" action="{{ route('referrals.staffAction', $submission->id) }}" style="display:inline;">
            @csrf
            <input type="hidden" name="action" value="complete">
            <button type="submit"
                    style="background:#16a34a;color:#fff;border:none;font-size:0.85rem;padding:0.9rem 1.5rem;border-radius:14px;font-weight:700;box-shadow:0 6px 20px rgba(22,163,74,0.3);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);display:flex;align-items:center;gap:0.5rem;cursor:pointer;white-space:nowrap;width:100%;"
                    onmouseover="this.style.transform='translateY(-3px)';this.style.filter='brightness(1.1)'"
                    onmouseout="this.style.transform='';this.style.filter=''">
                <i data-lucide="check-circle" width="16"></i> Mark Complete
            </button>
        </form>
        @endif

        {{-- Sectoral Presentation Button --}}
        @if($isRevisionContext)
        <button type="button" data-bs-toggle="modal" data-bs-target="#sectoralPresentationModal"
                style="background:#7c3aed;color:#fff;border:none;font-size:0.85rem;padding:0.9rem 1.5rem;border-radius:14px;font-weight:700;box-shadow:0 6px 20px rgba(124,58,237,0.3);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);display:flex;align-items:center;gap:0.5rem;cursor:pointer;white-space:nowrap;"
                onmouseover="this.style.transform='translateY(-3px)';this.style.filter='brightness(1.1)'"
                onmouseout="this.style.transform='';this.style.filter=''">
            <i data-lucide="presentation" width="16"></i> Sectoral Presentation
        </button>

        @endif

        {{-- RDC Presentation Button (for Sectoral context) --}}
        @if($isSectoralContext)
        <form method="POST" action="{{ route('referrals.staffAction', $submission->id) }}" style="display:inline;">
            @csrf
            <input type="hidden" name="action" value="rdc_presentation">
            <button type="submit"
                    style="background:#2563eb;color:#fff;border:none;font-size:0.85rem;padding:0.9rem 1.5rem;border-radius:14px;font-weight:700;box-shadow:0 6px 20px rgba(37,99,235,0.3);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);display:flex;align-items:center;gap:0.5rem;cursor:pointer;white-space:nowrap;width:100%;"
                    onmouseover="this.style.transform='translateY(-3px)';this.style.filter='brightness(1.1)'"
                    onmouseout="this.style.transform='';this.style.filter=''">
                <i data-lucide="arrow-right-circle" width="16"></i> RDC Presentation
            </button>
        </form>
        @endif

        {{-- RDC Approved Button (for RDC context) --}}
        @if($isRdcContext)
        <form method="POST" action="{{ route('referrals.staffAction', $submission->id) }}" style="display:inline;">
            @csrf
            <input type="hidden" name="action" value="rdc_approved">
            <button type="submit"
                    style="background:#16a34a;color:#fff;border:none;font-size:0.85rem;padding:0.9rem 1.5rem;border-radius:14px;font-weight:700;box-shadow:0 6px 20px rgba(22,163,74,0.3);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);display:flex;align-items:center;gap:0.5rem;cursor:pointer;white-space:nowrap;width:100%;"
                    onmouseover="this.style.transform='translateY(-3px)';this.style.filter='brightness(1.1)'"
                    onmouseout="this.style.transform='';this.style.filter=''">
                <i data-lucide="check-circle" width="16"></i> RDC Approved
            </button>
        </form>
        @endif

        @if($isRevisionContext || $isSectoralContext || $isRdcContext)
        <button type="button" data-bs-toggle="modal" data-bs-target="#commentsModal" class="btn-status-save"
            style="background:#154A9A;color:#fff;border:none;font-size:0.85rem;padding:0.9rem 1.5rem;border-radius:14px;font-weight:700;box-shadow:0 6px 20px rgba(21,74,154,0.3);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);display:flex;align-items:center;gap:0.5rem;cursor:pointer;white-space:nowrap;"
                onmouseover="this.style.transform='translateY(-3px)';this.style.filter='brightness(1.1)'"
                onmouseout="this.style.transform='';this.style.filter=''">
            <i data-lucide="message-square" width="16"></i> Comments
        </button>
        @endif

        <button type="button" data-bs-toggle="modal" data-bs-target="#attachmentsModal"
                style="background:#059669;color:#fff;border:none;font-size:0.85rem;padding:0.9rem 1.5rem;border-radius:14px;font-weight:700;box-shadow:0 6px 20px rgba(5,150,105,0.3);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);display:flex;align-items:center;gap:0.5rem;cursor:pointer;white-space:nowrap;"
                onmouseover="this.style.transform='translateY(-3px)';this.style.filter='brightness(1.1)'"
                onmouseout="this.style.transform='';this.style.filter=''">
            <i data-lucide="paperclip" width="16"></i> Attachments
        </button>

    </div>
    @endif
    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <h2 class="fw-bold mb-0" id="cpp-view-title" style="font-size:1.3rem;">
                {{ $submission->status === 'Draft' ? 'Draft Project Profile' : 'Project Profile View' }}
            </h2>
            <p class="text-muted small mb-0">Detailed information for the selected submission.</p>
        </div>
        <div class="d-flex gap-2" id="cpp-view-actions">
            @if($submission->status !== 'Draft')
            <button class="btn btn-primary shadow-sm btn-sm px-4 rounded-pill me-2" onclick="window.print()" title="Use the browser's native 'Save as PDF' via Print for 100% accurate formatting">
                <i data-lucide="printer" width="16" class="me-1"></i> Print / Save PDF
            </button>
            @else
            <a href="{{ route('v2.cipg_submissions.edit', $submission->id) }}" class="btn btn-primary btn-sm px-4 rounded-pill me-1">
                <i data-lucide="edit-3" width="16" class="me-1"></i> Edit Draft
            </a>
            @endif


            <a href="{{ url()->previous() == url()->current() ? route('v2.cipg_submissions.index') : url()->previous() }}" class="btn btn-outline-secondary btn-sm px-4 rounded-pill">
                <i data-lucide="chevron-left" width="16" class="me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Feedback Modal --}}
    @if($isCompletenessTest)
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
                <div class="modal-header border-0" style="background:#ef4444;color:#fff;padding:1.25rem 1.5rem;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-2"
                            style="width:32px;height:32px;background:rgba(255,255,255,0.15);">
                            <i data-lucide="message-circle" width="15" class="text-white"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" style="font-size:0.95rem;">Send Feedback</h6>
                            <p class="mb-0 text-white opacity-75" style="font-size:0.72rem;">Submission will be returned to agency for revision</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="px-4 py-3" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                        <div class="text-muted fw-bold text-uppercase mb-1" style="font-size:0.65rem;letter-spacing:.06em;">Submission</div>
                        <div class="fw-bold text-dark lh-sm" style="font-size:0.875rem;">{{ $submission->project_title }}</div>
                    </div>
                    <div class="px-4 pt-3 pb-4">
                        <label class="small fw-semibold text-dark mb-2 d-block">
                            Feedback / Notes for Revision <span class="text-danger">*</span>
                        </label>
                        <textarea id="pdipb-cpp-feedback-text" rows="5"
                            class="form-control border-0 rounded-3"
                            style="background:#f1f5f9;resize:none;font-size:0.85rem;"
                            placeholder="Describe what needs to be revised, corrected, or clarified..."></textarea>
                        <div id="pdipb-cpp-feedback-err" class="text-danger small mt-1" style="display:none;">
                            Please enter feedback before sending.
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top d-flex gap-2 justify-content-end"
                    style="padding:0.875rem 1.5rem;background:#f8fafc;">
                    <button type="button" class="btn btn-sm rounded-pill px-4 fw-medium"
                        style="background:#e2e8f0;color:#475569;border:none;" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm rounded-pill px-4 fw-semibold text-white"
                        id="pdipb-cpp-feedback-send" style="background:#ef4444;color:#fff;border:none;">
                        <i data-lucide="send" width="13" class="me-1"></i>Send Feedback
                    </button>
                </div>
                {{-- Hidden form to submit feedback via POST --}}
                <form id="pdipb-cpp-feedback-form" method="POST" action="{{ route('referrals.staffAction', $submission->id) }}" style="display:none;">
                    @csrf
                    <input type="hidden" name="action" value="feedback">
                    <input type="hidden" name="notes" id="pdipb-cpp-feedback-notes">
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Sectoral Presentation Modal --}}
    <div class="modal fade" id="sectoralPresentationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
                <div class="modal-header border-0" style="background:#7c3aed;color:#fff;padding:1.25rem 1.5rem;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-2"
                            style="width:32px;height:32px;background:rgba(255,255,255,0.15);">
                            <i data-lucide="presentation" width="15" class="text-white"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" style="font-size:0.95rem;">Sectoral Presentation</h6>
                            <p class="mb-0 text-white opacity-75" style="font-size:0.72rem;">Mark submission for sectoral presentation</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted mb-0" style="font-size:0.85rem;">
                        This submission is ready for <strong>Sectoral Presentation</strong>. Click the button below to proceed.
                    </p>
                </div>
                <div class="modal-footer border-top d-flex gap-2 justify-content-end"
                    style="padding:0.875rem 1.5rem;background:#f8fafc;">
                    <button type="button" class="btn btn-sm rounded-pill px-4 fw-medium"
                        style="background:#e2e8f0;color:#475569;border:none;" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm rounded-pill px-4 fw-semibold text-white"
                        style="background:#7c3aed;border:none;" onclick="document.getElementById('pdipb-sectoral-form').submit();">
                        <i data-lucide="check" width="13" class="me-1"></i>Proceed
                    </button>
                </div>
                <form id="pdipb-sectoral-form" method="POST" action="{{ route('referrals.staffAction', $submission->id) }}" style="display:none;">
                    @csrf
                    <input type="hidden" name="action" value="sectoral">
                </form>
            </div>
        </div>
    </div>

    {{-- Comments Modal --}}
    <div class="modal fade" id="commentsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow card-16">
                <div class="header-accent-blue" style="height: 6px; background: #154A9A;"></div>
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">Findings &amp; Recommendations</h5>
                        <p class="text-muted small mb-0">Record technical observations and required actions for this submission.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="commentsForm" method="POST" action="{{ route('referrals.saveComments', $submission->id) }}">
                    @csrf
                <div class="modal-body p-4">
                    <div id="findingsContainer" class="d-flex flex-column gap-3">
                        <!-- Default Row (Mandatory) -->
                        <div class="finding-row border rounded-3 p-3 bg-light position-relative">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary mb-1">Finding / Observation</label>
                                    <textarea class="form-control form-control-sm" name="findings[]" rows="2" placeholder="Describe what was found..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary mb-1">Recommendation / Action Required</label>
                                    <textarea class="form-control form-control-sm" name="recommendations[]" rows="2" placeholder="Describe the recommended action..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4" id="btnAddFindingRow">
                            <i data-lucide="plus-circle" width="16" class="me-2"></i> Add New Finding
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center">
                    <div class="text-muted small" id="findingsDateStatus"></div>
                    <div>
                        <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" id="btnSaveFindings" style="background:#154A9A;border-color:#154A9A;">
                            <i data-lucide="check-circle" width="18" class="me-2"></i> Submit
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Attachments Modal --}}
    <div class="modal fade" id="attachmentsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:600px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
                <div class="modal-header border-0" style="background:#059669;color:#fff;padding:1.25rem 1.5rem;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-2"
                            style="width:32px;height:32px;background:rgba(255,255,255,0.15);">
                            <i data-lucide="paperclip" width="15" class="text-white"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0" style="font-size:0.95rem;">Attachments</h6>
                            <p class="mb-0 text-white opacity-75" style="font-size:0.72rem;">View all submission attachments</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="attachments-list">
                        @php
                            $typeMap = [
                                'letter' => 'Letter Request Document',
                                'bor' => 'BOR/BOT Resolution Document',
                                'sp' => 'SP Resolution Document',
                                'sb' => 'SB Resolution Document',
                                'ded' => 'Detailed Engineering Design',
                                'env' => 'Environmental Clearance',
                                'hgdg' => 'HGDG Document',
                                'consult' => 'Public Consultation Documentation',
                                'geo_photo' => 'Geotagged Photo',
                                'spatial_cov' => 'Spatial Coverage File',
                                'sig_prep' => 'Signature (Prepared By)',
                                'sig_noted' => 'Signature (Noted By)',
                                'other' => 'Other Attachment'
                            ];

                            $normalizeType = function($a) {
                                $rawType = trim($a['type'] ?? $a['label'] ?? '');
                                if ($rawType && !in_array($rawType, ['other', ''])) return $rawType;
                                $fileName = $a['name'] ?? '';
                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $map = [
                                    'pdf' => 'PDF Document', 'doc' => 'Word Document', 'docx' => 'Word Document',
                                    'xls' => 'Excel Spreadsheet', 'xlsx' => 'Excel Spreadsheet', 'csv' => 'CSV File',
                                    'jpg' => 'Image', 'jpeg' => 'Image', 'png' => 'Image', 'gif' => 'Image',
                                    'zip' => 'Compressed Archive', 'rar' => 'Compressed Archive',
                                ];
                                return $map[$ext] ?? 'Other Attachment';
                            };
                        @endphp

                        @if(isset($d['attachments']) && count($d['attachments']) > 0)
                            <div class="d-flex flex-column gap-2">
                                @foreach($d['attachments'] as $attachment)
                                    @php
                                        $label = trim($attachment['label'] ?? '');
                                        $rawType = trim($attachment['type'] ?? '');
                                        // Priority: 1. Mapped Label, 2. User-defined Label, 3. Normalized Filename
                                        $displayLabel = $typeMap[$rawType] ?? ($label ?: $normalizeType($attachment));
                                    @endphp
                                    <a href="{{ $attachment['url'] }}" target="_blank" rel="noopener noreferrer"
                                       class="list-group-item list-group-item-action rounded-3 border d-block mb-2"
                                       style="border-color:#e2e8f0 !important; transition: all 0.2s; padding: 1rem;">
                                        <div class="d-flex align-items-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" 
                                                     style="width:40px; height:40px; background:rgba(21, 74, 154, 0.08); color:#154A9A;">
                                                    <i data-lucide="file-text" width="18"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-1 fw-bold text-dark" style="font-size:0.92rem; line-height:1.2;">{{ $attachment['name'] }}</p>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <span class="badge rounded-pill" style="background:#f1f5f9; color:#475569; font-size:0.68rem; font-weight:600; border:1px solid #e2e8f0;">
                                                            {{ $displayLabel }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold" style="font-size:0.75rem;">Open</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3 text-muted opacity-25">
                                    <i data-lucide="paperclip" width="48"></i>
                                </div>
                                <p class="text-muted small mb-0">No attachments for this submission</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-top d-flex gap-2 justify-content-end"
                    style="padding:0.875rem 1.5rem;background:#f8fafc;">
                    <button type="button" class="btn btn-sm rounded-pill px-4 fw-medium"
                        style="background:#e2e8f0;color:#475569;border:none;" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="cpp-view-container">
        <!-- Render from JS original layout -->
        <div id="printable-cpp" class="animate__animated animate__fadeIn" style="max-width:950px; margin: 0 auto; color:#000; font-family:'Times New Roman', serif;">
            
            <!-- PAGE 1: SECTIONS I, II, III -->
            <div class="page-container" style="background:#fff; padding:3rem; box-shadow:0 0 40px rgba(0,0,0,0.1); margin-bottom:2rem; position:relative; min-height:1050px;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:60px; height:60px; border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:5px; background:#fff;">
                            <img src="{{ asset('assets/images/rdc.png') }}" style="max-width:100%; max-height:100%;" alt="RDC Logo" onerror="this.style.display='none'">
                        </div>
                        <div style="width:60px; height:60px; border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:5px; background:#fff;">
                            <img src="{{ asset('assets/images/rnp.png') }}" style="max-width:100%; max-height:100%;" alt="RNP Logo" onerror="this.style.display='none'">
                        </div>
                        <div>
                            <p class="mb-0 fw-bold" style="font-size:0.75rem;">REPUBLIC OF THE PHILIPPINES</p>
                            <p class="mb-0 fw-bold" style="font-size:0.75rem; color:#108543;">REGIONAL DEVELOPMENT COUNCIL</p>
                            <p class="mb-0 fw-bold" style="font-size:0.75rem; color:#154A9A;">BICOL REGION</p>
                        </div>
                    </div>
                    <div class="text-end" style="font-size:0.6rem; color:#475569; line-height:1.4;">
                        <p class="mb-0">FM-PDI-01 | CPP Form | Revision No. 01</p>
                        <p class="mb-0">Effectivity Date: August 1, 2025</p>
                        <p class="mb-0 mt-3 fw-bold" style="font-size:0.7rem;">Annex C</p>
                        <p class="mb-0 mt-1">Submission ID: <span class="text-dark fw-bold">{{ $submission->id }}</span></p>
                    </div>
                </div>

                <div style="background:#154A9A; color:#fff; text-align:center; font-weight:bold; padding:6px; margin-top:20px;">
                    COMPREHENSIVE PROJECT PROFILE
                </div>

                <div class="row g-0 border-top border-bottom border-dark mt-2">
                    <div class="col-12 border-bottom border-dark p-2 d-flex align-items-center gap-2">
                        <span class="fw-bold" style="font-size:0.7rem; width:50px;">Agency:</span>
                        <div style="border:1px solid #000; font-size:0.82rem; min-height:20px; padding-left:4px; flex-grow:1; font-weight:bold;">{!! $g('f-agency') !!}</div>
                    </div>
                    <div class="col-6 border-end border-dark p-2 d-flex align-items-center gap-2">
                        <span class="fw-bold" style="font-size:0.7rem; width:45px;">Sector:</span>
                        <div style="border:1px solid #000; font-size:0.82rem; min-height:20px; padding-left:4px; flex-grow:1;">{{ $submission->sector->sector_name ?? $g('f-sector') }}</div>
                    </div>
                    <div class="col-6 p-2 d-flex align-items-center gap-2">
                        <span class="fw-bold" style="font-size:0.7rem; width:65px;">Sub-Sector:</span>
                        <div style="border:1px solid #000; font-size:0.82rem; min-height:20px; padding-left:4px; flex-grow:1;">{!! $g('f-sub-sector') !!}</div>
                    </div>
                </div>

                <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">I. PROJECT INFORMATION</div>
                <div class="ps-3">
                    <div class="mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">1. Project Title:</span>
                        <div style="border:1px solid #000; font-size:0.85rem; min-height:40px; padding:4px; line-height:1.2; font-weight:bold; background:#f8fafc;">{!! nl2br(e($g('f-title'))) !!}</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">2. Project Type:</span>
                            <div class="ms-2">
                                {!! $renderCheckboxGroup(['Capital Outlay', 'Technical Assistance'], $d['project-type'] ?? []) !!}
                            </div>
                        </div>
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">3. Project Components:</span>
                            <div style="border:1px solid #000; font-size:0.78rem; min-height:50px; padding:4px;">{!! nl2br(e($g('f-components'))) !!}</div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">4. Project Location:</span>
                        @if($g('project-coverage') === 'Regionwide')
                            <div class="row gx-1 mt-1 border p-1" style="background:#fdfdfd;">
                                <div class="col-12 d-flex align-items-center gap-1">
                                    <span style="font-size:0.6rem; width:100px; text-align:right;">Coverage:</span>
                                    <div style="border-bottom:1px solid #000; font-size:0.75rem; flex-grow:1; font-weight:bold;">
                                        Regionwide
                                    </div>
                                </div>
                            </div>
                        @elseif($g('project-coverage') === 'Inter-Province')
                            <div class="row gx-1 mt-1 border p-1" style="background:#fdfdfd;">
                                <div class="col-12 d-flex align-items-center gap-1">
                                    <span style="font-size:0.6rem; width:100px; text-align:right;">Inter-Province:</span>
                                    <div style="border-bottom:1px solid #000; font-size:0.75rem; flex-grow:1; font-weight:bold;">
                                        {{ $submission->locations->map(fn($l) => $l->province->province_name ?? '')->filter()->implode(', ') ?: (isset($d['f-provinces']) ? str_replace('||', ', ', $d['f-provinces']) : '') }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row gx-1 mt-1 border p-1" style="background:#fdfdfd;">
                                <div class="col-3 d-flex align-items-center gap-1">
                                    <span style="font-size:0.6rem; width:45px; text-align:right;">Province:</span>
                                    <div style="border-bottom:1px solid #000; font-size:0.75rem; flex-grow:1; font-weight:bold;">
                                        {{ $submission->locations->first()?->province?->province_name ?? (isset($d['f-provinces']) ? str_replace('||', ', ', $d['f-provinces']) : $g('f-province')) }}
                                    </div>
                                </div>
                                <div class="col-3 d-flex align-items-center gap-1">
                                    <span style="font-size:0.6rem; width:45px; text-align:right;">District:</span>
                                    <div style="border-bottom:1px solid #000; font-size:0.75rem; flex-grow:1;">{!! $g('f-district') !!}</div>
                                </div>
                                <div class="col-3 d-flex align-items-center gap-1">
                                    <span style="font-size:0.6rem; width:55px; text-align:right;">Municipality:</span>
                                    <div style="border-bottom:1px solid #000; font-size:0.75rem; flex-grow:1;">
                                        {{ $submission->locations->first()?->municipality?->municipality_name ?? $g('f-municipality') }}
                                    </div>
                                </div>
                                <div class="col-3 d-flex align-items-center gap-1">
                                    <span style="font-size:0.6rem; width:45px; text-align:right;">Barangay:</span>
                                    <div style="border-bottom:1px solid #000; font-size:0.75rem; flex-grow:1;">{!! $g('f-barangay') !!}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div style="font-weight:bold; font-size:0.85rem; margin-top:20px; margin-bottom:5px;">II. PROJECT STATUS</div>
                <div class="ps-3">
                    <div class="row g-0">
                        <div class="col-4">
                            {!! $renderCheckboxGroup(['Ongoing', 'Pipeline', 'Proposed'], $d['project-status'] ?? []) !!}
                        </div>
                        <div class="col-8">
                            <p class="mb-1" style="font-size:0.65rem; font-weight:bold;">Preparatory Works:</p>
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:14px; height:14px; border:1px solid #000; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">{!! !empty($d['prep-site']) ? '✓' : '' !!}</div>
                                    <span style="font-size:0.65rem;">Site is readily available</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:14px; height:14px; border:1px solid #000; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">{!! !empty($d['prep-row']) ? '✓' : '' !!}</div>
                                    <span style="font-size:0.65rem;">No issue on right-of-way</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:14px; height:14px; border:1px solid #000; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">{!! !empty($d['prep-ded']) ? '✓' : '' !!}</div>
                                    <span style="font-size:0.65rem;">Detailed Engineering Design was prepared</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="font-weight:bold; font-size:0.85rem; margin-top:20px; margin-bottom:5px;">III. ENDORSEMENTS</div>
                <div class="ps-3 mb-3">
                    <table class="table table-bordered table-sm mb-0" style="border-color:#000 !important; font-size:0.75rem;">
                        <thead>
                            <tr class="text-center" style="background:#f8fafc;">
                                <th style="width:40%; border-color:#000 !important;">Resolution / Letter</th>
                                <th style="width:30%; border-color:#000 !important;">Reference No.</th>
                                <th style="width:30%; border-color:#000 !important;">Date of Issuance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td style="border-color:#000 !important;">Sangguniang Panlalawigan</td><td style="border-color:#000 !important;">{!! $g('f-sp-res') !!}</td><td style="border-color:#000 !important;">{!! $g('f-sp-date') !!}</td></tr>
                            <tr><td style="border-color:#000 !important;">Sangguniang Bayan</td><td style="border-color:#000 !important;">{!! $g('f-sb-res') !!}</td><td style="border-color:#000 !important;">{!! $g('f-sb-date') !!}</td></tr>
                            <tr><td style="border-color:#000 !important;">Letter Request to SP / SB</td><td style="border-color:#000 !important;">{!! $g('f-letter-req') !!}</td><td style="border-color:#000 !important;">{!! $g('f-letter-date') !!}</td></tr>
                            <tr><td style="border-color:#000 !important;">BOR / BOT Resolution</td><td style="border-color:#000 !important;">{!! $g('f-bor-res') !!}</td><td style="border-color:#000 !important;">{!! $g('f-bor-date') !!}</td></tr>
                        </tbody>
                    </table>
                </div>

                <div style="position:absolute; bottom:15px; right:30px; font-size:0.6rem; color:#64748b;">Page 1 of 3</div>
            </div>

            <!-- PAGE 2: SECTIONS IV, V, VI, VII -->
            <div class="page-container" style="background:#fff; padding:3rem; box-shadow:0 0 40px rgba(0,0,0,0.1); margin-bottom:2rem; position:relative; min-height:1050px;">
                <div style="font-weight:bold; font-size:0.85rem; margin-bottom:5px;">IV. PROJECT JUSTIFICATION</div>
                <div class="ps-3 mb-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">Alignment to SDGs 2030:</span>
                            <div style="border:1px solid #000; font-size:0.65rem; min-height:30px; padding:2px; background:#fdfdfd;">{!! $g('f-alignment') !!}</div>
                        </div>
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">Alignment to RDP 2023-2028:</span>
                            <div style="border:1px solid #000; font-size:0.65rem; min-height:30px; padding:2px; background:#fdfdfd;">{!! $g('f-rdp-alignment') !!}</div>
                        </div>
                    </div>
                    <div class="mt-2 mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">1. Background / Demand for the Project:</span>
                        <div style="border:1px solid #000; font-size:0.7rem; min-height:60px; padding:4px; text-align:justify;">{!! nl2br(e($g('f-background'))) !!}</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">2. Goal:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-goal'))) !!}</div>
                        </div>
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">3. Purpose:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-purpose'))) !!}</div>
                        </div>
                        <div class="col-12 mt-1">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">4. Project Output/s:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-outputs'))) !!}</div>
                        </div>
                        <div class="col-6 mt-1">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">5. Project Activities:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-activities'))) !!}</div>
                        </div>
                        <div class="col-6 mt-1">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">6. Project Linkages:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-linkages'))) !!}</div>
                        </div>
                    </div>
                </div>

                <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">V. PROJECT FINANCING</div>
                <div class="ps-3 mb-3">
                    <div class="mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">1. Funding Requirement (PHP):</span>
                        @php
                            $totalCost = (float)($d['f-total-cost'] ?? 0);
                        @endphp
                        <div style="border:1px solid #000; font-size:0.82rem; min-height:20px; padding:4px; font-weight:bold;">₱ {{ number_format($totalCost, 2) }}</div>
                    </div>
                    <div class="row g-2">
                        <div class="col-7">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">2. Project Financing / Source of Fund:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! $g('f-funding-source') !!}</div>
                        </div>
                        <div class="col-5">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">3. Counterpart Funding:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! $g('f-counterpart-funding') !!}</div>
                        </div>
                    </div>
                </div>

                <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">VI. PROJECT BENEFITS AND COSTS</div>
                <div class="ps-3 mb-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">1. Beneficiaries:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:30px; padding:4px;">{!! $g('f-beneficiaries') !!}</div>
                        </div>
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">2. Social Benefits:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-social-benefits'))) !!}</div>
                        </div>
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">3. Economic Benefits:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-economic-benefits'))) !!}</div>
                        </div>
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">4. Social Costs:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-social-costs'))) !!}</div>
                        </div>
                        <div class="col-6">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">5. Economic Costs:</span>
                            <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-economic-costs'))) !!}</div>
                        </div>
                    </div>
                </div>

                <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">VII. PROJECT IMPLEMENTATION</div>
                <div class="ps-3 mb-3">
                    <div class="mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">1. Agencies Involved:</span>
                        <div style="border:1px solid #000; font-size:0.75rem; min-height:20px; padding:4px;">{!! $g('f-agencies-involved') !!}</div>
                    </div>
                    <div class="mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">2. Implementation Schedule:</span>
                        <table class="table table-bordered table-sm mb-0" style="border-color:#000 !important; font-size:0.68rem;">
                            <thead>
                                <tr class="text-center" style="background:#f0f5ff;">
                                    <th style="width:12%; border-color:#000 !important;">Year</th>
                                    <th style="width:48%; border-color:#000 !important;">Physical Target</th>
                                    <th style="width:20%; border-color:#000 !important;">Indicator</th>
                                    <th style="width:20%; border-color:#000 !important;">Amount (₱)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rows = is_array($d['_impl_schedule'] ?? null) ? $d['_impl_schedule'] : [];
                                @endphp
                                @if(count($rows) === 0)
                                    <tr><td colspan="4" class="text-center" style="border-color:#000 !important;">—</td></tr>
                                @else
                                    @foreach($rows as $r)
                                        <tr>
                                            <td style="border-color:#000 !important; text-align:center;">{{ $r['year'] ?? '—' }}</td>
                                            <td style="border-color:#000 !important; padding-left:4px;">{{ $r['target'] ?? '—' }}</td>
                                            <td style="border-color:#000 !important; text-align:center;">{{ $r['indicator'] ?? '—' }}</td>
                                            <td style="border-color:#000 !important; text-align:right; padding-right:4px;">{{ isset($r['amount']) && $r['amount'] !== '' ? number_format((float)$r['amount'], 2) : '—' }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div style="position:absolute; bottom:15px; right:30px; font-size:0.6rem; color:#64748b;">Page 2 of 3</div>
            </div>

            <!-- PAGE 3: SECTIONS VII(cont), VIII, IX, X, Signatures -->
            <div class="page-container" style="background:#fff; padding:3rem; box-shadow:0 0 40px rgba(0,0,0,0.1); margin-bottom:2rem; position:relative; min-height:1050px;">
                <div class="ps-3 mb-4">
                    <div class="mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">3. Implementation Arrangement:</span>
                        <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-impl-arrangement'))) !!}</div>
                    </div>
                    <div class="mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">4. Environmental Clearance:</span>
                        <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px;">{!! nl2br(e($g('f-env-clearance-desc'))) !!}</div>
                    </div>
                    <div class="mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">5. Social Acceptability / Public Consultations:</span>
                        <div style="border:1px solid #000; font-size:0.7rem; min-height:40px; padding:4px; background:#f9fafb;">
                            <strong>Status:</strong> {!! $g('consultation-status') !!}<br>
                            @if(($d['consultation-status'] ?? '') === 'Yes')
                                <strong>Dates:</strong> {!! $g('f-consult-done-dates') !!}
                            @else
                                <strong>Planned:</strong> {!! $g('f-consult-planned-date') !!}
                            @endif
                            <br>
                            <strong>Highlights:</strong> {!! nl2br(e($g('f-consult-highlights'))) !!}
                        </div>
                    </div>
                    <div class="mb-2">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">5.1 HGDG Score/Criteria:</span>
                        <div style="border:1px solid #000; font-size:0.7rem; min-height:20px; padding:4px;">{!! $g('f-hgdg') !!}</div>
                    </div>
                </div>

                <div style="font-weight:bold; font-size:0.85rem; margin-bottom:5px;">VIII. PROJECT LOGICAL FRAMEWORK</div>
                <div class="ps-1 mb-4" style="overflow-x:auto;">
                    <table class="table table-bordered table-sm mb-0" style="border-color:#000 !important; font-size:0.65rem; table-layout:fixed; width:100%;">
                        <thead class="text-center">
                            <tr style="background:#f8fafc;">
                                <th style="width:12%; border-color:#000 !important;">Hierarchy</th>
                                <th style="width:22%; border-color:#000 !important;">Narrative Summary</th>
                                <th style="width:22%; border-color:#000 !important;">Obj. Verifiable Indicators</th>
                                <th style="width:22%; border-color:#000 !important;">Means of Verification</th>
                                <th style="width:22%; border-color:#000 !important;">Assumptions / Risks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="fw-bold text-center" style="border-color:#000 !important;">Goal</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-goal-narrative'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-goal-indicators'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-goal-verification'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-goal-assumptions'))) !!}</td></tr>
                            <tr><td class="fw-bold text-center" style="border-color:#000 !important;">Purpose</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-purpose-narrative'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-purpose-indicators'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-purpose-verification'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-purpose-assumptions'))) !!}</td></tr>
                            <tr><td class="fw-bold text-center" style="border-color:#000 !important;">Outputs</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-outputs-narrative'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-outputs-indicators'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-outputs-verification'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-outputs-assumptions'))) !!}</td></tr>
                            <tr><td class="fw-bold text-center" style="border-color:#000 !important;">Inputs</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-inputs-narrative'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-inputs-indicators'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-inputs-verification'))) !!}</td><td style="border-color:#000 !important; padding:2px;">{!! nl2br(e($g('lf-inputs-assumptions'))) !!}</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-6">
                        <div style="font-weight:bold; font-size:0.85rem; margin-bottom:5px;">IX. GEOTAGGED PHOTO / MAP</div>
                        <div style="min-height:180px; background:#f8fafc; border:1px dashed #000; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding: 5px;">
                            @if($gSig('geo-photo-data') || ($d['geo-photo-url'] ?? null))
                                <img src="{{ $gSig('geo-photo-data') ?: $d['geo-photo-url'] }}" style="max-width:100%; max-height:170px; object-fit:contain;">
                            @else
                                <i data-lucide="map" width="30" class="mb-1 text-muted"></i>
                                <span class="text-muted" style="font-size:0.6rem;">No Photo Provided</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="font-weight:bold; font-size:0.85rem; margin-bottom:5px;">X. GEOLOCATION COORDINATES</div>
                        <div class="row g-2">
                            <div class="col-12 border p-2" style="background:#fdfdfd;">
                                <span style="font-size:0.7rem; display:block; font-weight:bold;">Beginning:</span>
                                <div style="font-size:0.75rem; font-family:monospace; padding-left:10px;">{!! $g('f-geo-start-lat') !!}, {!! $g('f-geo-start-lng') !!}</div>
                            </div>
                            <div class="col-12 border p-2 mt-2" style="background:#fdfdfd;">
                                <span style="font-size:0.7rem; display:block; font-weight:bold;">End:</span>
                                <div style="font-size:0.75rem; font-family:monospace; padding-left:10px;">{!! $g('f-geo-end-lat') ?: 'N/A' !!}, {!! $g('f-geo-end-lng') ?: 'N/A' !!}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <div class="row gx-5">
                        <div class="col-6">
                            <div style="background:#154A9A; color:#fff; padding:3px 12px; font-size:0.75rem; font-weight:bold;">Prepared by:</div>
                            <div class="text-center mt-3">
                                @if($gSig('sig-prep-data'))
                                    <img src="{!! $gSig('sig-prep-data') !!}" style="max-height:60px; max-width:180px; display:block; margin:0 auto 8px auto;">
                                @else
                                    <div style="height:50px;"></div>
                                @endif
                                <p class="mb-0 fw-bold" style="border-bottom:2px solid #000; display:inline-block; padding:0 1.5rem 2px 1.5rem; font-size:0.9rem;">{!! strtoupper($g('f-prepared-name') !== '—' ? $g('f-prepared-name') : $g('f-prep-name')) !!}</p>
                                <p class="mb-0 small" style="font-size:0.7rem;">{!! $g('f-prepared-pos') !== '—' ? $g('f-prepared-pos') : $g('f-prep-position') !!}</p>
                                <p class="mb-0 small mt-1" style="font-size:0.65rem; color:#334155;">{!! $g('f-prepared-date') !== '—' ? $g('f-prepared-date') : $g('f-prep-date') !!}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div style="background:#154A9A; color:#fff; padding:3px 12px; font-size:0.75rem; font-weight:bold;">Noted by:</div>
                            <div class="text-center mt-3">
                                @if($gSig('sig-noted-data'))
                                    <img src="{!! $gSig('sig-noted-data') !!}" style="max-height:60px; max-width:180px; display:block; margin:0 auto 8px auto;">
                                @else
                                    <div style="height:50px;"></div>
                                @endif
                                <p class="mb-0 fw-bold" style="border-bottom:2px solid #000; display:inline-block; padding:0 1.5rem 2px 1.5rem; font-size:0.9rem;">{!! strtoupper($g('f-noted-name')) !!}</p>
                                <p class="mb-0 small" style="font-size:0.7rem;">{!! $g('f-noted-pos') !== '—' ? $g('f-noted-pos') : $g('f-noted-position') !!}</p>
                                <p class="mb-0 small mt-1" style="font-size:0.65rem; color:#334155;">{!! $g('f-noted-date') !!}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="position:absolute; bottom:15px; right:30px; font-size:0.6rem; color:#64748b;">Page 3 of 3 — Viewed via RPTS Portals</div>
            </div>

        </div>
    </div>
</section>

<style>
    @media print {
        @page { margin: 0.5in; size: letter portrait; }
        body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
        .no-print, .btn-back-dash, #sidebarMenu, .topnav-container, .navbar { display: none !important; }
        #cpp-view { padding: 0 !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        #printable-cpp { 
            padding: 0 !important; 
            margin: 0 !important; 
            width: 100% !important; 
            box-shadow: none !important; 
            border: none !important; 
            background: #fff !important;
            font-size: 11pt !important;
        }
        .card { border: none !important; box-shadow: none !important; }
        /* Ensure tables look clean in print */
        .table { border-collapse: collapse !important; width: 100% !important; }
        .table td, .table th { border: 1px solid #000 !important; padding: 4px 8px !important; }
        .bg-light { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sendBtn = document.getElementById('pdipb-cpp-feedback-send');
        const feedbackText = document.getElementById('pdipb-cpp-feedback-text');
        const errorMsg = document.getElementById('pdipb-cpp-feedback-err');
        const hiddenNotes = document.getElementById('pdipb-cpp-feedback-notes');
        const feedbackForm = document.getElementById('pdipb-cpp-feedback-form');

        if (sendBtn && feedbackForm) {
            sendBtn.addEventListener('click', function() {
                const notes = feedbackText.value.trim();
                
                if (!notes) {
                    errorMsg.style.display = 'block';
                    feedbackText.classList.add('is-invalid');
                    return;
                }

                // Clear error state
                errorMsg.style.display = 'none';
                feedbackText.classList.remove('is-invalid');

                // Set hidden notes and submit
                hiddenNotes.value = notes;
                
                // Update UI state
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
                
                feedbackForm.submit();
            });

            // Clear error on input
            feedbackText.addEventListener('input', function() {
                errorMsg.style.display = 'none';
                feedbackText.classList.remove('is-invalid');
            });
        }

        // Comments submission
        window.submitComments = function() {
            const commentsText = document.getElementById('pdipb-comments-text');
            const commentsErr = document.getElementById('pdipb-comments-err');
            const comments = commentsText.value.trim();
            
            if (!comments) {
                commentsErr.style.display = 'block';
                commentsText.classList.add('is-invalid');
                return;
            }

            // Clear error
            commentsErr.style.display = 'none';
            commentsText.classList.remove('is-invalid');

            // Here you can implement the logic to save comments
            if (window.showSimpleAlert) {
                window.showSimpleAlert('Comment submitted successfully!', 'success');
            } else {
                alert('Comment submitted: ' + comments);
            }
            
            // Clear and close the modal
            commentsText.value = '';
            const modal = bootstrap.Modal.getInstance(document.getElementById('commentsModal'));
            if (modal) modal.hide();
        };

        // Findings & Recommendations functionality
        let findingRowCount = 1;

        window.addFindingRow = function() {
            const container = document.getElementById('findingsContainer');
            const rowNum = ++findingRowCount;
            const newRow = document.createElement('div');
            newRow.className = 'finding-row border rounded-3 p-3 bg-light position-relative';
            newRow.innerHTML = `
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-remove-finding" style="font-size: 0.7rem;"></button>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-secondary mb-1">Finding / Observation</label>
                        <textarea class="form-control form-control-sm" name="findings[]" rows="2" placeholder="Describe what was found..." required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-secondary mb-1">Recommendation / Action Required</label>
                        <textarea class="form-control form-control-sm" name="recommendations[]" rows="2" placeholder="Describe the recommended action..." required></textarea>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            attachRemoveFindingHandler(newRow.querySelector('.btn-remove-finding'));
            if (window.lucide) window.lucide.createIcons();
        };

        function attachRemoveFindingHandler(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('.finding-row').remove();
            });
        }

        // Attach handler to initial remove buttons
        document.querySelectorAll('.btn-remove-finding').forEach(btn => {
            attachRemoveFindingHandler(btn);
        });

        // Add Finding Row button
        const btnAddFinding = document.getElementById('btnAddFindingRow');
        if (btnAddFinding) {
            btnAddFinding.addEventListener('click', window.addFindingRow);
        }

        // Save Findings button — submits the real form
        const btnSaveFindings = document.getElementById('btnSaveFindings');
        const commentsForm    = document.getElementById('commentsForm');
        if (btnSaveFindings && commentsForm) {
            // btnSaveFindings is now type="submit" inside the form, no extra handler needed.
            // Just guard against empty first finding:
            commentsForm.addEventListener('submit', function(e) {
                const firstFinding = commentsForm.querySelector('textarea[name="findings[]"]');
                if (firstFinding && !firstFinding.value.trim()) {
                    e.preventDefault();
                    firstFinding.classList.add('is-invalid');
                    firstFinding.focus();
                    return;
                }
                btnSaveFindings.disabled = true;
                btnSaveFindings.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
            });
        }
    });
</script>
@endsection
