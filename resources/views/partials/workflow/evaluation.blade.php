<div class="row g-4 mb-4">
    <div class="col-md-7">
        <div class="bg-white p-4 rounded-4 shadow-sm border mb-3">
            <span class="small fw-bold d-block mb-4">DIVISION ASSESSMENT WORKLOAD</span>
            <div class="d-flex flex-column gap-3">
                @foreach(['PFPD', 'PMED', 'DRD'] as $divName)
                    @php 
                        $workload = $divisionWorkload[$divName] ?? ['total' => 0, 'completed' => 0, 'pending' => 0, 'percentage' => 0, 'color' => '#ccc', 'textClass' => 'text-muted'];
                    @endphp
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center {{ $workload['textClass'] }} fw-bold" style="width:24px; height:24px; font-size:0.6rem;">{{ $divName }}</div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-muted" style="font-size: 0.7rem;">Completed: <strong class="text-dark">{{ $workload['completed'] }}</strong> / {{ $workload['total'] }}</span>
                                <span class="small text-muted fw-bold" style="font-size: 0.7rem;">{{ $workload['percentage'] }}%</span>
                            </div>
                            <div class="progress" style="height:6px;">
                                <div class="progress-bar" style="width:{{ $workload['percentage'] }}%; background:{{ $workload['color'] }}" title="{{ $workload['pending'] }} Pending"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-5">
         <div class="bg-white p-4 rounded-4 shadow-sm border h-100">
            <span class="small fw-bold d-block mb-3">STATUS BREAKDOWN</span>
            <div class="d-flex flex-column gap-2">
                <div class="p-2 rounded bg-light border-start border-4 border-warning d-flex justify-content-between text-dark"><span>Pending Assessment</span><span class="fw-bold" id="eval-kpi-pending">0</span></div>
                <div class="p-2 rounded bg-light border-start border-4 border-success d-flex justify-content-between text-dark"><span>Assessed</span><span class="fw-bold" id="eval-kpi-assessed">0</span></div>
                <div class="p-2 rounded bg-light border-start border-4 border-primary d-flex justify-content-between text-dark"><span>Evaluated</span><span class="fw-bold" id="eval-kpi-evaluated">0</span></div>
            </div>
         </div>
    </div>
</div>

<!-- PAR Projects List -->
<div class="bg-white rounded-4 shadow-sm border overflow-hidden">
    <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i data-lucide="file-check" width="16" class="text-primary"></i>
            Evaluated PARs
            <span id="eval-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
        </h6>
        <input type="text" class="form-control form-control-sm bg-light border-0 rounded-pill px-3"
            id="evalSearchInput" placeholder="Search..." style="max-width:200px;">
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="small text-secondary fw-semibold ps-4">Project Title</th>
                    <th class="small text-secondary fw-semibold">Agency</th>
                    <th class="small text-secondary fw-semibold">Division</th>
                    <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                </tr>
            </thead>
            <tbody id="evalTableBody">
                <tr><td colspan="4" class="text-center py-4 text-muted small">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading...
                </td></tr>
            </tbody>
        </table>
    </div>
</div>

@if(auth()->user()->hasRole('administrator'))
<!-- ── ADMIN ONLY: Reviewed Technical Reports (Ready for Finalization) ── -->
<div class="row g-4 mt-4" id="admin-dashboard-reviewed-section">
    <div class="col-12">
        <div class="bg-white p-0 rounded-4 shadow-sm border overflow-hidden">
            <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center" style="background: rgba(5, 150, 105, 0.05);">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="check-square" width="18" height="18" class="text-success"></i>
                        Finalize PARs
                        <span id="reviewed-list-count" class="badge bg-success bg-opacity-10 text-success fw-bold ms-1" style="font-size:0.7rem;">0</span>
                    </h6>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th class="ps-4 text-secondary small py-3 fw-semibold">Project Title</th>
                            <th class="text-secondary small py-3 fw-semibold">Agency</th>
                            <th class="text-secondary small py-3 fw-semibold">Division</th>
                            <th class="text-end pe-4 text-secondary small py-3 fw-semibold">Action</th>
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
@endif