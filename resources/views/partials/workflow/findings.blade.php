<div class="row g-4">
    <div class="col-md-4">
        <div class="list-group list-group-flush border shadow-sm rounded-4 overflow-hidden">
            <div class="list-group-item active bg-primary border-0 p-3"><span class="small fw-bold">EDC COMMITTEE</span></div>
            <div class="list-group-item border-0 p-3"><span class="small fw-bold">IDC COMMITTEE</span></div>
            <div class="list-group-item border-0 p-3"><span class="small fw-bold">DAC COMMITTEE</span></div>
            <div class="list-group-item border-0 p-3"><span class="small fw-bold">SDC COMMITTEE</span></div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="bg-white p-4 rounded-4 shadow-sm border h-100">
            <div class="d-flex justify-content-between mb-4"><span class="small fw-bold uppercase">Technical Recommendations</span></div>
            <div class="p-3 bg-light rounded-3 mb-3 border-start border-4 border-navy">
                <p class="small mb-0 italic text-muted">"Recommended for approval with provisions for climate resilience mapping integration."</p>
            </div>
            <div class="row g-2">
                <div class="col-6"><div class="p-2 border rounded text-center"><span class="d-block x-small text-muted mb-1">Response Time (Avg)</span><span class="fw-bold">4.2 Days</span></div></div>
                <div class="col-6"><div class="p-2 border rounded text-center"><span class="d-block x-small text-muted mb-1">Key Compliance Rate</span><span class="fw-bold">92%</span></div></div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-4 shadow-sm border overflow-hidden mt-4">
    <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i data-lucide="message-square" width="16" class="text-primary"></i>
            Findings and Recommendations
            <span id="comment-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
        </h6>
        <div class="d-flex gap-2 align-items-center">
            <select id="listParSelect" class="form-select form-select-sm border-0 bg-light" style="max-width:250px; font-size:0.75rem;">
                <option value="">-- Choose PAR Assessment --</option>
            </select>
            <button id="newCommentsBtn" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                <i data-lucide="plus" width="14" class="me-1"></i> New
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="small text-secondary fw-semibold ps-4">Batch Title</th>
                    <th class="small text-secondary fw-semibold">Agency</th>
                    <th class="small text-secondary fw-semibold">Projects</th>
                    <th class="small text-secondary fw-semibold">Findings</th>
                    <th class="small text-secondary fw-semibold">Prepared By</th>
                    <th class="small text-secondary fw-semibold">Status</th>
                    <th class="small text-secondary fw-semibold">Date</th>
                    <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                </tr>
            </thead>
            <tbody id="comment-tbody">
                <tr><td colspan="8" class="text-center py-4 text-muted small">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading...
                </td></tr>
            </tbody>
        </table>
    </div>
</div>
