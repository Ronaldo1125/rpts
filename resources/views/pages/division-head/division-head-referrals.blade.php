<section id="division-head-referrals" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Division Referrals</h2>
            <p class="text-muted small mb-0">Manage projects referred to <span id="dh-division-label" class="fw-bold text-primary">your division</span></p>
        </div>
    </div>

    <!-- Assign Staff Modal -->
    <div class="modal fade" id="dhAssignStaffModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Assign Technical Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Select a staff member from your division to conduct the PAR assessment.</p>
                    <form id="dhAssignStaffForm">
                        <input type="hidden" name="referralId" id="dhAssignReferralId">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Technical Staff</label>
                            <select class="form-select bg-light border-0 py-2" name="staffName" id="dhAssignStaffSelect" required>
                                <option value="">-- Select Staff member --</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn text-white px-5 rounded-pill" id="dhConfirmAssignBtn" style="background-color: #154A9A;">
                        Confirm Assignment
                    </button>
                </div>
            </div>
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
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search referrals...">
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Project Title</th>
                            <th class="small text-secondary">Referrer</th>
                            <th class="small text-secondary">Assigned Staff</th>
                            <th class="small text-secondary">Referral Date</th>
                            <th class="small text-secondary">Status</th>
                            <th class="small text-secondary" style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dh-referral-tbody">
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Loading referrals...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Controls -->
            <div class="d-flex justify-content-between align-items-center mt-auto py-3">
                <span class="text-muted small" id="dh-referral-count-label">Showing 0 to 0 of 0 entries</span>
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
