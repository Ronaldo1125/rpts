@extends('layouts.app_v2')

@section('content')
<link rel="stylesheet" href="/css/dashboards.css">
<section id="staff-dashboard" class="page-content active container-fluid py-4">

    <!-- Welcome Banner -->
    <div class="mb-5 d-flex justify-content-between align-items-end">
        <div>
            <span class="badge fw-bold px-3 py-2 rounded-pill mb-2" style="font-size:0.7rem;letter-spacing:0.05em;background:#e0f2fe;color:#075985;">STAFF PORTAL</span>
            <h2 class="fw-bold mb-0">RDC <span class="text-primary">Operations Center</span></h2>
            <p class="text-muted small mb-0">Track and manage project validations, assessments, and technical reviews.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <div class="text-muted small fw-medium text-uppercase mb-1" style="font-size:0.65rem;letter-spacing:0.05em;">Logged in as</div>
            <div id="staff-dash-username" class="fw-bold text-dark">—</div>
        </div>
    </div>

    <!-- ── TOP KPI CARDS ── -->
    <div class="row g-4 mb-5">
        <!-- Total Projects -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('manage-submissions')">
                <div class="card-accent-primary"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Total Projects</p>
                            <h2 class="fw-bold mb-0 text-dark" id="staff-dash-proj-total">—</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                            <i data-lucide="layers" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Submissions -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('manage-submissions')">
                <div class="card-accent-info"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Total Submissions</p>
                            <h2 class="fw-bold mb-0 text-dark" id="staff-dash-total-count">—</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                            <i data-lucide="inbox" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- My PAR Assessments -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('staff-project-assessment')">
                <div class="card-accent-success"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">My PAR Assessments</p>
                            <h2 class="fw-bold mb-0 text-dark" id="staff-dash-par-count">—</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                            <i data-lucide="file-text" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- My Assignments (ID added for conditional hiding) -->
        <div class="col-12 col-sm-6 col-xl-3" id="staff-assignments-kpi">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('staff-referrals')">
                <div class="card-accent-warning"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">My Assignments</p>
                            <h2 class="fw-bold mb-0 text-dark" id="staff-dash-referral-count">—</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                            <i data-lucide="share-2" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── CHARTS ROW 1: Project Status + Submission Pipeline ── -->
    <div class="row g-4 mb-5">
        <!-- Project Status Donut -->
        <div class="col-md-5">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 text-uppercase letter-spacing-05 small">Project Status Distribution</h6>
                    <span class="badge bg-light text-dark border small fw-normal">Status Breakdown</span>
                </div>
                <div class="row align-items-center g-0">
                    <div class="col-md-6">
                        <div style="height:220px; position:relative;">
                            <canvas id="staff-status-chart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-3 mt-4 mt-md-0">
                        <div id="staff-status-legend" class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(59, 130, 246, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#3b82f6;flex-shrink:0;box-shadow: 0 0 0 3px rgba(59,130,246,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Ongoing</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#3b82f6; color:#fff;" id="staff-dash-proj-ongoing">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(167, 139, 250, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#a78bfa;flex-shrink:0;box-shadow: 0 0 0 3px rgba(167,139,250,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Proposed</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#a78bfa; color:#fff;" id="staff-dash-proj-proposed">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(16, 185, 129, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#10b981;flex-shrink:0;box-shadow: 0 0 0 3px rgba(16,185,129,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Completed</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#10b981; color:#fff;" id="staff-dash-proj-completed">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(239, 68, 68, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#ef4444;flex-shrink:0;box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);"></div>
                                    <span class="small fw-semibold text-dark">Terminated</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#ef4444; color:#fff;" id="staff-dash-proj-terminated">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(245, 158, 11, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#f59e0b;flex-shrink:0;box-shadow: 0 0 0 3px rgba(245,158,11,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Suspended</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#f59e0b; color:#fff;" id="staff-dash-proj-suspended">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(148, 163, 184, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#94a3b8;flex-shrink:0;box-shadow: 0 0 0 3px rgba(148,163,184,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Dropped</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#94a3b8; color:#fff;" id="staff-dash-proj-dropped">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CIPG Submission Pipeline Bar Chart -->
        <div class="col-md-7">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 text-uppercase letter-spacing-05 small">CIPG Submission Pipeline</h6>
                    <span class="badge bg-success bg-opacity-10 text-success small">LIVE</span>
                </div>
                <div style="height: 240px;">
                    <canvas id="staff-submission-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- My C&R Records -->
        <div class="col-md-4">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('staff-comments-recommendations')">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                        <i data-lucide="message-circle" width="18" height="18"></i>
                    </div>
                    <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">My C&R Records</h6>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="small text-muted">Total Records Filed</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" id="staff-dash-cr-count">0</span>
                </div>
                <div class="progress" style="height:8px;border-radius:4px;">
                    <div class="progress-bar bg-primary" id="staff-cr-bar" role="progressbar" style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="mt-3 small text-muted">Comments and recommendations submitted through the evaluation cycle.</div>
                <div class="mt-3">
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary small px-3">View Records →</span>
                </div>
            </div>
        </div>

        <!-- PDIPBD Completeness Test Section (Visible only for PDIPBD staff) -->
        <div class="col-md-8" id="pdipb-dashboard-cte-section" style="display:none;">
            <div class="bg-white p-0 rounded-5 shadow-sm border h-100 overflow-hidden">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center" style="background: rgba(21, 74, 154, 0.02);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-navy bg-opacity-10 p-2 rounded-3 text-navy">
                            <i data-lucide="clipboard-check" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Completeness Test and Validation</h6>
                    </div>
                    <a href="#" class="small text-primary fw-bold text-decoration-none"></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-secondary small py-3">Project Title</th>
                                <th class="text-secondary small py-3">Agency</th>
                                <th class="text-secondary small py-3">Status</th>
                                <th class="text-secondary small py-3">Submitted</th>
                                <th class="text-end pe-4 text-secondary small py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard-cte-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">Loading pending validations...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <!-- ── PDIPBD STAFF: Referred Technical Reports (Filtered inside loader) ── -->
    <div class="row g-4 mt-2" id="pdipb-dashboard-par-section" style="display: none;">
        <div class="col-12">
            <div class="bg-white p-0 rounded-5 shadow-sm border overflow-hidden">
                <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-50">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                            <i data-lucide="file-check" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Technical Reports for Review</h6>
                    </div>
                    <a href="#" class="small text-primary fw-bold text-decoration-none" onclick="if(window.switchPage) window.switchPage('pdipbd-staff-project-assessment')">View All →</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-secondary small py-3">Project Title</th>
                                <th class="text-secondary small py-3">Agency</th>
                                <th class="text-secondary small py-3">Eval. Division</th>
                                <th class="text-secondary small py-3">Referred On</th>
                                <th class="text-end pe-4 text-secondary small py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard-par-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">Loading reports...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── PDIPBD STAFF: Revised Submissions (Referred by Admin) ── -->
    <div class="row g-4 mt-2" id="pdipb-dashboard-revision-section" style="display: none;">
        <div class="col-12">
            <div class="bg-white p-0 rounded-5 shadow-sm border overflow-hidden">
                <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center" style="background: rgba(245, 158, 11, 0.05);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                            <i data-lucide="rotate-ccw" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Revised Submissions (Referred by Admin)</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-secondary small py-3">Project Title</th>
                                <th class="text-secondary small py-3">Agency</th>
                                <th class="text-secondary small py-3">Stage</th>
                                <th class="text-secondary small py-3">Referred On</th>
                                <th class="text-end pe-4 text-secondary small py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard-revision-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">Loading revisions...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── PDIPBD STAFF: Sectoral Presentation Referrals (Admin Referred from SecCom Presentation) ── -->
    <div class="row g-4 mt-2" id="pdipb-dashboard-sectoral-presentation-section" style="display: none;">
        <div class="col-12">
            <div class="bg-white p-0 rounded-5 shadow-sm border overflow-hidden">
                <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center" style="background: rgba(59, 130, 246, 0.05);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                            <i data-lucide="presentation" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Sectoral Presentation Referrals</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-secondary small py-3">Project Title</th>
                                <th class="text-secondary small py-3">Agency</th>
                                <th class="text-secondary small py-3">Status</th>
                                <th class="text-secondary small py-3">Referred On</th>
                                <th class="text-end pe-4 text-secondary small py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard-sectoral-presentation-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">Loading sectoral referrals...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── PDIPBD STAFF: RDC Presentation Referrals (Admin Referred to RDC Presentation) ── -->
    <div class="row g-4 mt-2" id="pdipb-dashboard-rdc-presentation-section" style="display: none;">
        <div class="col-12">
            <div class="bg-white p-0 rounded-5 shadow-sm border overflow-hidden">
                <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center" style="background: rgba(153, 27, 27, 0.05);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger">
                            <i data-lucide="award" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">RDC Presentation Referrals</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-secondary small py-3">Project Title</th>
                                <th class="text-secondary small py-3">Agency</th>
                                <th class="text-secondary small py-3">Status</th>
                                <th class="text-secondary small py-3">Referred On</th>
                                <th class="text-end pe-4 text-secondary small py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard-rdc-presentation-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">Loading RDC presentation referrals...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ── PDIPBD STAFF: Reviewed Technical Reports (Ready for Finalization) ── -->
    <div class="row g-4 mt-2" id="pdipb-dashboard-reviewed-section" style="display: none;">
        <div class="col-12">
            <div class="bg-white p-0 rounded-5 shadow-sm border overflow-hidden">
                <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center" style="background: rgba(5, 150, 105, 0.05);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                            <i data-lucide="check-square" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Reviewed Technical Reports (Ready for Final)</h6>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-secondary small py-3">Project Title</th>
                                <th class="text-secondary small py-3">Agency</th>
                                <th class="text-secondary small py-3">Status</th>
                                <th class="text-secondary small py-3">Reviewed Date</th>
                                <th class="text-end pe-4 text-secondary small py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dashboard-reviewed-par-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">Loading reviewed reports...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- ── My Recent Assignments (ID added for conditional hiding) ── -->
    <div class="row g-4 mt-1 mb-5" id="staff-recent-assignments-section">
        <div class="col-12">
            <div class="bg-white p-4 rounded-5 shadow-sm border">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                            <i data-lucide="share-2" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">My Recent Assignments</h6>
                    </div>
                    <button class="btn btn-sm btn-link text-primary fw-bold small p-0 text-decoration-none" onclick="if(window.switchPage) window.switchPage('staff-referrals')">View All Assignments</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 small text-muted fw-semibold ps-4">Project Title</th>
                                <th class="border-0 small text-muted fw-semibold">Agency</th>
                                <th class="border-0 small text-muted fw-semibold">Assigned By</th>
                                <th class="border-0 small text-muted fw-semibold">Date</th>
                                <th class="border-0 small text-muted fw-semibold text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody id="staff-dash-referrals-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">Loading assignments...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection