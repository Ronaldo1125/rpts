<div class="row g-4 mb-4">
    <div class="col-md-5">
        <div class="bg-white p-3 rounded-4 shadow-sm border h-100">
             <div class="d-flex justify-content-between mb-3 text-uppercase" style="letter-spacing:0.03em;">
                <span class="small fw-bold text-muted" id="sector-dist-title">Sectorial Distribution</span>
                <i data-lucide="pie-chart" width="14" class="text-primary"></i>
             </div>
             <div class="d-flex align-items-center justify-content-center py-2">
                <div style="width:180px; height:180px; position:relative;">
                    <canvas id="chart-sector-dist"></canvas>
                </div>
             </div>
             <div class="mt-3 px-1 d-flex flex-wrap justify-content-center column-gap-3 row-gap-2" id="sector-legend-container" style="font-size: 0.7rem;">
                <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#1e3a8a;"></div><span class="text-muted fw-medium">Social</span> <span class="fw-bold text-dark" id="wf-legend-count-social">0</span></div>
                <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#2563eb;"></div><span class="text-muted fw-medium">Economic</span> <span class="fw-bold text-dark" id="wf-legend-count-economic">0</span></div>
                <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#60a5fa;"></div><span class="text-muted fw-medium">Infra</span> <span class="fw-bold text-dark" id="wf-legend-count-infra">0</span></div>
                <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#bfdbfe;"></div><span class="text-muted fw-medium">Devt. Ad</span> <span class="fw-bold text-dark" id="wf-legend-count-insti">0</span></div>
             </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="bg-white p-3 rounded-4 shadow-sm border h-100">
            <div class="d-flex justify-content-between mb-4 text-uppercase" style="letter-spacing:0.03em;">
                <span class="small fw-bold text-muted">Agency Submissions <span id="active-sector-label" class="text-primary">(All)</span></span>
                <span class="badge bg-primary bg-opacity-10 text-primary small" id="chart-total-label">0 Projects</span>
            </div>
            <div style="height:250px; width:100%; margin-bottom:1rem;">
                <canvas id="chart-agency-submissions"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- CPP Submissions List (Admin) -->
<div class="bg-white rounded-4 shadow-sm border overflow-hidden admin-cpp-list-container">
    <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i data-lucide="layers" width="16" class="text-primary"></i>
            Initial Project Submissions
            <span id="cipg-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
        </h6>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
            <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="cipgSearchInput" placeholder="Search projects...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Sector</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Submitted</th>
                    <th class="small text-secondary fw-semibold text-end pe-4" data-sort-skip="true">Action</th>
                </tr>
            </thead>
            <tbody id="cipgTableBody">
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        <span class="text-muted small">Loading submissions...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="bg-white rounded-4 shadow-sm border overflow-hidden pdipb-cte-list-container" style="display:none;">
    <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i data-lucide="clipboard-check" width="16" class="text-navy"></i>
            Completeness Test and Validation
            <span id="cte-count" class="badge bg-navy bg-opacity-10 text-navy fw-bold ms-1" style="font-size:0.7rem;">0</span>
        </h6>
        <div class="input-group input-group-sm" style="max-width:220px;">
            <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
            <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="cteSearchInput" placeholder="Search validations...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Status</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Submitted</th>
                    <th class="small text-secondary fw-semibold text-end pe-4" data-sort-skip="true">Action</th>
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
