<!-- ══════════════════════════════════════════════════════
     CPP FORM — PAGE 5
     IX. Geotagged Photo · X. Geolocation Coordinates
     Prepared By · Noted By · Certification & Submit
════════════════════════════════════════════════════════ -->
<div class="form-step" id="step-5">

    <!-- IX. Geotagged Photo -->
    <div class="section-heading">
        <span class="section-num">IX</span> Geotagged Photo of Project Location or Location Map <span class="text-danger">*</span>
    </div>

    <p class="small text-muted mb-3">
        Attach a geotagged photo or location map clearly showing the project site.
        Accepted: JPG, PNG, PDF, SHP (zipped). Maximum 10MB.
    </p>

    <div class="drop-zone mb-2" id="geo-photo-zone" tabindex="0" role="button" style="padding:2rem;" title="This includes geotagged photos.">
        <i data-lucide="map-pin" width="36" class="mb-2" style="color:#94a3b8;"></i>
        <p class="fw-semibold small text-secondary mb-1">Drop geotagged photo / location map here or click to browse</p>
        <p class="text-muted mb-0" style="font-size:0.72rem;">JPG · PNG · PDF · Max 10MB</p>
    </div>
    <input type="file" id="geo-photo-input" accept=".jpg,.jpeg,.png,.pdf,.shp,.zip" style="display:none;">
    <div id="geo-photo-preview" class="mb-3"></div>

    <p class="small text-muted fst-italic mb-4" style="font-size:0.78rem; line-height:1.6;">
        Proponent/s may submit as attachment, the shapefiles (.shp) or equivalent geospatial
        data formats showing the precise project site or coverage area.
    </p>

    <!-- X. Geolocation Coordinates -->
    <div class="section-heading mt-2">
        <span class="section-num">X</span> Geolocation Coordinates
    </div>

    <div class="d-flex flex-column gap-2 mb-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" id="lineal-infra" name="geo-coordinate" value="geo-lineal">
            <label class="form-check-label small" for="chk-lineal-infra" title="For lineal infrastructure projects">For <strong>lineal</strong> infrastructure projects</label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="radio" id="building-infra" name="geo-coordinate" value="geo-building">
            <label class="form-check-label small" for="building-infra" title="For building infrastructure projects">For <strong>building</strong> infrastructure projects</label>
        </div>
        <div class="invalid-feedback" id="coordinate-error" style="display:none!important;">Please select a geo infrastructure type.
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Guiding note: linear vs building -->
         {{-- <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <p class="small mb-1" style="font-size:0.78rem;">
                        For <strong>linear</strong> infrastructure projects, provide
                        beginning and end points.
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="small mb-1" style="font-size:0.78rem;">
                        For <strong>building</strong> infrastructure projects, provide at least
                        one set of coordinates.
                    </p>
                </div>
            </div>
        </div> --}}

        <div class="col-12">
            <p class="small fw-semibold text-secondary mb-1"></p>
        </div>

        <div id="geo-beginning" style="display:none;">
            <div class="row">
                <div class="col-12 geo-location-heading" display="block">
                    <p class="small fw-semibold text-secondary mb-1">Beginning</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">
                        Latitude <span class="text-danger">*</span>
                    </label>
                    <input type="number" class="form-control" id="f-geo-start-lat" name="f-geo-start-lat" placeholder="e.g. 13.47026" min="-90"
                        max="90" step="0.00001" data-required>
                    <div class="invalid-feedback">Latitude is required.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">
                        Longitude <span class="text-danger">*</span>
                    </label>
                    <input type="number" class="form-control" id="f-geo-start-lng" name="f-geo-start-lng" placeholder="e.g. 123.23121" min="-180"
                        max="180" step="0.00001" data-required>
                    <div class="invalid-feedback">Longitude is required.</div>
                </div>
            </div>
        </div>
        

        <div class="col-12 mt-2">
            <p class="small fw-semibold text-secondary mb-1"></p>
        </div>
        <div id="geo-end" style="display:none;">
            <div class="row">
                 <div class="col-12 mt-2 geo-location-heading" display="block">
                    <p class="small fw-semibold text-secondary mb-1">End</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Latitude <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="f-geo-end-lat" name="f-geo-end-lat" placeholder="e.g. 13.47189" min="-90" max="90"
                        step="0.00001" data-required>
                    <div class="invalid-feedback">Latitude is required.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Longitude <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="f-geo-end-lng" name="f-geo-end-lng" placeholder="e.g. 123.2321" min="-180"
                        max="180" step="0.00001" data-required>
                    <div class="invalid-feedback">Longitude is required.</div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label small fw-bold mb-0"
                    style="text-transform:uppercase; letter-spacing:0.06em; color:#154A9A;">
                    <i data-lucide="paperclip" width="14" class="me-1"></i> Other Attachments
                </label>
                <span class="small text-muted">Optional — attach any additional supporting documents</span>
            </div>
            <div id="other-attachments-list">
                <!-- Rows injected by JS -->
            </div>
            <button type="button" id="add-attachment-btn"
                style="display:flex; align-items:center; gap:0.5rem; background:transparent; border:2px dashed #154A9A; color:#154A9A; border-radius:10px; padding:0.55rem 1.2rem; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all 0.2s; width:100%; justify-content:center; margin-top:0.5rem;">
                <i data-lucide="plus-circle" width="16"></i> Add Another Attachment
            </button>
        </div>
    </div>


    <!-- Signatures: Prepared By + Noted By (two-column) -->
    <div class="row g-4 mb-4">

        <!-- Prepared By -->
        <div class="col-md-6">
            <div class="p-4 rounded-3" style="border:1px solid #e2e8f0; background:#fafcff;">
                <p class="small fw-bold text-secondary mb-3"
                    style="text-transform:uppercase; letter-spacing:.06em; color:#154A9A !important;">
                    Prepared by:
                </p>

                <!-- Signature Mode -->
                <div class="d-flex gap-2 mb-3">
                    <input type="radio" class="btn-check" name="sig-prep-mode" id="sig-prep-draw" value="draw"
                        autocomplete="off" checked>
                    <label class="btn btn-sm" for="sig-prep-draw" style="border:1.5px solid #154A9A; color:#154A9A; border-radius:8px;
                               padding:0.4rem 1rem; font-size:0.78rem; font-weight:600;
                               background:#f0f5ff;">
                        <i data-lucide="pen" width="12" class="me-1"></i> Draw
                    </label>
                    <input type="radio" class="btn-check" name="sig-prep-mode" id="sig-prep-upload" value="upload"
                        autocomplete="off">
                    <label class="btn btn-sm" for="sig-prep-upload" style="border:1.5px solid #cbd5e1; color:#64748b; border-radius:8px;
                               padding:0.4rem 1rem; font-size:0.78rem; font-weight:600;
                               background:#fff;">
                        <i data-lucide="upload" width="12" class="me-1"></i> Upload
                    </label>
                </div>

                <!-- Canvas -->
                <div id="sig-prep-draw-panel">
                    <div style="border:1.5px solid #cbd5e1; border-radius:8px;
                                background:#fff; position:relative; overflow:hidden;">
                        <canvas id="sig-canvas-prep" width="420" height="130"
                            style="display:block; width:100%; cursor:crosshair; touch-action:none;"></canvas>
                        <span style="position:absolute;bottom:6px;right:10px;font-size:0.65rem;
                                     color:#cbd5e1;pointer-events:none;">sign here</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary sig-clear-btn"
                            data-target="sig-canvas-prep" data-hint="sig-prep-hint"
                            style="font-size:0.75rem; border-radius:6px;">
                            <i data-lucide="trash-2" width="12" class="me-1"></i> Clear
                        </button>
                        <span class="small text-muted" id="sig-prep-hint">Please sign above.</span>
                    </div>
                    <div class="invalid-feedback" id="sig-prep-canvas-err">
                    </div>
                </div>

                <!-- Upload -->
                <div id="sig-prep-upload-panel" style="display:none;">
                    <div class="drop-zone sig-upload-zone" id="sig-prep-upload-zone" tabindex="0" role="button"
                        style="padding:1.2rem; border-radius:8px; background:#fff;">
                        <i data-lucide="image" width="24" class="mb-1" style="color:#94a3b8;"></i>
                        <p class="small text-secondary mb-0">Upload signature image</p>
                    </div>
                    <input type="file" class="sig-upload-input" id="sig-prep-upload-input" accept=".jpg,.jpeg,.png,.pdf"
                        style="display:none;">
                    <div id="sig-prep-upload-preview" class="mt-1"></div>
                    <div class="invalid-feedback" id="sig-prep-upload-err">
                    </div>
                </div>

                <hr class="my-3">
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm" id="f-prep-name" name="f-prepared-name" placeholder="Full Name"
                        data-required style="border:none; border-bottom:1.5px solid #334155; border-radius:0;
                               background:transparent; font-weight:600; padding-left:0;">
                    <div class="invalid-feedback">Name is required.</div>
                </div>
                <input type="text" class="form-control form-control-sm" id="f-prep-position" name="f-prepared-pos"
                    placeholder="Position / Designation" data-required style="border:none; border-bottom:1px solid #94a3b8; border-radius:0;
                           background:transparent; font-size:0.78rem; padding-left:0; color:#64748b;">
                <div class="invalid-feedback">Position is required.</div>
                <div class="mt-3 pt-2" style="border-top:1px dashed #e2e8f0;">
                    <span class="text-muted" style="font-size:0.72rem; font-style:italic;">Date:</span>
                    <div id="f-prep-date-display" class="small fw-semibold text-secondary mt-1"
                        style="font-size:0.78rem; color:#334155;">— (auto-set on submission)</div>
                    <input type="hidden" id="f-prep-date" name="f-prepared-date">
                </div>
            </div>
        </div>

        <!-- Noted By -->
        <div class="col-md-6">
            <div class="p-4 rounded-3" style="border:1px solid #e2e8f0; background:#fafcff;">
                <p class="small fw-bold text-secondary mb-3"
                    style="text-transform:uppercase; letter-spacing:.06em; color:#154A9A !important;">
                    Noted by:
                </p>

                <!-- Signature Mode -->
                <div class="d-flex gap-2 mb-3">
                    <input type="radio" class="btn-check" name="sig-noted-mode" id="sig-noted-draw" value="draw"
                        autocomplete="off" checked>
                    <label class="btn btn-sm" for="sig-noted-draw" style="border:1.5px solid #154A9A; color:#154A9A; border-radius:8px;
                               padding:0.4rem 1rem; font-size:0.78rem; font-weight:600;
                               background:#f0f5ff;">
                        <i data-lucide="pen" width="12" class="me-1"></i> Draw
                    </label>
                    <input type="radio" class="btn-check" name="sig-noted-mode" id="sig-noted-upload" value="upload"
                        autocomplete="off">
                    <label class="btn btn-sm" for="sig-noted-upload" style="border:1.5px solid #cbd5e1; color:#64748b; border-radius:8px;
                               padding:0.4rem 1rem; font-size:0.78rem; font-weight:600;
                               background:#fff;">
                        <i data-lucide="upload" width="12" class="me-1"></i> Upload
                    </label>
                </div>

                <!-- Canvas -->
                <div id="sig-noted-draw-panel">
                    <div style="border:1.5px solid #cbd5e1; border-radius:8px;
                                background:#fff; position:relative; overflow:hidden;">
                        <canvas id="sig-canvas-noted" width="420" height="130"
                            style="display:block; width:100%; cursor:crosshair; touch-action:none;"></canvas>
                        <span style="position:absolute;bottom:6px;right:10px;font-size:0.65rem;
                                     color:#cbd5e1;pointer-events:none;">sign here</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary sig-clear-btn"
                            data-target="sig-canvas-noted" data-hint="sig-noted-hint"
                            style="font-size:0.75rem; border-radius:6px;">
                            <i data-lucide="trash-2" width="12" class="me-1"></i> Clear
                        </button>
                        <span class="small text-muted" id="sig-noted-hint">Please sign above.</span>
                    </div>
                    <div class="invalid-feedback" id="sig-noted-canvas-err">
                    </div>
                </div>

                <!-- Upload -->
                <div id="sig-noted-upload-panel" style="display:none;">
                    <div class="drop-zone sig-upload-zone" id="sig-noted-upload-zone" tabindex="0" role="button"
                        style="padding:1.2rem; border-radius:8px; background:#fff;">
                        <i data-lucide="image" width="24" class="mb-1" style="color:#94a3b8;"></i>
                        <p class="small text-secondary mb-0">Upload signature image</p>
                    </div>
                    <input type="file" class="sig-upload-input" id="sig-noted-upload-input"
                        accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
                    <div id="sig-noted-upload-preview" class="mt-1"></div>
                    <div class="invalid-feedback" id="sig-noted-upload-err">
                    </div>
                </div>

                <hr class="my-3">
                <div class="mb-2">
                    <input type="text" class="form-control form-control-sm" id="f-noted-name" name="f-noted-name" placeholder="Full Name"
                        data-required style="border:none; border-bottom:1.5px solid #334155; border-radius:0;
                               background:transparent; font-weight:600; padding-left:0;">
                    <div class="invalid-feedback">Name is required.</div>
                </div>
                <input type="text" class="form-control form-control-sm" id="f-noted-position" name="f-noted-pos"
                    placeholder="Position / Designation" data-required style="border:none; border-bottom:1px solid #94a3b8; border-radius:0;
                           background:transparent; font-size:0.78rem; padding-left:0; color:#64748b;">
                <div class="invalid-feedback">Position is required.</div>
                <div class="mt-3 pt-2" style="border-top:1px dashed #e2e8f0;">
                    <span class="text-muted" style="font-size:0.72rem; font-style:italic;">Date:</span>
                    <div id="f-noted-date-display" class="small fw-semibold text-secondary mt-1"
                        style="font-size:0.78rem; color:#334155;">— (auto-set on submission)</div>
                    <input type="hidden" id="f-noted-date" name="f-noted-date">
                </div>
            </div>
        </div>
    </div>

    <!-- Certification -->
    <div class="section-heading mt-2">
        <i data-lucide="check-square" width="16" style="color:#154A9A;"></i>
        Certification
    </div>

    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox" id="f-certify" data-required-check>
        <label class="form-check-label small fw-medium" for="f-certify">
            I certify that all information provided in this Comprehensive Project Profile
            is true and accurate to the best of my knowledge, and that this submission is
            made on behalf of the implementing agency.
            <span class="text-danger">*</span>
        </label>
    </div>
    <div class="invalid-feedback" id="certify-error"></div>

</div>
