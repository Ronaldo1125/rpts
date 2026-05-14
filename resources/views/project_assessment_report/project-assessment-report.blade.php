<section id="project-assessment-report" class="page-content container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-0">Project Assessment Report (PAR)</h2>
            <p class="text-muted small mb-0">FM-PDI-02 — Programs, Activities and Projects for Inclusion in the RDIP</p>
        </div>
        <div>
            <button class="btn text-white px-4 py-2 fw-semibold rounded-pill" id="newParBtn"
                style="background-color: #154A9A; border-color: #154A9A;">
                <i data-lucide="file-text" class="me-2" width="16"></i> New Assessment
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column" style="min-height: 300px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light" style="width: 70px;"
                        id="parEntriesSelect">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-bold">Search:</span>
                    <div class="input-group input-group-sm" style="max-width: 250px; min-width: 150px; flex-grow: 1;">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" id="parSearchInput"
                            placeholder="Search assessments...">
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="border-radius: 8px;">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Project Title</th>
                            <th class="small text-secondary">Proponent</th>
                            <th class="small text-secondary">Assessment Status</th>
                            <th class="small text-secondary">Prepared By</th>
                            <th class="small text-secondary">Date Prepared</th>
                            <th class="small text-secondary" data-sort-skip="true">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="par-tbody">
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No assessments found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-auto py-3">
                <span id="par-count" class="text-muted small">Showing 1 to 1 of 1 entries</span>
                <nav>
                    <ul class="custom-pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
