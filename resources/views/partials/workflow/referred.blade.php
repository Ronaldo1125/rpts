<!-- Division KPI cards — populated by initReferredStage() -->
<div class="mb-4 d-flex gap-3 overflow-x-auto pb-3 justify-content-center" id="ref-div-kpi-row">
    <div class="p-3 bg-white rounded-4 shadow-sm border text-center flex-grow-1" style="min-width:140px; border-bottom:4px solid #1e3a8a;">
        <span class="text-muted small fw-bold d-block mb-1 text-uppercase letter-spacing-05">PFPD</span>
        <h3 class="fw-bold mb-0 text-primary" id="ref-kpi-pfpd">—</h3>
        <span class="small text-muted">referred</span>
    </div>
    <div class="p-3 bg-white rounded-4 shadow-sm border text-center flex-grow-1" style="min-width:140px; border-bottom:4px solid #2563eb;">
        <span class="text-muted small fw-bold d-block mb-1 text-uppercase letter-spacing-05">PMED</span>
        <h3 class="fw-bold mb-0 text-primary" id="ref-kpi-pmed">—</h3>
        <span class="small text-muted">referred</span>
    </div>
    <div class="p-3 bg-white rounded-4 shadow-sm border text-center flex-grow-1" style="min-width:140px; border-bottom:4px solid #93c5fd;">
        <span class="text-muted small fw-bold d-block mb-1 text-uppercase letter-spacing-05">DRD</span>
        <h3 class="fw-bold mb-0 text-primary" id="ref-kpi-drd">—</h3>
        <span class="small text-muted">referred</span>
    </div>
    <div class="p-3 bg-white rounded-4 shadow-sm border text-center flex-grow-1" style="min-width:140px; border-bottom:4px solid #6366f1;">
        <span class="text-muted small fw-bold d-block mb-1 text-uppercase letter-spacing-05">Pending</span>
        <h3 class="fw-bold mb-0 text-indigo" id="ref-kpi-pending" style="color:#6366f1;">—</h3>
        <span class="small text-muted">awaiting referral</span>
    </div>
</div>

<!-- Validated CPPs list — populated by initReferredStage() -->
<div class="bg-white rounded-4 shadow-sm border overflow-hidden">
    <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i data-lucide="send" width="16" class="text-primary"></i>
            Validated CPPs — Awaiting Division Referral
            <span id="ref-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
        </h6>
        <input type="text" class="form-control form-control-sm bg-light border-0 rounded-pill px-3"
            id="refSearchInput" placeholder="Search..." style="max-width:200px;">
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="small text-secondary fw-semibold ps-4">Project Title</th>
                    <th class="small text-secondary fw-semibold">Agency</th>
                    <th class="small text-secondary fw-semibold">Validated By</th>
                    <th class="small text-secondary fw-semibold">Sector</th>
                    <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                </tr>
            </thead>
            <tbody id="ref-validated-tbody">
                <tr><td colspan="5" class="text-center py-4 text-muted small">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading...
                </td></tr>
            </tbody>
        </table>
    </div>
</div>
