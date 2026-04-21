<link rel="stylesheet" href="/css/roles.css">
<section id="roles" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Manage Roles</h2>
        </div>
        <div>
            <button class="btn btn-primary-rpts text-white px-4 py-2 fw-medium rounded-pill" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i data-lucide="plus" class="me-1" width="18"></i> Create Role
            </button>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden rounded-16">
                <!-- Header Accent -->
                <div class="modal-accent-primary"></div>

                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="addRoleModalLabel">Add Role</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="addRoleForm">
                        <div class="mb-3">
                            <label for="roleName" class="form-label small fw-semibold text-secondary mb-1">Name</label>
                            <input type="text" class="form-control rounded-12" id="roleName" placeholder="Enter Role Name" required>
                        </div>
                        <div class="mb-0">
                            <label for="rolePermissions" class="form-label small fw-semibold text-secondary mb-1">Permissions</label>
                            <select class="form-select rounded-12" id="rolePermissions" required>
                                <option value="" selected disabled>Nothing selected</option>
                                <option value="user-view">user-view</option>
                                <option value="user-create">user-create</option>
                                <option value="agency-view">agency-view</option>
                                <option value="agency-create">agency-create</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addRoleForm" class="btn btn-primary-rpts px-4 py-2 fw-semibold shadow-sm rounded-12">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light entries-select">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                     <span class="text-secondary small fw-bold">Search:</span>
                     <div class="input-group input-group-sm search-input-group">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search...">
                    </div>
                </div>
            </div>

            <div class="table-responsive table-overflow-visible">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Role Name</th>
                            <th class="small text-secondary" data-sort-skip="true">Permissions</th>
                            <th class="small text-secondary">Created At</th>
                            <th class="small text-secondary" data-sort-skip="true">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-dark">Implementing Agency</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-dark border">chapter-view</span>
                                </div>
                            </td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2 badge-soft-blue">5 Months Ago</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                        <i data-lucide="more-vertical" width="20"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="#" data-action="edit"><i data-lucide="edit-2" class="me-2" width="16"></i>Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" data-action="delete"><i data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-dark">Staff</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-dark border">agency-create</span>
                                    <span class="badge bg-light text-dark border">agency-delete</span>
                                    <span class="badge bg-light text-dark border">agency-edit</span>
                                </div>
                            </td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2 badge-soft-blue">5 Months Ago</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                        <i data-lucide="more-vertical" width="20"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="#" data-action="edit"><i data-lucide="edit-2" class="me-2" width="16"></i>Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" data-action="delete"><i data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-dark">User Guide</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-dark border">user-view</span>
                                    <span class="badge bg-light text-dark border">user-create</span>
                                    <span class="badge bg-light text-dark border">user-edit</span>
                                    <span class="badge bg-light text-dark border">project-view</span>
                                    <span class="badge bg-light text-dark border">project-create</span>
                                    <span class="badge bg-light text-dark border">chapter-view</span>
                                </div>
                            </td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2 badge-soft-blue">7 Months Ago</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
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
            
             <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">Showing 1 to 3 of 3 entries</span>
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
