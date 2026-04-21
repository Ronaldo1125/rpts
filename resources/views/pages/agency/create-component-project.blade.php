<section id="create-component-project" class="page-content container-fluid py-4">
    <div class="d-flex align-items-center mb-4 px-2">
        <button class="btn-back-dash me-3" id="compProjectBackBtn">
            <i data-lucide="chevron-left" width="16"></i> Back to Component Project List
        </button>
        <h5 class="fw-bold mb-0">Manage Component Project (New Project)</h5>
    </div>

    <div class="row g-4 px-2">
        <!-- Main Form Area -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary mb-4 border-bottom pb-3">Component Project Details</h5>

                    <form>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Component Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Enter Component Project Title" data-required>
                            <div class="invalid-feedback">Component project title is required.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Sub-Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Enter Project Title" data-required>
                            <div class="invalid-feedback">Sub-project title is required.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Sub-Project Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" rows="3" placeholder="Enter Project Description" data-required></textarea>
                            <div class="invalid-feedback">Description is required.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Indicator <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <select id="comp-indicator" class="form-select" data-required>
                                    <option value="">-- Select Indicator --</option>
                                </select>
                                <button class="btn btn-outline-primary" type="button" id="comp-add-indicator-btn" title="Add New Indicator">
                                    <i data-lucide="plus" width="16"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please select an indicator.</div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">Agency <span class="text-danger">*</span></label>
                                <select class="form-select" data-required>
                                    <option value="">-- Select Agency --</option>
                                </select>
                                <div class="invalid-feedback">Agency is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">Sector <span class="text-danger">*</span></label>
                                <select class="form-select" data-required>
                                    <option value="">-- Select Sector --</option>
                                </select>
                                <div class="invalid-feedback">Sector is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">Sub-Sector <span class="text-danger">*</span></label>
                                <select class="form-select" disabled data-required>
                                    <option value="">-- Select Sub-Sector --</option>
                                </select>
                                <div class="invalid-feedback">Sub-sector is required.</div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">Status <span class="text-danger">*</span></label>
                                <select class="form-select" data-required>
                                    <option value="">-- Select Status --</option>
                                </select>
                                <div class="invalid-feedback">Status is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">Funding Requirement <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" data-required onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                <div class="invalid-feedback">Funding requirement is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-secondary">Funding Category <span class="text-danger">*</span></label>
                                <select class="form-select" data-required>
                                    <option value="">-- Select Funding Category --</option>
                                </select>
                                <div class="invalid-feedback">Funding category is required.</div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">Year of Endorsement</label>
                                <select class="form-select">
                                    <option selected>-- Select Year of Endorsement --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-secondary">RDC Endorsement Number</label>
                                <input type="text" class="form-control">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">RDP Chapter <span class="text-danger">*</span></label>
                            <select class="form-select" data-required>
                                <option value="">-- Select RDP Chapters --</option>
                            </select>
                            <div class="invalid-feedback">RDP Chapter is required.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary d-block">Location</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="locationOptions" id="regionwide_comp"
                                    value="regionwide" checked>
                                <label class="form-check-label small" for="regionwide_comp">Regionwide</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="locationOptions"
                                    id="interProvince_comp" value="interProvince">
                                <label class="form-check-label small" for="interProvince_comp">Inter-Province</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="locationOptions"
                                    id="locationSpecific_comp" value="locationSpecific">
                                <label class="form-check-label small" for="locationSpecific_comp">Location
                                    Specific</label>
                            </div>
                        </div>
                </div>
            </div>

            <!-- Physical Target & Project Cost -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary text-uppercase small ls-1 mb-3">Physical Target</h6>
                    <div class="row g-2">
                        <div class="row g-2">
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2023</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2024</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2025</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2026</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2027</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2028</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted mb-1 d-block">Succeeding Years</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary text-uppercase small ls-1 mb-3">Project Cost (PM)</h6>
                    <div class="row g-2">
                        <div class="row g-2">
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2023</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2024</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2025</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2026</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2027</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col">
                                    <label class="small text-muted mb-1 d-block">2028</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted mb-1 d-block">Succeeding Years</label>
                                    <input type="number" class="form-control form-control-sm" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                                </div>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info Section (Same as Project Workspace) -->
            <div class="card border-0 shadow-sm mt-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary text-uppercase small ls-1 mb-3">Additional Information</h6>
                    <div class="row g-4">
                        <div class="col-md-7">
                            <label class="form-label small fw-bold text-secondary">Remarks</label>
                            <textarea class="form-control" rows="5" placeholder="Enter Project Remarks"></textarea>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-secondary">Attachments</label>
                            <div id="componentDropZone"
                                class="border border-2 border-dashed p-4 text-center rounded bg-light mb-2 cursor-pointer"
                                style="border-style: dashed !important;" tabindex="0" role="button">
                                <i data-lucide="upload-cloud" class="d-block mx-auto mb-2 text-primary" width="32"></i>
                                <small class="text-muted">Drop files or click to upload</small>
                            </div>
                            <input type="file" id="componentFileInput" name="component_files[]" multiple
                                style="display:none">
                            <div id="componentFileList" class="mt-2 text-start small"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Panel Actions -->
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 2rem; z-index: 10;">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold fs-7 mb-3 text-uppercase small text-secondary">Workspace Actions</h6>
                        <button
                            id="saveComponentProjectBtn"
                            class="btn btn-primary w-100 mb-2 py-2 fw-medium d-flex align-items-center justify-content-center gap-2"
                            style="background-color: #154A9A; border-color: #154A9A;">
                            <i data-lucide="check" width="18"></i> Save Component
                        </button>
                        <button
                            class="btn btn-outline-secondary w-100 py-2 fw-medium d-flex align-items-center justify-content-center gap-2"
                            id="compProjectCancelBtn">
                            <i data-lucide="x" width="18"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    #create-component-project .form-control.is-invalid,
    #create-component-project .form-select.is-invalid {
        border-color: #ef4444 !important;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1) !important;
    }

    #create-component-project .invalid-feedback {
        display: none;
        font-size: 0.72rem;
        color: #ef4444;
        margin-top: 0.3rem;
    }

    #create-component-project .form-control.is-invalid~.invalid-feedback,
    #create-component-project .form-select.is-invalid~.invalid-feedback {
        display: block;
    }

    .fs-7 {
        font-size: 0.8rem;
    }

    .ls-1 {
        letter-spacing: 0.5px;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .spin-animation {
        animation: spin 2s linear infinite;
    }
</style>
