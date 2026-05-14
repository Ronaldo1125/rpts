export function initCommentsRecommendationsLoader() {
    const activeSection = document.querySelector('.page-content.active');
    if (!activeSection) return;

    const listTbody = activeSection.querySelector('tbody[id$="-comment-tbody"]') || activeSection.querySelector('tbody#comment-tbody');
    const countSpan = activeSection.querySelector('span[id$="-comment-count"]') || activeSection.querySelector('span#comment-count');

    const formEl = document.getElementById('commentsRecommendationsForm');
    const parSelectEl = document.getElementById('crParSelect');
    const parSelectContainer = document.getElementById('crParSelectContainer');
    
    // Initiation bars (Admin/Staff only)
    const listParSelectEl = activeSection.querySelector('select[id$="ListParSelect"]') || activeSection.querySelector('select#listParSelect');
    const newCommentsBtn = activeSection.querySelector('button[id$="NewCommentsBtn"]') || activeSection.querySelector('button#newCommentsBtn');

    // Make local functions available globally if not already
    window.initCommentsRecommendationsLoader = initCommentsRecommendationsLoader;

    async function _getAllComments() {
        const data = await localforage.getItem('comments_recommendations');
        return Array.isArray(data) ? data : [];
    }

    async function _setAllComments(data) {
        await localforage.setItem('comments_recommendations', data);
    }

    async function _getAllPARs() {
        const data = await localforage.getItem('project_assessments');
        return Array.isArray(data) ? data : [];
    }

    function _extractProjectDataFromPar(par) {
        if (!par) return [];
        const fd = par.formData || {};
        const keys = Object.keys(fd).filter(k => k.startsWith('annex_title_'));
        
        if (keys.length > 0) {
            return keys.map(k => {
                const num = k.replace('annex_title_', '');
                return {
                    title: (fd[k] || '').toString().trim(),
                    totalCost: fd[`annex_total_${num}`] || ''
                };
            }).filter(p => p.title);
        }

        const fallback = (par.projectTitle || '').toString().trim();
        return fallback ? [{ title: fallback, totalCost: par.totalCost || '' }] : [];
    }

    function _setAccordionFromData(accordionContainer, projects) {
        if (!accordionContainer) return;
        accordionContainer.innerHTML = '';
        projectIndexCounter = 0;
        projects.forEach(p => {
            const item = _createProjectAccordionItem({
                projectTitle: p.title,
                totalCost: p.totalCost,
                findingsList: [{ findings: '', recommendations: '' }]
            });
            accordionContainer.appendChild(item);
        });
        if (window.lucide) window.lucide.createIcons();
    }

    async function _populateParDropdown(selectedParId = '', targetEl = null) {
        let el = targetEl || parSelectEl;
        
        // If no target provided, try to find it in active section
        if (!el) {
            const activeSection = document.querySelector('.page-content.active');
            if (activeSection) {
                el = activeSection.querySelector('select[id$="ListParSelect"]') || activeSection.querySelector('select#listParSelect');
            }
        }

        if (!el) return;

        let pars = await _getAllPARs();
        const currentUser = (window.__CURRENT_USER__ || {});
        const role = (currentUser.role || '').toLowerCase();
        const userEmail = (currentUser.email || '').toLowerCase();

        // If staff, only show their own
        if (role.includes('staff')) {
            pars = pars.filter(p => {
                const preparedBy = (p.preparedBy || '').toLowerCase();
                return preparedBy === userEmail;
            });
        }

        const placeholder = el.id.includes('List') ? '-- Choose PAR Assessment to start Evaluation --' : '-- Select PAR Assessment --';
        el.innerHTML = `<option value="">${placeholder}</option>`;
        
        pars.forEach(par => {
            if (!par || !par.id) return;
            const opt = document.createElement('option');
            opt.value = par.id;
            opt.textContent = `${par.projectTitle || par.id} — ${par.proponent || 'N/A'} (${par.status || 'Saved'})`;
            el.appendChild(opt);
        });
        el.value = selectedParId || '';
    }

    async function _renderListTable() {
        if (!listTbody) return;
        let all = await _getAllComments();
        const currentUser = (window.__CURRENT_USER__ || {});
        const role = (currentUser.role || '').toLowerCase();
        const userEmail = (currentUser.email || '').toLowerCase();

        // If staff, only show their own
        if (role.includes('staff')) {
            all = all.filter(c => {
                const preparedBy = (c.preparedBy || '').toLowerCase();
                return preparedBy === userEmail;
            });
        }

        if (all.length === 0) {
            const colspan = listTbody.closest('table')?.querySelectorAll('thead th').length || 7;
            listTbody.innerHTML = `<tr><td colspan="${colspan}" class="text-center py-5 text-muted">No records found. Click "New Comment" to create one.</td></tr>`;
            if (countSpan) countSpan.textContent = 'Showing 0 entries';
            return;
        }

        listTbody.innerHTML = all.map(c => {
            const dateStr = new Date(c.datePrepared).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            
            const projectsCount = (c.formData.projects || []).length;
            const findingsCount = (c.formData.projects || []).reduce((sum, p) => sum + (p.findingsList || []).length, 0);
            const titleStr = c.formData.batchTitle || 'Untitled Batch';
            const projectStr = projectsCount === 1 ? '1 Project' : `${projectsCount} Projects`;
            const totalFindings = findingsCount === 1 ? '1 Finding' : `${findingsCount} Findings`;

            const hasResponse = c.agencyResponse && c.agencyResponse.trim() !== '';
            const statusBadge = hasResponse 
                ? '<span class="status-badge status-responded">Responded</span>'
                : '<span class="status-badge status-pending">No Response</span>';

            return `
                <tr>
                    <td class="ps-3">
                        <div class="batch-name">${titleStr}</div>
                    </td>
                    <td>
                        <div class="cell-text text-dark" style="font-size:0.85rem;">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(c.formData.implementingAgency) : (c.formData.implementingAgency || '—')}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border fw-normal" style="font-size:0.75rem;">${projectStr}</span>
                    </td>
                    <td>
                        <span class="small fw-semibold text-secondary">${totalFindings}</span>
                    </td>
                    <td>
                        <div class="cell-text text-dark small">${c.preparedBy ? (c.preparedBy.split('@')[0]) : '—'}</div>
                    </td>
                    <td>
                        ${statusBadge}
                    </td>
                    <td>
                        <div class="cell-text text-dark small fw-medium">${dateStr}</div>
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light rounded-circle shadow-sm p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i data-lucide="more-vertical" width="15"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                <li>
                                    <a class="dropdown-item py-2 edit-cr" href="#" data-id="${c.id}">
                                        <i data-lucide="edit" width="14" class="me-2 text-primary"></i>View / Edit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger delete-cr" href="#" data-id="${c.id}">
                                        <i data-lucide="trash-2" width="14" class="me-2"></i>Delete Record
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        if (countSpan) countSpan.textContent = `Showing 1 to ${all.length} of ${all.length} entries`;
        if (window.lucide) window.lucide.createIcons();

        // Wire edit/delete
        listTbody.querySelectorAll('.edit-cr').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                sessionStorage.setItem('cr_edit_id', btn.dataset.id);
                if (window.switchPage) window.switchPage('comments-recommendations-form');
                setTimeout(() => initCommentsRecommendationsLoader(), 50);
            };
        });

        listTbody.querySelectorAll('.delete-cr').forEach(btn => {
            btn.onclick = async (e) => {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this record?')) {
                    const id = btn.dataset.id;
                    const allData = await _getAllComments();
                    const filtered = allData.filter(x => x.id !== id);
                    await _setAllComments(filtered);
                    _renderListTable();
                }
            };
        });
    }

    let projectIndexCounter = 0;

    function _createProjectAccordionItem(projectData = null) {
        projectIndexCounter++;
        const index = projectIndexCounter;
        const accordionId = `collapseProject${index}`;
        const headingId = `headingProject${index}`;

        const data = projectData || {
            projectTitle: '', findingsList: []
        };

        const html = `
            <div class="accordion-item shadow-sm border" style="border-radius: 12px; overflow: hidden; background: #fff;">
                <h2 class="accordion-header" id="${headingId}">
                    <button class="accordion-button ${projectData ? 'collapsed' : ''} bg-light py-3" type="button" data-bs-toggle="collapse" data-bs-target="#${accordionId}" style="box-shadow: none;">
                        <span class="fw-bold text-dark project-title-display">
                            ${data.projectTitle || `New Project ${index}`}
                        </span>
                        <div class="ms-auto me-3 delete-project-btn-container"></div>
                    </button>
                </h2>
                <div id="${accordionId}" class="accordion-collapse collapse ${projectData ? '' : 'show'}" aria-labelledby="${headingId}" data-bs-parent="#projectsAccordion">
                    <div class="accordion-body p-4 project-data-container">
                        <div class="row g-4 mb-4 mt-1">
                            <div class="col-md-9">
                                <label class="form-label small fw-semibold text-secondary">Project Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control project-title-input" value="${data.projectTitle}" placeholder="Enter full project title..." disabled>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold text-secondary">Cost (PhP Million)</label>
                                <input type="number" step="0.01" class="form-control project-cost-input" value="${data.totalCost || ''}" placeholder="0.00" disabled>
                            </div>
                        </div>

                        <div class="findings-list-container mb-4">
                            <!-- Rows will be added here -->
                        </div>

                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 btn-add-finding-row">
                                <i data-lucide="plus-circle" width="16" class="me-1"></i> Add Finding & Recommendation
                            </button>
                        </div>                        <div class="mt-4 pt-3 border-top text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger fw-semibold btn-remove-project">
                                <i data-lucide="trash-2" width="14" class="me-1"></i> Remove Project
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html.trim();
        const element = tempDiv.firstChild;

        // Auto-update header title
        const titleInput = element.querySelector('.project-title-input');
        const headerDisplay = element.querySelector('.project-title-display');
        if (titleInput && headerDisplay) {
            titleInput.addEventListener('input', (e) => {
                headerDisplay.textContent = e.target.value.trim() || `New Project ${index}`;
            });
        }

        // Setup remove project button
        const btnRemoveProject = element.querySelector('.btn-remove-project');
        if (btnRemoveProject) {
            btnRemoveProject.addEventListener('click', (e) => {
                e.stopPropagation();
                if (confirm("Are you sure you want to remove this project from the batch?")) {
                    element.remove();
                }
            });
        }

        // Helper to add a row
        const findingsContainer = element.querySelector('.findings-list-container');
        const addFindingBtn = element.querySelector('.btn-add-finding-row');

        function _addFindingRow(findings = '', recomms = '') {
            const rowHtml = `
                <div class="row g-3 mb-3 findings-row border p-3 rounded bg-light-subtle position-relative mx-0">
                    <button type="button" class="btn btn-link text-danger p-0 position-absolute btn-remove-finding-row" style="top: 0px; right: 8px; z-index: 5; text-decoration: none;">
                        <i data-lucide="x-circle" width="18"></i>
                    </button>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color: #64748b; font-size: 0.75rem;">Findings / Observations</label>
                        <textarea class="form-control project-findings-item" rows="3" placeholder="Detail the Secretariat's findings...">${findings}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold" style="color: #64748b; font-size: 0.75rem;">Secretariat's Recommendations</label>
                        <textarea class="form-control project-recomms-item" rows="3" placeholder="Specify actions required...">${recomms}</textarea>
                    </div>
                </div>
            `;
            const rowDiv = document.createElement('div');
            rowDiv.innerHTML = rowHtml.trim();
            const rowEl = rowDiv.firstChild;

            rowEl.querySelector('.btn-remove-finding-row').onclick = () => {
                if(findingsContainer.querySelectorAll('.findings-row').length > 1) {
                    rowEl.remove();
                } else {
                    if(window.showSimpleAlert) window.showSimpleAlert('At least one finding/recommendation pair is required.', 'warning');
                }
            };

            findingsContainer.appendChild(rowEl);
            if (window.lucide) window.lucide.createIcons({ target: rowEl });
        }

        // Initialize rows
        if (data.findingsList && data.findingsList.length > 0) {
            data.findingsList.forEach(f => _addFindingRow(f.findings, f.recommendations));
        } else if (data.findings || data.recommendations) {
            // Migration
            _addFindingRow(data.findings, data.recommendations);
        } else {
            // New record
            _addFindingRow();
        }

        if (addFindingBtn) {
            addFindingBtn.onclick = (e) => {
                e.preventDefault();
                _addFindingRow();
            };
        }

        return element;
    }


    async function _populateAgencyDropdown(selectedValue = '') {
        const select = formEl.querySelector('[name="implementingAgency"]');
        if (!select) return;
        
        await window.populateAgencyDropdown(select, selectedValue);
    }

    async function _initForm() {
        const accordionContainer = document.getElementById('projectsAccordion');
        const editId = sessionStorage.getItem('cr_edit_id');
        projectIndexCounter = 0; // Reset counter

        const backBtns = document.querySelectorAll('.btn-back-list, [onclick*="switchPage(\'comments-recommendations\')"]');
        backBtns.forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                const currentUser = (window.__CURRENT_USER__ || {});
                const role = (currentUser.role || '').toLowerCase();
                let target = 'comments-recommendations';
                if (role === 'admin') target = 'admin-comments-recommendations';
                else if (role.includes('staff')) target = 'staff-comments-recommendations';
                else if (role.includes('division')) target = 'division-head-comments-recommendations';

                if (window.switchPage) window.switchPage(target);
                setTimeout(() => initCommentsRecommendationsLoader(), 50);
            };
        });

        if (accordionContainer) accordionContainer.innerHTML = '';
        if (parSelectContainer) parSelectContainer.style.display = 'block'; // Default to show

        if (editId) {
            if (parSelectContainer) parSelectContainer.style.display = 'none';
            const all = await _getAllComments();
            const cr = all.find(x => x.id === editId);
            if (cr && cr.formData) {
                // Restore linked PAR id if present
                if (formEl) {
                    formEl.dataset.connectedParId = (cr.connectedParId || '').toString();
                    formEl.dataset.connectedCteId = (cr.connectedCteId || '').toString();
                }
                await _populateParDropdown((cr.connectedParId || '').toString());

                const batchTitleInput = formEl.querySelector(`[name="batchTitle"]`);
                if (batchTitleInput) batchTitleInput.value = cr.formData.batchTitle || '';

                await _populateAgencyDropdown(cr.formData.implementingAgency || '');

                const projects = cr.formData.projects || [];
                projects.forEach(p => {
                    const item = _createProjectAccordionItem(p);
                    accordionContainer.appendChild(item);
                });
            }
        } else {
            formEl.reset();
            await _populateAgencyDropdown();
            const prefillParId = sessionStorage.getItem('cr_prefill_par_id') || '';
            await _populateParDropdown(prefillParId);
            if (prefillParId && parSelectContainer) parSelectContainer.style.display = 'none';

            if (prefillParId && accordionContainer) {
                const pars = await _getAllPARs();
                const par = pars.find(p => p && p.id === prefillParId);
                if (par) {
                    if (formEl) {
                        formEl.dataset.connectedParId = par.id;
                        formEl.dataset.connectedCteId = par.connectedCteId || '';
                    }
                    const batchTitleInput = formEl.querySelector(`[name="batchTitle"]`);
                    if (batchTitleInput && !batchTitleInput.value) {
                        batchTitleInput.value = `${par.projectTitle || 'Untitled Project'}`;
                    }
                    await _populateAgencyDropdown((par.proponent || '').trim());
                    const projects = _extractProjectDataFromPar(par);
                    _setAccordionFromData(accordionContainer, projects);
                }
            } else if (accordionContainer) {
                accordionContainer.innerHTML = `
                    <div class="text-muted small px-2 py-3">
                        Select a PAR above to load its projects.
                    </div>
                `;
            }

            sessionStorage.removeItem('cr_prefill_par_id');
            sessionStorage.removeItem('cr_prefill_cte_id');
            sessionStorage.removeItem('cr_prefill_title');
            sessionStorage.removeItem('cr_prefill_agency');
        }

        // If we are editing an old record without a linked PAR, still show a hint.
        if (!editId && parSelectEl && !parSelectEl.value && accordionContainer && !accordionContainer.children.length) {
            accordionContainer.innerHTML = `
                <div class="text-muted small px-2 py-3">
                    Select a PAR above to load its projects.
                </div>
            `;
        }

        if (parSelectEl) {
            parSelectEl.onchange = async () => {
                const parId = (parSelectEl.value || '').trim();
                if (!parId) {
                    if (formEl) {
                        formEl.dataset.connectedParId = '';
                        formEl.dataset.connectedCteId = '';
                        
                        // Also clear agency and title
                        await _populateAgencyDropdown('');
                        const batchTitleInput = formEl.querySelector(`[name="batchTitle"]`);
                        if (batchTitleInput) batchTitleInput.value = '';
                    }
                    if (accordionContainer) {
                        accordionContainer.innerHTML = `
                            <div class="text-muted small px-2 py-3">
                                Select a PAR above to load its projects.
                            </div>
                        `;
                    }
                    return;
                }

                const pars = await _getAllPARs();
                const par = pars.find(p => p && p.id === parId);
                if (!par) return;

                if (formEl) {
                    formEl.dataset.connectedParId = par.id;
                    formEl.dataset.connectedCteId = par.connectedCteId || '';
                }

                const batchTitleInput = formEl.querySelector(`[name="batchTitle"]`);
                if (batchTitleInput && !batchTitleInput.value.trim()) {
                    batchTitleInput.value = `${par.projectTitle || 'Untitled Project'}`;
                }
                await _populateAgencyDropdown((par.proponent || '').trim());
                const projects = _extractProjectDataFromPar(par);
                _setAccordionFromData(accordionContainer, projects);
            };
        }

        const btnSave = document.getElementById('btnSaveComments');
        if (btnSave) {
            btnSave.onclick = async () => {
                const selectedParId = (parSelectEl?.value || '').trim();
                if (!selectedParId) {
                    if (window.showSimpleAlert) window.showSimpleAlert('Please select a PAR to link this record.', 'danger');
                    else alert('Please select a PAR to link this record.');
                    return;
                }

                const batchTitleInput = formEl.querySelector('[name="batchTitle"]');
                const batchTitle = batchTitleInput ? batchTitleInput.value.trim() : '';

                if (!batchTitle) {
                    if(window.showSimpleAlert) window.showSimpleAlert('Document / Batch Title is required.', 'danger');
                    else alert('Document / Batch Title is required.');
                    return;
                }

                // Gather projects
                const projectItems = document.querySelectorAll('.project-data-container');
                const projects = Array.from(projectItems).map(container => {
                    const rowContainers = container.querySelectorAll('.findings-row');
                    const findingsList = Array.from(rowContainers).map(row => ({
                        findings: row.querySelector('.project-findings-item').value.trim(),
                        recommendations: row.querySelector('.project-recomms-item').value.trim()
                    }));

                    return {
                        projectTitle: container.querySelector('.project-title-input').value.trim(),
                        totalCost: container.querySelector('.project-cost-input').value.trim(),
                        findingsList: findingsList
                    };
                });

                if (projects.length === 0) {
                    if(window.showSimpleAlert) window.showSimpleAlert('Please add at least one project evaluation.', 'danger');
                    else alert('Please add at least one project evaluation.');
                    return;
                }

                if (projects.some(p => !p.projectTitle)) {
                    if(window.showSimpleAlert) window.showSimpleAlert('All projects must have a title.', 'danger');
                    return;
                }

                // Ensure we persist connected ids based on selected PAR
                const pars = await _getAllPARs();
                const par = pars.find(p => p && p.id === selectedParId);
                if (formEl) {
                    formEl.dataset.connectedParId = selectedParId;
                    formEl.dataset.connectedCteId = par?.connectedCteId || (formEl.dataset.connectedCteId || '');
                }

                const currentUser = (window.__CURRENT_USER__ || {});
                const payload = {
                    id: editId || `CR-${Date.now()}`,
                    datePrepared: editId ? (await _getAllComments()).find(x => x.id === editId)?.datePrepared || new Date().toISOString() : new Date().toISOString(),
                    connectedParId: selectedParId,
                    connectedCteId: par?.connectedCteId || null,
                    preparedBy: currentUser.email || 'System User',
                    preparedByRole: currentUser.role || 'staff',
                    formData: {
                        batchTitle: batchTitle,
                        implementingAgency: (formEl.querySelector('[name="implementingAgency"]')?.value || '').trim(),
                        projects: projects
                    }
                };

                const all = await _getAllComments();
                const idx = all.findIndex(x => x.id === payload.id);
                if (idx >= 0) all[idx] = payload;
                else all.unshift(payload);

                await _setAllComments(all);

                // --- Auto-update CIPG/CPP status to 'For Revision' ---
                try {
                    const cppSubmissions = await localforage.getItem('cpp_submissions') || [];
                    let updatedCount = 0;
                    
                    projects.forEach(p => {
                        const titleToFind = (p.projectTitle || '').trim().toLowerCase();
                        if (!titleToFind) return;

                        cppSubmissions.forEach(sub => {
                            const subTitle = (sub.title || sub.formData?.['f-title'] || '').trim().toLowerCase();
                            if (subTitle === titleToFind) {
                                sub.status = 'For Revision';
                                sub.submissionStatus = 'For Revision';
                                updatedCount++;
                            }
                        });
                    });

                    if (updatedCount > 0) {
                        await localforage.setItem('cpp_submissions', cppSubmissions);
                        console.log(`[C&R] Automatically updated ${updatedCount} CIPG submission(s) to 'For Revision'`);
                    }
                } catch (statusErr) {
                    console.error('[C&R] Failed to auto-update CIPG status:', statusErr);
                }

                if(window.showSimpleAlert) window.showSimpleAlert('Batch evaluation saved successfully. Included projects marked for revision.', 'success');
                sessionStorage.removeItem('cr_edit_id');
                
                if (window.switchPage) {
                    const role = (currentUser.role || '').toLowerCase();
                    let target = 'comments-recommendations'; // default/agency
                    if (role === 'admin') target = 'admin-comments-recommendations';
                    else if (role.includes('staff')) target = 'staff-comments-recommendations';
                    else if (role.includes('division')) target = 'division-head-comments-recommendations';
                    window.switchPage(target);
                }
                setTimeout(() => initCommentsRecommendationsLoader(), 50);
            };
        }
    }

    if (listTbody) _renderListTable();
    if (listParSelectEl) {
        _populateParDropdown('', listParSelectEl);
        if (newCommentsBtn) {
            newCommentsBtn.onclick = () => {
                const parId = (listParSelectEl.value || '').trim();
                if (!parId) {
                    if (window.showSimpleAlert) window.showSimpleAlert('Please select a PAR Assessment from the dropdown first.', 'warning');
                    return;
                }
                sessionStorage.removeItem('cr_edit_id');
                sessionStorage.setItem('cr_prefill_par_id', parId);
                if (window.switchPage) window.switchPage('comments-recommendations-form');
                setTimeout(() => initCommentsRecommendationsLoader(), 50);
            };
        }
    }
    if (formEl) _initForm();
    if (window.lucide) window.lucide.createIcons();
}
