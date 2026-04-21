<link rel="stylesheet" href="/css/assessment.css">
<section id="project-assessment-report-form" class="page-content container-fluid py-4 text-dark">

    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-soft-pill badge-soft-blue">FM-PDI-02 | PAR Form</span>
                <span class="text-muted small">Programs, Activities and Projects for Inclusion in the RDIP</span>
            </div>
            <h2 class="fw-bold mb-0 text-dark fs-1-3">Project Assessment Report (PAR)</h2>
            <p class="text-muted small mb-0">Regional Development Council — Bicol Region</p>
        </div>
        <div class="d-flex gap-2 no-print">
            <button class="btn btn-outline-secondary btn-sm px-4 rounded-pill" onclick="window.print()">
                <i data-lucide="printer" class="me-1" width="16"></i> Print Assessment
            </button>
            <button class="btn btn-sm px-4 rounded-pill fw-semibold text-white" id="btnDownloadParDocx"
                style="background:#154A9A; border-color:#154A9A;">
                <i data-lucide="file-down" class="me-1" width="16"></i> Download DOCX
            </button>
            <button class="btn-back-list" id="parFormBackToList">
                <i data-lucide="chevron-left" width="16"></i> Back to List
            </button>
        </div>
    </div>


    <div class="card border-0 shadow-sm card-16">
        <div class="header-accent-blue"></div>
        <div class="card-body p-4 p-md-5">
            <!-- Source Linkage Info (Populated Dynamically) -->
            <div id="connected-validation-info" class="alert alert-info border-0 shadow-sm mb-4 rounded-12 bg-soft-blue-o5">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                            <i data-lucide="link" class="text-primary" width="20"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-0-85">Core Project Profile: <span id="connected-source-title" class="text-primary italic">Result</span></h6>
                            <p class="mb-0 text-muted small">This assessment is linked to the submitted project profile.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold fs-0-75" id="viewSourceCppBtn">
                        <i data-lucide="file-text" class="me-1" width="14"></i> View Source
                    </button>
                </div>
            </div>

            <form id="parFormMain" novalidate>
                <!-- Project Basic Info -->
                <div class="row g-4 mb-5">
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-secondary">1. Project Title <span class="text-danger">*</span></label>
                        <input type="text" name="projectTitle" class="form-control" placeholder="Enter full project title...">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-semibold text-secondary">2. Proponent / Implementing Agency <span class="text-danger">*</span></label>
                        <select class="form-select" name="implementingAgency" id="implementingAgencySelect" disabled>
                            <option value="">-- Select Agency --</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-secondary mb-2">3. Project Location <span class="text-danger">*</span></label>
                        
                        <div class="mb-3">
                            <div class="d-flex gap-4" id="coverage-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="project-coverage" id="coverage-regionwide" value="Regionwide">
                                    <label class="form-check-label small fw-medium" for="coverage-regionwide">Regionwide</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="project-coverage" id="coverage-inter-province" value="Inter-Province">
                                    <label class="form-check-label small fw-medium" for="coverage-inter-province">Inter-Province</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="project-coverage" id="coverage-location-specific" value="Location-Specific">
                                    <label class="form-check-label small fw-medium" for="coverage-location-specific">Location-Specific</label>
                                </div>
                            </div>
                        </div>

                        <!-- Location Specific Fields -->
                        <div id="location-specific-fields" style="display: none;" class="mt-3">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">Province <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-province" name="province" disabled>
                                        <option value="">-- Select --</option>
                                        <option>Albay</option>
                                        <option>Camarines Norte</option>
                                        <option>Camarines Sur</option>
                                        <option>Catanduanes</option>
                                        <option>Masbate</option>
                                        <option>Sorsogon</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">District <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-district" name="district" disabled>
                                        <option value="">-- Select --</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">City / Municipality <span class="text-danger">*</span></label>
                                    <select class="form-select" id="f-municipality" name="municipality" disabled>
                                        <option value="">-- Select --</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary">Barangay</label>
                                    <input type="text" class="form-control" name="barangay" placeholder="Enter Barangay...">
                                </div>
                            </div>
                        </div>

                        <!-- Inter-Province Fields -->
                        <div id="inter-province-fields" style="display: none;" class="mt-3">
                            <label class="form-label small fw-semibold text-secondary mb-1">Provinces <span class="text-danger">*</span></label>
                            <div class="position-relative" id="provinces-tag-container">
                                <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white min-h-38 pointer" id="f-provinces-box" tabindex="0">
                                    <input type="text" class="border-0 flex-grow-1 p-0 m-0 bg-transparent min-w-120 pointer caret-transparent" id="provinces-tag-input" placeholder="Select provinces..." autocomplete="off" disabled>
                                </div>
                                <ul class="dropdown-menu w-100 shadow-sm max-h-200 overflow-y-auto" id="provinces-dropdown">
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Albay"><input class="form-check-input mt-0 pe-none" type="checkbox"> Albay</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Camarines Norte"><input class="form-check-input mt-0 pe-none" type="checkbox"> Camarines Norte</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Camarines Sur"><input class="form-check-input mt-0 pe-none" type="checkbox"> Camarines Sur</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Catanduanes"><input class="form-check-input mt-0 pe-none" type="checkbox"> Catanduanes</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Masbate"><input class="form-check-input mt-0 pe-none" type="checkbox"> Masbate</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Sorsogon"><input class="form-check-input mt-0 pe-none" type="checkbox"> Sorsogon</a></li>
                                </ul>
                                <input type="hidden" id="f-provinces" name="interProvince" value="" disabled>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- SECTION I -->
                <div class="form-section-header">
                    <span class="form-section-num">I</span> Documentary requirements: (please place tick mark)
                </div>
                <div class="criteria-group">
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="doc1_f">
                        <label class="form-check-label fw-semibold" for="doc1_f">Official request for the project's inclusion in the RDIP</label>
                    </div>
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="doc2_f">
                        <label class="form-check-label fw-semibold" for="doc2_f">Comprehensive Project Profile / Feasibility Study / Pre-Feasibility Study</label>
                    </div>
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="doc3_f">
                        <label class="form-check-label fw-semibold" for="doc3_f">Endorsements (any of the following when applicable)</label>
                        <div class="sub-criteria">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="endo1_f">
                                <label class="form-check-label" for="endo1_f">Sangguniang Panlalawigan Resolution approving the PAP: <input type="text" class="form-control d-inline-block border-0 border-bottom bg-transparent py-0 rounded-pill px-3 w-300px h-auto"></label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="endo2_f">
                                <label class="form-check-label" for="endo2_f">Sangguniang Panlungsod resolution or ordinance approving the PAPs</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="endo3_f">
                                <label class="form-check-label" for="endo3_f">Endorsement of the Board of Trustees/Regents for projects to be implemented by state universities and colleges</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="endo4_f">
                                <label class="form-check-label" for="endo4_f">Other endorsements (pls specify) <input type="text" class="form-control d-inline-block border-0 border-bottom bg-transparent py-0 rounded-pill px-3 w-300px h-auto"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION II -->
                <div class="form-section-header">
                    <span class="form-section-num">II</span> Criteria for inclusion in the RDIP
                </div>
                <div class="criteria-group">
                    <h6 class="fw-bold text-dark mb-3">1. Typology</h6>
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="type1_f">
                        <label class="form-check-label fw-semibold" for="type1_f">Capital investment PAP</label>
                        <div class="sub-criteria">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="type1a_f">
                                <label class="form-check-label" for="type1a_f">For ICT PAPs, capital outlay components of the Information Systems Strategic Plan of the agency</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="type1b_f">
                                <label class="form-check-label" for="type1b_f">For culture PAPs, capital outlay components are required for the conservation of cultural properties as defined by RA 10066, S. 2009 or at the National Cultural Heritage Act of 2009</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="type1c_f">
                                <label class="form-check-label" for="type1c_f">Resiliency to withstand natural calamities is factored into infrastructure capital investments</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="type1d_f">
                                <label class="form-check-label" for="type1d_f">Requirements for pre-investment activities (e.g., master plans, F.S., etc.) must be undertaken</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="type1e_f">
                                <label class="form-check-label" for="type1e_f">Timelines and costs on the right of way, resettlement shall be included in the project cost</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="type2_f">
                        <label class="form-check-label fw-semibold" for="type2_f">Technical assistance, institutional development, human resource capacity building or system/process improvement PAPs</label>
                    </div>
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="type3_f">
                        <label class="form-check-label fw-semibold" for="type3_f">Relending PAPs to LGUs or other target beneficiaries</label>
                    </div>
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="type4_f">
                        <label class="form-check-label fw-semibold" for="type4_f">Government facilities that are part of the agency's development strategies and contribute to the outcome and output targets contained in the RDP-Results Matrices</label>
                    </div>

                    <h6 class="fw-bold text-dark mt-4 mb-3">2. Responsiveness</h6>
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="resp1_f">
                        <label class="form-check-label fw-semibold" for="resp1_f">Responsiveness to the Bicol RDP</label>
                    </div>
                    <div class="form-check criteria-item">
                        <input class="form-check-input" type="checkbox" id="resp2_f">
                        <label class="form-check-label fw-semibold" for="resp2_f">Included in any of the following:</label>
                        <div class="sub-criteria">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc1_f"><label class="form-check-label" for="inc1_f">National Expenditure Program</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc2_f"><label class="form-check-label" for="inc2_f">Multi-Year Obligational Authority / Multi-Year Contracting Authority</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc3_f"><label class="form-check-label" for="inc3_f">Existing masterplan/sector studies/procurement plan</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc4_f"><label class="form-check-label" for="inc4_f">List of RDC-endorsed NG PAPs</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc5_f"><label class="form-check-label" for="inc5_f">Signed agreements (e.g., peace agreements, etc.)</label></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc6_f"><label class="form-check-label" for="inc6_f">Existing laws, rules and regulations</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc7_f"><label class="form-check-label" for="inc7_f">Regional programs (e.g., HFEP, PAMANA)</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc8_f"><label class="form-check-label" for="inc8_f">Balik Probinsya Bagong Pag-asa Program</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc9_f"><label class="form-check-label" for="inc9_f">Regional Recovery Program</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="inc10_f"><label class="form-check-label" for="inc10_f">Other programs endorsed by the RDC. Please specify: <input type="text" class="form-control d-inline-block border-0 border-bottom bg-transparent py-0 rounded-pill px-3 w-200px fs-0-8"></label></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mt-4 mb-3">3. Readiness</h6>
                    <div class="sub-criteria ms-0">
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready1_f">
                            <label class="form-check-label" for="ready1_f">With completed project preparation documents</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready2_f">
                            <label class="form-check-label" for="ready2_f">For inclusion in the NEP for the next fiscal year</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready3_f">
                            <label class="form-check-label" for="ready3_f">With project preparation document currently being prepared and to be completed in the current fiscal year</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready4_f">
                            <label class="form-check-label" for="ready4_f">For inclusion in the NEP for the succeeding fiscal year</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready5_f">
                            <label class="form-check-label" for="ready5_f">With project preparation documents for completion in the next fiscal year</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready6_f">
                            <label class="form-check-label" for="ready6_f">For inclusion in the NEP for beyond fiscal year of the current administration</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready7_f">
                            <label class="form-check-label" for="ready7_f">With completed Right of Way acquisition and Resettlement Action Plan (when applicable)</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready8_f">
                            <label class="form-check-label" for="ready8_f">With ongoing Right of Way acquisition and Resettlement Action Plan (when applicable)</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" id="ready9_f">
                            <label class="form-check-label" for="ready9_f">Without Right of Way acquisition and Resettlement Action Plan (when applicable)</label>
                        </div>
                    </div>
                </div>

                <!-- SECTION III -->
                <div class="form-section-header">
                    <span class="form-section-num">III</span> Assessment of the PAP
                </div>
                <div class="criteria-group">
                    <h6 class="fw-bold text-dark mb-3">1. Brief of the PAP</h6>
                    <div class="mb-4">
                        <label class="form-label text-secondary">A. Background</label>
                        <textarea class="form-control" name="par_background" rows="4" placeholder="Detail the project background and rationale..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-secondary">B. Project components, Cost and Financing, and Implementation Schedule</label>
                        <textarea class="form-control" name="par_components" rows="4" placeholder="Detail the components, budget, and timeline..."></textarea>
                    </div>

                    <h6 class="fw-bold text-dark mt-4 mb-3">2. Assessment</h6>
                    <div class="mb-4">
                        <label class="form-label text-secondary">A. Project's Regional and Spatial Context</label>
                        <textarea class="form-control" name="par_spatial" rows="4" placeholder="Assess spatial alignment and regional impact..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-secondary">B. Qualitative Technical, Market, Economic, Social, and Environmental Evaluation</label>
                        <textarea class="form-control" name="par_qualitative" rows="4" placeholder="Provide multi-dimensional qualitative assessment..."></textarea>
                    </div>

                    <h6 class="fw-bold text-dark mt-4 mb-3">3. Recommendations</h6>
                    <div class="mb-4">
                        <textarea class="form-control" name="par_recommendations" rows="3" placeholder="Enter initial recommendations..."></textarea>
                    </div>

                    <h6 class="fw-bold text-dark mt-4 mb-3">4. Final Recommendations</h6>
                    <div class="mb-4">
                        <textarea class="form-control border-primary bg-soft-blue-o2 rounded-12" name="par_final_recs" rows="4" placeholder="Enter final consolidated recommendation for RDC..."></textarea>
                    </div>
                </div>

                <!-- SECTION IV: Annex A -->
                <div class="form-section-header mt-5">
                    <span class="form-section-num">IV</span> Annex A: Project Summary & Budgetary Requirements
                </div>
                <!-- Single Project Container -->
                <div id="parSingleProjectContainer" class="mb-4 mt-3">
                    <div class="border rounded-3 overflow-hidden shadow-sm p-4 bg-white">
                        <!-- Brief Description -->
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary">Brief Description</label>
                            <textarea class="form-control" name="annex_desc" rows="3" placeholder="Provide a summary of project scope and objectives..."></textarea>
                        </div>

                        <!-- Budgetary Requirements Table -->
                        <label class="form-label small fw-semibold text-secondary mb-2">Budgetary Requirements (in PhP)</label>
                        <div class="table-responsive border rounded-3 overflow-hidden mb-0">
                            <table class="table table-bordered table-sm align-middle mb-0 text-center" style="font-size: 0.75rem; min-width: 600px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-2">2024</th>
                                        <th class="py-2">2025</th>
                                        <th class="py-2">2026</th>
                                        <th class="py-2">2027</th>
                                        <th class="py-2">2028</th>
                                        <th class="py-2">2029</th>
                                        <th class="py-2 bg-info-subtle">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="budgetRowAnnex">
                                        <td class="p-0"><input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2" name="annex_2024" placeholder="0.00"></td>
                                        <td class="p-0"><input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2" name="annex_2025" placeholder="0.00"></td>
                                        <td class="p-0"><input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2" name="annex_2026" placeholder="0.00"></td>
                                        <td class="p-0"><input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2" name="annex_2027" placeholder="0.00"></td>
                                        <td class="p-0"><input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2" name="annex_2028" placeholder="0.00"></td>
                                        <td class="p-0"><input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2" name="annex_2029" placeholder="0.00"></td>
                                        <td class="p-0 bg-info-subtle"><input type="number" step="0.01" class="form-control form-control-sm border-0 bg-transparent text-center fw-bold project-total-input py-2" name="annex_total" value="0.00" readonly></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Signatories -->
                <div class="row g-4 mt-2 mb-5">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary">Prepared by:</label>
                        <input type="text" name="prepBy_name" class="form-control form-control-sm mb-1" placeholder="Name">
                        <input type="text" name="prepBy_pos" class="form-control form-control-sm" placeholder="Designation">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary">Reviewed by:</label>
                        <input type="text" name="revBy_name" class="form-control form-control-sm mb-1" placeholder="Name">
                        <input type="text" name="revBy_pos" class="form-control form-control-sm" placeholder="Designation">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-secondary">Approved by:</label>
                        <input type="text" name="appBy_name" class="form-control form-control-sm mb-1" placeholder="Name">
                        <input type="text" name="appBy_pos" class="form-control form-control-sm" placeholder="Designation">
                    </div>
                </div>

                <!-- Right-Center Floating Action Buttons (Icon Only) -->
                <div class="assessment-action-bar no-print">
                    <button type="button" class="btn-status-save btn-comments-fab" id="btnOpenCommentsModal" style="display: none;" title="Findings & Recommendations">
                        <i data-lucide="message-square" width="20" height="20"></i>
                        <span>Comments</span>
                    </button>
                    <button type="button" class="btn-status-save btn-evaluated" id="btnSaveEvaluated" style="display: none;">
                        <i data-lucide="clock" width="20" height="20"></i>
                        <span>Save as Assessed</span>
                    </button>
                    <button type="button" class="btn-status-save btn-approved" id="btnSaveApproved" style="display: none;">
                        <i data-lucide="check-circle" width="20" height="20"></i>
                        <span>Save as Evaluated</span>
                    </button>
                    <button type="button" class="btn-status-save btn-reviewed" id="btnSaveReviewed" style="display: none;">
                        <i data-lucide="clipboard-check" width="20" height="20"></i>
                        <span>Save as Reviewed</span>
                    </button>
                    <button type="button" class="btn-status-save btn-committee" id="btnSaveCommittee" style="display: none;">
                        <i data-lucide="presentation" width="20" height="20"></i>
                        <span>SecCom Presentation</span>
                    </button>
                    <button type="button" class="btn-status-save btn-final" id="btnSaveFinal" style="background:#059669; display:none;">
                        <i data-lucide="check-square" width="20" height="20"></i>
                        <span>Save as Final</span>
                    </button>
                </div>
            </form>
</section>

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
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" id="btnSubmitCommentsAgency" style="display: none;">
                        <i data-lucide="send" width="18" class="me-2"></i> Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- UPLOAD FINAL PAR MODAL -->
<div class="modal fade no-print" id="uploadFinalParModal" tabindex="-1" aria-labelledby="uploadFinalParModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="header-accent-green" style="height: 6px; background: #059669;"></div>
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10" style="width: 48px; height: 48px;">
                        <i data-lucide="upload-cloud" class="text-success" width="24"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="uploadFinalParModalLabel">Final Technical Report</h5>
                        <p class="text-muted small mb-0">Upload the scanned or digital final document</p>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="upload-zone p-4 rounded-4 text-center mb-3" style="border: 2px dashed #e2e8f0; background: #f8fafc; cursor: pointer;" onclick="document.getElementById('finalParFileInput').click()">
                    <i data-lucide="file-text" width="32" class="text-muted mb-2"></i>
                    <p class="small fw-semibold text-dark mb-1">Click to browse or drag & drop</p>
                    <p class="x-small text-muted mb-0">PDF or DOCX files accepted (Max 10MB)</p>
                    <input type="file" id="finalParFileInput" class="d-none" accept=".pdf,.doc,.docx">
                </div>
                
                <div id="finalParFileNameDisplay" class="alert alert-success d-none py-2 px-3 rounded-3 small border-0 shadow-sm align-items-center gap-2">
                    <i data-lucide="file-check" width="16"></i>
                    <span class="text-truncate flex-grow-1" id="finalParNameSpan"></span>
                    <i data-lucide="x" width="16" style="cursor: pointer;" id="btnRemoveFinalParFile"></i>
                </div>

                <div class="mt-4 form-check form-switch ps-0 d-flex align-items-center gap-3">
                    <input class="form-check-input ms-0" type="checkbox" role="switch" id="finalParSectoralCheckbox" style="cursor: pointer;">
                    <label class="form-check-label small fw-bold text-dark pt-1" for="finalParSectoralCheckbox" style="cursor: pointer;">
                        For Sectoral Presentation
                    </label>
                </div>

                <div class="mt-3">
                    <label class="small fw-bold text-dark mb-2 d-block">Completion Notes</label>
                    <textarea id="finalParNotes" class="form-control border-0 bg-light rounded-3 small" rows="3" style="resize: none;" placeholder="Provide any final remarks regarding this PAR..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1 fw-bold text-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success rounded-pill px-4 flex-grow-1 fw-bold shadow-sm" id="btnConfirmFinalizePar" style="background: #059669; border: none;">
                    <i data-lucide="check-circle" width="18" class="me-2"></i> Finalize PAR
                </button>
            </div>
        </div>
    </div>
</div>
