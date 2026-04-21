<link rel="stylesheet" href="/css/referrals.css">
<section id="admin-referrals" class="page-content container-fluid py-4 text-dark">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Project Referrals</h2>
            <p class="text-muted small mb-0">Manage and refer projects to divisions for assessment</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary-rpts text-white px-4 py-2 fw-semibold rounded-pill" id="adminNewReferralBtn">
                <i data-lucide="plus-circle" class="me-2" width="16"></i> New Referral
            </button>
        </div>
    </div>

    <!-- New Referral Modal -->
    <div class="modal fade" id="adminNewReferralModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                <div class="modal-accent-primary"></div>
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Create New Project Referral</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="adminNewReferralForm">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">Select Validated Project <span class="text-danger">*</span></label>
                                <select class="form-select bg-light border-0 py-2 rounded-12" name="projectSelect" id="adminReferralProjectSelect" required>
                                    <option value="">-- Choose from Completeness Test Results --</option>
                                </select>
                            </div>
                            <div class="col-md-12 pt-2">
                                <label class="form-label small fw-bold text-secondary">Division to Refer <span class="text-danger">*</span></label>
                                <select class="form-select bg-light border-0 py-2 rounded-12" name="referredToDivision" required>
                                    <option value="">-- Select Recipient Division --</option>
                                    <option value="PMED">PMED</option>
                                    <option value="PFPD">PFPD</option>
                                    <option value="DRD">DRD</option>
                                </select>
                            </div>
                            <div class="col-12 pt-2">
                                <label class="form-label small fw-bold text-secondary">Additional Instructions/Notes <span class="text-muted fw-normal">(Optional)</span></label>
                                <textarea class="form-control bg-light border-0 rounded-12" name="referralNotes" rows="3" placeholder="Provide context for the assessment..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-rpts text-white px-5 rounded-pill" id="adminSubmitReferralBtn">
                        <i data-lucide="send" class="me-2" width="16"></i> Create Referral
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-16">
        <div class="card-body d-flex flex-column" style="min-height: 400px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <!-- Table Header Controls -->
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
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search referrals...">
                    </div>
                </div>
            </div>

            <div class="table-responsive table-overflow-visible">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary table-title-col">Project Title</th>
                            <th class="small text-secondary">Referrer</th>
                            <th class="small text-secondary">Target Division</th>
                            <th class="small text-secondary">Referral Date</th>
                            <th class="small text-secondary">Status</th>
                            <th class="small text-secondary table-actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="admin-referral-tbody">
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Loading referrals...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Controls -->
            <div class="d-flex justify-content-between align-items-center mt-auto py-3">
                <span class="text-muted small" id="admin-referral-count">Showing 0 to 0 of 0 entries</span>
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

