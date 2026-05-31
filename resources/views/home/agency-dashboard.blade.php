@extends('layouts.app_v2')

@section('content')
<section id="agency-dashboard" class="page-content active container-fluid py-4">
    <div class="mb-5">
        <h2 class="fw-bold mb-1 border-start border-primary border-4 ps-3">Agency <span class="text-primary">Dashboard</span></h2>
        <p class="text-muted small ps-3 mb-0">Deep-dive analytics for your investment programming projects</p>
    </div>

    <!-- Removed Top Hero KPIs as requested -->

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

        <!-- Submission Status Pipeline -->
        <div class="col-md-7">
            <div class="bg-white p-4 rounded-5 shadow-sm border h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 text-uppercase letter-spacing-05 small">Submission Status Pipeline</h6>
                    <span class="badge bg-success bg-opacity-10 text-success small">LIVE</span>
                </div>
                <div style="height: 240px;">
                    <canvas id="agency-pipeline-chart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Project Cost Per Year -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="bg-white p-5 rounded-5 shadow-sm border h-100">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h6 class="fw-bold mb-1 text-uppercase letter-spacing-05 small">Project Cost Per Year</h6>
                        <p class="text-muted small mb-0">Programmed project costs per year (in ₱ Billions)</p>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-semibold">Cost Breakdown</span>
                </div>
                <div style="height: 280px;">
                    <canvas id="agency-cost-chart"></canvas>
                </div>
                <div class="mt-4 pt-3 small text-muted d-flex justify-content-between align-items-center border-top border-light">
                    <div class="d-flex gap-4">
                        <span><i data-lucide="calendar" width="14" class="text-primary me-1"></i> 2023–2028 + Succeeding Years</span>
                    </div>
                    <span class="fw-bold text-dark" id="cost-total"></span>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Chart) return;

    const pStats = @json($projectStats ?? []);
    const sStats = @json($submissionStats ?? []);
    const costPerYear = @json($costPerYear ?? []);

    // 1. Status Pie Chart
    const ctxStatus = document.getElementById('agency-status-chart')?.getContext('2d');
    if (ctxStatus) {
        const rawData = [
            { label: 'Ongoing',    count: pStats.ongoing || 0,    color: '#4c1d95', id: 'legend-ongoing' },
            { label: 'Approved',   count: pStats.approved || 0,   color: '#5b21b6', id: 'legend-approved' },
            { label: 'Completed',  count: pStats.completed || 0,  color: '#6d28d9', id: 'legend-completed' },
            { label: 'Proposed',   count: pStats.proposed || 0,   color: '#7c3aed', id: 'legend-proposed' },
            { label: 'Suspended',  count: pStats.suspended || 0,  color: '#8b5cf6', id: 'legend-suspended' },
            { label: 'Dropped',    count: pStats.dropped || 0,    color: '#a78bfa', id: 'legend-dropped' },
            { label: 'Terminated', count: pStats.terminated || 0, color: '#c4b5fd', id: 'legend-terminated' }
        ];

        rawData.forEach(item => {
            const el = document.getElementById(item.id);
            if (el) el.textContent = item.count;
        });

        const filtered = rawData.filter(d => d.count > 0);
        const counts   = filtered.map(d => d.count);
        const labels   = filtered.map(d => d.label);
        const colors   = filtered.map(d => d.color);
        const hasData  = counts.length > 0;

        new window.Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: hasData ? labels : ['No Data'],
                datasets: [{
                    data: hasData ? counts : [1],
                    backgroundColor: hasData ? colors : ['#e2e8f0'],
                    borderWidth: hasData ? 2 : 0,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: ctx => {
                                if (!hasData) return ' No project data yet';
                                const total = counts.reduce((a,b)=>a+b,0);
                                return ` ${ctx.label}: ${ctx.parsed} (${total > 0 ? Math.round((ctx.parsed/total)*100) : 0}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Submission Status Pipeline (Bar Chart)
    const ctxPipeline = document.getElementById('agency-pipeline-chart')?.getContext('2d');
    if (ctxPipeline) {
        new Chart(ctxPipeline, {
            type: 'bar',
            data: {
                labels: ['Drafts', 'Submitted', 'Resubmitted', 'Incomplete', 'Validated', ['For', 'Revision'], 'Revised', 'SecCom', 'RDC', 'Approved'],
                datasets: [{
                    label: 'Submissions',
                    data: [
                        sStats.drafts || 0,
                        sStats.submitted || 0,
                        sStats.resubmitted || 0,
                        sStats.incomplete || 0,
                        sStats.validated || 0,
                        sStats.revision || 0,
                        sStats.revised || 0,
                        sStats.seccom || 0,
                        sStats.rdc || 0,
                        sStats.approved || 0
                    ],
                    backgroundColor: [
                        'rgba(148,163,184,0.85)', // Drafts
                        'rgba(99,102,241,0.85)',  // Submitted
                        'rgba(245,158,11,0.85)',  // Resubmitted
                        'rgba(244,63,94,0.85)',   // Incomplete
                        'rgba(147,51,234,0.85)',  // Validated
                        'rgba(124,58,237,0.85)',  // For Revision
                        'rgba(14,165,233,0.85)',  // Revised
                        'rgba(236,72,153,0.85)',  // SecCom
                        'rgba(20,184,166,0.85)',  // RDC
                        'rgba(34,197,94,0.85)'    // Approved
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 } },
                        grid: { color: 'rgba(0,0,0,0.04)' }
                    },
                    x: {
                        ticks: { 
                            font: { size: 10 },
                            maxRotation: 0,
                            minRotation: 0,
                            autoSkip: false
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }


    // 3. Project Cost Per Year (Bar Chart)
    const ctxCost = document.getElementById('agency-cost-chart')?.getContext('2d');
    if (ctxCost) {
        const years = Object.keys(costPerYear);
        // Values stored in millions — convert to billions for display
        const amounts = Object.values(costPerYear).map(v => (parseFloat(v) || 0) / 1000);
        const totalCost = amounts.reduce((a, b) => a + b, 0);
        const hasData = amounts.some(v => v > 0);

        const costTotalEl = document.getElementById('cost-total');
        if (costTotalEl) {
            costTotalEl.textContent = hasData
                ? 'Total: ₱' + totalCost.toLocaleString('en-PH', { minimumFractionDigits: 3 }) + ' Billion'
                : 'No cost data recorded yet';
        }

        new Chart(ctxCost, {
            type: 'bar',
            data: {
                labels: years,
                datasets: [{
                    label: 'Project Cost (₱ Billion)',
                    data: amounts,
                    backgroundColor: [
                        'rgba(21,74,154,0.80)',
                        'rgba(37,99,235,0.80)',
                        'rgba(14,165,233,0.80)',
                        'rgba(99,102,241,0.80)',
                        'rgba(139,92,246,0.80)',
                        'rgba(168,85,247,0.80)',
                        'rgba(16,185,129,0.80)'
                    ],
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 70
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' ₱' + ctx.parsed.y.toLocaleString('en-PH', { minimumFractionDigits: 3 }) + 'B'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '₱ in Billions',
                            color: '#94a3b8',
                            font: { size: 11 }
                        },
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 },
                            callback: val => '₱' + val.toLocaleString('en-PH', { minimumFractionDigits: 2 }) + 'B'
                        },
                        grid: { color: 'rgba(0,0,0,0.03)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 11 }, maxRotation: 0 }
                    }
                }
            }
        });
    }
});
</script>
@endsection
