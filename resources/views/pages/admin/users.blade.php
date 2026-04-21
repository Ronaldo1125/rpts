<link rel="stylesheet" href="/css/users.css">
<section id="users" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Manage Users</h2>
        </div>
        <div>
            <button class="btn btn-primary-rpts text-white px-4 py-2 fw-medium rounded-pill"
                 data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i data-lucide="user-plus" class="me-1" width="18"></i> Create User
            </button>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden rounded-16">
                <!-- Header Accent -->
                <div class="modal-accent-primary"></div>

                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="userName" class="form-label small fw-semibold text-secondary mb-1">Name</label>
                            <input type="text" class="form-control rounded-12" id="userName" placeholder="Enter Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail"
                                class="form-label small fw-semibold text-secondary mb-1">Email</label>
                            <input type="email" class="form-control rounded-12" id="userEmail" placeholder="Enter Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="userPassword"
                                class="form-label small fw-semibold text-secondary mb-1">Password:</label>
                            <div class="input-group">
                                <input type="password" class="form-control input-group-control-rpts border-end-0" id="userPassword"
                                    placeholder="Password" required>
                                <span class="input-group-text bg-white border-start-0 pe-3 toggle-password input-group-text-rpts">
                                    <i data-lucide="eye" class="text-secondary opacity-50" width="18" height="18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="userConfirmPassword"
                                class="form-label small fw-semibold text-secondary mb-1">Confirm Password:</label>
                            <div class="input-group">
                                <input type="password" class="form-control input-group-control-rpts border-end-0" id="userConfirmPassword"
                                    placeholder="Confirm Password" required>
                                <span class="input-group-text bg-white border-start-0 pe-3 toggle-password input-group-text-rpts">
                                    <i data-lucide="eye" class="text-secondary opacity-50" width="18" height="18"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="userRole" class="form-label small fw-semibold text-secondary mb-1">Role:</label>
                            <select class="form-select rounded-12" id="userRole" required>
                                <option value="" selected disabled>-- Select role --</option>
                                <option value="admin">Administrator</option>
                                <option value="division-head">Division Head</option>
                                <option value="staff">Staff</option>
                                <option value="agency">Implementing Agency</option>
                            </select>
                        </div>
                        <!-- Conditional Staff Fields -->
                        <div id="staffFields" class="d-none">
                            <div class="mb-3">
                                <label for="userDivision" class="form-label small fw-semibold text-secondary mb-1">Division:</label>
                                <select class="form-select rounded-12" id="userDivision">
                                    <option value="" selected disabled>-- Select division --</option>
                                    <option value="PMED">PMED</option>
                                    <option value="PFPD">PFPD</option>
                                    <option value="DRD">DRD</option>
                                    <option value="PDIPBD">PDIPBD</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-0 d-none" id="agencyFieldContainer">
                            <label for="userAgency"
                                class="form-label small fw-semibold text-secondary mb-1">Agency:</label>
                            <select class="form-select rounded-12" id="userAgency">
                                <option value="" selected disabled>-- Select agency --</option>
                                <option value="agency1">Agency 1</option>
                                <option value="agency2">Agency 2</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addUserForm" class="btn btn-primary-rpts px-4 py-2 fw-semibold shadow-sm rounded-12">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column users-card-body">
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
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3"
                            placeholder="Search...">
                    </div>
                </div>
            </div>
            <div class="table-responsive table-overflow-visible">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Name</th>
                            <th class="small text-secondary">Email Address</th>
                            <th class="small text-secondary">Agency</th>
                            <th class="small text-secondary">Joined</th>
                            <th class="small text-secondary" data-sort-skip="true">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://api.dicebear.com/7.x/initials/svg?seed=AT" class="rounded"
                                        width="32" height="32" alt="Avatar">
                                    <span>Argel Joseph S. Tria</span>
                                </div>
                            </td>
                            <td class="py-3">astria@depdev.gov.ph</td>
                            <td class="py-3"><span>Agency 1</span></td>
                            <td class="py-3"><span class="badge rounded-pill fw-medium small px-3 py-2 badge-soft-blue">3 Months Ago</span></td>
                            <td class="py-3">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown"
                                        data-bs-boundary="viewport" aria-expanded="false">
                                        <i data-lucide="more-vertical" width="20"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="#" data-action="edit"><i data-lucide="edit-2"
                                                     class="me-2" width="16"></i>Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" data-action="delete"><i
                                                     data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://api.dicebear.com/7.x/initials/svg?seed=AB" class="rounded"
                                        width="32" height="32" alt="Avatar">
                                    <span>Armylene B. Posada</span>
                                </div>
                            </td>
                            <td class="py-3">abposada@depdev.gov.ph</td>
                            <td class="py-3"><span>Agency 1</span></td>
                            <td class="py-3"><span class="badge rounded-pill fw-medium small px-3 py-2 badge-soft-blue">3 Months Ago</span></td>
                            <td class="py-3">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown"
                                        data-bs-boundary="viewport" aria-expanded="false">
                                        <i data-lucide="more-vertical" width="20"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><a class="dropdown-item" href="#" data-action="edit"><i data-lucide="edit-2"
                                                     class="me-2" width="16"></i>Edit</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" data-action="delete"><i
                                                     data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">Showing 1 to 2 of 2 entries</span>
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
