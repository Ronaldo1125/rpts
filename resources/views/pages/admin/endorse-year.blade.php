<section id="endorse-year" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Manage Endorse Years</h2>
        </div>
        <div>
            <button class="btn btn-primary text-white px-4 py-2 fw-medium rounded-pill" style="background-color: #154A9A; border-color: #154A9A;" data-bs-toggle="modal" data-bs-target="#addEndorseYearModal">
                <i data-lucide="plus" class="me-1" width="18"></i> Create Endorse Year
            </button>
        </div>
    </div>

    <!-- Add Endorse Year Modal -->
    <div class="modal fade" id="addEndorseYearModal" tabindex="-1" aria-labelledby="addEndorseYearModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1rem;">
                <!-- Header Accent -->
                <div style="height: 4px; background-color: #154A9A;"></div>

                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="addEndorseYearModalLabel">Add Endorse Year</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="addEndorseYearForm">
                        <div class="mb-0">
                            <label for="endorseYear" class="form-label small fw-semibold text-secondary mb-1">Endorse Year</label>
                            <input type="text" class="form-control" id="endorseYear" placeholder="Enter Endorse Year" style="border-radius: 0.75rem;" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addEndorseYearForm" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="background-color: #0248D4; border-color: #0248D4; border-radius: 0.75rem !important;">Save</button>
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
                            <th class="small text-secondary">Endorse Year</th>
                            <th class="small text-secondary">Created At</th>
                            <th class="small text-secondary" data-sort-skip="true">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-primary" style="color: inherit !important;">1111</td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2" style="background-color: #e8f0fe; color: #0032A6;">2 Weeks Ago</span></td>
                            <td>
                                <div class="dropdown position-static">
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
                            <td class="text-primary" style="color: inherit !important;">2023</td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2" style="background-color: #e8f0fe; color: #0032A6;">2 Months Ago</span></td>
                            <td>
                                <div class="dropdown position-static">
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
                            <td class="text-primary" style="color: inherit !important;">2024</td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2" style="background-color: #e8f0fe; color: #0032A6;">2 Months Ago</span></td>
                            <td>
                                <div class="dropdown position-static">
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
                            <td class="text-primary" style="color: inherit !important;">2025</td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2" style="background-color: #e8f0fe; color: #0032A6;">2 Months Ago</span></td>
                            <td><button class="btn btn-sm btn-link text-dark p-0"><i data-lucide="more-vertical" width="20"></i></button></td>
                        </tr>
                        <tr>
                            <td class="text-primary" style="color: inherit !important;">2026</td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2" style="background-color: #e8f0fe; color: #0032A6;">2 Months Ago</span></td>
                            <td><button class="btn btn-sm btn-link text-dark p-0"><i data-lucide="more-vertical" width="20"></i></button></td>
                        </tr>
                        <tr>
                            <td class="text-primary" style="color: inherit !important;">2027</td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2" style="background-color: #e8f0fe; color: #0032A6;">2 Months Ago</span></td>
                            <td><button class="btn btn-sm btn-link text-dark p-0"><i data-lucide="more-vertical" width="20"></i></button></td>
                        </tr>
                         <tr>
                            <td class="text-primary" style="color: inherit !important;">2028</td>
                            <td><span class="badge rounded-pill fw-medium small px-3 py-2" style="background-color: #e8f0fe; color: #0032A6;">2 Months Ago</span></td>
                            <td><button class="btn btn-sm btn-link text-dark p-0"><i data-lucide="more-vertical" width="20"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">Showing 1 to 7 of 7 entries</span>
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
