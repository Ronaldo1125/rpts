<section id="agency" class="page-content container-fluid py-4 text-dark">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h2 class="fw-bold mb-0">Manage Agencies</h2>
            <p class="text-muted small mb-0">Configure implementing agencies for the RDC process</p>
        </div>
        <div>
            <button class="btn btn-primary-rpts text-white px-4 py-2 fw-medium rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addAgencyModal">
                <i data-lucide="plus" class="me-1" width="18"></i> Create Agency
            </button>
        </div>
    </div>

    <!-- Add Agency Modal -->
    <div class="modal fade" id="addAgencyModal" tabindex="-1" aria-labelledby="addAgencyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                <div class="modal-accent-primary"></div>
                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="addAgencyModalLabel">Add Agency</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="addAgencyForm">
                        <div class="mb-4">
                            <label for="agencyName" class="form-label small fw-semibold text-secondary mb-1">Agency Name</label>
                            <input type="text" class="form-control rounded-12" id="agencyName" placeholder="Enter Agency Name" required>
                        </div>
                        <div class="mb-0">
                            <label for="agencyAcronym" class="form-label small fw-semibold text-secondary mb-1">Agency Acronym</label>
                            <input type="text" class="form-control rounded-12" id="agencyAcronym" placeholder="Enter Agency Acronym" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addAgencyForm" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12 shadow-sm">Save Agency</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-16">
        <div class="card-body d-flex flex-column" style="min-height: 300px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light entries-select" id="agencyEntriesSelect">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-bold">Search:</span>
                    <div class="input-group input-group-sm search-input-group" id="agencySearchGroup">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search agencies...">
                    </div>
                </div>
            </div>

            <div class="table-responsive table-overflow-visible">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary ps-3">Agency Name</th>
                            <th class="small text-secondary">Agency Acronym</th>
                            <th class="small text-secondary">Created At</th>
                            <th class="small text-secondary text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="agency-list-tbody">
                        <tr>
                            <td class="py-3 ps-3 fw-medium">Bicol University</td>
                            <td class="py-3"><span class="badge-soft-pill badge-soft-blue">BU</span></td>
                            <td class="py-3"><span class="badge-soft-pill badge-soft-blue">2 Weeks Ago</span></td>
                            <td class="py-3 text-end pe-3">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                        <i data-lucide="more-vertical" width="20"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="#" data-action="edit"><i data-lucide="edit-2" class="me-2" width="16"></i>Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" data-action="delete"><i data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-auto py-3">
                <span class="text-muted small">Showing 1 to 6 of 6 entries</span>
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
