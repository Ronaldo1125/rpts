<section id="cpp-form" class="page-content container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="cpp-badge">FM-PDI-01</span>
                <span class="text-muted small">Revision No. 01 · Effectivity: August 1, 2025 · Annex C</span>
            </div>
            <h2 class="fw-bold mb-0 text-dark" style="font-size:1.3rem;">Comprehensive Project Profile</h2>
            <p class="text-muted small mb-0">Regional Development Council — Bicol Region</p>
        </div>
        <button class="btn-back-dash" id="cppBackSubmissions">
            <i data-lucide="chevron-left" width="16"></i> Back to Submissions
        </button>
    </div>

    <div class="cpp-float-actions no-print" id="cppFloatActions">
        <button class="btn-cpp-draft" id="cppDraftBtn" onclick="cppDraft()" title="Save your progress as Draft">
            <i data-lucide="save" width="18"></i> Save as Draft
        </button>
    </div>

    <!-- VIEW MODE ACTIONS (Attachments, Version History, etc.) -->
    <div class="cpp-float-actions no-print" id="cppViewActions" style="display: none;">
        <button class="btn-cpp-draft" id="viewAttachmentsBtn" title="View Submitted Attachments">
            <i data-lucide="paperclip" width="18"></i> Attachments
        </button>
        <button class="btn-cpp-draft" id="viewHistoryBtn" title="View Submission History">
            <i data-lucide="clock" width="18"></i> Version History
        </button>
    </div>

    <!-- Step Progress Indicator -->
    <div class="cpp-stepper no-print" id="cppStepper">
        <div class="cpp-step active" data-step="1">
            <div class="step-circle">1</div>
            <span class="step-label">Page 1 — Project Info</span>
        </div>
        <div class="cpp-step" data-step="2">
            <div class="step-circle">2</div>
            <span class="step-label">Page 2 — Justification &amp; Financing</span>
        </div>
        <div class="cpp-step" data-step="3">
            <div class="step-circle">3</div>
            <span class="step-label">Page 3 — Implementation</span>
        </div>
        <div class="cpp-step" data-step="4">
            <div class="step-circle">4</div>
            <span class="step-label">Page 4 — Logical Framework</span>
        </div>
        <div class="cpp-step" data-step="5">
            <div class="step-circle">5</div>
            <span class="step-label">Page 5 — Signature &amp; Submit</span>
        </div>
    </div>

    <!-- Validation Alert -->
    <div class="cpp-alert" id="cppAlert">
        <i data-lucide="alert-circle" width="16" style="flex-shrink:0;"></i>
        <span id="cppAlertMsg">Please fill in all required fields marked with * before proceeding.</span>
    </div>

    <!-- Card Wrapper -->
    <div class="card border-0 shadow-sm rounded-16 overflow-hidden">
        <div class="cpp-header-accent"></div>
        <div class="card-body p-4 p-md-5">
            <form id="cppMainForm" novalidate>
                <!-- Step pages are fetched and injected by js/cpp-form.js -->
                <div id="cpp-steps-container"></div>
            </form>

            <!-- Navigation Buttons -->
            <div class="cpp-nav no-print">
                <button class="btn btn-cpp-prev" id="cppPrevBtn" style="visibility:hidden;" onclick="cppPrev()">
                    <i data-lucide="arrow-left" width="15" class="me-1"></i> Previous
                </button>
                <span class="small text-muted" id="cppStepCounter">Step 1 of 4</span>
                <div class="d-flex gap-2">
                    <button class="btn btn-cpp-next" id="cppNextBtn" onclick="cppNext()">
                        Next <i data-lucide="arrow-right" width="15" class="ms-1"></i>
                    </button>
                    <button class="btn btn-cpp-submit d-none" id="cppSubmitBtn" onclick="cppSubmit()">
                        <i data-lucide="send" width="15" class="me-1"></i> Submit CPP
                    </button>
                </div>
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
                <div id="attachments-modal-list"></div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:1rem 1.75rem 1.5rem;">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- FINDINGS & RECOMMENDATIONS MODAL -->
<div class="modal fade no-print" id="parCommentsModal" tabindex="-1" aria-labelledby="parCommentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow card-16">
            <div class="header-accent-blue" style="height: 6px; background: #154A9A;"></div>
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="parCommentsModalLabel">Findings & Recommendations</h5>
                    <p class="text-muted small mb-0">Record technical observations and required actions for this evaluation.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="parFindingsContainer" class="d-flex flex-column gap-3">
                    <!-- Dynamic rows will be injected here -->
                </div>
                
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4" id="btnAddFindingRow">
                        <i data-lucide="plus-circle" width="16" class="me-2"></i> Add New Finding
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center">
                <div class="text-muted small" id="parCommentsDateStatus"></div>
                <div>
                    <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 shadow-sm fw-bold" id="btnSaveCommentsOnly">
                        <i data-lucide="save" width="18" class="me-2"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" id="btnSubmitCommentsAgency">
                        <i data-lucide="send" width="18" class="me-2"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Version History Modal -->
<div class="modal fade" id="versionHistoryModal" tabindex="-1" aria-labelledby="versionHistoryModalLabel" aria-hidden="true">
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
                <div id="version-history-list" style="max-height:420px; overflow-y:auto; padding:1.25rem 1.75rem;"></div>
            </div>
            <div class="modal-footer border-0 pt-0" style="padding:1rem 1.75rem 1.5rem;">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
