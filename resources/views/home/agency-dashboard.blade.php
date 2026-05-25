@extends('layouts.app_v2')

@section('content')
<section id="agency-dashboard" class="page-content active container-fluid py-4">
    <div class="mb-5">
        <h2 class="fw-bold mb-1 border-start border-primary border-4 ps-3">Agency <span class="text-primary">Dashboard</span></h2>
        <p class="text-muted small ps-3 mb-0">Deep-dive analytics for your investment programming projects</p>
    </div>

    <!-- Top Hero KPIs (Minimized Cards) -->
    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-primary"></div>
                <div class="card-body p-3 p-xl-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05" style="font-size:0.65rem;">Total Projects</p>
                            <h3 class="fw-bold mb-0 text-dark" id="total-projects-count">0</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2.5 rounded-3 text-primary">
                            <i data-lucide="layers" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-success"></div>
                <div class="card-body p-3 p-xl-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05" style="font-size:0.65rem;">Submitted</p>
                            <h3 class="fw-bold mb-0 text-dark" id="submitted-count">0</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2.5 rounded-3 text-success">
                            <i data-lucide="send" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-secondary"></div>
                <div class="card-body p-3 p-xl-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05" style="font-size:0.65rem;">Drafts</p>
                            <h3 class="fw-bold mb-0 text-dark" id="drafts-count">0</h3>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-2.5 rounded-3 text-secondary">
                            <i data-lucide="edit-3" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-danger"></div>
                <div class="card-body p-3 p-xl-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05" style="font-size:0.65rem;">Incomplete</p>
                            <h3 class="fw-bold mb-0 text-dark" id="incomplete-count">0</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2.5 rounded-3 text-danger">
                            <i data-lucide="alert-circle" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 overflow-hidden rounded-20">
                <div class="card-accent-warning"></div>
                <div class="card-body p-3 p-xl-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small fw-semibold text-uppercase letter-spacing-05" style="font-size:0.65rem;">For Revision</p>
                            <h3 class="fw-bold mb-0 text-dark" id="revision-count">0</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2.5 rounded-3 text-warning">
                            <i data-lucide="refresh-cw" width="24" height="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visual Representation Row 1 -->
    <div class="row g-4 mb-5">
        <!-- Status Distribution Chart -->
        <div class="col-md-5">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 text-uppercase letter-spacing-05 small">Project Status Distribution</h6>
                    <span class="badge bg-light text-dark border small fw-normal">Status Breakdown</span>
                </div>
                <div class="row align-items-center g-0">
                    <div class="col-md-6">
                        <div style="height: 240px; position:relative;">
                            <canvas id="agency-status-chart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-4 mt-4 mt-md-0">
                        <div id="agency-status-legend" class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(59, 130, 246, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:#3b82f6;box-shadow: 0 0 0 3px rgba(59,130,246,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Ongoing</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#3b82f6; color:#fff;" id="legend-ongoing">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(16, 185, 129, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:#10b981;box-shadow: 0 0 0 3px rgba(16,185,129,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Completed</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#10b981; color:#fff;" id="legend-completed">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(167, 139, 250, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:#a78bfa;box-shadow: 0 0 0 3px rgba(167,139,250,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Proposed</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#a78bfa; color:#fff;" id="legend-proposed">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(245, 158, 11, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:#f59e0b;box-shadow: 0 0 0 3px rgba(245,158,11,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Suspended</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#f59e0b; color:#fff;" id="legend-suspended">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(148, 163, 184, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:#94a3b8;box-shadow: 0 0 0 3px rgba(148,163,184,0.15);"></div>
                                    <span class="small fw-semibold text-dark">Dropped</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#94a3b8; color:#fff;" id="legend-dropped">0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(239, 68, 68, 0.05);">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle flex-shrink-0" style="width:12px;height:12px;background:#ef4444;box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);"></div>
                                    <span class="small fw-semibold text-dark">Terminated</span>
                                </div>
                                <span class="badge rounded-pill fw-bold" style="background:#ef4444; color:#fff;" id="legend-terminated">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Pulse -->
        <div class="col-md-7">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 text-uppercase letter-spacing-05 small">Submission Intensity</h6>
                    <i data-lucide="bar-chart-3" width="16" class="text-muted"></i>
                </div>
                <div style="height: 200px;">
                    <canvas id="agency-pulse-chart"></canvas>
                </div>
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">Weekly Average</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" id="weekly-avg">--</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Secondary Figures -->
    <div class="row g-4 mb-4">
        <!-- MTIP Programming Horizon (Full Width Hero) -->
        <div class="col-12">
             <div class="bg-white p-5 rounded-5 shadow-sm border h-100">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h6 class="fw-bold mb-1 text-uppercase letter-spacing-05 small">MTIP Programming Horizon</h6>
                        <p class="text-muted small mb-0">Project distribution across the Medium-Term Investment Program years</p>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-semibold">Portfolio Trend</span>
                </div>
                <div style="height: 280px;">
                    <canvas id="agency-mtip-chart"></canvas>
                </div>
                <div class="mt-4 pt-3 small text-muted d-flex justify-content-between align-items-center border-top border-light">
                    <div class="d-flex gap-4">
                         <span><i data-lucide="check-circle" width="14" class="text-success me-1"></i> 85% Data Completeness</span>
                         <span><i data-lucide="calendar" width="14" class="text-primary me-1"></i> Current Cycle: 2023-2028</span>
                    </div>
                    <span class="fw-bold text-dark" id="mtip-total">119 Total Managed Projects</span>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .letter-spacing-05 { letter-spacing: 0.05em; }
    .transition { transition: all 0.3s ease; }
    .hover-shadow:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .hover-scale:hover { transform: scale(1.03); }
    .border-dashed { border-style: dashed !important; }
</style>
@endsection

@section('scripts')
<script type="module">
    import { initAgencyDashboardLoader } from '{{ asset('js/agency/agency-dashboard-loader.js') }}';
    
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof initAgencyDashboardLoader === 'function') {
            initAgencyDashboardLoader();
        }
    });
</script>
@endsection
