export function initTestAndEvaluationLoader() {
    window.initTestAndEvaluationLoader = initTestAndEvaluationLoader;

    const listTbody = document.getElementById('cte-tbody');
    const countSpan = document.getElementById('cte-count');
    const newBtn = document.getElementById('newCteBtn');

    const formEl = document.getElementById('completenessForm');
    const saveBtn = document.getElementById('cteSaveBtn');
    const finalizeBtn = document.getElementById('cteFinalizeBtn');
    const backBtn = document.getElementById('cteBackToList');

    if (!listTbody && !formEl) return;

    // Populate CIPG submissions dropdown (Project Titles)
    async function _populateProjectTitles() {
        if (!formEl) return;
        const dropdown = document.getElementById('project-title-dropdown');
        if (!dropdown) return;
        
        let submissions = await localforage.getItem('cpp_submissions');
        if (!submissions) submissions = [];

        // Exclude drafts from Test & Validation selection
        submissions = submissions.filter(sub => (sub.status || sub.submissionStatus) !== 'Draft');
        
        dropdown.innerHTML = '';
        if (submissions.length === 0) {
            dropdown.innerHTML = '<li><span class="dropdown-item text-muted small">No CIPG submissions found.</span></li>';
        }
        submissions.forEach(sub => {
            const title = sub.title || sub.formData?.['f-title'] || 'Untitled';
            const li = document.createElement('li');
            li.innerHTML = `<a class="dropdown-item d-flex align-items-center gap-2" href="#" data-val="${title}">
                <input class="form-check-input mt-0 project-title-cb" type="checkbox" value="${title}"> ${title}
            </a>`;
            dropdown.appendChild(li);
        });

        // 1. CLEAR OLD LISTENERS on the dropdown itself
        const freshDropdown = dropdown.cloneNode(true); 
        dropdown.parentNode.replaceChild(freshDropdown, dropdown);
        const activeDropdown = freshDropdown; // Use the replaced one for the listener below

        // Toggle logic for the box
        const box = document.getElementById('f-alignment-box');
        const input = document.getElementById('project-title-input');
        
        if (box) {
            // Hide if already open from a previous session/mount
            if (window.bootstrap) {
                const existingInstance = window.bootstrap.Dropdown.getInstance(box);
                if (existingInstance) existingInstance.hide();
            }

            // Remove old listeners by replacing the element
            const freshBox = box.cloneNode(true);
            box.parentNode.replaceChild(freshBox, box);
            freshBox.addEventListener('click', (e) => {
                const isViewOnly = sessionStorage.getItem('cte_view_only') === '1';
                if (isViewOnly) return; 

                if (window.bootstrap) {
                    const bsDrop = window.bootstrap.Dropdown.getOrCreateInstance(freshBox, { reference: 'parent' });
                    bsDrop.toggle();
                }
            });
        }
        
        activeDropdown.addEventListener('click', (e) => {
            const isViewOnly = sessionStorage.getItem('cte_view_only') === '1';
            if (isViewOnly) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }

            const a = e.target.closest('a');
            if (!a) return;
            e.preventDefault();
            e.stopPropagation();

            const cb = a.querySelector('input[type="checkbox"]');
            if (cb && e.target !== cb) {
                cb.checked = !cb.checked;
            } else if (cb && e.target === cb) {
                // If user clicked the checkbox directly, don't toggle it again via logic
                // but we let the default happen or we enforce it.
                // Actually, just let it happen.
            }
            
            // Update the visible text input and hidden field for validation
            const freshInput = document.getElementById('project-title-input');
            const hiddenInput = document.getElementById('f-alignment');
            const checked = activeDropdown.querySelectorAll('input:checked');
            const selectedText = checked.length > 0
                ? Array.from(checked).map(c => c.value).join(', ')
                : '';
                
            if (freshInput) freshInput.value = selectedText;
            if (hiddenInput) {
                hiddenInput.value = selectedText;
                // Clear error if something is selected
                const err = document.getElementById('f-alignment-err');
                if (selectedText && err) err.style.display = 'none';
            }
        });
    }

    function _statusPill(text, bg, color) {
        return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.7rem;padding:0.35em 0.8em;">${text}</span>`;
    }

    function _renderStatus(st) {
        if (st === 'Draft') return _statusPill('Draft', '#f1f5f9', '#0f172a');
        if (st === 'Final') return _statusPill('Final', '#dcfce7', '#15803d');
        return _statusPill(st || 'Draft', '#e0f2fe', '#075985');
    }

    function _timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return 'Just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        const days = Math.floor(hours / 24);
        if (days < 30) return `${days} day${days > 1 ? 's' : ''} ago`;

        return date.toLocaleDateString();
    }

    async function _getAll() {
        let items = await localforage.getItem('cte_validations');
        if (!Array.isArray(items)) items = [];
        return items;
    }

    async function _setAll(items) {
        await localforage.setItem('cte_validations', items);
    }

    async function renderTable() {
        if (!listTbody) return;
        const items = await _getAll();

        if (items.length === 0) {
            listTbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i data-lucide="clipboard-list" width="40" class="mb-3 opacity-20"></i>
                            <p class="mb-0">No validations found.</p>
                            <p class="small">Click “New Validation” to create one.</p>
                        </div>
                    </td>
                </tr>
            `;
            if (countSpan) countSpan.textContent = 'Showing 0 to 0 of 0 entries';
        } else {
            listTbody.innerHTML = items.map(v => {
                const title = v?.formData?.projectTitle || 'Untitled';
                const agency = v?.formData?.implementingAgency || '—';
                const statusHtml = _renderStatus(v.status);
                return `
                    <tr>
                        <td class="py-3" style="max-width:320px;">
                            <div class="fw-semibold small">${title}</div>
                        </td>
                        <td class="small text-muted py-3">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(v?.formData?.implementingAgency) : (v?.formData?.implementingAgency || '—')}</td>
                        <td class="py-3"><span class="small text-muted">${_timeAgo(v.date)}</span></td>
                        <td class="py-3">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                    <i data-lucide="more-vertical" width="20"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li><a class="dropdown-item small cte-view" href="#" data-id="${v.id}">
                                        <i data-lucide="eye" class="me-2" width="14"></i>View</a>
                                    </li>
                                    <li><a class="dropdown-item small cte-edit" href="#" data-id="${v.id}">
                                        <i data-lucide="edit-2" class="me-2" width="14"></i>Edit</a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item small text-danger cte-delete" href="#" data-id="${v.id}">
                                        <i data-lucide="trash-2" class="me-2" width="14"></i>Delete</a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            if (countSpan) countSpan.textContent = `Showing 1 to ${items.length} of ${items.length} entries`;
        }

        if (window.lucide) window.lucide.createIcons();

        if (window.bootstrap?.Dropdown) {
            document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                if (!window.bootstrap.Dropdown.getInstance(el)) {
                    new window.bootstrap.Dropdown(el, { popperConfig: { strategy: 'fixed' } });
                }
            });
        }

        _attachListHandlers();
    }

    function _attachListHandlers() {
        document.querySelectorAll('.cte-view').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                sessionStorage.setItem('cte_edit_id', btn.dataset.id);
                sessionStorage.setItem('cte_view_only', '1');
                if (window.switchPage) window.switchPage('test-and-evaluation-form');
                setTimeout(() => initTestAndEvaluationLoader(), 50);
            });
        });

        document.querySelectorAll('.cte-edit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                sessionStorage.removeItem('cte_view_only');
                sessionStorage.removeItem('cte_no_edit_link');
                sessionStorage.setItem('cte_edit_id', btn.dataset.id);
                if (window.switchPage) window.switchPage('test-and-evaluation-form');
                setTimeout(() => initTestAndEvaluationLoader(), 50);
            });
        });

        document.querySelectorAll('.cte-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                const doDelete = async () => {
                    const all = await _getAll();
                    await _setAll(all.filter(x => x.id !== id));
                    renderTable();
                };

                if (window.showConfirmModal) {
                    window.showConfirmModal({
                        title: 'Delete Validation',
                        message: 'Are you sure you want to delete this validation? This action cannot be undone.',
                        confirmText: 'Delete',
                        confirmClass: 'btn-danger',
                        onConfirm: doDelete
                    });
                } else if (confirm('Delete this validation?')) {
                    doDelete();
                }
            });
        });
    }

    function _readFormData() {
        const fd = new FormData(formEl);
        const obj = {};
        fd.forEach((value, key) => { obj[key] = String(value); });
        return obj;
    }

    function _fillFormData(data = {}) {
        const titleInput = formEl.querySelector('[name="projectTitle"]');
        const agencyInput = formEl.querySelector('[name="implementingAgency"]');
        const authInput = formEl.querySelector('[name="authorizedOfficial"]');
        if (titleInput) titleInput.value = data.projectTitle || '';
        if (agencyInput) agencyInput.value = data.implementingAgency || '';
        if (authInput) authInput.value = data.authorizedOfficial || '';
        const hiddenInput = document.getElementById('f-alignment');
        if (hiddenInput) hiddenInput.value = data.projectTitle || '';

        // Restore contact persons
        const contactFields = [
            'dir_name', 'dir_designation', 'dir_office', 'dir_tel', 'dir_email',
            'focal_name', 'focal_designation', 'focal_office', 'focal_tel', 'focal_email'
        ];
        contactFields.forEach(name => {
            const el = formEl.querySelector(`[name="${name}"]`);
            if (el) el.value = data[name] || '';
        });


        // Restore documentary checklist
        for (let i = 1; i <= 3; i++) {
            ['date_sub', 'date_rec', 'status', 'remarks'].forEach(suffix => {
                const name = `doc${i}_${suffix}`;
                const el = formEl.querySelector(`[name="${name}"]`);
                if (el) el.value = data[name] || '';
            });
        }

        // Restore checkbox states in dropdown
        const titles = (data.projectTitle || '').split(',').map(t => t.trim()).filter(Boolean);
        const dropdown = document.getElementById('project-title-dropdown');
        if (dropdown) {
            dropdown.querySelectorAll('.project-title-cb').forEach(cb => {
                cb.checked = titles.includes(cb.value);
            });
        }

        // Reset validation state
        formEl.classList.remove('was-validated');
        const alignmentErr = document.getElementById('f-alignment-err');
        if (alignmentErr) alignmentErr.style.display = 'none';



        const isViewOnly = sessionStorage.getItem('cte_view_only') === '1';

        if (isViewOnly) {
            // Disable all form controls
            formEl.querySelectorAll('input, textarea, select, .project-title-cb').forEach(el => {
                el.disabled = true;
            });
            const box = document.getElementById('f-alignment-box');
            if (box) box.style.cursor = 'default';
            const actionDiv = document.getElementById('main-form-actions');
            if (actionDiv) {
                actionDiv.classList.add('d-none');
                actionDiv.classList.remove('d-flex');
            }

            // Show read-only banner (only once)
            const form = document.getElementById('completenessForm');
            if (form && !document.getElementById('view-only-banner')) {
                const banner = document.createElement('div');
                banner.id = 'view-only-banner';
                banner.className = 'alert alert-info border-0 d-flex align-items-center gap-2 mb-4';
                
                const showEdit = sessionStorage.getItem('cte_no_edit_link') !== '1';
                banner.innerHTML = `
                    <i data-lucide="eye" width="18"></i> 
                    <span>You are viewing this validation in <strong>read-only</strong> mode. 
                    ${showEdit ? '<a href="#" id="switchToEditBtn" class="fw-bold">Click here to edit.</a>' : ''}</span>
                `;
                form.prepend(banner);
                if (window.lucide) window.lucide.createIcons();
                
                if (showEdit) {
                    document.getElementById('switchToEditBtn')?.addEventListener('click', (e) => {
                        e.preventDefault();
                        sessionStorage.removeItem('cte_view_only');
                        // Re-init as editable without reloading page
                        _fillFormData(data);
                    });
                }
            }
        } else {
            // EDIT MODE — ensure all controls are enabled and clean up any stale view banners
            formEl.querySelectorAll('input, textarea, select, .project-title-cb').forEach(el => {
                el.disabled = false;
            });
            const box = document.getElementById('f-alignment-box');
            if (box) box.style.cursor = 'pointer';
            const stale = document.getElementById('view-only-banner');
            if (stale) stale.remove();
            const actionDiv = document.getElementById('main-form-actions');
            if (actionDiv) {
                actionDiv.classList.remove('d-none');
                actionDiv.classList.add('d-flex');
            }
        }
    }

    function _validateForm() {
        if (!formEl) return true;

        // Custom validation for Project Titles dropdown
        const hiddenAlignment = document.getElementById('f-alignment');
        const alignmentErr = document.getElementById('f-alignment-err');
        let customValid = true;

        if (hiddenAlignment && hiddenAlignment.hasAttribute('data-required')) {
            if (!hiddenAlignment.value.trim()) {
                if (alignmentErr) alignmentErr.style.display = 'block';
                customValid = false;
            } else {
                if (alignmentErr) alignmentErr.style.display = 'none';
            }
        }

        const isValid = formEl.checkValidity();
        formEl.classList.add('was-validated');

        if (!isValid || !customValid) {
            const firstInvalid = formEl.querySelector('.form-control:invalid, .form-select:invalid, #f-alignment-err[style*="block"]');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
        return true;
    }

    async function _save(status) {
        if (!_validateForm()) return;
        
        const all = await _getAll();
        const editId = sessionStorage.getItem('cte_edit_id');

        const formData = _readFormData();
        const payload = {
            id: editId || `CTE-${Date.now()}`,
            status: status,
            date: new Date().toISOString(),
            formData: formData
        };

        const idx = all.findIndex(x => x.id === payload.id);
        if (idx >= 0) all[idx] = { ...all[idx], ...payload };
        else all.unshift(payload);

        await _setAll(all);
        // Persist the ID so if we refer it immediately, we have the cId
        sessionStorage.setItem('cte_edit_id', payload.id);

        if (window.showSimpleAlert) {
            window.showSimpleAlert('Validation finalized.', 'success');
        }

        // Show optional referral modal instead of redirecting
        const modalEl = document.getElementById('postFinalizeModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    async function _submitReferral(formData) {
        const projectTitle = formData.projectTitle || 'Untitled';
        const implementingAgency = formData.implementingAgency || 'N/A';
        const targetEl = document.getElementById('modalReferralTarget');
        const notesEl = document.getElementById('modalReferralNotes');
        const dateEl = document.getElementById('modalReferralDate');

        const target = targetEl ? targetEl.value : '';
        const notes = notesEl ? notesEl.value : '';
        const date = dateEl ? dateEl.value : '';

        if (!target) {
            if (window.showSimpleAlert) window.showSimpleAlert('Please select a staff or division to refer to.', 'warning');
            return;
        }

        const currentUser = (window.__CURRENT_USER__ || {});
        const currentCteId = sessionStorage.getItem('cte_edit_id');

        const newRef = {
            id: `REF-${Date.now()}`,
            cteId: currentCteId, // Link to the source validation
            projectTitle: projectTitle,
            agency: implementingAgency,
            referrerName: currentUser.email || 'System User',
            referrerRole: currentUser.role === 'admin' ? 'Admin' : 'Technical Staff',
            referredToDivision: target,
            referralDate: date || new Date().toISOString(),
            referralNotes: notes,
            status: 'Pending PAR',
            assignedStaff: ''
        };

        let referrals = await localforage.getItem('project_referrals');
        if (!Array.isArray(referrals)) referrals = [];
        referrals.unshift(newRef);
        await localforage.setItem('project_referrals', referrals);

        // ── Update submission stage to 'Project Appraisal' ────────────────────
        if (currentCteId) {
            const subs = await localforage.getItem('cpp_submissions') || [];
            const sIdx = subs.findIndex(s => s.id === currentCteId || s.cteId === currentCteId);
            if (sIdx >= 0) {
                subs[sIdx].stage = 'Project Appraisal';
                await localforage.setItem('cpp_submissions', subs);
            }
        }

        // Hide modal and cleanup
        const modalEl = document.getElementById('postFinalizeModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        if (window.showSimpleAlert) window.showSimpleAlert('Project referred for PAR assessment. Stage updated to Project Appraisal.', 'success');
        
        if (window.switchPage) window.switchPage('test-and-evaluation');
        setTimeout(() => initTestAndEvaluationLoader(), 80);
    }

    function _wireButtons() {
        // New Validation
        if (newBtn) {
            const fresh = newBtn.cloneNode(true);
            newBtn.parentNode.replaceChild(fresh, newBtn);
            fresh.addEventListener('click', () => {
                sessionStorage.removeItem('cte_edit_id');
                sessionStorage.removeItem('cte_view_only');
                sessionStorage.removeItem('cte_no_edit_link');
                if (formEl) formEl.reset(); // Clear basic inputs
                if (window.switchPage) window.switchPage('test-and-evaluation-form');
                setTimeout(() => initTestAndEvaluationLoader(), 50);
            });
            if (window.lucide) window.lucide.createIcons();
        }

        if (backBtn) {
            const fresh = backBtn.cloneNode(true);
            backBtn.parentNode.replaceChild(fresh, backBtn);
            fresh.addEventListener('click', () => {
                const viaPar = sessionStorage.getItem('cte_no_edit_link') === '1';
                
                if (viaPar) {
                    // Important: don't clear edit context for PAR yet just in case
                    if (window.switchPage) window.switchPage('project-assessment-report-form');
                    if (window.initProjectAssessmentLoader) {
                        setTimeout(() => window.initProjectAssessmentLoader(), 50);
                    }
                } else {
                    if (window.switchPage) window.switchPage('test-and-evaluation');
                    setTimeout(() => initTestAndEvaluationLoader(), 80);
                }
            });
            if (window.lucide) window.lucide.createIcons();
        }

        // Finalize
        if (finalizeBtn) {
            const fresh = finalizeBtn.cloneNode(true);
            finalizeBtn.parentNode.replaceChild(fresh, finalizeBtn);
            fresh.addEventListener('click', (e) => { e.preventDefault(); _save('Final'); });
        }

        // Referral specific buttons in Modal
        const skipBtn = document.getElementById('modalSkipBtn');
        const sendRefBtn = document.getElementById('modalSubmitRefBtn');

        if (skipBtn) {
            skipBtn.onclick = () => {
                const modalEl = document.getElementById('postFinalizeModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                
                if (window.switchPage) window.switchPage('test-and-evaluation');
                setTimeout(() => initTestAndEvaluationLoader(), 80);
            };
        }

        if (sendRefBtn) {
            sendRefBtn.onclick = () => {
                _submitReferral(_readFormData());
            };
        }
    }

    async function _initFormIfNeeded() {
        if (!formEl) return;

        const editId = sessionStorage.getItem('cte_edit_id');
        let item = null;
        if (editId) {
            const all = await _getAll();
            item = all.find(x => x.id === editId);
        }

        // Populate agencies dropdown
        const agencySelect = formEl.querySelector('[name="implementingAgency"]');
        if (agencySelect) {
            const currentVal = item?.formData?.implementingAgency || '';
            await window.populateAgencyDropdown(agencySelect, currentVal);
        }

        if (!editId) {
            _fillFormData({});
            return;
        }
        _fillFormData(item?.formData || {});
    }

    _wireButtons();
    _populateProjectTitles();
    _initFormIfNeeded();
    renderTable();
}

