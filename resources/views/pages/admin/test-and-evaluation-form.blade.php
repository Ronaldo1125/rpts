<link rel="stylesheet" href="/css/assessment.css">
<section id="test-and-evaluation-form" class="page-content container-fluid py-4 text-dark">
    

    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-soft-pill badge-soft-blue">FORM-CTE-01</span>
                <span class="text-muted small">Checklist for RDIP Inclusion</span>
            </div>
            <h2 class="fw-bold mb-0 fs-1-3">Completeness Test and Validation</h2>
            <p class="text-muted small mb-0">Regional Development Council — Bicol Region</p>
        </div>
        <button class="btn-back-list no-print" id="cteBackToList">
            <i data-lucide="chevron-left" width="16"></i> Back to List
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-16 overflow-hidden">
        <div class="header-accent-blue"></div>
        <div class="card-body p-4 p-md-5">
            <form id="completenessForm" novalidate>

                <div class="mb-4" id="sdg-alignment-section">
        <label class="form-label small fw-semibold text-secondary">
            Project Titles <span class="text-danger">*</span>
        </label>
        <div class="position-relative" id="project-title-section">
            <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white rounded-12 min-h-38 pointer" id="f-alignment-box" tabindex="0">
                <input type="text" name="sdg_tags" class="border-0 flex-grow-1 p-0 m-0 bg-transparent min-w-120 pointer caret-transparent" id="project-title-input"
                    placeholder="Select Project Titles..." autocomplete="off" readonly>
            </div>
            <ul class="dropdown-menu w-100 shadow-sm max-h-250 overflow-y-auto" id="project-title-dropdown">
                <!-- Populated dynamically from CIPG submissions -->
            </ul>
            <input type="hidden" id="f-alignment" value="" data-required>
            <div class="invalid-feedback" id="f-alignment-err">Please select at least one project title.</div>
        </div>
    </div>

                <div class="form-section-header">
                    <span class="form-section-num">I</span> Project Details &amp; Transmittal
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold text-secondary">Program/Project Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-12" name="projectTitle" placeholder="Enter Program/Project Title" required>
                        <div class="invalid-feedback">Project Title is required.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary">Implementing Agency <span class="text-danger">*</span></label>
                        <select class="form-select rounded-12" name="implementingAgency" id="implementingAgencySelect" required>
                            <option value="">Select Agency</option>
                            <!-- Options will be populated dynamically -->
                        </select>
                        <div class="invalid-feedback">Implementing Agency is required.</div>
                    </div>
                </div>

                <div class="form-section-header mt-4">
                    <span class="form-section-num">II</span> Transmittal letter signed by
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary">Head of Agency/Authorized Official <span class="text-danger">*</span></label>
                    <input type="text" class="form-control rounded-12" name="authorizedOfficial" placeholder="Enter Head of Agency/Authorized Official" required>
                    <div class="invalid-feedback">Authorized Official name is required.</div>
                </div>

                <div class="form-section-header mt-4">
                    <span class="form-section-num">III</span> Contact Persons and Details
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light border-0 rounded-16 h-100 p-4">
                            <h6 class="fw-bold text-primary-rpts mb-3 fs-0-85">Director Level</h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm rounded-8" name="dir_name" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Designation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm rounded-8" name="dir_designation" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Office <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm rounded-8" name="dir_office" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Tel. No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm rounded-8" name="dir_tel" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-sm rounded-8" name="dir_email" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border-0 rounded-16 h-100 p-4">
                            <h6 class="fw-bold text-primary-rpts mb-3 fs-0-85">Focal Technical Staff</h6>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm rounded-8" name="focal_name" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Designation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm rounded-8" name="focal_designation" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Office <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm rounded-8" name="focal_office" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Tel. No. <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm rounded-8" name="focal_tel" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-secondary mb-1">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-sm rounded-8" name="focal_email" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section-header mt-4">
                    <span class="form-section-num">IV</span> Documentary Requirements Checklist
                </div>

                <div class="table-responsive bg-light rounded-16 p-0 mb-4 overflow-hidden">
                    <table class="table table-bordered mb-0 fs-0-85">
                        <thead class="bg-primary bg-opacity-10">
                            <tr>
                                <th class="text-secondary fw-semibold p-3 w-35">CIPG Requisite Document</th>
                                <th class="text-secondary fw-semibold p-3 w-12">Date of Submission</th>
                                <th class="text-secondary fw-semibold p-3 w-13">Date Received by RDC</th>
                                <th class="text-secondary fw-semibold p-3 w-15">Status</th>
                                <th class="text-secondary fw-semibold p-3 w-25">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3">
                                    <p class="mb-0 fw-medium text-dark">Official request for the projects/program's inclusion in the RDIP by the head of the implementing agency</p>
                                </td>
                                <td class="p-3">
                                    <input type="date" class="form-control form-control-sm rounded-8" name="doc1_date_sub">
                                </td>
                                <td class="p-3">
                                    <input type="date" class="form-control form-control-sm rounded-8" name="doc1_date_rec">
                                </td>
                                <td class="p-3">
                                    <select class="form-select form-select-sm rounded-8" name="doc1_status">
                                        <option value="">-- Status --</option>
                                        <option value="Submitted">Submitted</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Incomplete">Incomplete</option>
                                    </select>
                                </td>
                                <td class="p-3">
                                    <textarea class="form-control form-control-sm rounded-8" name="doc1_remarks" rows="2" placeholder="Note/Remarks..."></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3">
                                    <p class="mb-1 fw-medium text-dark">Comprehensive Project Profile, including the following:</p>
                                    <ul class="mb-0 ps-3 text-muted small">
                                        <li>Accomplished sector-specific GAD checklist of the HGDG</li>
                                        <li>Pre-feasibility study (FS) or FS, if available</li>
                                    </ul>
                                </td>
                                <td class="p-3">
                                    <input type="date" class="form-control form-control-sm rounded-8" name="doc2_date_sub">
                                </td>
                                <td class="p-3">
                                    <input type="date" class="form-control form-control-sm rounded-8" name="doc2_date_rec">
                                </td>
                                <td class="p-3">
                                    <select class="form-select form-select-sm rounded-8" name="doc2_status">
                                        <option value="">-- Status --</option>
                                        <option value="Submitted">Submitted</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Incomplete">Incomplete</option>
                                    </select>
                                </td>
                                <td class="p-3">
                                    <textarea class="form-control form-control-sm rounded-8" name="doc2_remarks" rows="2" placeholder="Note/Remarks..."></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3">
                                    <p class="mb-1 fw-medium text-dark">Endorsement/s, as applicable:</p>
                                    <ul class="mb-0 ps-3 text-muted small">
                                        <li>Sangguniang Panlalawigan resolutions or ordinance approving the programs/projects by local development councils of the province, component cities, or municipalities; or</li>
                                        <li>Sangguniang Panlungsod or resolution or ordinance approving the programs/projects by local development councils of highly urbanized cities or independent component cities; or</li>
                                        <li>Certification from LGU planning office that the program/project is in their Local Development Investment Program; or</li>
                                        <li>Resolution of the Board of Trustees/Regents endorsing the project and certifying that the project is in the Land Use Development and Infrastructure Plan (LUDIP) of the concerned SUCs as approved by the Board of Trustees/Regents in the case of infrastructure projects.</li>
                                    </ul>
                                </td>
                                <td class="p-3">
                                    <input type="date" class="form-control form-control-sm rounded-8" name="doc3_date_sub">
                                </td>
                                <td class="p-3">
                                    <input type="date" class="form-control form-control-sm rounded-8" name="doc3_date_rec">
                                </td>
                                <td class="p-3">
                                    <select class="form-select form-select-sm rounded-8" name="doc3_status">
                                        <option value="">-- Status --</option>
                                        <option value="Submitted">Submitted</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Incomplete">Incomplete</option>
                                    </select>
                                </td>
                                <td class="p-3">
                                    <textarea class="form-control form-control-sm rounded-8" name="doc3_remarks" rows="3" placeholder="Note/Remarks..."></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Post-Submission Referral Modal -->
                <div class="modal fade" id="postFinalizeModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                            <div class="modal-accent-primary"></div>
                            <div class="modal-header border-0 pb-0 pt-4 px-4 text-center d-block">
                                <h4 class="modal-title fw-bold">Validation Finalized!</h4>
                                <p class="text-muted">The project has been successfully validated. Would you like to refer it now for PAR assessment?</p>
                            </div>
                            <div class="modal-body p-4">
                                <div class="card bg-light border-0 rounded-16">
                                    <div class="card-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold text-secondary">Refer to Division <span class="text-danger">*</span></label>
                                                <select class="form-select border-0 shadow-sm rounded-12 p-10-16" id="modalReferralTarget">
                                                    <option value="">-- Select Division --</option>
                                                    <option value="PDIPBD">PDIPBD</option>
                                                    <option value="PMED">PMED</option>
                                                    <option value="PFPD">PFPD</option>
                                                    <option value="DRD">DRD</option>
                                                    <option value="FAD">FAD</option>
                                                </select>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <label class="form-label small fw-bold text-secondary">Instructions / Note for Assessor <span class="text-muted fw-normal">(Optional)</span></label>
                                                <textarea class="form-control border-0 shadow-sm rounded-12 p-10-16" id="modalReferralNotes" rows="2" placeholder="Optional notes for the PAR stage..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pb-4 px-4 justify-content-center gap-2">
                                <button type="button" class="btn btn-light px-4 rounded-pill fw-semibold" id="modalSkipBtn">
                                    No, Back to List
                                </button>
                                <button type="button" class="btn btn-primary-rpts text-white px-5 rounded-pill fw-semibold" id="modalSubmitRefBtn">
                                    <i data-lucide="send" class="me-2" width="16"></i> Yes, Refer Project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="main-form-actions" class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-primary-rpts text-white px-5 rounded-pill fw-bold" id="cteFinalizeBtn">
                        Finalize Validation
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
