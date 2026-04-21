/**
 * RPTS Project Workspace - Bootstrap 5 Integration
 */

import { createFileDrop } from '../common/file-drop.js';

export function initProjectDetails() {
    // Target the inline workspace elements
    const workspacePage = document.getElementById('project-workspace');
    const workspaceContent = document.getElementById('workspace-form-content');
    const workspaceTitle = document.getElementById('workspace-title');
    const sidePanel = document.getElementById('workspace-side-panel');

    let currentProjectData = null;
    let isEditMode = false;
    let isNewProject = false;
    let selectedProvinces = []; // Shared state for multi-select

    function _getRdpChapterOptions() {
        const opts = [];
        document.querySelectorAll('#rdp-chapter table tbody tr').forEach(row => {
            const td = row.querySelector('td');
            if (td) opts.push(td.textContent.trim());
        });
        return opts;
    }

    function _getIndicatorOptions() {
        const opts = [];
        document.querySelectorAll('#indicator table tbody tr').forEach(row => {
            const td = row.querySelector('td');
            if (td) opts.push(td.textContent.trim());
        });
        return opts;
    }

    const mockFullProjectDetail = {
        name: "BU Tabaco Campus Medical & Dental Clinic",
        description: "One building completed - Rehabilitation/renovation of Building J-OB Complex including electrical, plumbing, mechanical and auxiliary works.",
        indicator: "1 Building constructed",
        agency: "Bicol University (BU)",
        sector: "Social",
        subSector: "Healthcare",
        status: "Proposed",
        fundingRequirement: "10.00 M",
        fundingCategory: "General Fund",
        endorsementYear: "1111",
        rdcNumber: "RDC-V-2024-089",
        rdpChapter: "Chapter 4 Promote Human and Social Development",
        coverage: "Location-Specific",
        province: "Albay",
        district: "1st District",
        city: "Tabaco City",
        provinces: "",
        years: ["2023", "2024", "2025", "2026", "2027", "2028", "Succeeding Years"],
        physicalTarget: ["", "", "", "", "", "", ""],
        projectCost: ["", "", "", "", "", "", ""],
        remarks: "Proposed for the next budget cycle based on regional health priorities.",
        attachments: ["Medical_Clinic_Floor_Plan.pdf", "Endorsement_Letter_2024.pdf"]
    };

    const emptyProjectDetail = {
        name: "",
        description: "",
        indicator: "",
        agency: "",
        sector: "",
        subSector: "",
        status: "",
        fundingRequirement: "",
        fundingCategory: "",
        endorsementYear: "",
        rdcNumber: "",
        rdpChapter: "",
        coverage: "Nationwide",
        province: "",
        district: "",
        city: "",
        provinces: "",
        years: ["2023", "2024", "2025", "2026", "2027", "2028", "Succeeding Years"],
        physicalTarget: ["", "", "", "", "", "", ""],
        projectCost: ["", "", "", "", "", "", ""],
        remarks: "",
        attachments: []
    };

    const renderWorkspace = () => {
        if (!currentProjectData || !workspaceContent) return;
        const data = currentProjectData;
        // ensure arrays exist
        data.years = data.years || [];
        data.physicalTarget = data.physicalTarget || data.years.map(() => '');
        data.projectCost = data.projectCost || data.years.map(() => '');
        const displayName = (data.name || "").trim() || (isNewProject ? "New Project" : "Untitled Project");
        workspaceTitle.textContent = isEditMode ? `Editing: ${displayName}` : displayName;

        const inputAttr = isEditMode ? '' : 'disabled';
        const readonlyClass = isEditMode ? '' : 'bg-light';

        // Main Form Content
        workspaceContent.innerHTML = `
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <h6 class="fw-bold text-primary text-uppercase small ls-1 mb-0">Project Information</h6>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Project Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-lg fw-bold ${readonlyClass}" value="${data.name || ''}" placeholder="Enter project title" ${inputAttr} data-required>
                        <div class="invalid-feedback">Project title is required.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Project Description <span class="text-danger">*</span></label>
                        <textarea class="form-control ${readonlyClass}" rows="3" placeholder="Enter project description" ${inputAttr} data-required>${data.description || ''}</textarea>
                        <div class="invalid-feedback">Project description is required.</div>
                    </div>

                    <div class="mb-4">
                         <label class="form-label fw-medium">Indicator <span class="text-danger">*</span></label>
                         <div class="input-group">
                             <select id="f-indicator" class="form-select ${readonlyClass}" ${inputAttr} data-required>
                                 <option value="">-- Select Indicator --</option>
                                 ${_getIndicatorOptions().map(ind => `<option value="${ind}" ${data.indicator === ind ? 'selected' : ''}>${ind}</option>`).join('')}
                             </select>
                             ${isEditMode ? `
                             <button class="btn btn-outline-primary" type="button" id="add-new-indicator-btn" title="Add New Indicator" style="border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem;">
                                 <i data-lucide="plus" width="16"></i>
                             </button>
                             ` : ''}
                         </div>
                         <div class="invalid-feedback">Please select an indicator.</div>
                    </div>

                    <!-- Classification -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Agency <span class="text-danger">*</span></label>
                            <select class="form-select ${readonlyClass}" ${inputAttr} data-required><option value="${data.agency || ''}">${data.agency || '-- Select Agency --'}</option></select>
                            <div class="invalid-feedback">Agency is required.</div>
                        </div>
                         <div class="col-md-4">
                            <label class="form-label fw-medium">Sector <span class="text-danger">*</span></label>
                            <select class="form-select ${readonlyClass}" ${inputAttr} data-required><option value="${data.sector || ''}">${data.sector || '-- Select Sector --'}</option></select>
                            <div class="invalid-feedback">Sector is required.</div>
                        </div>
                         <div class="col-md-4">
                            <label class="form-label fw-medium">Sub Sector <span class="text-danger">*</span></label>
                            <select class="form-select ${readonlyClass}" ${inputAttr} data-required><option value="${data.subSector || ''}">${data.subSector || '-- Select Sub-Sector --'}</option></select>
                            <div class="invalid-feedback">Sub-sector is required.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                             <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                             <select class="form-select ${readonlyClass}" ${inputAttr} data-required><option value="${data.status || ''}">${data.status || '-- Select Status --'}</option></select>
                             <div class="invalid-feedback">Status is required.</div>
                        </div>
                        <div class="col-md-4">
                             <label class="form-label fw-medium">Funding Requirement <span class="text-danger">*</span></label>
                             <input type="text" class="form-control ${readonlyClass}" value="${data.fundingRequirement || ''}" placeholder="Enter amount" ${inputAttr} data-required>
                             <div class="invalid-feedback">Funding requirement is required.</div>
                        </div>
                        <div class="col-md-4">
                             <label class="form-label fw-medium">Funding Category <span class="text-danger">*</span></label>
                             <select class="form-select ${readonlyClass}" ${inputAttr} data-required><option value="${data.fundingCategory || ''}">${data.fundingCategory || '-- Select Category --'}</option></select>
                             <div class="invalid-feedback">Funding category is required.</div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                             <label class="form-label fw-medium">Year of Endorsement</label>
                             <select class="form-select ${readonlyClass}" ${inputAttr}><option>${data.endorsementYear || ''}</option></select>
                        </div>
                         <div class="col-md-6">
                             <label class="form-label fw-medium">RDC Endorsement Number</label>
                             <input type="text" class="form-control ${readonlyClass}" value="${data.rdcNumber || ''}" placeholder="Enter RDC number" ${inputAttr}>
                        </div>
                    </div>

                    <div class="mb-4">
                         <label class="form-label fw-medium">RDP Chapter <span class="text-danger">*</span></label>
                         <select id="f-rdpchapter" class="form-select ${readonlyClass}" ${inputAttr} data-required>
                             <option value="">-- Select RDP Chapter --</option>
                             ${_getRdpChapterOptions().map(ch => `<option value="${ch}" ${data.rdpChapter === ch ? 'selected' : ''}>${ch}</option>`).join('')}
                         </select>
                         <div class="invalid-feedback">RDP Chapter is required.</div>
                    </div>

                    <!-- Dynamic Location Selection (CIPG Style) -->
                    <label class="form-label fw-medium mb-2">Location <span class="text-danger">*</span></label>
                    <div class="mb-3 d-flex flex-wrap gap-4" id="location-radio-group">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="project-coverage" id="cov-nationwide" value="Nationwide" ${data.coverage === 'Nationwide' || isNewProject ? 'checked' : ''} ${inputAttr}>
                            <label class="form-check-label small fw-medium" for="cov-nationwide">Nationwide</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="project-coverage" id="cov-regionwide" value="Regionwide" ${data.coverage === 'Regionwide' ? 'checked' : ''} ${inputAttr}>
                            <label class="form-check-label small fw-medium" for="cov-regionwide">Regionwide</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="project-coverage" id="cov-inter-province" value="Inter-Province" ${data.coverage === 'Inter-Province' ? 'checked' : ''} ${inputAttr}>
                            <label class="form-check-label small fw-medium" for="cov-inter-province">Inter-Province</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="project-coverage" id="cov-location-specific" value="Location-Specific" ${data.coverage === 'Location-Specific' ? 'checked' : ''} ${inputAttr}>
                            <label class="form-check-label small fw-medium" for="cov-location-specific">Location-Specific</label>
                        </div>
                    </div>
                    <div class="invalid-feedback" id="location-error">Please select a location coverage.</div>

                    <!-- Location Specific Fields -->
                    <div id="location-specific-fields" style="display: ${data.coverage === 'Location-Specific' ? 'block' : 'none'};">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">Province <span class="text-danger">*</span></label>
                                <select class="form-select ${readonlyClass}" id="f-province" ${inputAttr} data-required ${data.coverage !== 'Location-Specific' ? 'disabled' : ''}>
                                    <option value="">-- Select Province --</option>
                                    <option ${data.province === 'Albay' ? 'selected' : ''}>Albay</option>
                                    <option ${data.province === 'Camarines Norte' ? 'selected' : ''}>Camarines Norte</option>
                                    <option ${data.province === 'Camarines Sur' ? 'selected' : ''}>Camarines Sur</option>
                                    <option ${data.province === 'Catanduanes' ? 'selected' : ''}>Catanduanes</option>
                                    <option ${data.province === 'Masbate' ? 'selected' : ''}>Masbate</option>
                                    <option ${data.province === 'Sorsogon' ? 'selected' : ''}>Sorsogon</option>
                                </select>
                                <div class="invalid-feedback">Province is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">District <span class="text-danger">*</span></label>
                                <select class="form-select ${readonlyClass}" id="f-district" ${inputAttr} data-required ${data.coverage !== 'Location-Specific' ? 'disabled' : ''}>
                                    <option value="">-- Select District --</option>
                                    ${data.district ? `<option selected>${data.district}</option>` : ''}
                                </select>
                                <div class="invalid-feedback">District is required.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-secondary">City/Municipality <span class="text-danger">*</span></label>
                                <select class="form-select ${readonlyClass}" id="f-municipality" ${inputAttr} data-required ${data.coverage !== 'Location-Specific' ? 'disabled' : ''}>
                                    <option value="">-- Select City/Municipality --</option>
                                    ${data.city ? `<option selected>${data.city}</option>` : ''}
                                </select>
                                <div class="invalid-feedback">City/Municipality is required.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Inter-Province Fields -->
                    <div id="inter-province-fields" style="display: ${data.coverage === 'Inter-Province' ? 'block' : 'none'};">
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary mb-1">Select Provinces <span class="text-danger">*</span></label>
                            <div class="position-relative" id="provinces-tag-container">
                                <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white" id="f-provinces-box" tabindex="0" style="min-height: 38px; cursor: text;">
                                    <input type="text" class="border-0 flex-grow-1 p-0 m-0 bg-transparent" id="provinces-tag-input" style="outline: none; min-width: 120px;" placeholder="Select provinces..." autocomplete="off" ${inputAttr} ${data.coverage !== 'Inter-Province' ? 'disabled' : ''}>
                                </div>
                                <ul class="dropdown-menu w-100 shadow-sm" id="provinces-dropdown" style="max-height: 200px; overflow-y: auto;">
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Albay"><input class="form-check-input mt-0 pe-none" type="checkbox" ${data.provinces && data.provinces.includes('Albay') ? 'checked' : ''}> Albay</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Camarines Norte"><input class="form-check-input mt-0 pe-none" type="checkbox" ${data.provinces && data.provinces.includes('Camarines Norte') ? 'checked' : ''}> Camarines Norte</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Camarines Sur"><input class="form-check-input mt-0 pe-none" type="checkbox" ${data.provinces && data.provinces.includes('Camarines Sur') ? 'checked' : ''}> Camarines Sur</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Catanduanes"><input class="form-check-input mt-0 pe-none" type="checkbox" ${data.provinces && data.provinces.includes('Catanduanes') ? 'checked' : ''}> Catanduanes</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Masbate"><input class="form-check-input mt-0 pe-none" type="checkbox" ${data.provinces && data.provinces.includes('Masbate') ? 'checked' : ''}> Masbate</a></li>
                                    <li><a class="dropdown-item d-flex align-items-center gap-2" href="#" data-prov="Sorsogon"><input class="form-check-input mt-0 pe-none" type="checkbox" ${data.provinces && data.provinces.includes('Sorsogon') ? 'checked' : ''}> Sorsogon</a></li>
                                </ul>
                                <input type="hidden" id="f-provinces" value="${data.provinces || ''}" ${data.coverage !== 'Inter-Province' ? 'disabled' : ''} data-required>
                                <div class="invalid-feedback">Please select at least one province.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            
            <!-- Physical Target & Project Cost -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary text-uppercase small ls-1 mb-3">Physical Target</h6>
                    <div class="row g-2">
                        ${data.years.map((yr, idx) => `
                            <div class="col${idx === data.years.length - 1 ? ' col-md-2' : ''}">
                                <label class="small text-muted mb-1 d-block">${yr}</label>
                                <input type="number" class="form-control form-control-sm" value="${data.physicalTarget && data.physicalTarget[idx] ? data.physicalTarget[idx] : ''}" ${inputAttr} data-physical-target data-pt-index="${idx}" onkeydown="if(['e','E','+','-'].includes(event.key))event.preventDefault();">
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary text-uppercase small ls-1 mb-3">Project Cost (PM)</h6>
                    <div class="row g-2">
                        ${data.years.map((yr, idx) => `
                            <div class="col${idx === data.years.length - 1 ? ' col-md-2' : ''}">
                                <label class="small text-muted mb-1 d-block">${yr}</label>
                                <input type="number" class="form-control form-control-sm" value="${data.projectCost && data.projectCost[idx] ? data.projectCost[idx] : ''}" ${inputAttr} data-project-cost data-pc-index="${idx}" onkeydown="if(['e','E','+','-'].includes(event.key))event.preventDefault();">
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>

            
            <!-- Additional Info -->
             <div class="card border-0 shadow-sm mt-4">
                 <div class="card-body p-4">
                    <h6 class="fw-bold text-primary text-uppercase small ls-1 mb-3">Additional Information</h6>
                     <div class="row g-4">
                        <div class="col-md-7">
                             <label class="form-label fw-medium">Remarks</label>
                             <textarea class="form-control ${readonlyClass}" rows="5" placeholder="Enter remarks" ${inputAttr}>${data.remarks || ''}</textarea>
                        </div>
                                                        <div class="col-md-5">
                                                        <label class="form-label fw-medium">Attachments</label>
                                                            ${isEditMode ? `
                                                                <div id="workspaceDropZone" class="border border-2 border-dashed p-4 text-center rounded bg-light mb-2 cursor-pointer" tabindex="0" role="button">
                                                                        <i data-lucide="upload-cloud" class="d-block mx-auto mb-2 text-primary" width="24"></i>
                                                                        <small class="text-muted">Drop files or click to upload</small>
                                                                </div>
                                                                <input type="file" id="workspaceFileInput" name="workspace_files[]" multiple style="display:none">
                                                                <div id="workspaceFileList" class="mt-2 text-start small"></div>
                                                        ` : ''}
                                                        <div class="d-flex flex-column gap-2">
                                ${(data.attachments || []).map(file => `
                                    <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-white">
                                        <div class="d-flex align-items-center gap-2 text-truncate">
                                            <i data-lucide="file-text" width="16" class="text-muted"></i>
                                            <span class="small text-truncate" style="max-width: 150px;">${file}</span>
                                        </div>
                                         ${isEditMode ?
                '<button class="btn btn-link btn-sm text-danger p-0"><i data-lucide="x" width="16"></i></button>' :
                '<button class="btn btn-link btn-sm text-primary p-0"><i data-lucide="download" width="16"></i></button>'}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                     </div>
                 </div>
             </div>
        `;

        // Side Panel Content
        if (sidePanel) {
            if (isNewProject) {
                // Creation Mode Sidebar
                sidePanel.innerHTML = `
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="fw-bold fs-7 mb-3">Workspace Actions</h6>
                            <button class="btn btn-primary w-100 mb-2 py-2 fw-medium d-flex align-items-center justify-content-center gap-2" id="workspace-save-btn-side" style="background-color: #154A9A; border-color: #154A9A;">
                                <i data-lucide="check" width="18"></i> Save Project
                            </button>
                            <button class="btn btn-outline-secondary w-100 py-2 fw-medium d-flex align-items-center justify-content-center gap-2" id="workspaceCancelBtn" data-page="projects">
                                <i data-lucide="x" width="18"></i> Cancel
                            </button>
                        </div>
                    </div>
                `;

                const sideSaveBtn = document.getElementById('workspace-save-btn-side');
                if (sideSaveBtn) {
                    sideSaveBtn.onclick = () => {
                        saveProjectAction();
                    };
                }

                // Handle cancel button click - reset state before navigating
                // Since sidePanel.innerHTML is recreated each time renderWorkspace is called,
                // we need to add the listener fresh each time
                const cancelBtn = sidePanel.querySelector('[data-page="projects"]');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', (e) => {
                        // Reset state before navigation happens
                        currentProjectData = null;
                        isEditMode = false;
                        isNewProject = false;
                        // Note: Content will be cleared by resetProjectWorkspace in navigation.js
                    }, { capture: true }); // Use capture phase to run before navigation handler
                }
            } else {
                // View/Edit Mode Sidebar
                const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
                const isAgency = currentUser.role === 'agency';

                sidePanel.innerHTML = `
                    ${isAgency ? '' : `
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-3">
                            <h6 class="fw-bold fs-7 mb-3">Quick Actions</h6>
                            <button class="btn ${isEditMode ? 'btn-primary text-white' : 'btn-primary'} w-100 mb-2" id="toggle-mode-btn" ${isEditMode ? 'style="background-color: #154A9A; border-color: #154A9A;"' : ''}>
                                 <i data-lucide="${isEditMode ? 'check' : 'edit-3'}" class="me-1"></i> ${isEditMode ? 'Save Project' : 'Edit Project'}
                            </button>
                            ${isEditMode ? '<button class="btn btn-outline-secondary w-100 mb-2" id="cancel-edit-btn">Cancel</button>' : ''}
                            <button class="btn btn-outline-danger w-100 mb-2" id="delete-project-btn">
                                 <i data-lucide="trash-2" class="me-2" width="16"></i> Delete Project
                            </button>
                        </div>
                    </div>
                    `}

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-3">
                             <h6 class="fw-bold fs-7 mb-2">Project Info</h6>
                             <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                                <i data-lucide="info" width="16"></i>
                                <span>${data.status}</span>
                             </div>
                             <div class="d-flex align-items-center gap-2 text-muted small">
                                <i data-lucide="calendar" width="16"></i>
                                <span>Endorsed ${data.endorsementYear}</span>
                             </div>
                        </div>
                    </div>
                `;

                const toggleBtn = document.getElementById('toggle-mode-btn');
                if (toggleBtn) {
                    toggleBtn.onclick = () => {
                        if (isEditMode) {
                            saveProjectAction();
                        } else {
                            isEditMode = true;
                            renderWorkspace();
                        }
                    };
                }

                const cancelBtn = document.getElementById('cancel-edit-btn');
                if (cancelBtn) {
                    cancelBtn.onclick = () => {
                        isEditMode = false;
                        renderWorkspace();
                    }
                }

                const deleteBtn = document.getElementById('delete-project-btn');
                if (deleteBtn) {
                    deleteBtn.onclick = () => {
                        if (window.showConfirmModal) {
                            window.showConfirmModal({
                                title: 'Delete Project',
                                message: `Are you sure you want to delete project "<strong>${data.name}</strong>"? This action cannot be undone.`,
                                confirmText: 'Delete Project',
                                confirmClass: 'btn-danger',
                                onConfirm: () => {
                                    if (window.showSimpleAlert) {
                                        window.showSimpleAlert('Project deleted successfully.', 'success');
                                    }
                                    // Reset state and navigate back
                                    currentProjectData = null;
                                    isEditMode = false;
                                    isNewProject = false;
                                    if (window.switchPage) window.switchPage('projects');
                                }
                            });
                        }
                    };
                }

                // Handle back button in header - reset state before navigating
                // Select the back button in the header (not the cancel button in side panel)
                const headerArea = document.querySelector('#project-workspace > .d-flex.align-items-center');
                const backBtn = headerArea ? headerArea.querySelector('[data-page="projects"]') : null;
                if (backBtn && !backBtn.hasAttribute('data-back-listener')) {
                    backBtn.setAttribute('data-back-listener', 'true');
                    backBtn.addEventListener('click', () => {
                        // Reset state before navigation
                        currentProjectData = null;
                        isEditMode = false;
                        isNewProject = false;
                        // Note: Content will be cleared by resetProjectWorkspace in navigation.js
                    }, { capture: true }); // Use capture phase to run before navigation handler
                }
            }
        }

        if (window.lucide) window.lucide.createIcons();

        // Initialize dynamic location logic (CIPG style)
        _initWorkspaceLocationLogic();

        // initialize workspace attachments drop if present
        try {
            createFileDrop('workspaceDropZone', 'workspaceFileInput', 'workspaceFileList');
        } catch (err) { }

        // Logic for adding new indicator from the workspace
        const addIndBtn = workspaceContent.querySelector('#add-new-indicator-btn');
        if (addIndBtn) {
            addIndBtn.onclick = () => {
                const modalEl = document.getElementById('addIndicatorModal');
                if (modalEl) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    bsModal.show();
                }
            };
        }
    };

    // Open Workspace Function
    window.openProjectWorkspace = (projectData) => {
        // Reset state first to ensure clean start
        currentProjectData = null;
        isEditMode = false;
        isNewProject = false;

        // Clear existing content before rendering new
        if (workspaceContent) workspaceContent.innerHTML = '';
        if (sidePanel) sidePanel.innerHTML = '';
        if (workspaceTitle) workspaceTitle.textContent = '';

        // Set new project data
        if (projectData.isNew) {
            currentProjectData = { ...emptyProjectDetail };
            isEditMode = true;
            isNewProject = true;
        } else {
            currentProjectData = projectData.name && projectData.name.includes("Tabaco")
                ? mockFullProjectDetail
                : { ...mockFullProjectDetail, name: (projectData.name || "Untitled Project") };
            isEditMode = projectData.edit === true;
            isNewProject = false;
        }
        renderWorkspace();

        if (window.switchPage) {
            window.switchPage('project-workspace');
        }
    };

    // Global Event Listener for project clicks
    document.addEventListener('click', (e) => {
        const createBtn = e.target.closest('.create-project-btn');
        if (createBtn) {
            e.preventDefault();
            e.stopPropagation();
            window.openProjectWorkspace({ name: "New Project", isNew: true });
            return;
        }

        // Three-dot dropdown actions (View, Edit, Delete)
        const viewAction = e.target.closest('.project-action-view');
        const editAction = e.target.closest('.project-action-edit');
        const deleteAction = e.target.closest('.project-action-delete');

        if (viewAction || editAction || deleteAction) {
            e.preventDefault();
            e.stopPropagation();
            const row = (viewAction || editAction || deleteAction).closest('tr');
            const name = row ? row.querySelector('td:first-child').textContent.trim() : '';
            if (deleteAction) {
                if (name && window.showConfirmModal) {
                    window.showConfirmModal({
                        title: 'Delete Project',
                        message: `Are you sure you want to delete project "<strong>${name}</strong>"? This action cannot be undone.`,
                        confirmText: 'Delete Project',
                        confirmClass: 'btn-danger',
                        onConfirm: () => {
                            row && row.remove();
                            if (window.showSimpleAlert) {
                                window.showSimpleAlert('Project deleted successfully.', 'success');
                            }
                        }
                    });
                }
                return;
            }
            if (name) {
                window.openProjectWorkspace({ name, edit: !!editAction });
            }
            return;
        }

        // Click on row (but not on dropdown) opens project in view mode
        const dropdown = e.target.closest('.project-actions-dropdown');
        const row = e.target.closest('.clickable-row');
        if (row && !dropdown) {
            e.preventDefault();
            e.stopPropagation();
            const name = row.querySelector('td:first-child').textContent.trim();
            if (name) window.openProjectWorkspace({ name });
        }
    });

    const _validateFields = (container) => {
        let isValid = true;
        const requiredFields = container.querySelectorAll('[data-required]');

        requiredFields.forEach(field => {
            // skip elements that are disabled or not currently visible
            if (field.disabled || field.offsetParent === null) return;

            if (!field.value || field.value.trim() === '') {
                isValid = false;
                field.classList.add('is-invalid');
                // Remove invalid class on input/change
                field.addEventListener('input', () => field.classList.remove('is-invalid'), { once: true });
                field.addEventListener('change', () => field.classList.remove('is-invalid'), { once: true });
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Validate coverage radio group
        const coverageChecked = container.querySelector('input[name="project-coverage"]:checked');
        if (!coverageChecked) {
            isValid = false;
            const radios = container.querySelectorAll('input[name="project-coverage"]');
            radios.forEach(r => r.classList.add('is-invalid'));
            const locErr = container.querySelector('#location-error');
            if (locErr) locErr.style.display = 'block';
            radios.forEach(r => r.addEventListener('change', () => {
                radios.forEach(rr => rr.classList.remove('is-invalid'));
                if (locErr) locErr.style.display = 'none';
            }, { once: true }));
        } else {
            // additional location-specific requirements
            if (coverageChecked.value === 'Location-Specific') {
                ['#f-province', '#f-district', '#f-municipality'].forEach(sel => {
                    const el = container.querySelector(sel);
                    if (el && !el.disabled && (!el.value || el.value === '')) {
                        isValid = false;
                        el.classList.add('is-invalid');
                        el.addEventListener('change', () => el.classList.remove('is-invalid'), { once: true });
                    }
                });
            } else if (coverageChecked.value === 'Inter-Province') {
                const hidden = container.querySelector('#f-provinces');
                if (hidden && (!hidden.value || hidden.value.trim() === '')) {
                    isValid = false;
                    const box = container.querySelector('#f-provinces-box');
                    if (box) {
                        box.classList.add('border-danger');
                        box.addEventListener('click', () => box.classList.remove('border-danger'), { once: true });
                    }
                    // show feedback text if available
                    const feedback = hidden.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.style.display = 'block';
                    }
                }
            }
        }

        return isValid;
    };

    const saveProjectAction = () => {
        if (!_validateFields(workspaceContent)) {
            // scroll to first invalid element so user can see feedback
            const firstInvalid = workspaceContent.querySelector('.is-invalid, .border-danger');
            if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (window.showSimpleAlert) {
                window.showSimpleAlert('Please fill in all required fields.', 'danger');
            }
            return;
        }

        if (window.showSimpleAlert) {
            window.showSimpleAlert('Project Saved Successfully.', 'success');
        }

        // capture physical target and project cost data back into currentProjectData
        if (currentProjectData) {
            const ptInputs = workspaceContent.querySelectorAll('[data-physical-target]');
            currentProjectData.physicalTarget = Array.from(ptInputs).map(inp => inp.value.trim());
            const pcInputs = workspaceContent.querySelectorAll('[data-project-cost]');
            currentProjectData.projectCost = Array.from(pcInputs).map(inp => inp.value.trim());
        }

        isEditMode = false;
        isNewProject = false;
        renderWorkspace();
    };    // Reset Workspace State — called by navigation.js when navigating away
    window.resetProjectWorkspace = () => {
        currentProjectData = null;
        isEditMode = false;
        isNewProject = false;
        if (workspaceContent) workspaceContent.innerHTML = '';
        if (sidePanel) sidePanel.innerHTML = '';
        if (workspaceTitle) workspaceTitle.textContent = '';
    };

    /* ── Bicol Region location data ── */
    const LOCATION_DATA = {
        'Albay': {
            '1st District': ['Legazpi City', 'Ligao City', 'Libon', 'Oas', 'Polangui', 'St. Michaels (Pitogo)'],
            '2nd District': ['Tabaco City', 'Bacacay', 'Malilipot', 'Malinao', 'Santo Domingo', 'Tiwi'],
            '3rd District': ['Camalig', 'Daraga', 'Guinobatan', 'Jovellar', 'Manito', 'Rapu-Rapu'],
        },
        'Camarines Norte': {
            '1st District': ['Daet', 'Basud', 'Capalonga', 'Jose Panganiban', 'Labo', 'Mercedes', 'San Vicente', 'Sta. Elena', 'Talisay', 'Vinzons'],
            '2nd District': ['Paracale', 'San Lorenzo Ruiz', 'Garchitorena', 'Mambulao (Jose Panganiban)'],
        },
        'Camarines Sur': {
            '1st District': ['Naga City', 'Bombom', 'Calabanga', 'Camaligan', 'Canaman', 'Gainza', 'Magarao', 'Milaor', 'Minalabac', 'Pamplona', 'Pasacao', 'San Fernando'],
            '2nd District': ['Partido Area: Goa', 'Lagonoy', 'Presentacion', 'Sangay', 'San Jose', 'Tigaon', 'Tinambac'],
            '3rd District': ['Iriga City', 'Baao', 'Balatan', 'Bula', 'Buhi', 'Bato', 'Nabua', 'Pili'],
            '4th District': ['Caramoan', 'Del Gallego', 'Lupi', 'Ragay', 'Sipocot', 'Cabusao'],
        },
        'Catanduanes': {
            '1st District': ['Virac', 'Bagamanoc', 'Baras', 'Bato', 'Caramoran', 'Gigmoto'],
            '2nd District': ['Pandan', 'Panganiban', 'San Andres', 'San Miguel', 'Viga'],
        },
        'Masbate': {
            '1st District': ['Masbate City', 'Aroroy', 'Baleno', 'Balud', 'Batuan', 'Cataingan', 'Cawayan'],
            '2nd District': ['Claveria', 'Dimasalang', 'Esperanza', 'Mandaon', 'Milagros', 'Mobo', 'Monreal'],
            '3rd District': ['Palanas', 'Pio V. Corpuz', 'Placer', 'San Fernando', 'San Jacinto', 'San Pascual', 'Uson'],
        },
        'Sorsogon': {
            '1st District': ['Sorsogon City', 'Barcelona', 'Bulusan', 'Casiguran', 'Castilla', 'Donsol', 'Gubat'],
            '2nd District': ['Irosin', 'Juban', 'Magallanes', 'Matnog', 'Pilar', 'Prieto Diaz', 'Santa Magdalena'],
        },
    };

    function _initWorkspaceLocationLogic() {
        const rads = document.querySelectorAll('input[name="project-coverage"]');
        const locFields = document.getElementById('location-specific-fields');
        const interProvFields = document.getElementById('inter-province-fields');
        const provinceEl = document.getElementById('f-province');
        const districtEl = document.getElementById('f-district');
        const municipalEl = document.getElementById('f-municipality');

        if (!rads.length) return;

        function _populateSelect(el, items, placeholder) {
            el.innerHTML = `<option value="">${placeholder}</option>`;
            items.forEach(val => {
                const opt = document.createElement('option');
                opt.value = val; opt.textContent = val;
                el.appendChild(opt);
            });
        }

        function _resetSelect(el, placeholder) {
            el.innerHTML = `<option value="">${placeholder}</option>`;
            el.disabled = true;
            el.value = '';
        }

        rads.forEach(r => r.addEventListener('change', () => {
            const isLocSpec = r.value === 'Location-Specific';
            const isInterProv = r.value === 'Inter-Province';

            // clear any validation styling from previous state
            rads.forEach(rr => rr.classList.remove('is-invalid'));
            const locErr = document.querySelector('#location-error');
            if (locErr) locErr.style.display = 'none';
            ['#f-province', '#f-district', '#f-municipality'].forEach(sel => {
                const el = document.querySelector(sel);
                if (el) el.classList.remove('is-invalid');
            });
            const box = document.querySelector('#f-provinces-box');
            if (box) box.classList.remove('border-danger');
            const hiddenProv = document.querySelector('#f-provinces');
            if (hiddenProv) {
                const fb = hiddenProv.nextElementSibling;
                if (fb && fb.classList.contains('invalid-feedback')) fb.style.display = 'none';
            }

            if (locFields) locFields.style.display = isLocSpec ? 'block' : 'none';
            if (interProvFields) interProvFields.style.display = isInterProv ? 'block' : 'none';

            if (provinceEl) { provinceEl.disabled = !isLocSpec; }
            if (!isLocSpec) {
                _resetSelect(districtEl, '-- Select District --');
                _resetSelect(municipalEl, '-- Select City/Municipality --');
                if (provinceEl) provinceEl.value = '';
            }

            const tagInput = document.getElementById('provinces-tag-input');
            const tagHidden = document.getElementById('f-provinces');
            const tagBox = document.getElementById('f-provinces-box');
            if (tagInput) { tagInput.disabled = !isInterProv; }
            if (tagHidden) { tagHidden.disabled = !isInterProv; }

            // Clear Inter-Province state when switching away
            if (!isInterProv) {
                selectedProvinces = [];
                if (tagHidden) tagHidden.value = '';
                if (tagBox) {
                    const tags = tagBox.querySelectorAll('.prov-tag');
                    tags.forEach(t => t.remove());
                }
                if (tagInput) { tagInput.placeholder = 'Select provinces...'; tagInput.value = ''; }
                // Reset checkboxes in dropdown
                const dropdown = document.getElementById('provinces-dropdown');
                if (dropdown) {
                    dropdown.querySelectorAll('input[type="checkbox"]').forEach(c => c.checked = false);
                }
            }
        }));

        if (provinceEl) {
            provinceEl.addEventListener('change', () => {
                _resetSelect(districtEl, '-- Select District --');
                _resetSelect(municipalEl, '-- Select City/Municipality --');
                const districts = LOCATION_DATA[provinceEl.value];
                if (districts && provinceEl.value) {
                    _populateSelect(districtEl, Object.keys(districts), '-- Select District --');
                    districtEl.disabled = false;
                }
            });
        }

        if (districtEl) {
            districtEl.addEventListener('change', () => {
                _resetSelect(municipalEl, '-- Select City/Municipality --');
                const province = provinceEl ? provinceEl.value : '';
                const districts = LOCATION_DATA[province];
                const cities = districts ? districts[districtEl.value] : null;
                if (cities && districtEl.value) {
                    _populateSelect(municipalEl, cities, '-- Select City/Municipality --');
                    municipalEl.disabled = false;
                }
            });
        }

        // Initialize Provinces Tag Input
        _initWorkspaceProvincesTagInput();

        // Initial Population if data exists
        if (provinceEl && provinceEl.value) {
            const districts = LOCATION_DATA[provinceEl.value];
            if (districts) {
                const currentDistrict = districtEl.getAttribute('data-value') || districtEl.value;
                _populateSelect(districtEl, Object.keys(districts), '-- Select District --');
                districtEl.value = currentDistrict;
                districtEl.disabled = provinceEl.disabled;

                if (districtEl.value) {
                    const cities = districts[districtEl.value];
                    if (cities) {
                        const currentCity = municipalEl.getAttribute('data-value') || municipalEl.value;
                        _populateSelect(municipalEl, cities, '-- Select City/Municipality --');
                        municipalEl.value = currentCity;
                        municipalEl.disabled = districtEl.disabled;
                    }
                }
            }
        }
    }

    function _initWorkspaceProvincesTagInput() {
        const box = document.getElementById('f-provinces-box');
        const input = document.getElementById('provinces-tag-input');
        const dropdown = document.getElementById('provinces-dropdown');
        const hiddenInput = document.getElementById('f-provinces');
        if (!box || !input || !dropdown || !hiddenInput) return;

        // Sync from hidden input (populated during renderWorkspace)
        selectedProvinces = hiddenInput.value ? hiddenInput.value.split(', ').map(s => s.trim()).filter(v => v) : [];

        function renderTags() {
            const tags = box.querySelectorAll('.prov-tag');
            tags.forEach(t => t.remove());
            selectedProvinces.forEach(prov => {
                const tag = document.createElement('span');
                tag.className = 'prov-tag d-inline-flex align-items-stretch border rounded';
                tag.style.background = '#e2e8f0';
                tag.style.fontSize = '0.86rem';
                tag.innerHTML = `
                    <span class="prov-remove border-end px-2 text-muted d-flex align-items-center justify-content-center" style="cursor:pointer;" data-prov="${prov}">&times;</span>
                    <span class="px-2 py-1" style="color:#1e293b;">${prov}</span>
                `;
                box.insertBefore(tag, input);
            });
            hiddenInput.value = selectedProvinces.join(', ');
            input.placeholder = selectedProvinces.length > 0 ? '' : 'Select provinces...';
        }
        renderTags();

        box.addEventListener('click', (e) => {
            if (e.target.classList.contains('prov-remove')) {
                const prov = e.target.getAttribute('data-prov');
                selectedProvinces = selectedProvinces.filter(p => p !== prov);
                renderTags();
                filterDropdown();
                input.focus();
                return;
            }
            input.focus();
        });

        const showDropdown = () => dropdown.classList.add('show');
        const hideDropdown = () => setTimeout(() => dropdown.classList.remove('show'), 200);

        input.addEventListener('focus', () => { filterDropdown(); showDropdown(); });
        input.addEventListener('blur', hideDropdown);
        input.addEventListener('input', () => { filterDropdown(); showDropdown(); });

        function filterDropdown() {
            const val = input.value.toLowerCase().trim();
            const items = dropdown.querySelectorAll('.dropdown-item');
            items.forEach(item => {
                const prov = item.getAttribute('data-prov');
                const match = prov.toLowerCase().includes(val);
                const chk = item.querySelector('input[type="checkbox"]');
                if (chk) chk.checked = selectedProvinces.includes(prov);
                item.parentElement.style.display = match ? '' : 'none';
            });
        }

        dropdown.addEventListener('mousedown', (e) => {
            e.preventDefault();
            let item = e.target.closest('.dropdown-item');
            if (item) {
                const prov = item.getAttribute('data-prov');
                if (selectedProvinces.includes(prov)) {
                    selectedProvinces = selectedProvinces.filter(p => p !== prov);
                } else {
                    selectedProvinces.push(prov);
                }
                input.value = '';
                renderTags();
                filterDropdown();
                input.focus();
            }
        });

        dropdown.addEventListener('click', (e) => {
            if (e.target.closest('a')) e.preventDefault();
        });
    }

    /* ── Discard Modal for Single projects ── */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#workspaceBackBtn, #workspaceCancelBtn');
        if (!btn) return;

        // Only show modal if editing or creating
        if (!isEditMode && !isNewProject) {
            if (window.switchPage) window.switchPage('projects');
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        if (window.showConfirmModal) {
            window.showConfirmModal({
                title: 'Discard Changes?',
                message: 'Are you sure you want to leave? Any unsaved changes to this project profile will be lost.',
                confirmText: 'Discard & Leave',
                confirmClass: 'btn-danger',
                onConfirm: () => {
                    window.resetProjectWorkspace();
                    if (window.switchPage) window.switchPage('projects');
                }
            });
        } else if (confirm('Discard changes and leave?')) {
            window.resetProjectWorkspace();
            window.switchPage('projects');
        }
    });

    /* ── Discard Modal for Component Project ── */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#compProjectBackBtn, #compProjectCancelBtn');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        if (window.showConfirmModal) {
            window.showConfirmModal({
                title: 'Discard Changes?',
                message: 'Are you sure you want to leave? Any unsaved changes to this component project will be lost.',
                confirmText: 'Discard & Leave',
                confirmClass: 'btn-danger',
                onConfirm: () => {
                    if (window.switchPage) window.switchPage('component-project');
                }
            });
        } else if (confirm('Discard changes and leave?')) {
            window.switchPage('component-project');
        }
    });

    /* ── Save Validation for Component Project ── */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#saveComponentProjectBtn');
        if (!btn) return;

        const mainSection = document.getElementById('create-component-project');
        if (!mainSection) return;

        e.preventDefault();
        e.stopPropagation();

        if (!_validateFields(mainSection)) {
            if (window.showSimpleAlert) {
                window.showSimpleAlert('Please fill in all required fields.', 'danger');
            }
            return;
        }

        if (window.showSimpleAlert) {
            window.showSimpleAlert('Component Project Saved Successfully.', 'success');
        }

        if (window.switchPage) window.switchPage('component-project');
    });

    /* ── Add Indicator for Component Project ── */
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('#comp-add-indicator-btn');
        if (!btn) return;

        const modalEl = document.getElementById('addIndicatorModal');
        if (!modalEl) return;

        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.show();
    });

    // Function to populate component indicators
    window.populateComponentIndicators = () => {
        const select = document.getElementById('comp-indicator');
        if (!select) return;
        const currentVal = select.value;
        select.innerHTML = '<option value="">-- Select Indicator --</option>';
        _getIndicatorOptions().forEach(ind => {
            const opt = document.createElement('option');
            opt.value = ind; opt.textContent = ind;
            if (ind === currentVal) opt.selected = true;
            select.add(opt);
        });
    }
}
