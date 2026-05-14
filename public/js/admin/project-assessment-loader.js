import { initParDocxExport } from './par-docx-export.js';

export function initProjectAssessmentLoader() {
    window.initProjectAssessmentLoader = initProjectAssessmentLoader;

    // Wire DOCX download button if on the form page
    initParDocxExport();

    const activeSection = document.querySelector('.page-content.active');
    if (!activeSection) return;

    const activeTbody = activeSection.querySelector('tbody[id$="-par-tbody"]') || activeSection.querySelector('tbody#par-tbody');
    const activeCountSpan = activeSection.querySelector('span[id$="-par-count"]') || activeSection.querySelector('span#par-count');
    
    // Wire up refresh buttons
    activeSection.querySelectorAll('button[id$="RefreshParBtn"]').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            _renderPARTable(activeTbody, activeCountSpan);
        };
    });

    const adminValidationSelect = document.getElementById('adminParValidationSelect');
    const adminNewBtn = document.getElementById('adminNewParBtn');
    const newBtn = document.getElementById('newParBtn');

    const formEl = document.getElementById('parFormMain');
    const btnReview = document.getElementById('btnSaveReview');
    const btnEvaluated = document.getElementById('btnSaveEvaluated');
    const btnRevision = document.getElementById('btnSaveRevision');
    const btnCommittee = document.getElementById('btnSaveCommittee');
    const btnApproved = document.getElementById('btnSaveApproved');
    const btnSaveReviewed = document.getElementById('btnSaveReviewed');
    const btnSaveFinal = document.getElementById('btnSaveFinal');
    const btnAddFinding = document.getElementById('btnAddFindingRow');
    const btnSaveCommentsOnly = document.getElementById('btnSaveCommentsOnly');
    const btnSubmitCommentsAgency = document.getElementById('btnSubmitCommentsAgency');
    const parCommentsDateStatus = document.getElementById('parCommentsDateStatus');
    const btnOpenCommentsModal = document.getElementById('btnOpenCommentsModal');

    // Role-based button visibility
    const currentUser = (window.__CURRENT_USER__ || {});
    const currentUserRole = currentUser.role || '';
    const userDivision = (currentUser.division || '').toUpperCase();
    const _role = currentUserRole.toLowerCase();
    const isPdipbd = userDivision.includes('PDIPBD') || userDivision.includes('PDIPB');
    const hideSubmitFromReviewQueues = sessionStorage.getItem('pdipb_hide_submit_comments') === '1';

    // Show submit button only to PDIPBD staff
    if (btnSubmitCommentsAgency) {
        btnSubmitCommentsAgency.style.display = (isPdipbd && !hideSubmitFromReviewQueues) ? 'inline-block' : 'none';
        btnSubmitCommentsAgency.onclick = (e) => {
            e.preventDefault();
            _savePARComments(true); // isSubmitted = true
        };
    }

    if (btnOpenCommentsModal) {
        btnOpenCommentsModal.onclick = (e) => {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('parCommentsModal'));
            modal.show();
        };
    }

    if (btnAddFinding) {
        btnAddFinding.onclick = (e) => {
            e.preventDefault();
            _addPARFindingRow();
        };
    }

    if (btnSaveCommentsOnly) {
        btnSaveCommentsOnly.onclick = (e) => {
            e.preventDefault();
            _savePARComments(false); // isSubmitted = false
        };
    }

    // ── Update Action Bar Buttons ──
    async function updateActionBarButtons() {
        const currentParId = sessionStorage.getItem('par_edit_id');
        let currentStatus = '';
        if (currentParId) {
            const allAss = await localforage.getItem('project_assessments') || [];
            const p = allAss.find(x => x.id === currentParId);
            currentStatus = p?.status || '';
        }

        const isReadonly = formEl?.dataset.isReadonly === '1';

        // Hide ALL by default if readonly (except comments which handled below)
        const actionButtons = document.querySelectorAll('.assessment-action-bar .btn-status-save:not(.btn-comments-fab)');
        if (isReadonly) {
            actionButtons.forEach(btn => btn.style.display = 'none');
            return;
        }

        // Standard role-based logic
        if (btnEvaluated) btnEvaluated.style.display = (_role.includes('division') || isPdipbd) ? 'none' : 'flex';
        if (btnApproved) btnApproved.style.display = (_role.includes('staff') || isPdipbd) ? 'none' : 'flex';
        if (btnRevision) btnRevision.style.display = isPdipbd ? 'none' : 'flex';
        if (btnReview) btnReview.style.display = isPdipbd ? 'none' : 'flex';

        // PDIPBD Specific logic (depends on status)
        if (isPdipbd) {
            const isReviewed = currentStatus === 'Reviewed';
            const isFinal = currentStatus === 'Final';

            // Show 'Save as Reviewed' even if already reviewed, so they can update it
            if (btnSaveReviewed) btnSaveReviewed.style.display = isFinal ? 'none' : 'flex';
            
            // ALWAYS Hide Presentation button as requested (will be handled by Finalize modal)
            if (btnCommittee) btnCommittee.style.display = 'none';
            
            // Finalize button shows only when reviewed
            if (btnSaveFinal) btnSaveFinal.style.display = isReviewed ? 'flex' : 'none';
        } else {
            if (btnSaveReviewed) btnSaveReviewed.style.display = 'none';
            if (btnCommittee) btnCommittee.style.display = 'none';
            if (btnSaveFinal) btnSaveFinal.style.display = 'none';
        }
        
        // Final sanity check for onclicks (already assigned usually but making sure)
        if (btnSaveReviewed) btnSaveReviewed.onclick = (e) => { e.preventDefault(); _saveAssessment('Reviewed'); };
        if (btnCommittee) btnCommittee.onclick = (e) => { e.preventDefault(); _saveAssessment('Sectoral Presentation'); };

        if (window.lucide) window.lucide.createIcons();
    }

    updateActionBarButtons();

    if (btnSaveFinal) {

        btnSaveFinal.onclick = async (e) => { 
            e.preventDefault(); 
            // Reset modal fields
            const fileInput = document.getElementById('finalParFileInput');
            const nameDisp = document.getElementById('finalParFileNameDisplay');
            const notesArea = document.getElementById('finalParNotes');
            if (fileInput) fileInput.value = '';
            if (nameDisp) nameDisp.classList.add('d-none');
            if (notesArea) notesArea.value = '';

            // Auto-check sectoral if there are no existing comments/findings
            const sectoralCb = document.getElementById('finalParSectoralCheckbox');
            const confirmBtn = document.getElementById('btnConfirmFinalizePar');
            if (sectoralCb) {
                const parId = sessionStorage.getItem('par_edit_id');
                const allCR = await localforage.getItem('comments_recommendations') || [];
                const linkedCR = allCR.find(cr => cr.connectedParId === parId);
                const hasFindings = linkedCR?.formData?.projects?.[0]?.findingsList?.some(f => f.findings || f.recommendations);
                sectoralCb.checked = !hasFindings;
                if (confirmBtn) {
                    confirmBtn.innerHTML = sectoralCb.checked
                        ? '<i data-lucide="presentation" width="14" class="me-2"></i> For Sectoral Presentation'
                        : '<i data-lucide="check-circle" width="18" class="me-2"></i> Finalize PAR';
                    if (window.lucide) window.lucide.createIcons({ target: confirmBtn });
                }
            }
            
            const modal = new bootstrap.Modal(document.getElementById('uploadFinalParModal'));
            modal.show();
        };
    }

    // ── Location Data (CPP-Style) ──
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

    // Shared location select helpers
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

    if (activeTbody) {
        _renderPARTable(activeTbody, activeCountSpan);
    }
    if (adminValidationSelect) {
        _populateValidationSelect(adminValidationSelect);
    }

    // Navigation handlers
    if (newBtn) {
        newBtn.onclick = () => {
            sessionStorage.removeItem('par_edit_id');
            sessionStorage.removeItem('par_prefill_cte_id');
            sessionStorage.removeItem('par_prefill_title');
            sessionStorage.removeItem('par_prefill_agency');
            if (window.switchPage) window.switchPage('project-assessment-report-form');
            setTimeout(() => initProjectAssessmentLoader(), 50);
        };
    }

    if (adminNewBtn) {
        adminNewBtn.onclick = () => {
            const selectedCteId = adminValidationSelect.value;
            if (!selectedCteId) {
                if (window.showSimpleAlert) window.showSimpleAlert('Please select a project from Test & Evaluation first.', 'warning');
                return;
            }
            
            const selectedOpt = adminValidationSelect.options[adminValidationSelect.selectedIndex];
            sessionStorage.removeItem('par_edit_id');
            sessionStorage.setItem('par_prefill_cte_id', selectedCteId);
            sessionStorage.setItem('par_prefill_title', selectedOpt.dataset.projectTitle);
            sessionStorage.setItem('par_prefill_agency', selectedOpt.dataset.agency);
            
            if (window.switchPage) window.switchPage('project-assessment-report-form');
            setTimeout(() => initProjectAssessmentLoader(), 50);
        };
    }

    const backBtn = document.getElementById('parFormBackToList');
    if (backBtn) {
        backBtn.onclick = () => {
            // Clear all possible prefill session data on back
            sessionStorage.removeItem('par_edit_id');
            sessionStorage.removeItem('par_prefill_cte_id');
            sessionStorage.removeItem('par_prefill_title');
            sessionStorage.removeItem('par_prefill_agency');

            // Role-based redirect
            const currentUser = (window.__CURRENT_USER__ || {});
            const role = (currentUser.role || '').toLowerCase();
            let target = 'project-assessment-report';
            if (role === 'admin') target = 'admin-project-assessment';
            else if (role.includes('division')) target = 'division-head-project-assessment';
            else if (role.includes('staff')) target = 'staff-project-assessment';

            if (window.switchPage) window.switchPage(target);
            setTimeout(() => initProjectAssessmentLoader(), 80);
        };
    }

    const editId = sessionStorage.getItem('par_edit_id');

    async function _initForm() {
        if (!formEl) return;
        
        formEl.reset(); // Clear old cached data
        formEl.dataset.connectedCteId = ''; // Clear old linked data
        _togglePARFormReadonly(false); // Reset to editable state by default
        
        const titleInput = formEl.querySelector('[name="projectTitle"]');
        if (titleInput) titleInput.readOnly = false;

        const proponentInput = formEl.querySelector('[name="implementingAgency"]');
        if (proponentInput) proponentInput.disabled = false;

        // Initial population (for New or Prefill)
        const agencySelect = formEl.querySelector('[name="implementingAgency"]');
        if (agencySelect && !editId) {
            const prefill = sessionStorage.getItem('par_prefill_agency') || '';
            await window.populateAgencyDropdown(agencySelect, prefill);
        }
        
        const allCtes = await _getAllCtes();

        // CASE A: Existing Assessment (Edit Mode)
        if (editId) {
            const all = await _getAllPARs();
            const par = all.find(x => x.id === editId);
            
            // Re-populate agency dropdown with the saved value (ensures it's in the list)
            const agencySelect = formEl.querySelector('[name="implementingAgency"]');
            const savedAgency = par?.formData?.implementingAgency || par?.proponent;
            if (agencySelect && savedAgency) {
                await window.populateAgencyDropdown(agencySelect, savedAgency);
            }

            if (par && par.formData) {

                // 2. Populate text/select/radio inputs
                // Sort keys to ensure province is set before district, etc.
                const sortedEntries = Object.entries(par.formData).sort(([a], [b]) => {
                    if (a.includes('province') && (b.includes('district') || b.includes('municipality'))) return -1;
                    if (a.includes('district') && b.includes('municipality')) return -1;
                    return 0;
                });

                for (let [name, value] of sortedEntries) {
                    const inputs = formEl.querySelectorAll(`[name="${name}"]`);
                    inputs.forEach(input => {
                        if (input.type === 'radio') {
                            if (input.value === value) {
                                input.checked = true;
                                input.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        } else if (input.type !== 'checkbox') {
                            input.value = value;
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    });
                }
                
                // Fallbacks for top-level PAR data if missing from formData
                if (par.projectTitle) {
                    const titleInput = formEl.querySelector('[name="projectTitle"]');
                    if (titleInput) {
                        titleInput.value = par.projectTitle;
                        titleInput.readOnly = true; // Lock the title
                        titleInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
                if (par.proponent) {
                    const agencyInput = formEl.querySelector('[name="implementingAgency"]');
                    if (agencyInput) {
                        agencyInput.value = par.proponent;
                        agencyInput.disabled = true; // Lock the agency
                        agencyInput.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }

                // 3. Populate checkboxes
                formEl.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    if (par.formData.hasOwnProperty(cb.id)) {
                        cb.checked = par.formData[cb.id];
                        cb.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });

                // 4. Trigger budget calculations for the single Annex form
                const budgetInputs = formEl.querySelectorAll('.budgetary-year-input');
                if (budgetInputs.length > 0) {
                    budgetInputs[0].dispatchEvent(new Event('input', { bubbles: true }));
                }

                if (par.connectedCteId) {
                    formEl.dataset.connectedCteId = par.connectedCteId;
                    _showConnectedInfo(par.projectTitle, par.proponent);
                }

                // Re-render province tags if interProvince has value
                if (par.formData.interProvince && typeof _initPARProvincesTag === 'function') {
                    _initPARProvincesTag();
                }

                // Check if form should be readonly based on status & role
                let isReadonly = false;
                const role = (currentUser.role || '').toLowerCase();
                const status = par.status;
                
                // Finalized/Locked statuses per role
                const staffLocked = ['Assessed', 'Evaluated', 'Sectoral Presentation', 'Reviewed', 'Final'];
                const divisionLocked = ['Evaluated', 'Sectoral Presentation', 'Reviewed', 'Final'];

                if (role.includes('staff') && staffLocked.includes(status)) isReadonly = true;
                if (role.includes('division') && divisionLocked.includes(status)) isReadonly = true;
                
                // PDIPBD staff should be able to edit even if Evaluated/Reviewed, but NOT if Final
                if (isPdipbd && status !== 'Final') isReadonly = false;
                
                if (isReadonly) {
                    _togglePARFormReadonly(true);
                } else {
                    _togglePARFormReadonly(false);
                    
                    // Specific override: Proponent and Title stay locked if they were already set, even for PDIPBD
                    if (par.projectTitle) {
                        const titleInput = formEl.querySelector('[name="projectTitle"]');
                        if (titleInput) titleInput.readOnly = true;
                    }
                    if (par.proponent) {
                        const agencyInput = formEl.querySelector('[name="implementingAgency"]');
                        if (agencyInput) agencyInput.disabled = true;
                    }
                }
            }
        } 
        // CASE B: New Referral Pre-fill
        else {
            const prefillTitle = sessionStorage.getItem('par_prefill_title');
            const prefillAgency = sessionStorage.getItem('par_prefill_agency');
            const prefillCteId = sessionStorage.getItem('par_prefill_cte_id');
            
            if (prefillTitle) {
                const titleInput = formEl.querySelector('[name="projectTitle"]');
                if (titleInput) {
                    titleInput.value = prefillTitle;
                    titleInput.readOnly = true; // Lock the title
                }
            }
            
            if (prefillAgency) {
                const proponentInput = formEl.querySelector('[name="implementingAgency"]');
                if (proponentInput) {
                    proponentInput.value = prefillAgency;
                    proponentInput.disabled = true; // Lock the agency
                }
                sessionStorage.removeItem('par_prefill_agency');
            }

            if (prefillCteId) {
                formEl.dataset.connectedCteId = prefillCteId;
                
                const title = prefillTitle || '';
                const agency = prefillAgency || '';
                _showConnectedInfo(title, agency);
                
                // Fetch CTE to pre-fill agency if not already set (fallback)
                const cte = allCtes.find(x => x.id === prefillCteId);
                if (cte) {
                    const proponentInput = formEl.querySelector('[name="implementingAgency"]');
                    if (proponentInput && !proponentInput.value && cte.formData?.implementingAgency) {
                        proponentInput.value = cte.formData.implementingAgency;
                    }
                }
            } else if (prefillTitle || prefillAgency) {
                // If it came directly without a CTE
                _showConnectedInfo(prefillTitle || '', prefillAgency || '');
            }
        }
        
        // Always attach budget calculations to the single Annex A table
        _attachBudgetCalculationLogic(formEl.querySelector('#parSingleProjectContainer'));

        // Initialize Standalone Comments Section (Outside Form)
        _initPARCommentsSection();
    }

    /**
     * Helper to lock/unlock all form inputs
     */
    function _togglePARFormReadonly(isReadonly) {
        if (!formEl) return;
        
        // 1. Inputs, Selects, Textareas
        const inputs = formEl.querySelectorAll('input, select, textarea');
        inputs.forEach(el => {
            if (el.id === 'provinces-tag-input') return; // Handled by container
            el.disabled = isReadonly;
        });

        // 2. Specialized Checkboxes/Radios if needed
        formEl.style.pointerEvents = isReadonly ? 'none' : 'auto';
        formEl.dataset.isReadonly = isReadonly ? '1' : '0';
        
        // 3. Action Bar buttons
        const actionBar = document.querySelector('.assessment-action-bar');
        if (actionBar) {
            actionBar.style.pointerEvents = 'auto'; // Always allow clicking action buttons (like Comments)
            // Hide action buttons but KEEP Comments button
            const actionButtons = actionBar.querySelectorAll('.btn-status-save:not(.btn-comments-fab)');
            actionButtons.forEach(btn => btn.style.display = isReadonly ? 'none' : 'flex');
            
            const commentsBtn = actionBar.querySelector('.btn-comments-fab');
            if (commentsBtn) commentsBtn.style.display = 'flex'; // Always show
            
            if (!isReadonly) {
                updateActionBarButtons();
            }
        }

        // 4. Modal footer buttons
        const modalFooter = document.querySelector('#parCommentsModal .modal-footer');
        if (modalFooter) {
            const saveBtn = modalFooter.querySelector('#btnSaveCommentsOnly');
            const submitBtn = modalFooter.querySelector('#btnSubmitCommentsAgency');
            const addBtn = document.getElementById('btnAddFindingRow');
            const isPdipbd = (currentUser.division || '').toUpperCase().includes('PDIPB');
            
            if (saveBtn) saveBtn.style.display = isReadonly ? 'none' : 'inline-block';
            if (submitBtn) submitBtn.style.display = (isReadonly || !isPdipbd || hideSubmitFromReviewQueues) ? 'none' : 'inline-block';
            if (addBtn) addBtn.style.display = isReadonly ? 'none' : 'inline-block';

            // Ensure modal content is clickable even if form is locked
            const modalEl = document.getElementById('parCommentsModal');
            if (modalEl) modalEl.style.pointerEvents = 'auto';
        }
        
        // 5. Visual indicator
        const header = formEl.querySelector('.form-section-header');
        if (header) {
            const existingBadge = header.querySelector('.readonly-badge');
            if (isReadonly && !existingBadge) {
                const badge = document.createElement('span');
                badge.className = 'badge bg-warning text-dark ms-3 readonly-badge';
                badge.innerHTML = '<i data-lucide="lock" width="12" class="me-1"></i> VIEW ONLY';
                header.appendChild(badge);
                if (window.lucide) window.lucide.createIcons();
            } else if (!isReadonly && existingBadge) {
                existingBadge.remove();
            }
        }
        
        // Expose state for row creation
        formEl.dataset.isReadonly = isReadonly ? '1' : '0';
    }

    async function _getAllCtes() {
        const data = await localforage.getItem('cte_validations');
        return Array.isArray(data) ? data : [];
    }

    function _attachBudgetCalculationLogic(container) {
        if (!container) return;
        const inputs = container.querySelectorAll('.budgetary-year-input');
        const totalDisp = container.querySelector('.project-total-input');
        
        const calc = () => {
            let total = 0;
            inputs.forEach(inp => {
                total += parseFloat(inp.value) || 0;
            });
            totalDisp.value = total.toFixed(2);
        };

        inputs.forEach(inp => inp.addEventListener('input', calc));
    }

    async function _showConnectedInfo(projectTitle, projectAgency) {
        const infoBar = document.getElementById('connected-validation-info');
        const viewBtn = document.getElementById('viewSourceCppBtn');
        const sourceTitleEl = document.getElementById('connected-source-title');
        if (!infoBar || (!projectTitle && !projectAgency)) return;

        // Display whatever title we have
        if (sourceTitleEl) sourceTitleEl.textContent = projectTitle || 'Untitled Project';
        infoBar.style.display = 'block';

        if (viewBtn) {
            viewBtn.onclick = async () => {
                // Attempt to find the CPP by matching the title and agency
                const allOpps = await localforage.getItem('cpp_submissions') || [];
                // Simple matching: find closest match by title
                const match = allOpps.find(s => {
                    const sTitle = s.title || s.formData?.['f-title'] || '';
                    return sTitle.toLowerCase().includes(projectTitle.toLowerCase());
                });

                if (match) {
                    sessionStorage.setItem('cpp_view_id', match.id);
                    sessionStorage.setItem('cpp_view_return_page', 'project-assessment-report-form');
                    if (window.switchPage) window.switchPage('cpp-view');
                    if (window.initCppView) {
                        setTimeout(() => window.initCppView(), 50);
                    }
                } else {
                    if (window.showSimpleAlert) {
                        window.showSimpleAlert('Could not find the linked Core Project Profile.', 'warning');
                    } else {
                        alert('Could not find the linked Core Project Profile.');
                    }
                }
            };
        }
    }

    if (formEl) _initForm();

    async function _getAllPARs() {
        const data = await localforage.getItem('project_assessments');
        return Array.isArray(data) ? data : [];
    }

    async function _setAllPARs(data) {
        await localforage.setItem('project_assessments', data);
    }

    function _readPARFormData() {
        if (!formEl) return {};
        const fd = new FormData(formEl);
        const data = {};
        for (let [k, v] of fd.entries()) {
            data[k] = v;
        }
        // Also capture checkboxes
        formEl.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            data[cb.id] = cb.checked;
        });

        return data;
    }

    async function _saveAssessment(status) {
        const all = await _getAllPARs();
        const editId = sessionStorage.getItem('par_edit_id');
        
        const projectTitle = formEl.querySelector('[name="projectTitle"]').value || 'Untitled Project';
        const proponent = formEl.querySelector('[name="implementingAgency"]').value || 'N/A';

        const currentUser = (window.__CURRENT_USER__ || {});
        const payload = {
            id: editId || `PAR-${Date.now()}`,
            status: status,
            projectTitle: projectTitle,
            proponent: proponent,
            preparedBy: currentUser.email || 'System User',
            preparedByRole: currentUser.role || 'staff',
            connectedCteId: formEl.dataset.connectedCteId || null,
            datePrepared: new Date().toISOString(),
            formData: _readPARFormData()
        };

        const idx = all.findIndex(x => x.id === payload.id);
        if (idx >= 0) all[idx] = payload;
        else all.unshift(payload);

        await _setAllPARs(all);

        // 1. Link orphan comments if this is a new PAR
        if (!editId && payload.connectedCteId) {
            const allCR = await localforage.getItem('comments_recommendations') || [];
            let updated = false;
            allCR.forEach(cr => {
                if (!cr.connectedParId && cr.connectedCteId === payload.connectedCteId) {
                    cr.connectedParId = payload.id;
                    updated = true;
                }
            });
            if (updated) {
                await localforage.setItem('comments_recommendations', allCR);
            }
        }

        // 2. Update Referral Status so "Start PAR" disappears from dashboard
        if (payload.connectedCteId) {
            const referrals = await localforage.getItem('project_referrals') || [];
            const rIdx = referrals.findIndex(r => r.cteId === payload.connectedCteId);
            if (rIdx >= 0) {
                referrals[rIdx].status = (status === 'Assessed' || status === 'Evaluated') ? 'Assessed' : 'Draft';
                await localforage.setItem('project_referrals', referrals);
            }
        }

        let msg = "Assessment saved.";
        if (status === 'Assessed') msg = "Project assessment saved as Assessed.";
        if (status === 'Evaluated') msg = "Project assessment finalized as Evaluated.";

        if (window.showSimpleAlert) {
            window.showSimpleAlert(msg, 'success');
        }

        if (window.switchPage) {
            const currentUser = (window.__CURRENT_USER__ || {});
            const role = (currentUser.role || '').toLowerCase();
            let target = 'project-assessment-report';
            if (role === 'admin') target = 'admin-project-assessment';
            else if (role.includes('division')) target = 'division-head-project-assessment';
            else if (role.includes('staff')) target = 'staff-project-assessment';
            window.switchPage(target);
        }
        setTimeout(() => initProjectAssessmentLoader(), 80);
    }

    async function _populateValidationSelect(selectEl) {
        if (!selectEl) return;
        const ctes = await _getAllCtes();
        const finalCtes = ctes.filter(c => c.status === 'Final');
        
        selectEl.innerHTML = '<option value="">-- Choose from Test & Validation --</option>';
        finalCtes.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.dataset.projectTitle = c.formData?.projectTitle || 'Untitled';
            opt.dataset.agency = c.formData?.implementingAgency || '';
            opt.textContent = `${opt.dataset.projectTitle} (${opt.dataset.agency})`;
            selectEl.appendChild(opt);
        });
    }

    async function _renderPARTable(tbody, countSpan) {
        if (!tbody) return;
        let [all, referrals] = await Promise.all([
            _getAllPARs(),
            localforage.getItem('project_referrals')
        ]);
        referrals = referrals || [];

        const currentUser = (window.__CURRENT_USER__ || {});
        const role = (currentUser.role || '').toLowerCase();
        const userEmail = (currentUser.email || '').toLowerCase();
        const isPdipbdPage = tbody.id === 'pdipbd-par-tbody' || tbody.id === 'dashboard-par-tbody';

        // Filtering logic
        if (isPdipbdPage) {
            // PDIPBD Staff view includes reports referred to them
            if (role === 'admin') {
                all = all.filter(p => p.isReferredToPdipbd === true);
            } else {
                // For PDIPBD Staff, show those assigned to THEM OR those they prepared themselves
                all = all.filter(p => {
                    const isReferredToMe = (p.isReferredToPdipbd === true && p.pdipbdAssignedStaffId === currentUser.id);
                    const isPreparedByMe = (p.preparedBy || '').toLowerCase() === userEmail;
                    return isReferredToMe || isPreparedByMe;
                });
            }
        } else if (role.includes('staff')) {
            // Regular technical staff see only their own work
            all = all.filter(p => {
                const preparedBy = (p.preparedBy || '').toLowerCase();
                return preparedBy === userEmail;
            });
            
            // If we are on a "Dashboard" (not the main sidebar page), hide finalized reports
            const isDashboard = !!tbody.closest('.dash-card-view, .dashboard-section'); 
            if (isDashboard) {
                const hiddenStatuses = ['Assessed', 'Evaluated', 'Sectoral Committee'];
                all = all.filter(p => !hiddenStatuses.includes(p.status));
            }
        }

        if (all.length === 0) {
            const colspan = tbody.closest('table')?.querySelectorAll('thead th').length || 6;
            tbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-5 text-muted">No assessments found.</td></tr>`;
            if (countSpan) countSpan.textContent = 'Showing 0 entries';
            return;
        }

        function _getDivBadge(div) {
            const map = {
                'PFPD': ['#dbeafe', '#1e40af'],
                'PMED': ['#bfdbfe', '#1d4ed8'],
                'DRD':  ['#e0f2fe', '#0369a1'],
            };
            const [bg, color] = map[div] || ['#f1f5f9', '#334155'];
            return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.7rem;padding:0.3em 0.75em;">${div || '—'}</span>`;
        }

        tbody.innerHTML = all.map(par => {
            let badgeClass = 'bg-secondary';
            if (par.status === 'Review') badgeClass = 'bg-indigo';
            if (par.status === 'Assessed') badgeClass = 'bg-success';
            if (par.status === 'For Revision') badgeClass = 'bg-warning text-dark';
            if (par.status === 'Sectoral Committee') badgeClass = 'bg-primary';
            if (par.status === 'Evaluated') badgeClass = 'bg-dark';
            if (par.status === 'Referred to PDIPBD') badgeClass = 'bg-info text-white';
            if (par.status === 'Final') badgeClass = 'bg-success';

            const ref = referrals.find(r => r.cteId === par.connectedCteId);
            const division = ref ? ref.referredToDivision : (par.evaluatingDivision || '—');

            // Decide content for the 4th column based on which table is being rendered
            const col4Content = (tbody.id === 'staff-par-tbody')
                ? `<span class="small text-muted">${par.preparedBy ? (par.preparedBy.split('@')[0]) : '—'}</span>`
                : _getDivBadge(division);

            return `
                <tr>
                    <td class="fw-bold text-dark">${par.projectTitle}</td>
                    <td>${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(par.proponent) : (par.proponent || '—')}</td>
                    <td><span class="badge ${badgeClass} border border-white-subtle px-3 py-1 rounded-pill">${par.status}</span></td>
                    <td>${col4Content}</td>
                    <td class="small text-muted">${new Date(par.datePrepared).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                <i data-lucide="more-vertical" width="16"></i>
                            </button>
                            <ul class="dropdown-menu border-0 shadow">
                                <li><a class="dropdown-item py-2 edit-par" href="#" data-id="${par.id}"><i data-lucide="edit" width="14" class="me-2 text-primary"></i>View / Edit</a></li>
                                <li><a class="dropdown-item py-2 text-danger delete-par" href="#" data-id="${par.id}"><i data-lucide="trash-2" width="14" class="me-2"></i>Delete</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        if (countSpan) countSpan.textContent = `Showing 1 to ${all.length} of ${all.length} entries`;
        if (window.lucide) window.lucide.createIcons();

        // Wire edit/delete
        tbody.querySelectorAll('.edit-par').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                sessionStorage.setItem('par_edit_id', btn.dataset.id);
                if (window.switchPage) window.switchPage('project-assessment-report-form');
                setTimeout(() => initProjectAssessmentLoader(), 50);
            };
        });

        tbody.querySelectorAll('.delete-par').forEach(btn => {
            btn.onclick = async (e) => {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this assessment?')) {
                    const id = btn.dataset.id;
                    const all = await _getAllPARs();
                    const filtered = all.filter(x => x.id !== id);
                    await _setAllPARs(filtered);
                    _renderPARTable(tbody, countSpan);
                }
            };
        });
    }

    if (btnReview) btnReview.onclick = (e) => { e.preventDefault(); _saveAssessment('Review'); };
    if (btnEvaluated) btnEvaluated.onclick = (e) => { e.preventDefault(); _saveAssessment('Assessed'); };
    if (btnRevision) btnRevision.onclick = (e) => { e.preventDefault(); _saveAssessment('For Revision'); };
    if (btnApproved) btnApproved.onclick = (e) => { e.preventDefault(); _saveAssessment('Evaluated'); };

    // ── Finalization Modal Logic ──
    const fileInput = document.getElementById('finalParFileInput');
    const nameDisp = document.getElementById('finalParFileNameDisplay');
    const nameSpan = document.getElementById('finalParNameSpan');
    const btnRemoveFile = document.getElementById('btnRemoveFinalParFile');
    const btnConfirmFinal = document.getElementById('btnConfirmFinalizePar');

    if (fileInput) {
        fileInput.onchange = (e) => {
            const file = e.target.files[0];
            if (file) {
                if (nameSpan) nameSpan.textContent = file.name;
                if (nameDisp) {
                    nameDisp.classList.remove('d-none');
                    nameDisp.classList.add('d-flex');
                }
            }
        };
    }

    if (btnRemoveFile) {
        btnRemoveFile.onclick = () => {
            if (fileInput) fileInput.value = '';
            if (nameDisp) {
                nameDisp.classList.add('d-none');
                nameDisp.classList.remove('d-flex');
            }
        };
    }

    const sectoralCheckbox = document.getElementById('finalParSectoralCheckbox');
    if (sectoralCheckbox && btnConfirmFinal) {
        sectoralCheckbox.onchange = () => {
            if (sectoralCheckbox.checked) {
                btnConfirmFinal.innerHTML = '<i data-lucide="presentation" width="14" class="me-2"></i> For Sectoral Presentation';
            } else {
                btnConfirmFinal.innerHTML = '<i data-lucide="check-circle" width="18" class="me-2"></i> Finalize PAR';
            }
            if (window.lucide) window.lucide.createIcons({ target: btnConfirmFinal });
        };
    }

    if (btnConfirmFinal) {
        btnConfirmFinal.onclick = async () => {
            const file = fileInput?.files[0];
            if (!file) {
                if (window.showSimpleAlert) window.showSimpleAlert('Please upload the final technical report file.', 'warning');
                else alert('Please upload the final technical report file.');
                return;
            }

            const notes = document.getElementById('finalParNotes')?.value || '';
            const isSectoral = document.getElementById('finalParSectoralCheckbox')?.checked;
            
            // Convert to Base64 to save in localforage
            const reader = new FileReader();
            reader.onload = async (e) => {
                const base64 = e.target.result;
                
                const all = await _getAllPARs();
                const editId = sessionStorage.getItem('par_edit_id');
                const idx = all.findIndex(x => x.id === editId);
                
                if (idx >= 0) {
                    all[idx].status = 'Final';
                    all[idx].finalReportFile = {
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        data: base64,
                        uploadedAt: new Date().toISOString()
                    };
                    all[idx].finalizationNotes = notes;
                    await _setAllPARs(all);

                    const connectedCteId = all[idx].connectedCteId;

                    // ── Check if comments/findings exist ─────────────────────
                    let hasFindings = false;
                    let crIdx = -1;
                    if (connectedCteId) {
                        const allCR = await localforage.getItem('comments_recommendations') || [];
                        crIdx = allCR.findIndex(cr => cr.connectedParId === editId || cr.connectedCteId === connectedCteId);
                        if (crIdx >= 0) {
                            hasFindings = allCR[crIdx].formData?.projects?.[0]?.findingsList?.some(f => f.findings || f.recommendations) || false;
                        }
                    }

                    // ── Decide final CPP status ───────────────────────────────
                    // Rule 1: Sectoral checkbox checked (& no comments) → Sectoral Presentation
                    // Rule 2: Comments exist → For Revision (submit findings to agency)
                    let cppStatus, cppStage, successMsg;

                    if (isSectoral && !hasFindings) {
                        cppStatus = 'Sectoral Presentation';
                        cppStage  = 'Sectoral Committee';
                        successMsg = 'PAR finalized. Project moved to Sectoral Presentation.';
                    } else if (hasFindings) {
                        cppStatus = 'For Revision';
                        cppStage  = 'Project Appraisal';
                        successMsg = 'PAR finalized. Findings & Recommendations submitted to agency for revision.';

                        // Auto-submit the comments to the agency
                        const allCR = await localforage.getItem('comments_recommendations') || [];
                        if (crIdx >= 0 && allCR[crIdx].status !== 'Submitted') {
                            allCR[crIdx].status = 'Submitted';
                            allCR[crIdx].dateSubmitted = new Date().toISOString();
                            await localforage.setItem('comments_recommendations', allCR);
                        }
                    } else {
                        cppStatus = 'Technical Report Finalized';
                        cppStage  = 'Finalization';
                        successMsg = 'PAR has been successfully finalized.';
                    }

                    // ── Apply to CPP submission ───────────────────────────────
                    // connectedCteId is the CTE validation ID (CTE-xxx), NOT the CPP submission ID.
                    // The referral record bridges: referral.cteId → referral.submissionId → cpp_submissions
                    if (connectedCteId) {
                        const subs = await localforage.getItem('cpp_submissions') || [];
                        const referrals = await localforage.getItem('project_referrals') || [];

                        // Step 1: Find the referral that links this CTE to a submission
                        const ref = referrals.find(r => r.cteId === connectedCteId);
                        const submissionId = ref?.submissionId;

                        let sIdx = -1;
                        if (submissionId) {
                            // Preferred: match by submissionId from referral
                            sIdx = subs.findIndex(s => s.id === submissionId);
                        }
                        if (sIdx < 0) {
                            // Fallback 1: direct ID match (in case cteId IS the submission id)
                            sIdx = subs.findIndex(s => s.id === connectedCteId || s.cteId === connectedCteId);
                        }
                        if (sIdx < 0 && ref?.projectTitle) {
                            // Fallback 2: match by project title
                            const t = ref.projectTitle.toLowerCase().trim();
                            sIdx = subs.findIndex(s =>
                                (s.title || s.formData?.['f-title'] || '').toLowerCase().trim() === t
                            );
                        }

                        if (sIdx >= 0) {
                            subs[sIdx].status           = cppStatus;
                            subs[sIdx].projectStatus    = cppStatus;
                            subs[sIdx].submissionStatus = cppStatus;
                            subs[sIdx].stage            = cppStage;
                            await localforage.setItem('cpp_submissions', subs);
                        } else {
                            console.warn('[PAR Finalize] Could not find CPP submission for CTE ID:', connectedCteId, 'Referral:', ref);
                        }
                    }

                    const modalEl = document.getElementById('uploadFinalParModal');
                    bootstrap.Modal.getInstance(modalEl)?.hide();

                    if (window.showSimpleAlert) window.showSimpleAlert(successMsg, 'success');
                    
                    // Redirect back
                    if (window.switchPage) {
                        const currentUser = (window.__CURRENT_USER__ || {});
                        const role = (currentUser.role || '').toLowerCase();
                        let target = 'project-assessment-report';
                        if (role === 'admin') target = 'admin-project-assessment';
                        else if (role.includes('division')) target = 'division-head-project-assessment';
                        else if (role.includes('staff')) target = 'staff-project-assessment';
                        window.switchPage(target);
                    }
                    setTimeout(() => initProjectAssessmentLoader(), 80);
                }
            };
            reader.readAsDataURL(file);
        };
    }

    // Initialize Lucide icons for dynamic buttons
    if (window.lucide) window.lucide.createIcons();

    // ── Location Logic (CPP-Style) ──
    function _initPARLocation() {
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
            if (locFields) locFields.style.display = isLocSpec ? 'block' : 'none';
            if (interProvFields) interProvFields.style.display = isInterProv ? 'block' : 'none';

            if (provinceEl) { provinceEl.disabled = !isLocSpec; }
            _resetSelect(districtEl, '-- Select District --');
            _resetSelect(municipalEl, '-- Select City/Municipality --');

            const tagInput = document.getElementById('provinces-tag-input');
            const tagHidden = document.getElementById('f-provinces');
            const tagBox = document.getElementById('f-provinces-box');
            if (tagInput) tagInput.disabled = !isInterProv;
            if (tagHidden) { 
                tagHidden.disabled = !isInterProv; 
                if (!isInterProv) tagHidden.value = ''; 
            }
            if (tagBox) {
                tagBox.style.opacity = isInterProv ? '1' : '0.6';
                tagBox.style.pointerEvents = isInterProv ? '' : 'none';
                tagBox.style.cursor = isInterProv ? 'text' : 'not-allowed';
            }
        }));

        if (provinceEl) {
            provinceEl.addEventListener('change', () => {
                _resetSelect(districtEl, '-- Select District --');
                _resetSelect(municipalEl, '-- Select City/Municipality --');
                const districts = LOCATION_DATA[provinceEl.value];
                if (districts) {
                    _populateSelect(districtEl, Object.keys(districts), '-- Select District --');
                    districtEl.disabled = false;
                }
            });
        }

        if (districtEl) {
            districtEl.addEventListener('change', () => {
                _resetSelect(municipalEl, '-- Select City/Municipality --');
                const districts = LOCATION_DATA[provinceEl.value];
                const cities = districts ? districts[districtEl.value] : null;
                if (cities) {
                    _populateSelect(municipalEl, cities, '-- Select City/Municipality --');
                    municipalEl.disabled = false;
                }
            });
        }

        _initPARProvincesTag();
    }

    function _initPARProvincesTag() {
        const box = document.getElementById('f-provinces-box');
        const input = document.getElementById('provinces-tag-input');
        const dropdown = document.getElementById('provinces-dropdown');
        const hiddenInput = document.getElementById('f-provinces');
        if (!box || !input || !dropdown || !hiddenInput) return;

        let selected = hiddenInput.value ? hiddenInput.value.split(', ').filter(v => v) : [];

        function render() {
            box.querySelectorAll('.prov-tag').forEach(t => t.remove());
            selected.forEach(p => {
                const t = document.createElement('span');
                t.className = 'prov-tag d-inline-flex align-items-center border rounded px-2 py-1 bg-light small me-1 mb-1';
                t.innerHTML = `<span class="me-1">${p}</span><span class="prov-remove text-muted" style="cursor:pointer" data-prov="${p}">&times;</span>`;
                box.insertBefore(t, input);
            });
            hiddenInput.value = selected.join(', ');
            input.placeholder = selected.length > 0 ? '' : 'Select provinces...';
        }

        render();

        box.onclick = (e) => {
            if (e.target.classList.contains('prov-remove')) {
                const p = e.target.dataset.prov;
                selected = selected.filter(x => x !== p);
                render();
            }
            input.focus();
        };

        const show = () => dropdown.classList.add('show');
        const hide = () => setTimeout(() => dropdown.classList.remove('show'), 200);

        input.onfocus = show;
        input.onblur = hide;

        dropdown.onmousedown = (e) => {
            e.preventDefault();
            const item = e.target.closest('.dropdown-item');
            if (item) {
                const p = item.dataset.prov;
                if (selected.includes(p)) selected = selected.filter(x => x !== p);
                else selected.push(p);
                render();
            }
        };
    }

    if (document.getElementById('coverage-group')) _initPARLocation();

    // ── STANDALONE COMMENTS LOGIC ──

    async function _initPARCommentsSection() {
        const container = document.getElementById('parFindingsContainer');
        const parCommentsDateStatus = document.getElementById('parCommentsDateStatus');
        if (!container) return;
        
        container.innerHTML = '';
        if (parCommentsDateStatus) parCommentsDateStatus.innerHTML = '';

        const parId = sessionStorage.getItem('par_edit_id');
        if (!parId) {
            _addPARFindingRow(); // Default empty entry for new PAR
            return;
        }

        const allCR = await localforage.getItem('comments_recommendations') || [];
        const linkedCR = allCR.find(cr => cr.connectedParId === parId);
        
        if (linkedCR && linkedCR.formData && linkedCR.formData.projects && linkedCR.formData.projects.length > 0) {
            // Check status / dates to populate info text
            if (parCommentsDateStatus) {
                if (linkedCR.status === 'Submitted') {
                    parCommentsDateStatus.innerHTML = `Submitted to Agency on: <span class="fw-bold text-dark">${new Date(linkedCR.dateSubmitted || linkedCR.datePrepared).toLocaleString()}</span>`;
                } else if (linkedCR.lastSaved) {
                    parCommentsDateStatus.innerHTML = `Last saved: <span class="fw-bold text-dark">${new Date(linkedCR.lastSaved).toLocaleString()}</span>`;
                }
            }

            // For simplicity, we assume one project per linked CR in this view
            const project = linkedCR.formData.projects[0];
            if (project.findingsList && project.findingsList.length > 0) {
                project.findingsList.forEach(f => _addPARFindingRow(f.findings, f.recommendations));
            } else {
                _addPARFindingRow();
            }
        } else {
            _addPARFindingRow();
        }
    }

    function _addPARFindingRow(findings = '', recomms = '') {
        const container = document.getElementById('parFindingsContainer');
        if (!container) return;

        const isReadonly = formEl?.dataset.isReadonly === '1';

        const rowHtml = `
            <div class="row g-3 mb-3 par-findings-row border p-3 rounded bg-white shadow-sm position-relative mx-0" style="border-left: 4px solid #154A9A !important;">
                ${!isReadonly ? `
                <button type="button" class="btn btn-link text-danger p-0 position-absolute btn-remove-par-finding" style="top: 0px; right: 8px; z-index: 5; text-decoration: none;">
                    <i data-lucide="x-circle" width="18"></i>
                </button>` : ''}
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.65rem;">Secretariat Findings</label>
                    <textarea class="form-control par-finding-item border-0 bg-light" rows="3" placeholder="Detail the technical observations..." ${isReadonly ? 'readonly disabled' : ''}>${findings}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.65rem;">Secretariat Recommendations</label>
                    <textarea class="form-control par-recomm-item border-0 bg-light" rows="3" placeholder="Specify actions required..." ${isReadonly ? 'readonly disabled' : ''}>${recomms}</textarea>
                </div>
            </div>
        `;
        const div = document.createElement('div');
        div.innerHTML = rowHtml.trim();
        const rowEl = div.firstChild;

        const removeBtn = rowEl.querySelector('.btn-remove-par-finding');
        if (removeBtn) {
            removeBtn.onclick = () => {
                if (container.querySelectorAll('.par-findings-row').length > 1) {
                    rowEl.remove();
                } else {
                    if (window.showSimpleAlert) window.showSimpleAlert('At least one finding entry is required.', 'warning');
                }
            };
        }

        container.appendChild(rowEl);
        if (window.lucide) window.lucide.createIcons({ target: rowEl });
    }

    async function _savePARComments(isSubmit = false) {
        const parId = sessionStorage.getItem('par_edit_id');
        // Check removed per request: allowing saving comments even without parId (e.g. new draft)

        const findingRows = document.querySelectorAll('.par-findings-row');
        const findingsList = Array.from(findingRows).map(row => ({
            findings: row.querySelector('.par-finding-item').value.trim(),
            recommendations: row.querySelector('.par-recomm-item').value.trim()
        }));

        const allEmpty = findingsList.every(f => !f.findings && !f.recommendations);

        // Only block submission (not draft save) when all fields are empty
        if (isSubmit && allEmpty) {
            if (window.showSimpleAlert) window.showSimpleAlert('Please enter at least one finding or recommendation before submitting to the agency.', 'warning');
            return;
        }

        // Fetch current PAR data for context or read from form
        const allPARs = await localforage.getItem('project_assessments') || [];
        const par = allPARs.find(p => p.id === parId);
        
        const currentTitle = par?.projectTitle || formEl.querySelector('[name="projectTitle"]').value || 'Untitled Project';
        const currentProponent = par?.proponent || formEl.querySelector('[name="implementingAgency"]').value || 'N/A';
        const currentCteId = par?.connectedCteId || formEl.dataset.connectedCteId || null;

        const allCR = await localforage.getItem('comments_recommendations') || [];
        let cr = allCR.find(x => x.connectedParId === parId && parId !== null);

        const currentUser = (window.__CURRENT_USER__ || {});
        const payload = {
            id: cr ? cr.id : `CR-${Date.now()}`,
            datePrepared: cr ? cr.datePrepared : new Date().toISOString(),
            lastSaved: new Date().toISOString(),
            dateSubmitted: isSubmit ? new Date().toISOString() : (cr ? cr.dateSubmitted : null),
            status: isSubmit ? 'Submitted' : (cr?.status === 'Submitted' && !isSubmit ? 'Draft' : 'Draft'),
            connectedParId: parId,
            connectedCteId: currentCteId,
            preparedBy: currentUser.email || 'System User',
            preparedByRole: currentUser.role || 'staff',
            formData: {
                batchTitle: `Feedback for ${currentTitle}`,
                implementingAgency: currentProponent,
                projects: [{
                    projectTitle: currentTitle,
                    totalCost: par?.totalCost || '',
                    findingsList: findingsList
                }]
            }
        };

        const idx = allCR.findIndex(x => x.id === payload.id);
        if (idx >= 0) allCR[idx] = payload;
        else allCR.unshift(payload);

        await localforage.setItem('comments_recommendations', allCR);

        // ── Automated Workflow Status Updates ──
        if (isSubmit && currentCteId) {
            try {
                const referrals = await localforage.getItem('project_referrals') || [];
                const ref = referrals.find(r => r.cteId === currentCteId);
                
                if (ref) {
                    // 1. Update the referral status
                    ref.status = 'For Revision';
                    await localforage.setItem('project_referrals', referrals);

                    // 2. Update the main CPP submission status if available
                    if (ref.submissionId) {
                        const submissions = await localforage.getItem('cpp_submissions') || [];
                        const subIdx = submissions.findIndex(s => s.id === ref.submissionId);
                        if (subIdx >= 0) {
                            submissions[subIdx].status = 'For Revision';
                            // Also ensure submissionStatus is synchronized
                            submissions[subIdx].submissionStatus = 'For Revision';
                            await localforage.setItem('cpp_submissions', submissions);
                            console.log(`[PAR] CPP Submission ${ref.submissionId} updated to 'For Revision'`);
                        }
                    }
                }
            } catch (err) {
                console.error('[PAR] Error in automated status update:', err);
            }
        }

        if (window.showSimpleAlert) window.showSimpleAlert(`Findings & Recommendations successfully ${isSubmit ? 'submitted to agency' : 'saved as draft'}.`, 'success');

        // Update UI logic
        const dateHtml = isSubmit 
            ? `Submitted to Agency on: <span class="fw-bold text-dark">${new Date().toLocaleString()}</span>`
            : `Last saved: <span class="fw-bold text-dark">${new Date().toLocaleString()}</span>`;
        const parCommentsDateStatus = document.getElementById('parCommentsDateStatus');
        if (parCommentsDateStatus) parCommentsDateStatus.innerHTML = dateHtml;

        // Close modal
        const modalEl = document.getElementById('parCommentsModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
    }
}
