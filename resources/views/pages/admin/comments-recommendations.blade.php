<link rel="stylesheet" href="/css/comments.css">
<section id="comments-recommendations" class="page-content container-fluid py-4 text-dark">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
        <div>
            <h2 class="fw-bold mb-0">Comments & Recommendations</h2>
            <p class="text-muted small mb-0">Manage project comments and recommendations for the RDC process</p>
        </div>
        <div class="d-flex align-items-center">
            <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                <select class="form-select border-0 px-4 py-2" id="listParSelect" style="min-width: 950px; font-size: 0.85rem; background-color: #f8fafc;">
                    <option value="">-- Choose PAR Assessment to add Comments & Recommendations --</option>
                </select>
                <button class="btn btn-primary-rpts text-white fw-bold px-4 cr-action-btn" type="button" id="newCommentsBtn">
                    <i data-lucide="message-square" class="me-2" width="18"></i> New Comment
                </button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-16">
        <div class="card-body d-flex flex-column" style="min-height: 300px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light entries-select" id="commentEntriesSelect">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-bold">Search:</span>
                    <div class="input-group input-group-sm search-input-group" id="commentSearchGroup">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" id="commentSearchInput" placeholder="Search comments...">
                    </div>
                </div>
            </div>

            <div class="table-responsive table-overflow-visible">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 20%">Batch Title / Document</th>
                            <th style="width: 15%">Implementing Agency</th>
                            <th style="width: 10%">Projects</th>
                            <th style="width: 10%">Findings</th>
                            <th style="width: 15%">Prepared By</th>
                            <th style="width: 12%">Status</th>
                            <th style="width: 10%">Date Prepared</th>
                            <th class="text-end pe-3" style="width: 8%">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="comment-tbody">
                        <tr class="text-center">
                            <td colspan="8" class="py-4 text-muted small">No entries found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-auto py-3">
                <span id="comment-count" class="text-muted small">Showing 0 to 0 of 0 entries</span>
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
