<!-- Admin View -->
<div class="admin-revised-container">
    <div class="bg-white p-4 rounded-4 shadow-sm border animate-popup">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05 d-flex align-items-center gap-2">
                <i data-lucide="refresh-cw" width="16" class="text-primary"></i>
                Revised Projects
                <span id="revised-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
            </h6>
            <div class="input-group input-group-sm" style="max-width:220px;">
                <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="revisedSearchInput" placeholder="Search...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                        <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                        <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Stage</th>
                        <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Revised Date</th>
                        <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                    </tr>
                </thead>
                <tbody id="revisedTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            <span class="text-muted">Loading revised submissions...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Staff View -->
<div class="pdipb-revised-container" style="display: none;">
    <div class="bg-white p-0 rounded-5 shadow-sm border overflow-hidden">
        <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center" style="background: rgba(245, 158, 11, 0.05);">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                    <i data-lucide="rotate-ccw" width="18" height="18"></i>
                </div>
                <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Revised Submissions (Referred by Admin)</h6>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-secondary small py-3">Project Title</th>
                        <th class="text-secondary small py-3">Agency</th>
                        <th class="text-secondary small py-3">Stage</th>
                        <th class="text-secondary small py-3">Referred On</th>
                        <th class="text-end pe-4 text-secondary small py-3">Action</th>
                    </tr>
                </thead>
                <tbody id="dashboard-revision-tbody">
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted small">Loading revisions...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
