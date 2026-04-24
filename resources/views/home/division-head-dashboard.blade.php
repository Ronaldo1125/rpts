@extends('layouts.app_v2')

@section('content')
<link rel="stylesheet" href="/css/dashboards.css">
<section id="division-head-dashboard" class="page-content active container-fluid py-4">

    <!-- Welcome Banner -->
    <div class="mb-5 d-flex justify-content-between align-items-end">
        <div>
            <span class="badge fw-bold px-3 py-2 rounded-pill mb-2" style="font-size:0.7rem;letter-spacing:0.05em;background:#fef3c7;color:#92400e;">DIVISION HEAD PORTAL</span>
            <h2 class="fw-bold mb-0">Division <span class="text-primary">Command Center</span></h2>
            <p class="text-muted small mb-0">Oversee assessments, review comments and monitor division output.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <div class="text-muted small fw-medium text-uppercase mb-1" style="font-size:0.65rem;letter-spacing:0.05em;">Active Division</div>
            <div id="active-division-display" class="fw-bold text-dark">—</div>
        </div>
    </div>

    <!-- ── TOP KPI CARDS ── -->
    <div class="row g-4 mb-5">
    <!-- Assign Staff Modal (Reused from Referrals page) -->
    <div class="modal fade" id="dhAssignStaffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Assign Technical Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Select a staff member from your division to conduct the PAR assessment.</p>
                    <form id="dhAssignStaffForm">
                        <input type="hidden" name="referralId" id="dhAssignReferralId">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Technical Staff</label>
                            <select class="form-select bg-light border-0 py-2" name="staffName" id="dhAssignStaffSelect" required>
                                <option value="">-- Select Staff member --</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn text-white px-5 rounded-pill" id="dhConfirmAssignBtn" style="background-color: #154A9A;">
                        Confirm Assignment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Projects -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('manage-submissions')">
                <div class="card-accent-primary"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Total Projects</p>
                            <h2 class="fw-bold mb-0 text-dark" id="dh-dash-proj-total">—</h2>
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
                            <h2 class="fw-bold mb-0 text-dark" id="dh-dash-total-count">—</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded-3 text-info">
                            <i data-lucide="inbox" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- PAR Assessments -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('division-head-project-assessment')">
                <div class="card-accent-success"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">PAR Assessments</p>
                            <h2 class="fw-bold mb-0 text-dark" id="dh-dash-par-count">—</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                            <i data-lucide="file-text" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Division Referrals -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('division-head-referrals')">
                <div class="card-accent-warning"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Division Referrals</p>
                            <h2 class="fw-bold mb-0 text-dark" id="dh-dash-referral-count">—</h2>
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
                            <canvas id="dh-status-chart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-3 mt-4 mt-md-0">
                        <div id="dh-status-legend" class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(59, 130, 246, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#3b82f6;flex-shrink:0;box-shadow: 0 0 0 3px rgba(59,130,246,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Ongoing</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#3b82f6; color:#fff;" id="dh-dash-proj-ongoing">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(167, 139, 250, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#a78bfa;flex-shrink:0;box-shadow: 0 0 0 3px rgba(167,139,250,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Proposed</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#a78bfa; color:#fff;" id="dh-dash-proj-proposed">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(16, 185, 129, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#10b981;flex-shrink:0;box-shadow: 0 0 0 3px rgba(16,185,129,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Completed</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#10b981; color:#fff;" id="dh-dash-proj-completed">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(239, 68, 68, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#ef4444;flex-shrink:0;box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);"></div>
                                    <span class="small fw-semibold text-dark">Terminated</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#ef4444; color:#fff;" id="dh-dash-proj-terminated">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(245, 158, 11, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#f59e0b;flex-shrink:0;box-shadow: 0 0 0 3px rgba(245,158,11,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Suspended</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#f59e0b; color:#fff;" id="dh-dash-proj-suspended">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(148, 163, 184, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle" style="width:12px;height:12px;background:#94a3b8;flex-shrink:0;box-shadow: 0 0 0 3px rgba(148,163,184,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Dropped</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#94a3b8; color:#fff;" id="dh-dash-proj-dropped">0</span>
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
                    <canvas id="dh-submission-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- C&R Records with Progress -->
        <div class="col-md-4">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('division-head-comments-recommendations')">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                        <i data-lucide="message-circle" width="18" height="18"></i>
                    </div>
                    <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">C&amp;R Records</h6>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="small text-muted">Total Records Filed</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" id="dh-dash-cr-count">0</span>
                </div>
                <div class="progress" style="height:8px;border-radius:4px;">
                    <div class="progress-bar bg-primary" id="dh-cr-bar" role="progressbar" style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="mt-3 small text-muted">Comments and recommendations submitted through the division evaluation cycle.</div>
                <div class="mt-3">
                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary small px-3">View Records →</span>
                </div>
            </div>
        </div>

        <!-- Recent Referrals Table -->
        <div class="col-md-8">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                            <i data-lucide="share-2" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Division Referrals</h6>
                    </div>
                    <button class="btn btn-sm btn-link text-primary fw-bold small p-0 text-decoration-none" onclick="if(window.switchPage) window.switchPage('division-head-referrals')">Manage All</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 small text-muted fw-semibold">Project Title</th>
                                <th class="border-0 small text-muted fw-semibold">Agency</th>
                                <th class="border-0 small text-muted fw-semibold">Added</th>
                                <th class="border-0 small text-muted fw-semibold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dh-dash-referrals-tbody">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">Loading pending referrals...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <div class="row g-4 mb-5">
        <!-- Assessed Reports Table (Pending Approval) -->
        <div class="col-12">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100">
                <div class="px-2 py-2 d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                            <i data-lucide="file-check" width="18" height="18"></i>
                        </div>
                        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Assessed Reports (Awaiting Evaluation)</h6>
                    </div>
                    <button class="btn btn-sm btn-link text-primary fw-bold small p-0 text-decoration-none" onclick="if(window.switchPage) window.switchPage('division-head-project-assessment')">View All Assessments</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 small text-muted fw-semibold ps-4">Project Title</th>
                                <th class="border-0 small text-muted fw-semibold">Proponent</th>
                                <th class="border-0 small text-muted fw-semibold">Staff Assigned</th>
                                <th class="border-0 small text-muted fw-semibold">Date Assessed</th>
                                <th class="border-0 small text-muted fw-semibold text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="dh-dash-assessed-tbody">
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">Loading reports...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection