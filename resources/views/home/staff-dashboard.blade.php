@extends('layouts.app_v2')

@section('content')
    <link rel="stylesheet" href="/css/dashboards.css">
    <section id="staff-dashboard" class="page-content active container-fluid py-4">

        <!-- Welcome Banner -->
        <div class="mb-5 d-flex justify-content-between align-items-end">
            <div>
                @if(auth()->user()->division && strtoupper(auth()->user()->division->name) === 'PDIPBD')
                    <span class="badge fw-bold px-3 py-2 rounded-pill mb-2"
                        style="font-size:0.7rem;letter-spacing:0.05em;background:#fff1f2;color:#9f1239;">PDIPBD STAFF
                        PORTAL</span>
                @else
                    <span class="badge fw-bold px-3 py-2 rounded-pill mb-2"
                        style="font-size:0.7rem;letter-spacing:0.05em;background:#e0f2fe;color:#075985;">STAFF PORTAL</span>
                @endif
                <h2 class="fw-bold mb-0">RDC <span class="text-primary">Operations Center</span></h2>
                <p class="text-muted small mb-0">Track and manage project validations, assessments, and technical reviews.
                </p>
            </div>
            <div class="text-end d-none d-md-block">
                <div class="text-muted small fw-medium text-uppercase mb-1"
                    style="font-size:0.65rem;letter-spacing:0.05em;">Logged in as</div>
                <div id="staff-dash-username" class="fw-bold text-dark">—</div>
            </div>
        </div>

        @php
            $isPdipbd = auth()->user()->division && strtoupper(auth()->user()->division->name) === 'PDIPBD';
        @endphp

        @if($isPdipbd)
            <!-- ── TOP KPI CARDS (PDIPBD / ADMIN VIEW) ── -->
            <div class="row g-4 mb-5">
                <!-- RDIP Projects -->
                <div class="col-12 col-sm-6 col-xl">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('manage-submissions')">
                        <div class="card-accent-secondary"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">RDIP Projects</p>
                                    <h2 class="fw-bold mb-0 text-dark">{{ number_format($totalMasterProjects ?? 0) }}</h2>
                                </div>
                                <div class="bg-secondary bg-opacity-10 p-2.5 rounded-3 text-secondary">
                                    <i data-lucide="database" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Submissions -->
                <div class="col-12 col-sm-6 col-xl">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('manage-submissions')">
                        <div class="card-accent-primary"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Total Submissions</p>
                                    <h2 class="fw-bold mb-0 text-dark">{{ number_format($totalSubmissions ?? 0) }}</h2>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-2.5 rounded-3 text-primary">
                                    <i data-lucide="file-text" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Investment Pipeline -->
                <div class="col-12 col-sm-6 col-xl">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                        <div class="card-accent-success"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Investment Pipeline</p>
                                    <h2 class="fw-bold mb-0 text-dark">{{ $totalMasterInvestment ?? '₱0.00' }}</h2>
                                </div>
                                <div class="bg-success bg-opacity-10 p-2.5 rounded-3 text-success">
                                    <i data-lucide="pie-chart" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Ongoing Projects -->
                <div class="col-12 col-sm-6 col-xl">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                        <div class="card-accent-warning"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Ongoing Projects</p>
                                    <h2 class="fw-bold mb-0 text-dark">{{ number_format($ongoingMasterProjects ?? 0) }}</h2>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2.5 rounded-3 text-warning">
                                    <i data-lucide="activity" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Completed Projects -->
                <div class="col-12 col-sm-6 col-xl">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                        <div class="card-accent-info"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Completed Projects</p>
                                    <h2 class="fw-bold mb-0 text-dark">{{ number_format($completedMasterProjects ?? 0) }}</h2>
                                </div>
                                <div class="bg-info bg-opacity-10 p-2.5 rounded-3 text-info">
                                    <i data-lucide="check-square" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- ── TOP KPI CARDS (REGULAR STAFF PIPELINE) ── -->
            <div class="row g-4 mb-5">
                <!-- RDIP Projects -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('manage-submissions')">
                        <div class="card-accent-secondary"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">RDIP Projects</p>
                                    <h2 class="fw-bold mb-0 text-dark">{{ number_format($totalMasterProjects ?? 0) }}</h2>
                                </div>
                                <div class="bg-secondary bg-opacity-10 p-2.5 rounded-3 text-secondary">
                                    <i data-lucide="database" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Submissions -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('manage-submissions')">
                        <div class="card-accent-primary"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Total Submissions</p>
                                    <h2 class="fw-bold mb-0 text-dark">{{ number_format($totalSubmissions ?? 0) }}</h2>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-2.5 rounded-3 text-primary">
                                    <i data-lucide="file-text" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Pending Assignments -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('staff-referrals')">
                        <div class="card-accent-warning"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">Pending Assignments</p>
                                    <h2 class="fw-bold mb-0 text-dark" id="staff-dash-referral-count">—</h2>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                                    <i data-lucide="inbox" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Completed Assessments -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20" style="cursor:pointer;" onclick="if(window.switchPage) window.switchPage('staff-project-assessment')">
                        <div class="card-accent-success"></div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05">My PAR Assessments</p>
                                    <h2 class="fw-bold mb-0 text-dark">{{ number_format($totalStaffPars ?? 0) }}</h2>
                                </div>
                                <div class="bg-success bg-opacity-10 p-2 rounded-3 text-success">
                                    <i data-lucide="check-circle" width="24" height="24"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @php
            $isPdipbd = auth()->user()->division && strtoupper(auth()->user()->division->name) === 'PDIPBD';
        @endphp

        @if(!$isPdipbd)
            <!-- ── CHARTS ROW: Project Status + Submission Pipeline (Visible for non-PDIPBD staff) ── -->
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
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                        style="background: rgba(59, 130, 246, 0.05);">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle"
                                                style="width:12px;height:12px;background:#3b82f6;flex-shrink:0;box-shadow: 0 0 0 3px rgba(59,130,246,0.15);">
                                            </div>
                                            <span class="small fw-semibold text-dark">Ongoing</span>
                                        </div>
                                        <span class="badge rounded-pill fw-bold" style="background:#3b82f6; color:#fff;"
                                            id="staff-dash-proj-ongoing">0</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                        style="background: rgba(167, 139, 250, 0.05);">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle"
                                                style="width:12px;height:12px;background:#a78bfa;flex-shrink:0;box-shadow: 0 0 0 3px rgba(167,139,250,0.15);">
                                            </div>
                                            <span class="small fw-semibold text-dark">Proposed</span>
                                        </div>
                                        <span class="badge rounded-pill fw-bold" style="background:#a78bfa; color:#fff;"
                                            id="staff-dash-proj-proposed">0</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                        style="background: rgba(16, 185, 129, 0.05);">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle"
                                                style="width:12px;height:12px;background:#10b981;flex-shrink:0;box-shadow: 0 0 0 3px rgba(16,185,129,0.15);">
                                            </div>
                                            <span class="small fw-semibold text-dark">Completed</span>
                                        </div>
                                        <span class="badge rounded-pill fw-bold" style="background:#10b981; color:#fff;"
                                            id="staff-dash-proj-completed">0</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                        style="background: rgba(239, 68, 68, 0.05);">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle"
                                                style="width:12px;height:12px;background:#ef4444;flex-shrink:0;box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);">
                                            </div>
                                            <span class="small fw-semibold text-dark">Terminated</span>
                                        </div>
                                        <span class="badge rounded-pill fw-bold" style="background:#ef4444; color:#fff;"
                                            id="staff-dash-proj-terminated">0</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                        style="background: rgba(245, 158, 11, 0.05);">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle"
                                                style="width:12px;height:12px;background:#f59e0b;flex-shrink:0;box-shadow: 0 0 0 3px rgba(245,158,11,0.15);">
                                            </div>
                                            <span class="small fw-semibold text-dark">Suspended</span>
                                        </div>
                                        <span class="badge rounded-pill fw-bold" style="background:#f59e0b; color:#fff;"
                                            id="staff-dash-proj-suspended">0</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3"
                                        style="background: rgba(148, 163, 184, 0.05);">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle"
                                                style="width:12px;height:12px;background:#94a3b8;flex-shrink:0;box-shadow: 0 0 0 3px rgba(148,163,184,0.15);">
                                            </div>
                                            <span class="small fw-semibold text-dark">Dropped</span>
                                        </div>
                                        <span class="badge rounded-pill fw-bold" style="background:#94a3b8; color:#fff;"
                                            id="staff-dash-proj-dropped">0</span>
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
        @endif

        @if($isPdipbd)
            <!-- ── PDIPBD STAFF: CIPG Evaluation Workflow Pipeline ── -->
            <div class="row g-4 mt-2 px-0" id="pdipb-dashboard-pipeline-section">
                <div class="col-12">

                    <div class="card border-0 shadow-sm rounded-20">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="accent-pip-primary"></div>
                                    <span class="fw-bold text-dark section-title-workflow">CIPG Evaluation Workflow</span>
                                </div>
                                <div class="small text-muted fw-medium">Click a stage to view analytics</div>
                            </div>
                            <div class="process-container mb-0">
                                <div class="process-step step-1" onclick="toggleStepDetails(event, 'submission')">
                                    <div class="step-content">
                                        <span class="step-label">Submission</span>
                                        <div class="step-meta">
                                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                                            <span class="step-count" id="staff-dash-initial-count">—</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-step step-2" onclick="toggleStepDetails(event, 'referred')">
                                    <div class="step-content">
                                        <span class="step-label">Referral to Division</span>
                                        <div class="step-meta">
                                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                                            <span class="step-count" id="staff-dash-referred-count">—</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-step step-3" onclick="toggleStepDetails(event, 'evaluation')">
                                    <div class="step-content">
                                        <span class="step-label">Project Assessment Report</span>
                                        <div class="step-meta">
                                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                                            <span class="step-count" id="staff-dash-eval-count">—</span>
                                        </div>
                                    </div>
                                </div>
                                <!--<div class="process-step step-4" onclick="toggleStepDetails(event, 'findings')">
                                    <div class="step-content">
                                        <span class="step-label">Comments &amp; Recommendations</span>
                                        <div class="step-meta">
                                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                                            <span class="step-count" id="staff-dash-findings-count">—</span>
                                        </div>
                                    </div>
                                </div>-->
                                <div class="process-step step-5" onclick="toggleStepDetails(event, 'revised')">
                                    <div class="step-content">
                                        <span class="step-label">Revised Submissions</span>
                                        <div class="step-meta">
                                            <i data-lucide="calendar" width="12" height="12"></i>
                                            <span class="step-count" id="staff-dash-revised-count">—</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-step step-6" onclick="toggleStepDetails(event, 'sectoral')">
                                    <div class="step-content">
                                        <span class="step-label">SecCom Presentation</span>
                                        <div class="step-meta">
                                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                                            <span class="step-count" id="staff-dash-sectoral-count">—</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-step step-7" onclick="toggleStepDetails(event, 'rdc-pres')">
                                    <div class="step-content">
                                        <span class="step-label">RDC Presentation</span>
                                        <div class="step-meta">
                                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                                            <span class="step-count" id="staff-dash-rdc-pres-count">—</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="process-step step-8" onclick="toggleStepDetails(event, 'approved')">
                                    <div class="step-content">
                                        <span class="step-label">RDC Approved</span>
                                        <div class="step-meta">
                                            <i data-lucide="bar-chart-3" width="12" height="12"></i>
                                            <span class="step-count" id="staff-dash-approved-count">—</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Expandable Details Panel — injected by toggleStepDetails / initStageCharts -->
                            <div id="workflow-details-panel" class="workflow-details-panel shadow-sm">
                                <div class="workflow-details-inner" data-active-stage="">
                                    <!-- Populated dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        @endif

        @if(!$isPdipbd)
            <!-- ── My Recent Assignments (Visible for non-PDIPBD staff) ── -->
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
                            <button class="btn btn-sm btn-link text-primary fw-bold small p-0 text-decoration-none"
                                onclick="if(window.switchPage) window.switchPage('staff-referrals')">View All
                                Assignments</button>
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
                                    @forelse($myAssignments as $assignment)
                                        <tr>
                                            <td class="fw-medium text-dark ps-4 py-3" style="max-width:240px;">
                                                <div class="text-truncate" title="{{ $assignment->submission?->project_title }}">
                                                    {{ $assignment->submission?->project_title }}
                                                </div>
                                            </td>
                                            <td class="text-muted small py-3">
                                                {{ $assignment->submission?->user?->agency?->agency_name ?? '—' }}
                                            </td>
                                            <td class="text-muted small py-3">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span
                                                        class="text-dark fw-medium">{{ $assignment->fromUser?->name ?? 'Division Head' }}</span>
                                                    <span class="text-muted"
                                                        style="font-size:0.7rem;">({{ $assignment->fromDivision?->name ?? '—' }})</span>
                                                </div>
                                            </td>
                                            <td class="text-muted small py-3">{{ $assignment->referred_at->format('M d, Y') }}</td>
                                            <td class="text-end pe-4 py-3">
                                                <a href="{{ route('project-assessment-reports.create', ['submission_id' => $assignment->cipg_submission_id, 'referral_id' => $assignment->id]) }}"
                                                    class="btn btn-sm btn-primary rounded-pill px-3 fw-bold staff-start-par-btn"
                                                    style="font-size:0.7rem; background:#154A9A; border:none;">
                                                    Start PAR
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted small">No active assignments found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
    </section>

@endsection

@section('scripts')
    <script>
        window.__ADMIN_DASHBOARD_SUBMISSIONS__ = @json($dashboardSubmissions ?? []);
        window.__ADMIN_DASHBOARD_USERS__ = @json($dashboardPdipbStaff ?? []);
        window.__STAFF_ASSIGNMENTS__ = @json($myAssignments ?? []);
        window.__EVALUATED_REPORTS__ = @json($evaluatedReports ?? []);
        window.__REVIEWED_REPORTS__ = @json($reviewedReports ?? []);
        window.__ADMIN_DASHBOARD_EVALUATED_PARS__ = @json($evaluatedReports ?? []);
        window.__MASTER_PROJECT_STATUSES__ = @json($masterProjectStatuses ?? []);
        window.__SUBMISSION_PIPELINE_COUNTS__ = @json($submissionPipelineCounts ?? []);
    </script>
    <script type="module">
        import { initStaffDashboard } from '{{ asset('js/staff/staff-loader.js') }}';
        import '{{ asset('js/common/ui-utils.js') }}';

        document.addEventListener('DOMContentLoaded', () => {
            initStaffDashboard();
        });
    </script>
@endsection