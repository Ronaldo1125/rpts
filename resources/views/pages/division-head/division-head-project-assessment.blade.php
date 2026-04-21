<section id="division-head-project-assessment" class="page-content container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
        <div>
            <h2 class="fw-bold mb-0">Project Assessment Report</h2>
            <p class="text-muted small mb-0">Review and manage technical assessments for regional projects</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
        <div class="card-body d-flex flex-column" style="min-height: 450px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <!-- Table Controls -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3" style="width: 80px;" id="divHeadParEntriesSelect">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <span class="input-group-text bg-light border-0 rounded-start-pill px-3">
                            <i data-lucide="search" width="14" class="text-secondary"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-0 rounded-end-pill px-3" id="divHeadParSearchInput"
                            placeholder="Search assessments...">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive" style="border-radius: 12px; overflow: visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="small text-secondary fw-bold border-bottom-0 ps-3">Project Title</th>
                            <th class="small text-secondary fw-bold border-bottom-0">Proponent</th>
                            <th class="small text-secondary fw-bold border-bottom-0">Status</th>
                            <th class="small text-secondary fw-bold border-bottom-0">Prepared By</th>
                            <th class="small text-secondary fw-bold border-bottom-0">Date Prepared</th>
                            <th class="small text-secondary fw-bold border-bottom-0 text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="division-head-par-tbody">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                <span class="text-muted">Loading assessments...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-auto py-3 px-2">
                <span id="division-head-par-count" class="text-muted small">Showing 0 to 0 of 0 entries</span>
                <nav>
                    <ul class="custom-pagination mb-0" id="divHeadParPagination">
                        <!-- Dynamic -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
