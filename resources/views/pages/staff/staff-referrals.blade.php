<section id="staff-referrals" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">My Assignments</h2>
            <p class="text-muted small mb-0">Projects assigned to you for PAR Assessment</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column" style="min-height: 400px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <!-- Table Header Controls -->
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
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search my tasks...">
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Project Details</th>
                            <th class="small text-secondary">Division</th>
                            <th class="small text-secondary">Assignment</th>
                            <th class="small text-secondary">Received Date</th>
                            <th class="small text-secondary">Status</th>
                            <th class="small text-secondary">Action</th>
                        </tr>
                    </thead>
                    <tbody id="staff-referral-tbody">
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Loading assignments...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Controls -->
            <div class="d-flex justify-content-between align-items-center mt-auto py-3">
                <span class="text-muted small" id="staff-referral-count-label">Showing 0 to 0 of 0 entries</span>
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
