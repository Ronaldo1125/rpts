<section id="admin-comments-recommendations" class="page-content container-fluid py-4">
    <style>
        #admin-comments-recommendations .card {
            border-radius: 1rem;
            overflow: hidden;
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        #admin-comments-recommendations .table {
            table-layout: fixed;
            width: 100%;
        }
        #admin-comments-recommendations .table thead th {
            background-color: #f8fafc;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            color: #64748b;
            padding: 0.75rem 0.75rem;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        #admin-comments-recommendations .table tbody td {
            padding: 0.85rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        #admin-comments-recommendations .table tbody tr:hover {
            background-color: #f8fafc;
        }
        #admin-comments-recommendations .cell-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: block;
        }
        #admin-comments-recommendations .batch-name {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }
        #admin-comments-recommendations .status-badge {
            padding: 0.3em 0.65em;
            font-size: 0.68rem;
            font-weight: 600;
            border-radius: 6px;
            white-space: nowrap;
            display: inline-block;
        }
        #admin-comments-recommendations .status-pending { background: #fef3c7; color: #92400e; }
        #admin-comments-recommendations .status-responded { background: #dcfce7; color: #16a34a; }
        #admin-comments-recommendations .action-btn {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
            color: #64748b;
            background: #f1f5f9;
            border: none;
        }
        #admin-comments-recommendations .action-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .cr-action-btn:hover {
            background: #0d3a7a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(21, 74, 154, 0.2);
        }
        .cr-action-btn:active {
            transform: translateY(0);
        }
    </style>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
        <div>
            <h2 class="fw-bold mb-0">Comments & Recommendations</h2>
            <p class="text-muted small mb-0">Manage and initiate project evaluations for the RDC process</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                <select class="form-select border-0 px-4 py-2" id="adminListParSelect" style="min-width: 950px; font-size: 0.85rem; background-color: #f8fafc;">
                    <option value="">-- Choose PAR Assessment to add Comments & Recommendations --</option>
                </select>
                <button class="btn text-white fw-bold px-4 cr-action-btn" type="button" id="adminNewCommentsBtn" style="background: #154A9A; transition: all 0.2s;">
                    <i data-lucide="message-square" class="me-2" width="18"></i> New Comment
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column" style="min-height: 450px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3" style="width: 80px;" id="adminCrEntriesSelect">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light border-0 rounded-start-pill px-3">
                            <i data-lucide="search" width="14" class="text-secondary"></i>
                        </span>
                        <input type="text" class="form-control bg-light border-0 rounded-end-pill px-3" id="adminCrSearchInput"
                            placeholder="Search comments...">
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="border-radius: 12px; overflow: visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-3 border-bottom-0" style="width: 22%;">Batch Title / Document</th>
                            <th class="border-bottom-0" style="width: 15%;">Implementing Agency</th>
                            <th class="border-bottom-0" style="width: 10%;">Projects</th>
                            <th class="border-bottom-0 text-center" style="width: 8%;">Findings</th>
                            <th class="border-bottom-0" style="width: 15%;">Prepared By</th>
                            <th class="border-bottom-0" style="width: 10%;">Status</th>
                            <th class="border-bottom-0" style="width: 12%;">Date Prepared</th>
                            <th class="text-end pe-3 border-bottom-0" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="admin-comment-tbody">
                        <tr class="text-center">
                            <td colspan="8" class="py-5">
                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                <span class="text-muted">Loading records...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-auto py-3 px-2">
                <span id="admin-comment-count" class="text-muted small">Showing 0 to 0 of 0 entries</span>
                <nav>
                    <ul class="custom-pagination mb-0" id="adminCrPagination">
                        <!-- Dynamic -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
