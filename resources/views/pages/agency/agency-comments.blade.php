<section id="agency-comments" class="page-content container-fluid py-4">
    <style>
        #agency-comments .badge-tag {
            font-size: 0.68rem;
            letter-spacing: 0.06em;
            background: #e8f0fe;
            color: #154A9A;
            padding: 0.3em 0.9em;
            border-radius: 50px;
            font-weight: 700;
        }
        #agency-comments .card {
            border-radius: 1rem;
            overflow: hidden;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        #agency-comments .table thead th {
            background-color: #f8fafc;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        #agency-comments .table tbody td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        #agency-comments .table tbody tr:hover {
            background-color: #f8fafc;
        }
        #agency-comments .status-badge {
            padding: 0.4em 0.8em;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 6px;
        }
        #agency-comments .status-pending { background: #fee2e2; color: #dc2626; }
        #agency-comments .status-responded { background: #dcfce7; color: #16a34a; }
        
        #agency-comments .action-btn {
            padding: 0.5rem 1rem;
            font-weight: 600;
            font-size: 0.8rem;
            border-radius: 8px;
            transition: all 0.2s;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Comments & Recommendations</h2>
            <p class="text-muted small mb-0">View RDC feedback and provide agency responses</p>
        </div>
    </div>

    <!-- Comments List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column" style="min-height: 300px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light" style="width: 70px;" id="agencyCommentEntriesSelect">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-bold">Search:</span>
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" id="searchTitle" placeholder="Search documents...">
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Document Title</th>
                            <th>Projects Evaluated</th>
                            <th>Total Findings</th>
                            <th>Status</th>
                            <th>Date Received</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="agencyCommentsTableBody">
                        <tr class="text-center">
                            <td colspan="6" class="py-4 text-muted small">
                                <i data-lucide="inbox" width="48" class="mb-2"></i>
                                <div>No comments and recommendations found</div>
                                <small class="text-muted">Check back later for RDC feedback on your submissions</small>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <span id="agencyCommentCount" class="text-muted small">Showing 0 to 0 of 0 entries</span>
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

    <!-- View Comments Modal -->
    <div class="modal fade" id="viewCommentsModal" tabindex="-1" aria-labelledby="viewCommentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="viewCommentsModalLabel">
                        <i data-lucide="message-square" width="20" class="me-2"></i>
                        Comments & Recommendations
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-semibold text-secondary mb-3">Document Information</h6>
                            <div class="d-flex flex-wrap gap-4">
                                <div class="mb-2">
                                    <small class="text-muted d-block">Title:</small>
                                    <div id="modalDocTitle" class="fw-medium"></div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Date Received:</small>
                                    <div id="modalDateReceived" class="fw-medium"></div>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block">Status:</small>
                                    <div id="modalStatus"></div>
                                </div>
                                <div class="mb-2 ms-auto text-end">
                                    <small class="text-muted d-block">Last Response Update:</small>
                                    <div id="responseLastUpdated" class="fw-medium small">Not yet responded</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="fw-semibold text-secondary mb-3">Projects and Findings</h6>
                    <div id="projectsFindingsContainer">
                        <!-- Dynamically populated -->
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary px-4" id="btnSaveAgencyResponse" 
                        style="background-color: #154A9A; border-color: #154A9A;">
                        <i data-lucide="save" width="16" class="me-1"></i> Save Response
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
