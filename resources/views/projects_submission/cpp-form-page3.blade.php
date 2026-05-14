<!-- ══════════════════════════════════════════════════════
     CPP FORM — PAGE 3
     Sections: VI (continued) · VII. Project Implementation
════════════════════════════════════════════════════════ -->
<div class="form-step" id="step-3">

    <!-- VI (continued) Social & Economic Costs -->
    <div class="section-heading">
        <span class="section-num">VI</span> Project Benefits and Costs <span
            style="font-weight:400;font-size:0.72rem;text-transform:none;letter-spacing:0;">(continued)</span>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            4. Social Costs <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-social-costs" rows="3"
            placeholder="Discuss unwanted or unintended effects of the project (dislocation, pollution). This would include a discussion of right-of-way (ROW) issues, the mode of ROW acquisition and the resettlement action plan in cases of displacement."
            data-required></textarea>
        <div class="invalid-feedback">Social costs field is required.</div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            5. Economic Costs <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-economic-costs" rows="3"
            placeholder="Discuss the direct cost or the expenses directly attributed to the project, such as the (1) Capital Cost; and (2) Maintenance and Operating Costs. Indicate BCR, if applicable.
            This section would discuss harmful economic effects of athe project (e.g. destruction of marine resources) and the mitigating measures."
            data-required></textarea>
        <div class="invalid-feedback">Economic costs field is required.</div>
    </div>

    <!-- VII. Project Implementation -->
    <div class="section-heading mt-4">
        <span class="section-num">VII</span> Project Implementation
    </div>

    <!-- 1. Agencies Involved -->
    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            1. Agencies Involved <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control" id="f-agencies-involved"
            placeholder="Indicate the names and roles of the proponent, endorsing, executing, implementing and coordinating agencies." data-required>
        <div class="invalid-feedback">Agencies involved is required.</div>
    </div>

    <!-- 2. Implementation Schedule -->
    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            2. Implementation Schedule <span class="text-danger">*</span>
        </label>
        <div class="table-responsive">
            <table class="table table-bordered table-sm small" id="impl-schedule-table"
            style="font-size:0.8rem; border-color:#e2e8f0;">
            <thead style="background:#f0f5ff;">
                <tr>
                <th class="text-secondary fw-semibold" style="width:12%;">Year</th>
                <th class="text-secondary fw-semibold" style="width:38%;">Physical Target</th>
                <th class="text-secondary fw-semibold" style="width:25%;">Indicator</th>
                <th class="text-secondary fw-semibold" style="width:20%;">Amount (₱)</th>
                <th style="width:5%;"></th>
                </tr>
            </thead>
            <tbody id="impl-schedule-body" title="Present the timetable and project implementation targets (physical and financial) including baseline data.">
                <tr>
                <td>
                    <input type="text" class="form-control form-control-sm" placeholder="Year" data-required>
                    <div class="invalid-feedback">Year is required.</div>
                </td>
                <td>
                    <textarea class="form-control form-control-sm" rows="2"
                    placeholder="Physical Target" data-required></textarea>
                    <div class="invalid-feedback">Physical target is required.</div>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" placeholder="Indicator" data-required>
                    <div class="invalid-feedback">Indicator is required.</div>
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm" placeholder="0.00" min="0"
                    step="0.01" data-required onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();">
                    <div class="invalid-feedback">Amount is required.</div>
                </td>
                <td class="text-center align-middle">
                    <!-- first row has no delete button -->
                </td>
                </tr>
            </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="impl-add-row"
            style="font-size:0.78rem; border-radius:6px; border-color:#154A9A; color:#154A9A;">
            + Add Row
        </button>
        <div class="invalid-feedback" id="impl-schedule-err"></div>
    </div>

    <!-- 3. Implementation Arrangement -->
    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            3. Implementation Arrangement <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-impl-arrangement" rows="3"
            placeholder="Describe the organizations or agencies/LGUs involved to implement the project. This includes the names and roles of the proponent, endorsing, executing or implementing and coordinating agencies. A discussion on how the project will affect and will be affected by other projects shall be placed in this section."
            data-required></textarea>
        <div class="invalid-feedback">Implementation arrangement is required.</div>
    </div>

    <!-- 4. Environmental Clearance -->
    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            4. Environmental Clearance <span class="text-danger">*</span>
        </label>
        <textarea class="form-control mb-3" id="f-env-clearance-desc" rows="3"
            placeholder="Discuss ECC compliance. Legal impediments/political opposition if any. This section discusses the project's effects to the environment, disaster risk reduction/cliimate change adaptation, and mitigating measures in case of negative effects. The ECC shall be attached to the CPP, if available."
            data-required></textarea>
        <div class="invalid-feedback">Environmental clearance discussion is required.</div>

        <div id="env-clearance-upload-panel"></div>
    </div>

    <!-- 5. Social Acceptability -->
    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            5. Social Acceptability <span class="text-danger">*</span>
        </label>
        <textarea class="form-control mb-3" id="f-social-accept" rows="4"
            placeholder="Discuss actual social or legal/political opposition from the community that will be affected."
            data-required></textarea>
        <div class="invalid-feedback">Social acceptability is required.</div>

        <p class="small fw-semibold text-secondary mb-2">Conducted public consultation/s?</p>
        <div class="d-flex flex-column gap-2 mb-3">
            <div class="form-check d-flex align-items-center gap-2 flex-wrap">
                <input class="form-check-input mt-0" type="radio" name="consultation-status" id="consult-no" value="No"
                    data-upload-panel="consult-yes-panel">
                <label class="form-check-label small" for="consult-no">
                    No, but it will be conducted on:
                </label>
                <input type="date" class="form-control form-control-sm" id="f-consult-planned-date"
                    style="max-width:180px;">
            </div>
            <div class="form-check d-flex align-items-center gap-2 flex-wrap">
                <input class="form-check-input mt-0" type="radio" name="consultation-status" id="consult-yes"
                    value="Yes" data-upload-panel="consult-yes-panel"
                    data-upload-label="Public Consultation Documentation">
                <label class="form-check-label small" for="consult-yes">
                    Yes. Date/s conducted:
                </label>
                <!-- Multi-date tag picker -->
                <div id="consult-dates-box" style="display:inline-flex; align-items:center; flex-wrap:wrap; gap:0.35rem;
                           border:1.5px solid #dee2e6; border-radius:8px; padding:0.3rem 0.5rem;
                           min-width:220px; max-width:380px; background:#fff; cursor:pointer;
                           transition: border-color 0.2s;" id="consult-dates-box"
                    title="Click the calendar to add a date">
                    <!-- Tags injected here by JS -->
                    <span class="consult-date-add d-flex align-items-center gap-1 small text-secondary"
                        style="cursor:pointer; user-select:none; white-space:nowrap;">
                        <i data-lucide="calendar" width="14" style="color:#154A9A;"></i>
                        <span id="consult-dates-placeholder">Pick date(s)</span>
                    </span>
                    <input type="date" id="consult-date-picker"
                        style="position:absolute; opacity:0; pointer-events:none; width:0; height:0;" tabindex="-1">
                </div>
                <input type="hidden" id="f-consult-done-dates">

            </div>
            <div id="consult-yes-panel" style="display:none; margin-left:1.6rem; margin-top:0.5rem;"></div>
        </div>
        <div class="invalid-feedback" id="consult-status-err"></div>

        <div class="mb-3">
            <label class="form-label small fw-semibold text-secondary">Highlights of the Public Consultation/s</label>
            <input type="text" class="form-control" id="f-consult-highlights"
                placeholder="Discuss the acceptance or the non-acceptance of the project based on the public consultation conducted. Attach minutes/or highlights of consultations with stakeholders.">
        </div>

        <div class="mb-2">
            <label class="form-label small fw-semibold text-secondary">5.1 HGDG (Harmonized Gender and Development
                Guidelines)</label>
            <textarea class="form-control" id="f-hgdg" rows="2"
                placeholder="Discuss the HGDG score and basic criteria resulting to said score. An accomplishsed GAD Checklist on project identification must be attached when submitting the CPP (refer to HGDG for a copy of the checklist)."
                data-upload-panel="p3-hgdg-panel" data-upload-label="HGDG Document"></textarea>
            <div id="p3-hgdg-panel" style="display:none; margin-top:0.65rem;"></div>
        </div>
    </div>

</div>
