<!-- ══════════════════════════════════════════════════════
     CPP FORM — PAGE 2
     Sections: IV. Project Justification
               V.  Project Financing
               VI. Project Benefits and Costs
════════════════════════════════════════════════════════ -->
<div class="form-step" id="step-2">

    <!-- IV. Project Justification -->
    <div class="section-heading">
        <span class="section-num">IV</span> Project Justification
    </div>

    <div class="mb-4" id="sdg-alignment-section">
        <label class="form-label small fw-semibold text-secondary">
            Alignment to Sustainable Development Goals (SDGs) <span class="text-danger">*</span>
        </label>
        <div class="position-relative" id="sdg-tag-container">
            <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white" id="f-alignment-box" tabindex="0" style="min-height: 38px; cursor: pointer;">
                <input type="text" class="border-0 flex-grow-1 p-0 m-0 bg-transparent" id="sdg-tag-input"
                    style="outline: none; min-width: 120px; cursor: pointer; caret-color: transparent;" placeholder="Select SDGs..." autocomplete="off" readonly>
            </div>
            <ul class="dropdown-menu w-100 shadow-sm" id="sdg-dropdown" style="max-height: 250px; overflow-y: auto;">
                @foreach($sdg_goals as $goal)
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-val="{{ $goal->number }}: {{ $goal->title }}"><input class="form-check-input mt-0 pe-none" type="checkbox" value="{{ $goal->number }}: {{ $goal->title }}"> {{ $goal->number }}: {{ $goal->title }}</a></li>
                @endforeach
            </ul>
            <input type="hidden" id="f-alignment" name="f-alignment" value="" data-required>
            <div class="invalid-feedback d-block" id="f-alignment-err" style="display:none!important;">Please select at least one SDG.</div>
        </div>
    </div>


    <!-- RDP 2023-2028 Alignment -->
    <div class="mb-4" id="rdp-alignment-section">
        <label class="form-label small fw-semibold text-secondary">
            Alignment to RDP 2023&ndash;2028 <span class="text-danger">*</span>
        </label>
        <div class="position-relative" id="rdp-tag-container">
            <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white" id="f-rdp-alignment-box" tabindex="0" style="min-height: 38px; cursor: pointer;">
                <input type="text" class="border-0 flex-grow-1 p-0 m-0 bg-transparent" id="rdp-tag-input"
                    style="outline: none; min-width: 120px; cursor: pointer; caret-color: transparent;" placeholder="Select RDP chapters..." autocomplete="off" readonly>
            </div>
            <ul class="dropdown-menu w-100 shadow-sm" id="rdp-dropdown" style="max-height: 250px; overflow-y: auto;">
                @foreach($chapters as $chapter)
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-val="{{ $chapter->chapter_name }}"><input class="form-check-input mt-0 pe-none" type="checkbox"> {{ $chapter->chapter_name }}</a></li>
                @endforeach
            </ul>
            <input type="hidden" id="f-rdp-alignment" name="f-rdp-alignment" value="" data-required>
            <div class="invalid-feedback d-block" id="f-rdp-alignment-err" style="display:none!important;">Please select at least one RDP chapter.</div>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            1. Project Background <span class="text-danger">*</span>

        </label>
        <textarea class="form-control" id="f-background" name="f-background" rows="5"
            placeholder="Discuss how and why the project was conceived (Similar to a situational analysis)."
            data-required></textarea>
        <div class="invalid-feedback">Project background is required.</div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            2. Goal <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-goal" name="f-goal" rows="3"
            placeholder="Reason for undertaking the project. It is the ultimate objective of the project. This is what the project is expected to achieve in developmental terms once it is completed within the allocated time. It provides the picture of the end-of-project results.  It indicates the concrete benefits and impacts on  the target groups when the project is implemented. It also reflects the intended response of the target group(s) and implementing group(s) to the goods and services offered by the project.  Baseline data should be included in the discussion,e.g. the project aims to increase production from _____ in ____ to ____ in _____." data-required></textarea>
        <div class="invalid-feedback">Goal is required.</div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            3. Purpose <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-purpose" name="f-purpose" rows="3"
            placeholder="What the project is expected to achieve in developmental terms. Indicates what the project will deliver to be able to achieve the project objective/s." data-required></textarea>
        <div class="invalid-feedback">Purpose is required.</div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            4. Project Output/s <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-outputs" name="f-outputs" rows="4"
            placeholder="What the project will deliver to be able to achieve the purpose." data-required></textarea>
        <div class="invalid-feedback">Project outputs are required.</div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            5. Project Activities <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-activities" name="f-activities" rows="3"
            placeholder="Indicate the activities to be done for each output."
            data-required></textarea>
        <div class="invalid-feedback">Project activities are required.</div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            6. Project Linkages <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-linkages" name="f-linkages" rows="2"
            placeholder="Illustrate how the project will affect and will be affected by other projects. This includes the names and roles of the proponent, endorsing, executing or implementing and coordinating agencies. A discussion on how the project will affect and will be affected by other projects shall be placed in this section."
            data-required></textarea>
        <div class="invalid-feedback">Project linkages are required.</div>
    </div>

    <!-- V. Project Financing -->
    <div class="section-heading mt-4">
        <span class="section-num">V</span> Project Financing
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">
            1. Funding Requirement (PHP) <span class="text-danger">*</span>
        </label>
        <div class="row mb-4">
            <div class="col-3">
                <label class="form-label small fw-semibold text-secondary">
                    NGA
                </label>
                <input type="number" class="form-control" id="f-nga-funding" name="f-nga-funding" placeholder="0.00" min="0" step="0.01" title="NGA funding."
                    style="border-left:none;" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();" oninput="if(this.value.includes('.')) { let p = this.value.split('.'); if(p[1].length > 2) this.value = p[0] + '.' + p[1].slice(0,2); }">
            </div>
            <div class="col-3">
                <label class="form-label small fw-semibold text-secondary">
                    LGU
                </label>
                <input type="number" class="form-control" id="f-lgu-funding" name="f-lgu-funding" placeholder="0.00" min="0" step="0.01" title="LGU counterpart funding."
                    style="border-left:none;" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();" oninput="if(this.value.includes('.')) { let p = this.value.split('.'); if(p[1].length > 2) this.value = p[0] + '.' + p[1].slice(0,2); }">
            </div>
            <div class="col-3">
                <label class="form-label small fw-semibold text-secondary">
                    ODA
                </label>
                <input type="number" class="form-control" id="f-oda-funding" name="f-oda-funding" placeholder="0.00" min="0" step="0.01" title="ODA funding."
                    style="border-left:none;" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();" oninput="if(this.value.includes('.')) { let p = this.value.split('.'); if(p[1].length > 2) this.value = p[0] + '.' + p[1].slice(0,2); }">
            </div>
             <div class="col-3">
                <label class="form-label small fw-semibold text-secondary">
                    OTHERS
                </label>
                <input type="number" class="form-control" id="f-others-funding" name="f-others-funding" placeholder="0.00" min="0" step="0.01" title="Other funding."
                    style="border-left:none;" onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();"  oninput="if(this.value.includes('.')) { let p = this.value.split('.'); if(p[1].length > 2) this.value = p[0] + '.' + p[1].slice(0,2); }">
            </div>
        </div>


        <div class="input-group mb-3">
            <label class="input-group-text small text-muted" style="background:#f8fafc;border-right:none;">TOTAL</label>
            <input type="number" class="form-control" id="f-total-cost" name="f-total-cost" placeholder="0.00" min="0" step="0.01" title="Summary of total funding required."
                style="border-left:none;" readonly>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">
            2. Project Financing / Source of Fund <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-funding-source" name="f-funding-source" rows="3"
            placeholder="Discuss how the project will be funded during implementation &project operation. Specify the source of fund for the project, i.e.; Philippine Government thru the General Appropriations Act, Official Development Assistance  (pls. specify), PPP and other sources of funds."
            data-required></textarea>
        <div class="invalid-feedback">Source of fund description is required.</div>
    </div>

    <div class="mb-4">
        <label class="form-label small fw-semibold text-secondary">
            3. Counterpart Funding <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control" id="f-counterpart-funding" name="f-counterpart-funding"
            placeholder="Specify the agency providing the counterpart and the amount of the counterpart." data-required>
        <div class="invalid-feedback">Counterpart funding is required (enter "None" if not applicable).</div>
    </div>

    <!-- VI. Project Benefits and Costs -->
    <div class="section-heading mt-4">
        <span class="section-num">VI</span> Project Benefits and Costs
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">
            1. Beneficiaries <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-beneficiaries" name="f-beneficiaries" rows="2"
            placeholder="Enumerate the direct and indirect beneficiaries of the project disaggregated by sex.  Children as beneficiaries, shall be quantified."
            data-required></textarea>
        <div class="invalid-feedback">Beneficiaries field is required.</div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">
            2. Social Benefits <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-social-benefits" name="f-social-benefits" rows="2"
            placeholder="Discuss the desirable effects on the lives of the people. This would include a discussion on how the project will positively affect women, children, indigeneous peoples, and other disadvantaged groups.  It should also include whether these groups were consulted/involved in project identification."
            data-required></textarea>
        <div class="invalid-feedback">Social benefits field is required.</div>
    </div>

    <div class="mb-3">
        <label class="form-label small fw-semibold text-secondary">
            3. Economic Benefits <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" id="f-economic-benefits" name="f-economic-benefits" rows="2"
            placeholder="This would include a discussion on the  positive effects on the economy (e.g. production, exports, lower prices). Also indicate the economic viability indicators (EIRR/NPV) if applicable."
            data-required></textarea>
        <div class="invalid-feedback">Economic benefits field is required.</div>
    </div>

</div>
