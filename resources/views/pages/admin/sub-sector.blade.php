<section id="sub-sector" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Manage Sub-Sectors</h2>
        </div>
        <div>
            <button class="btn btn-primary text-white px-4 py-2 fw-medium rounded-pill" style="background-color: #154A9A; border-color: #154A9A;" data-bs-toggle="modal" data-bs-target="#addSubSectorModal">
                <i data-lucide="plus" class="me-1" width="18"></i> Create Sub-Sector
            </button>
        </div>
    </div>

    <!-- Add Sub-Sector Modal -->
    <div class="modal fade" id="addSubSectorModal" tabindex="-1" aria-labelledby="addSubSectorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1rem;">
                <!-- Header Accent -->
                <div style="height: 4px; background-color: #154A9A;"></div>

                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="addSubSectorModalLabel">Add Sub-Sector</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="addSubSectorForm">
                        <div class="mb-4">
                            <label for="subSectorName" class="form-label small fw-semibold text-secondary mb-1">Sub-Sector Name</label>
                            <input type="text" class="form-control" id="subSectorName" placeholder="Enter Sub-Sector Name" style="border-radius: 0.75rem;" required>
                        </div>
                        <div class="mb-0">
                            <label for="parentSector" class="form-label small fw-semibold text-secondary mb-1">Sector:</label>
                            <select class="form-select" id="parentSector" style="border-radius: 0.75rem;" required>
                                <option value="" selected disabled>Select sector ...</option>
                                <option value="1">Development Administration</option>
                                <option value="2">Economic</option>
                                <option value="3">Environment</option>
                                <option value="4">Infrastructure</option>
                                <option value="5">Social</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addSubSectorForm" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="background-color: #0248D4; border-color: #0248D4; border-radius: 0.75rem !important;">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light" style="width: 70px;">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                     <span class="text-secondary small fw-bold">Search:</span>
                     <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Sub-Sector Name</th>
                            <th class="small text-secondary">Created At</th>
                            <th class="small text-secondary" data-sort-skip="true">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                <i data-lucide="inbox" class="mb-2" width="32"></i>
                                <p class="mb-0 small">No sub-sectors found. Add a new one to get started.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">Showing 0 entries</span>
                <nav>
                    <ul class="custom-pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&lsaquo;</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&rsaquo;</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
