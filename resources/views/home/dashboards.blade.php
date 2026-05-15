@extends('layouts.app_v2')

@section('content')
<link rel="stylesheet" href="/css/dashboards.css">
<section id="dashboards" class="page-content active container-fluid py-4">

    <!-- Header Banner -->
    <div class="mb-5 d-flex justify-content-between align-items-end">
        <div>
            <span class="badge fw-bold px-3 py-2 rounded-pill mb-2 badge-admin-portal">ADMIN PORTAL</span>
            <h2 class="fw-bold mb-0">Project <span class="text-primary">Evaluation and RDIP Inclusion</span></h2>
            <p class="text-muted small mb-0">Full access to all submissions, assessments, and workflow management.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <div class="text-muted small fw-medium text-uppercase mb-1 text-logged-in-label">Logged in as</div>
            <div id="admin-dash-username" class="fw-bold text-dark">—</div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Total Proposals -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-primary"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Total Proposals</p>
                            <h2 class="fw-bold mb-0 text-dark" id="admin-dash-proj-total">—</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2.5 rounded-3 text-primary">
                            <i data-lucide="layers" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- In Review -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-info"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">In Review</p>
                            <h2 class="fw-bold mb-0 text-dark" id="admin-dash-sub-review">—</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2.5 rounded-3 text-info">
                            <i data-lucide="eye" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Approved -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-success"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Approved</p>
                            <h2 class="fw-bold mb-0 text-dark" id="admin-dash-sub-approved">—</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2.5 rounded-3 text-success">
                            <i data-lucide="check-circle" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Rejected -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-danger"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Rejected</p>
                            <h2 class="fw-bold mb-0 text-dark" id="admin-dash-sub-rejected">—</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2.5 rounded-3 text-danger">
                            <i data-lucide="x-circle" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- For Revision -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-warning"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Revision</p>
                            <h2 class="fw-bold mb-0 text-dark" id="admin-dash-sub-revision">—</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2.5 rounded-3 text-warning">
                            <i data-lucide="edit-3" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── EVALUATION PROCESS & WORKFLOW ── -->
    <div class="card border-0 shadow-sm mb-5 rounded-20">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="accent-pip-primary"></div>
                    <span class="fw-bold text-dark section-title-workflow">Workflow Status</span>
                </div>
                <div class="small text-muted fw-medium">Click a stage to view analytics</div>
            </div>
            
            <div class="process-container mb-0">
                <div class="process-step step-1" onclick="toggleStepDetails(event, 'submission')">
                    <div class="step-content">
                        <span class="step-label">Submission</span>
                        <div class="step-meta">
                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                            <span class="step-count" id="admin-dash-initial-count">{{ $dashboardInitialCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                <div class="process-step step-2" onclick="toggleStepDetails(event, 'referred')">
                    <div class="step-content">
                        <span class="step-label">Referral to Division</span>
                        <div class="step-meta">
                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                            <span class="step-count" id="admin-dash-referred-count">38</span>
                        </div>
                    </div>
                </div>
                <div class="process-step step-3" onclick="toggleStepDetails(event, 'evaluation')">
                    <div class="step-content">
                        <span class="step-label">Project Assessment Report</span>
                        <div class="step-meta">
                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                            <span class="step-count" id="admin-dash-eval-count">{{ $dashboardEvaluationCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                <!--<div class="process-step step-4" onclick="toggleStepDetails(event, 'findings')">
                    <div class="step-content">
                        <span class="step-label">Comments & Recommendations</span>
                        <div class="step-meta">
                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                            <span class="step-count" id="admin-dash-findings-count">28</span>
                        </div>
                    </div>
                </div>-->
                <div class="process-step step-5" onclick="toggleStepDetails(event, 'revised')">
                    <div class="step-content">
                        <span class="step-label">Revised Submissions</span>
                        <div class="step-meta">
                            <i data-lucide="calendar" width="12" height="12"></i>
                            <span class="step-count" id="admin-dash-revised-count">15</span>
                        </div>
                    </div>
                </div>
                <div class="process-step step-6" onclick="toggleStepDetails(event, 'sectoral')">
                    <div class="step-content">
                        <span class="step-label">SecCom Presentation</span>
                        <div class="step-meta">
                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                            <span class="step-count" id="admin-dash-sectoral-count">44</span>
                        </div>
                    </div>
                </div>
                <div class="process-step step-7" onclick="toggleStepDetails(event, 'rdc-pres')">
                    <div class="step-content">
                        <span class="step-label">RDC Presentation</span>
                        <div class="step-meta">
                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                            <span class="step-count" id="admin-dash-rdc-pres-count">30</span>
                        </div>
                    </div>
                </div>
                <div class="process-step step-8" onclick="toggleStepDetails(event, 'approved')">
                    <div class="step-content">
                        <span class="step-label">RDC Approved</span>
                        <div class="step-meta">
                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                            <span class="step-count" id="admin-dash-approved-count">24</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expandable Content Panel — content is injected by initAdminDashboard via toggleStepDetails -->
            <div id="workflow-details-panel" class="workflow-details-panel shadow-sm">
                <div class="workflow-details-inner" data-active-stage="">
                    <!-- Populated dynamically by js/admin/admin-dashboard-loader.js -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── Refer to PDIPB Staff Modal ── -->
<div class="modal fade" id="referPdipbModal" tabindex="-1" aria-labelledby="referPdipbModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <!-- Accent bar -->
            <div style="height:5px; background:linear-gradient(90deg,#154A9A,#1e6fd9);"></div>
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm"
                        style="width:40px;height:40px;background:linear-gradient(135deg,#154A9A,#1e6fd9);">
                        <i data-lucide="send" width="18" class="text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="referPdipbModalLabel" style="font-size:1rem;">
                            Refer to PDIPB Staff
                        </h5>
                        <p class="mb-0 text-muted" style="font-size:0.75rem;">
                            Forward CPP submission for completeness review
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-4 pb-3 pt-3">
                <!-- Project info pill -->
                <div class="p-3 rounded-3 mb-3 d-flex align-items-start gap-3"
                    style="background:#f0f7ff; border:1px solid #bfdbfe;">
                    <i data-lucide="file-text" width="16" class="text-primary mt-1 flex-shrink-0"></i>
                    <div>
                        <div class="text-muted fw-bold text-uppercase mb-1" style="font-size:0.62rem;letter-spacing:.06em;">
                            Submission
                        </div>
                        <div class="fw-bold text-dark lh-sm" id="referPdipb-project-title" style="font-size:0.9rem;">—</div>
                        <div class="text-muted mt-1" id="referPdipb-agency" style="font-size:0.75rem;">—</div>
                    </div>
                </div>

                <!-- Info note -->
                <div class="d-flex align-items-start gap-2 mb-3 px-1">
                    <i data-lucide="info" width="14" class="text-primary mt-1 flex-shrink-0"></i>
                    <p class="small text-muted mb-0">
                        This will forward the CPP to PDIPB staff for <strong>Completeness Test &amp; Validation</strong>.
                        The submission will appear in their validation queue automatically.
                    </p>
                </div>

                <!-- PDIPB Staff Selector -->
                <div class="mb-3">
                    <label class="small fw-semibold text-dark mb-2 d-block">
                        Assign to PDIPB Staff <span class="text-danger">*</span>
                    </label>
                    <select id="referPdipbStaff" class="form-select border-0 rounded-3"
                        style="background:#f8fafc; font-size:0.85rem;" required>
                        <option value="">— Loading staff list… —</option>
                    </select>
                    <div id="referPdipbStaffErr" class="text-danger small mt-1" style="display:none;">
                        Please select a PDIPB staff member.
                    </div>
                </div>

                <!-- Optional notes -->
                <div class="mb-1">
                    <label class="small fw-semibold text-dark mb-2 d-block">
                        Additional Instructions <span class="text-muted fw-normal">(optional)</span>
                    </label>
                    <textarea id="referPdipbNotes" rows="3"
                        class="form-control border-0 rounded-3"
                        style="background:#f8fafc; resize:none; font-size:0.85rem;"
                        placeholder="Any specific areas to check or notes for the PDIPB reviewer…"></textarea>
                </div>

                <!-- Hidden submission ID -->
                <input type="hidden" id="referPdipbSid">
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-sm rounded-pill px-4 fw-medium"
                    style="background:#e2e8f0;color:#475569;border:none;" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-5 fw-semibold text-white"
                    id="confirmReferPdipbBtn"
                    style="background:linear-gradient(135deg,#154A9A,#1e6fd9); border:none;">
                    <i data-lucide="send" width="13" class="me-1"></i> Refer to PDIPB
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Attachments Modal -->
<div class="modal fade" id="attachmentsModal" tabindex="-1" aria-labelledby="attachmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <div class="modal-header border-0 pb-0"
                style="background: linear-gradient(135deg, #14532d 0%, #16a34a 100%); color:#fff; padding:1.5rem 1.75rem;">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="attachmentsModalLabel">
                        <i data-lucide="paperclip" width="18" class="me-2"></i>Submitted Attachments
                    </h5>
                    <p class="mb-0 small opacity-75" id="attachments-modal-project-title" style="font-size:0.8rem;"></p>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height:520px; overflow-y:auto; padding:1.5rem 1.75rem;">
                <div id="attachments-modal-list">
                    <!-- Injected by JS -->
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:1rem 1.75rem 1.5rem;">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Version History Modal -->
<div class="modal fade" id="versionHistoryModal" tabindex="-1" aria-labelledby="versionHistoryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
            <div class="modal-header border-0 pb-0"
                style="background: linear-gradient(135deg, #154A9A 0%, #1e6fd9 100%); color:#fff; padding:1.5rem 1.75rem;">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="versionHistoryModalLabel">
                        <i data-lucide="clock" width="18" class="me-2"></i>Submission Version History
                    </h5>
                    <p class="mb-0 small opacity-75" id="version-modal-project-title" style="font-size:0.8rem;"></p>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="version-history-list" style="max-height:420px; overflow-y:auto; padding:1.25rem 1.75rem;">
                    <!-- Injected by JS -->
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:1rem 1.75rem 1.5rem;">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
    </div>

    <!-- Workflow Stage Templates (Injected into JS) -->
    <div class="d-none" id="workflow-templates">
        <script type="text/html" id="workflow-template-submission">@include('partials.workflow.submission')</script>
        <script type="text/html" id="workflow-template-referred">@include('partials.workflow.referred')</script>
        <script type="text/html" id="workflow-template-evaluation">@include('partials.workflow.evaluation')</script>
        <script type="text/html" id="workflow-template-sectoral">@include('partials.workflow.sectoral')</script>
        <script type="text/html" id="workflow-template-findings">@include('partials.workflow.findings')</script>
        <script type="text/html" id="workflow-template-revised">@include('partials.workflow.revised')</script>
        <script type="text/html" id="workflow-template-rdc-pres">@include('partials.workflow.rdc-pres')</script>
        <script type="text/html" id="workflow-template-approved">@include('partials.workflow.approved')</script>
    </div>

@endsection

@section('scripts')
<script>
    window.__ADMIN_DASHBOARD_SUBMISSIONS__ = @json($dashboardSubmissions ?? []);
    window.__ADMIN_DASHBOARD_USERS__ = @json($dashboardPdipbStaff ?? []);
    window.__ADMIN_DASHBOARD_EVALUATED_PARS__ = @json($dashboardEvaluatedPars ?? []);
    window.__ADMIN_DASHBOARD_REVIEWED_PARS__ = @json($dashboardReviewedPars ?? []);
</script>
{{-- Cache-bust: sync server-injected data into localforage before any module loader runs.
     This ensures that clearing the DB table is immediately reflected in the dashboard
     even though other JS files read localforage directly. --}}
<script>
(async function bustLocalforageCache() {
    if (typeof localforage === 'undefined') return;
    try {
        // Always write the server payload (even empty []) so stale cache is evicted.
        await localforage.setItem('cpp_submissions', window.__ADMIN_DASHBOARD_SUBMISSIONS__ ?? []);
        // Clear derived caches so they are rebuilt from the fresh submission list.
        await localforage.removeItem('project_referrals');
        await localforage.removeItem('project_assessments');
    } catch (e) {
        console.warn('[CacheBust] localforage sync failed:', e);
    }
})();
</script>
<script type="module">
    import { initAdminDashboard } from '{{ asset('js/admin/admin-dashboard-loader.js') }}';
    import '{{ asset('js/common/ui-utils.js') }}';

    document.addEventListener('DOMContentLoaded', () => {
        initAdminDashboard();
    });
</script>
@endsection
