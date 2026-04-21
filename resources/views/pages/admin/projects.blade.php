<section id="projects" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Manage Projects</h2>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary create-project-btn text-white px-4 py-2 fw-medium rounded-pill" style="background-color: #154A9A; border-color: #154A9A;">
                <i data-lucide="plus" class="me-1" width="18"></i> Create Project
            </button>
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
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search projects...">
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Project Name</th>
                            <th class="small text-secondary" data-sort-skip="true">Description</th>
                            <th class="small text-secondary agency-col">Agency</th>
                            <th class="small text-secondary">Funding Category</th>
                            <th class="small text-secondary">Created At</th>
                            <th class="small text-secondary" data-sort-skip="true">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="clickable-row cursor-pointer" style="cursor: pointer;" data-agency="Bicol University (BU)">
                            <td style="width: 20%; color: inherit !important;">BU Tabaco Campus Medical & Dental Clinic</td>
                            <td class="small" style="width: 30%;">One building completed</td>
                            <td class="small agency-col">Bicol University (BU)</td>
                            <td class="small">No Funding Category Yet</td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 fw-normal small">2 weeks ago</span></td>
                            <td>
                                <div class="dropdown project-actions-dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i data-lucide="more-vertical" width="18"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item project-action-view" href="#"><i data-lucide="eye" width="16" class="me-2"></i> View</a></li>
                                        <li><a class="dropdown-item project-action-edit" href="#"><i data-lucide="pencil" width="16" class="me-2"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item project-action-delete text-danger" href="#"><i data-lucide="trash-2" width="16" class="me-2"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr class="clickable-row cursor-pointer" style="cursor: pointer;" data-agency="DPWH Region V">
                            <td style="color: inherit !important;">Conduct of RP-FP classes in the FDS...</td>
                            <td class="small">Capacitate local implementers on the conduct...</td>
                            <td class="small agency-col">DPWH Region V</td>
                            <td class="small">No Funding Category Yet</td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 fw-normal small">2 weeks ago</span></td>
                            <td>
                                <div class="dropdown project-actions-dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i data-lucide="more-vertical" width="18"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item project-action-view" href="#"><i data-lucide="eye" width="16" class="me-2"></i> View</a></li>
                                        <li><a class="dropdown-item project-action-edit" href="#"><i data-lucide="pencil" width="16" class="me-2"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item project-action-delete text-danger" href="#"><i data-lucide="trash-2" width="16" class="me-2"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr class="clickable-row cursor-pointer" style="cursor: pointer;" data-agency="DICT - Bicol">
                            <td style="color: inherit !important;">Refresher Course on Pre-Marriage Counseling</td>
                            <td class="small">A two-day training on how to run a Pre-...</td>
                            <td class="small agency-col">DICT - Bicol</td>
                            <td class="small">No Funding Category Yet</td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 fw-normal small">2 weeks ago</span></td>
                            <td>
                                <div class="dropdown project-actions-dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i data-lucide="more-vertical" width="18"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item project-action-view" href="#"><i data-lucide="eye" width="16" class="me-2"></i> View</a></li>
                                        <li><a class="dropdown-item project-action-edit" href="#"><i data-lucide="pencil" width="16" class="me-2"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item project-action-delete text-danger" href="#"><i data-lucide="trash-2" width="16" class="me-2"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr class="clickable-row cursor-pointer" style="cursor: pointer;" data-agency="DPWH Region V">
                            <td style="color: inherit !important;">Upgrading/Rehab of Facilities at Main Hospital...</td>
                            <td class="small">Rehabilitation/renovation of Building J-OB...</td>
                            <td class="small agency-col">DPWH Region V</td>
                            <td class="small">No Funding Category Yet</td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 fw-normal small">2 weeks ago</span></td>
                            <td>
                                <div class="dropdown project-actions-dropdown">
                                    <button class="btn btn-light btn-sm border-0 rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Actions">
                                        <i data-lucide="more-vertical" width="18"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item project-action-view" href="#"><i data-lucide="eye" width="16" class="me-2"></i> View</a></li>
                                        <li><a class="dropdown-item project-action-edit" href="#"><i data-lucide="pencil" width="16" class="me-2"></i> Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item project-action-delete text-danger" href="#"><i data-lucide="trash-2" width="16" class="me-2"></i> Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">Showing 1 to 4 of 4 entries</span>
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
<style>
    .bg-indigo-50 { background-color: #e8f0fe !important; }
    .text-indigo-700 { color: #0032A6 !important; }
</style>
