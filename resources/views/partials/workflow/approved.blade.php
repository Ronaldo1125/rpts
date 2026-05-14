<div class="p-4 bg-white rounded-4 shadow-sm border text-center mb-4">
    <div class="row g-4">
        <div class="col-md-6 border-end text-center">
            <h2 class="fw-bold text-success mb-1">18</h2>
            <span class="small text-muted uppercase fw-bold">Approved Projects</span>
        </div>
        <div class="col-md-6 text-center">
            <h2 class="fw-bold text-primary mb-1">5</h2>
            <span class="small text-muted uppercase fw-bold">Resolutions Issued</span>
        </div>
    </div>
</div>
<div class="bg-white p-4 rounded-4 shadow-sm border mb-3 animate-popup">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05 d-flex align-items-center gap-2">
            <i data-lucide="award" width="16" class="text-success"></i>
            RDC Approved Projects
            <span id="rdc-approved-list-count" class="badge bg-success bg-opacity-10 text-success fw-bold ms-1" style="font-size:0.7rem;">0</span>
        </h6>
        <div class="input-group input-group-sm" style="max-width:240px;">
            <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
            <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="rdcApprovedSearchInput" placeholder="Search RDC approved...">
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Stage</th>
                    <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Status</th>
                    <th class="small text-secondary fw-semibold text-end pe-4" style="white-space:nowrap;">Updated</th>
                </tr>
            </thead>
            <tbody id="rdcApprovedTableBody">
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                        <span class="text-muted">Loading RDC approved submissions...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="bg-light p-3 rounded-4 border">
    <span class="small fw-bold d-block mb-3">LATEST RDC RESOLUTIONS</span>
    <ul class="list-unstyled mb-0 small text-muted">
        <li class="mb-2 d-flex gap-2"><i data-lucide="file-check" width="14" class="text-success"></i> Resolution No. 42 s. 2024 - Project Alpha Approval</li>
        <li class="mb-2 d-flex gap-2"><i data-lucide="file-check" width="14" class="text-success"></i> Resolution No. 43 s. 2024 - RDP Sectoral Update</li>
        <li class="d-flex gap-2"><i data-lucide="file-check" width="14" class="text-success"></i> Resolution No. 45 s. 2024 - Infrastructure Funding</li>
    </ul>
</div>
