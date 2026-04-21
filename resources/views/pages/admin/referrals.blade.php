<link rel="stylesheet" href="/css/referrals.css">
<section id="referrals" class="page-content container-fluid py-4 text-dark">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Project Referrals</h2>
            <p class="text-muted small mb-0">List of projects referred from Completeness Test for PAR Assessment</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary-rpts text-white px-4 py-2 fw-semibold rounded-pill" id="newReferralBtn">
                <i data-lucide="plus-circle" class="me-2" width="16"></i> New Referral
            </button>
        </div>
    </div>

    <!-- New Referral Modal -->
    <div class="modal fade" id="newReferralModal" tabindex="-1" aria-labelledby="newReferralModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                <div class="modal-accent-primary"></div>
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="newReferralModalLabel">Create New Project Referral</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="newReferralForm">
                        <div class="row g-4">
                            <!-- Project Selection -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">A. Select Validated Project</label>
                                <select class="form-select bg-light border-0 py-2 rounded-12" name="projectSelect" id="referralProjectSelect" required>
                                    <option value="">-- Choose from Completeness Test Results --</option>
                                    <!-- Dynamic Options from CTE -->
                                </select>
                                <div class="form-text mt-1 small">Only projects with "Final" validation status are listed here.</div>
                            </div>

                            <!-- Sender Info (Now Automated) -->
                            <div class="col-12 pt-1">
                                <p class="text-muted small italic px-1"><i data-lucide="info" width="14" class="me-1"></i> Referrer identity will be automatically recorded as the currently logged-in user.</p>
                            </div>

                            <!-- Recipient Info -->
                            <div class="col-md-6 pt-2">
                                <label class="form-label small fw-bold text-secondary">C. Refer To (Division)</label>
                                <select class="form-select bg-light border-0 py-2 rounded-12" name="referredToDivision" required>
                                    <option value="">-- Select Recipient Division --</option>
                                    <option value="PMED">PMED</option>
                                    <option value="PFPD">PFPD</option>
                                    <option value="DRD">DRD</option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="col-12 pt-2">
                                <label class="form-label small fw-bold text-secondary">D. Additional Instructions/Notes</label>
                                <textarea class="form-control bg-light border-0 rounded-12" name="referralNotes" rows="3" placeholder="Provide context or specific areas of concern for the PAR assessment..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-rpts text-white px-5 rounded-pill" id="submitReferralBtn">
                        <i data-lucide="send" class="me-2" width="16"></i> Create Referral
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Staff Modal -->
    <div class="modal fade" id="assignStaffModal" tabindex="-1" aria-labelledby="assignStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-16 overflow-hidden">
                <div class="modal-accent-primary"></div>
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="assignStaffModalLabel">Assign Technical Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Select a staff member from <span id="assign-division-name" class="fw-bold text-dark"></span> to conduct the PAR assessment.</p>
                    <form id="assignStaffForm">
                        <input type="hidden" name="referralId" id="assignReferralId">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Technical Staff</label>
                            <select class="form-select bg-light border-0 py-2 rounded-12" name="staffName" id="assignStaffSelect" required>
                                <option value="">-- Select Staff member --</option>
                                <!-- Dynamic Options -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-rpts text-white px-5 rounded-pill" id="confirmAssignBtn">
                        Confirm Assignment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-16">
        <div class="card-body d-flex flex-column" style="min-height: 400px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light entries-select" id="referralEntriesSelect">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-bold">Search:</span>
                    <div class="input-group input-group-sm search-input-group" id="referralSearchGroup">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" id="referralSearchInput" placeholder="Search referrals...">
                    </div>
                </div>
            </div>

            <div class="table-responsive table-overflow-visible">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary table-title-col">Project Title</th>
                            <th class="small text-secondary">Referrer / Sender</th>
                            <th class="small text-secondary">Target Division</th>
                            <th class="small text-secondary">Referral Date</th>
                            <th class="small text-secondary">Status</th>
                            <th class="small text-secondary table-actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="referral-tbody">
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Loading referrals...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-auto py-3">
                <span id="referral-count" class="text-muted small">Showing 1 to 2 of 2 entries</span>
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

