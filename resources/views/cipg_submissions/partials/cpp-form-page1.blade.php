<!-- ══════════════════════════════════════════════════════
     CPP FORM — PAGE 1
     Sections: Agency/Sector · I. Project Information
               II. Project Status · III. Endorsements
════════════════════════════════════════════════════════ -->
<div class="form-step active" id="step-1">

    <!-- Agency & Sector -->
    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary" for="f-agency">
            Implementing Agency <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control bg-light" value="{{ auth()->user()->agency ? auth()->user()->agency->agency_name . ' (' . auth()->user()->agency->agency_acronym . ')' : 'No Agency Assigned' }}" placeholder="The name of the agency proposing the project." readonly>
        <input type="hidden" id="f-agency" name="agency_id" value="{{ auth()->user()->agency_id ?? '' }}" data-required>
        <div class="invalid-feedback">Implementing agency is required.</div>
    </div>
    <div class="row g-2 mb-4">
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-sector">
                Sector <span class="text-danger">*</span>
            </label>
            <select class="form-select" id="f-sector" name="f-sector" data-required title="Indicate what sector the project falls i.e.; economic, infrastructure, development administration, social sector projects.">
                <option value="">-- Select Sector --</option>
                @foreach($sectors as $sector)
                    <option value="{{ $sector->id }}">{{ $sector->sector_name }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">Please select a sector.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-sub-sector">
                Sub-Sector <span class="text-danger">*</span>
            </label>
            <select class="form-select" id="f-sub-sector" name="f-sub-sector" data-required disabled>
                <option value="">-- Select Sub-Sector --</option>
            </select>
            <div class="invalid-feedback">Please select a sub-sector.</div>
        </div>
    </div>

    <!-- I. Project Information -->
    <div class="section-heading">
        <span class="section-num">I</span> Project Information
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary" for="f-title">
            1. Project Title <span class="text-danger">*</span>
        </label>
            <textarea class="form-control" id="f-title" name="f-title" rows="3"
            placeholder="Short but descriptive of the nature of the project."
            data-required></textarea>
        <div class="invalid-feedback">Project title is required.</div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            2. Project Type <span class="text-danger">*</span>
        </label>
        <div class="d-flex gap-4 mt-1" id="type-group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="type-capital" name="project-type"
                    value="Capital Outlay">
                <label class="form-check-label small" for="type-capital" title="Refers to construction or acquisition of equipment and detailed engg studies.">Capital Outlay</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="type-technical" name="project-type"
                    value="Technical Assistance">
                <label class="form-check-label small" for="type-technical" title="FS, institutional and human capability bldg. activities.">Technical Assistance</label>
            </div>
        </div>
        <div class="invalid-feedback d-block" id="type-error" style="display:none!important;">Project type is required
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary" for="f-components">3. Project Components</label>
        <input type="text" class="form-control" id="f-components" name="f-components"
            placeholder="For multi-sectoral or IAD projects each major component must have a separate sub-project profile.">
    </div>

    <!-- Location -->
    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary mb-2">
            4. Project Location <span class="text-danger">*</span>
        </label>
        <div class="d-flex gap-4" id="coverage-group">
            <div class="form-check" title="Indicate where the area will be implemented or coverage area.">
                <input class="form-check-input" type="radio" name="project-coverage" id="coverage-regionwide"
                    value="Regionwide">
                <label class="form-check-label small fw-medium" for="coverage-regionwide">Regionwide</label>
            </div>
            <div class="form-check" title="Indicate where the area will be implemented or coverage area.">
                <input class="form-check-input" type="radio" name="project-coverage" id="coverage-inter-province"
                    value="Inter-Province">
                <label class="form-check-label small fw-medium" for="coverage-inter-province">Inter-Province</label>
            </div>
            <div class="form-check" title="Indicate where the area will be implemented or coverage area.">
                <input class="form-check-input" type="radio" name="project-coverage" id="coverage-location-specific"
                    value="Location-Specific">
                <label class="form-check-label small fw-medium" for="coverage-location-specific">Location-Specific</label>
            </div>
        </div>
        <div class="invalid-feedback d-block" id="coverage-error" style="display:none!important;">Project coverage is
            required.</div>
    </div>

    <div id="location-specific-fields" style="display: none;">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary" for="f-province">
                    Province <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="f-province" name="f-province" required disabled title="Indicate where the area will be implemented or coverage area.">
                    <option value="">-- Select Province --</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}">{{ $province->province_name }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">Province is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary" for="f-district">District <span
                        class="text-danger">*</span></label>
                <select class="form-select" id="f-district" name="f-district" required disabled title="Indicate where the area will be implemented or coverage area.">
                    <option value="">-- Select District --</option>
                </select>
                <div class="invalid-feedback">District is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary" for="f-municipality">City / Municipality <span
                        class="text-danger">*</span></label>
                <select class="form-select" id="f-municipality" name="f-municipality" required disabled title="Indicate where the area will be implemented or coverage area.">
                    <option value="">-- Select City/Municipality --</option>
                </select>
                <div class="invalid-feedback">City/Municipality is required.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold text-secondary" for="f-barangay">Barangay <span
                        class="text-secondary fw-normal">(Optional)</span></label>
                <select class="form-select" id="f-barangay" name="f-barangay" disabled title="Indicate where the area will be implemented or coverage area.">
                    <option value="">-- Select Barangay --</option>
                </select>
            </div>
        </div>
    </div>

    <div id="inter-province-fields" style="display: none;">
        <div class="mt-3" title="Indicate where the area will be implemented or coverage area.">
            <label class="form-label small fw-semibold text-secondary mb-1" for="provinces-tag-input">Provinces <span
                    class="text-danger">*</span></label>
            <div class="position-relative" id="provinces-tag-container">
                <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white" id="f-provinces-box"
                    tabindex="0" style="min-height: 38px; cursor: pointer;">
                    <input type="text" class="border-0 flex-grow-1 p-0 m-0 bg-transparent" id="provinces-tag-input"
                        style="outline: none; min-width: 120px; cursor: pointer; caret-color: transparent;" placeholder="Select provinces..." autocomplete="off">
                </div>
                <ul class="dropdown-menu w-100 shadow-sm" id="provinces-dropdown"
                    style="max-height: 200px; overflow-y: auto;">
                    @foreach($provinces as $province)
                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-val="{{ $province->province_name }}"><input
                                class="form-check-input mt-0 pe-none" type="checkbox"> {{ $province->province_name }}</a></li>
                    @endforeach
                </ul>
                <input type="hidden" id="f-provinces" value="" data-required>
                <div class="invalid-feedback d-block" id="f-provinces-err" style="display:none!important;">Please select
                    at least one province.</div>
            </div>
        </div>
    </div>


    <!-- II. Project Status -->
    <div class="section-heading mt-5">
        <span class="section-num">II</span> Project Status
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary mb-2" for="status-ongoing">
            Current Project Status <span class="text-danger">*</span>
        </label>
        <div class="d-flex gap-4" id="status-group">
            <div class="form-check" title="refers to project already being implemented.">
                <input class="form-check-input" type="radio" name="project-status" id="status-ongoing" value="Ongoing">
                <label class="form-check-label small fw-medium" for="status-ongoing">1. On-going</label>
            </div>
            <div class="form-check" title="refers to projects with approved funding.">
                <input class="form-check-input" type="radio" name="project-status" id="status-pipeline"
                    value="Pipeline">
                <label class="form-check-label small fw-medium" for="status-pipeline">2. Pipeline</label>
            </div>
            <div class="form-check" title="refers to projects that have not yet been implemented.">
                <input class="form-check-input" type="radio" name="project-status" id="status-proposed"
                    value="Proposed">
                <label class="form-check-label small fw-medium" for="status-proposed">3. Proposed</label>
            </div>
        </div>
        <div class="invalid-feedback" id="status-error" style="display:none!important;">Please select a project status.
        </div>
    </div>

    <div class="prep-card mb-4">
        <label class="small fw-semibold text-secondary mb-1" for="prep-site">Status of Project Preparation</label>
        <p class="text-muted mb-3" style="font-size:0.73rem;font-style:italic;">
            Note: If no checkmark is indicated in the applicable box/es below, there is an assumption
            that the said pre-implementation requirements are not prepared yet.
        </p>
        <div class="d-flex flex-column gap-2">
            <div class="form-check" title="Lot/land for the project is available. Deed of donation is secured.">
                <input class="form-check-input" type="checkbox" id="prep-site" name="prep-site">
                <label class="form-check-label small" for="prep-site">Site is readily available</label>
            </div>
            <div class="form-check" title="No legal impediment as to right-of-way. If there are affected properties or households, the persons involved were compensated and no issue arises from the right-of-way acquisition.">
                <input class="form-check-input" type="checkbox" id="prep-row">
                <label class="form-check-label small" for="prep-row">No issue on right-of-way acquisition</label>
            </div>
            <div class="form-check" title="Has a document that contains a full definition of all aspects of the project.">
                <input class="form-check-input" type="checkbox" id="prep-ded">
                <label class="form-check-label small" for="prep-ded">Detailed Engineering Design was prepared</label>
            </div>

            <!-- DED Upload Panel (toggled by cpp-form.js) -->
            <div id="ded-upload-panel" style="display:none; margin-top:0.65rem; margin-left:1.6rem;">
                <div id="ded-dropzone" style="
                    border: 1.5px dashed #154A9A;
                    border-radius: 10px;
                    background: rgba(21,74,154,0.04);
                    padding: 1.1rem 1.25rem;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    cursor: pointer;
                    transition: background 0.2s ease, border-color 0.2s ease;
                ">
                    <div
                        style="width:36px;height:36px;border-radius:8px;background:rgba(21,74,154,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i data-lucide="upload-cloud" width="18" style="color:#154A9A;"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p class="mb-0 small fw-semibold" style="color:#154A9A;">Attach DED Document</p>
                        <p class="mb-0" style="font-size:0.72rem;color:#94a3b8;">Click to browse or drag &amp; drop
                            &nbsp;&middot;&nbsp; PDF, DOCX, DWG (max 20 MB)</p>
                        <p id="ded-file-name" class="mb-0 mt-1" style="font-size:0.75rem;color:#475569;display:none;">
                        </p>
                    </div>
                    <button type="button"
                        style="flex-shrink:0;background:#154A9A;color:#fff;border:none;border-radius:7px;padding:0.38rem 0.9rem;font-size:0.72rem;font-weight:700;pointer-events:none;">Browse</button>
                </div>
                <input type="file" id="ded-file-input" accept=".pdf,.doc,.docx,.dwg" style="display:none;">
            </div>
        </div>
    </div>

    <!-- III. Endorsements -->
    <div class="section-heading mt-4">
        <span class="section-num">III</span> Endorsements
    </div>

    <div class="row g-3" id="endorsements-row">

        <!-- SP Resolution -->
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-sp-res">Sangguniang Panlalawigan Resolution No.</label>
            <input type="text" class="form-control" id="f-sp-res" name="f-sp-res" placeholder="SP Resolution No."
                data-upload-panel="sp-upload-panel" data-upload-label="SP Resolution Document">
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-sp-date">Date of Issuance</label>
            <input type="date" class="form-control" id="f-sp-date" name="f-sp-date">
        </div>
        <div class="col-12" id="sp-upload-panel" style="display:none; padding-top:0.1rem;">
            <!-- filled by _initEndorsementUploads -->
        </div>

        <!-- SB Resolution -->
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-sb-res">Sangguniang Bayan Resolution No.</label>
            <input type="text" class="form-control" id="f-sb-res" name="f-sb-res" placeholder="SB Resolution No."
                data-upload-panel="sb-upload-panel" data-upload-label="SB Resolution Document">
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-sb-date">Date of Issuance</label>
            <input type="date" class="form-control" id="f-sb-date" name="f-sb-date">
        </div>
        <div class="col-12" id="sb-upload-panel" style="display:none; padding-top:0.1rem;">
            <!-- filled by _initEndorsementUploads -->
        </div>

        <!-- Letter Request -->
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-letter-req">Letter Request to SP and SB</label>
            <input type="text" class="form-control" id="f-letter-req" name="f-letter-req" placeholder="Reference No. / Description"
                data-upload-panel="letter-upload-panel" data-upload-label="Letter Request Document">
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-letter-date">Date of Transmittal to SP &amp; SB</label>
            <input type="date" class="form-control" id="f-letter-date" name="f-letter-date">
        </div>
        <div class="col-12" id="letter-upload-panel" style="display:none; padding-top:0.1rem;">
            <!-- filled by _initEndorsementUploads -->
        </div>

        <!-- BOR/BOT Resolution -->
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-bor-res">BOR/BOT Resolution No.</label>
            <input type="text" class="form-control" id="f-bor-res" name="f-bor-res" placeholder="BOR/BOT Resolution No."
                data-upload-panel="bor-upload-panel" data-upload-label="BOR/BOT Resolution Document">
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-semibold text-secondary" for="f-bor-date">Date of Issuance</label>
            <input type="date" class="form-control" id="f-bor-date" name="f-bor-date">
        </div>
        <div class="col-12" id="bor-upload-panel" style="display:none; padding-top:0.1rem;">
            <!-- filled by _initEndorsementUploads -->
        </div>

    </div>

</div>
