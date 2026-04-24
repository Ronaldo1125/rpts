@extends('layouts.app_v2')
@section('content')

    <section id="submissions" class="page-content container-fluid py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-0">Submit Project</h2>
                <p class="text-muted small mb-0">Comprehensive Investment Programming Guide — FM-PDI-01</p>
            </div>
            <div>
                <!-- Navigates to the full-page CPP form via SPA router -->
                <button class="btn text-white px-4 py-2 fw-semibold rounded-pill shadow-sm" id="newCppBtn"
                    style="background-color: #154A9A; border-color: #154A9A;">
                    <i data-lucide="file-plus" class="me-2" width="16"></i> New CPP Submission
                </button>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column" style="min-height: 300px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
                @include('partials.table-header')

                <div class="table-responsive" style="border-radius: 8px;">
                    <table class="table table-hover align-middle" data-sortable="true">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-secondary">Project Title</th>
                                <th class="small text-secondary">Sector</th>
                                <th class="small text-secondary">Stage</th>
                                <th class="small text-secondary">Status of Submission</th>
                                <th class="small text-secondary">Submitted</th>
                                <th class="small text-secondary" data-sort-skip="true">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="submissions-tbody">
                            <!-- Dynamic submissions will be injected here -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span id="submissions-count" class="text-muted small">Showing 0 to 0 of 0 entries</span>
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
    <!-- Attachments Modal -->
    <div class="modal fade" id="attachmentsModal" tabindex="-1" aria-labelledby="attachmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
                <div class="modal-header border-0 pb-0"
                    style="background: linear-gradient(135deg, #14532d 0%, #16a34a 100%); color:#fff; padding:1.5rem 1.75rem;">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="attachmentsModalLabel">
                            <i data-lucide="paperclip" width="18" class="me-2"></i>Submitted Attachments
                        </h5>
                        <p class="mb-0 small opacity-75" id="attachments-modal-project-title" style="font-size:0.8rem;"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height:520px; overflow-y:auto; padding:1.5rem 1.75rem;">
                    <div id="attachments-modal-list">
                        <!-- Injected by JS -->
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0" style="padding:1rem 1.75rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Version History Modal -->
    <div class="modal fade" id="versionHistoryModal" tabindex="-1" aria-labelledby="versionHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
                <div class="modal-header border-0 pb-0"
                    style="background: linear-gradient(135deg, #154A9A 0%, #1e6fd9 100%); color:#fff; padding:1.5rem 1.75rem;">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="versionHistoryModalLabel">
                            <i data-lucide="clock" width="18" class="me-2"></i>Submission Version History
                        </h5>
                        <p class="mb-0 small opacity-75" id="version-modal-project-title" style="font-size:0.8rem;"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="version-history-list" style="max-height:420px; overflow-y:auto; padding:1.25rem 1.75rem;">
                        <!-- Injected by JS -->
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0" style="padding:1rem 1.75rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Secretariat Comments Modal -->
    <div class="modal fade" id="parFeedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
                <div class="modal-header border-0 pb-0"
                    style="background: linear-gradient(135deg, #b45309 0%, #d97706 100%); color:#fff; padding:1.5rem 1.75rem;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:32px;height:32px;">
                                <i data-lucide="message-square" width="16" style="color: white !important;"></i>
                            </div>
                            <h5 class="modal-title fw-bold mb-0">Comments and Recommendations</h5>
                        </div>
                        <p class="mb-0 small opacity-75" id="par-feedback-modal-project-title" style="font-size:0.8rem;">
                        </p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:1.75rem;">
                    <div class="alert alert-warning border-0 rounded-4 d-flex gap-3 mb-4shadow-sm"
                        style="background:#fffbeb; color:#92400e;">
                        <i data-lucide="info" width="20" class="flex-shrink-0 mt-1" style="color:#92400e !important;"></i>
                        <div class="small">
                            Your submission has been reviewed by the technical staff. Please address the following findings
                            and recommendations before resubmitting.
                        </div>
                    </div>

                    <div id="par-feedback-list-container" class="d-flex flex-column gap-3">
                        <!-- Injected by JS -->
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:1rem 1.75rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                    <button type="button"
                        class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark border-0 edit-sub-from-feedback"
                        style="background:#f59e0b;">
                        <i data-lucide="edit-3" width="14" class="me-1"></i> Edit Submission
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection