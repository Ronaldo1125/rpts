<!-- Admin View -->
<div class="admin-rdc-container">
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="bg-white p-4 rounded-4 shadow-sm border h-100 text-center">
                <span class="small fw-bold d-block mb-3">RDIP DECISION FUNNEL</span>
                <div class="funnel-container d-flex flex-column align-items-center gap-1">
                    <div style="width:100%; height:25px; background:#154A9A; border-radius:15px 15px 2px 2px;"></div>
                    <div style="width:85%; height:25px; background:#3b82f6; border-radius:2px;"></div>
                    <div style="width:65%; height:25px; background:#f97316; border-radius:2px;"></div>
                    <div style="width:40%; height:25px; background:#10b981; border-radius:2px 2px 15px 15px;"></div>
                </div>
                <div class="mt-3 small text-muted">Final Approval Funnel (RDIP)</div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="bg-white p-4 rounded-4 shadow-sm border mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary px-3">READY FOR RDC: 24</span>
                    <span class="badge bg-danger px-3">DEFERRED: 11</span>
                </div>
                <div class="p-3 bg-light rounded text-muted small">
                    <div class="d-flex justify-content-between border-bottom pb-1 mb-1"><span>Priority Projects</span><span class="fw-bold text-dark">18</span></div>
                    <div class="d-flex justify-content-between"><span>RDC Agenda Items</span><span class="fw-bold text-dark">6</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white p-4 rounded-4 shadow-sm border animate-popup">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05 d-flex align-items-center gap-2">
                <i data-lucide="arrow-right-circle" width="16" class="text-primary"></i>
                RDC Presentation Projects
                <span id="rdc-pres-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
            </h6>
            <div class="input-group input-group-sm" style="max-width:240px;">
                <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="rdcPresSearchInput" placeholder="Search RDC items...">
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
                <tbody id="rdcPresTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <span class="text-muted">Loading RDC presentation submissions...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Staff View -->
<div class="pdipb-rdc-container" style="display: none;">
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
