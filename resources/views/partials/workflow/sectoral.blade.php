<!-- Admin View -->
<div class="admin-sectoral-container">
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="p-3 text-white border-0 rounded shadow-sm text-center" style="background:#154A9A;"><span class="small fw-bold opacity-75">EDC</span><h3 class="mt-1 mb-0 fw-bold">12</h3></div></div>
        <div class="col-md-3"><div class="p-3 text-white border-0 rounded shadow-sm text-center" style="background:#0ea5e9;"><span class="small fw-bold opacity-75">IDD</span><h3 class="mt-1 mb-0 fw-bold">8</h3></div></div>
        <div class="col-md-3"><div class="p-3 text-white border-0 rounded shadow-sm text-center" style="background:#10b981;"><span class="small fw-bold opacity-75">DAC</span><h3 class="mt-1 mb-0 fw-bold">15</h3></div></div>
        <div class="col-md-3"><div class="p-3 text-white border-0 rounded shadow-sm text-center" style="background:#6366f1;"><span class="small fw-bold opacity-75">SDC</span><h3 class="mt-1 mb-0 fw-bold">9</h3></div></div>
    </div>

    <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
        <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i data-lucide="list" width="16" class="text-primary"></i>
                Sectoral Presentation CPPs
                <span id="sectoral-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
            </h6>
            <div class="input-group input-group-sm" style="max-width:220px;">
                <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="sectoralSearchInput" placeholder="Search...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background:#f8fafc;">
                    <tr>
                        <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                        <th class="small text-secondary fw-semibold">Agency</th>
                        <th class="small text-secondary fw-semibold">Sector</th>
                        <th class="small text-secondary fw-semibold">Status</th>
                        <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                    </tr>
                </thead>
                <tbody id="sectoralTableBody">
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted small">
                            Loading sectoral presentation CPPs…
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Staff View -->
<div class="pdipb-sectoral-container" style="display: none;">
    <div class="bg-white p-0 rounded-5 shadow-sm border overflow-hidden">
        <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center" style="background: rgba(59, 130, 246, 0.05);">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary">
                    <i data-lucide="presentation" width="18" height="18"></i>
                </div>
                <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05">Sectoral Presentation Referrals</h6>
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
                <tbody id="dashboard-sectoral-presentation-tbody">
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted small">Loading sectoral referrals...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
