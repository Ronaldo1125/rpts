<link rel="stylesheet" href="/css/comments.css">
<section id="comments-recommendations-form" class="page-content container-fluid py-4 text-dark" style="display: none;">
    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-soft-pill badge-soft-blue">FORM-CR-01</span>
                <span class="text-muted small">Evaluation Feedback Loop</span>
            </div>
            <h2 class="fw-bold mb-0 fs-1-3">Findings & Recommendations</h2>
            <p class="text-muted small mb-0">Regional Development Council — Bicol Region</p>
        </div>
        <button class="btn-back-list no-print" onclick="if(window.switchPage) window.switchPage('comments-recommendations');">
            <i data-lucide="chevron-left" width="16"></i> Back to List
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-16 overflow-hidden">
        <div class="header-accent-blue"></div>
        <div class="card-body p-4 p-md-5">
            <form id="commentsRecommendationsForm" novalidate>

                <div class="form-section-header">
                    <span class="form-section-num">I</span> General Information
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-12" id="crParSelectContainer">
                        <label class="form-label small fw-semibold text-secondary">Link to PAR <span class="text-danger">*</span></label>
                        <select class="form-select rounded-12" id="crParSelect">
                            <option value="">-- Select PAR Assessment --</option>
                        </select>
                        <div class="form-text text-muted small">Selecting a PAR will automatically load its project list.</div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-secondary">Document / Batch Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-12" name="batchTitle" placeholder="e.g. Proposed DepEd Projects for Inclusion in the RDIP 2023-2028">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-secondary">Implementing Agency <span class="text-danger">*</span></label>
                        <select class="form-select rounded-12" name="implementingAgency" id="implementingAgencySelect" disabled>
                            <option value="">-- Select Agency --</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 mb-3 border-bottom-soft pb-3">
                    <div class="form-section-header mb-0 border-bottom-0 pb-0">
                        <span class="form-section-num">II</span> Evaluated Projects
                    </div>
                </div>

                <!-- Accordion Container for Projects -->
                <div class="accordion d-flex flex-column gap-3" id="projectsAccordion">
                    <!-- Dynamically populated -->
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                    <button type="button" class="btn btn-light px-4 shadow-sm text-secondary fw-semibold border rounded-pill" onclick="if(window.switchPage) window.switchPage('comments-recommendations');">Cancel</button>
                    <button type="button" class="btn btn-submit-action shadow-sm rounded-pill" id="btnSaveComments">Save Comments & Recommendations</button>
                </div>

            </form>
        </div>
    </div>
</section>
