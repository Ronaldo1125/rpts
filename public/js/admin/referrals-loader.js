export function initReferralsLoader() {
    window.initReferralsLoader = initReferralsLoader;

    const referralTable = document.getElementById('referral-tbody');
    const newReferralBtn = document.getElementById('newReferralBtn');
    const submitBtn = document.getElementById('submitReferralBtn');
    const projectSelect = document.getElementById('referralProjectSelect');
    const referralForm = document.getElementById('newReferralForm');
    const countSpan = document.getElementById('referral-count');

    if (!referralTable) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    async function _api(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                ...(options.headers || {}),
            },
            ...options,
        });

        let data = null;
        try {
            data = await response.json();
        } catch (_) {
            data = null;
        }

        if (!response.ok) {
            const msg = data?.message || 'Request failed';
            throw new Error(msg);
        }

        return data;
    }

    // --- Helper Functions ---

    function _statusBadge(status) {
        if (status === 'Accepted') return '<span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1">Accepted</span>';
        if (status === 'Processed') return '<span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1">Processed</span>';
        return '<span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1">Pending PAR</span>';
    }

    async function _getReferrals() {
        const res = await _api('/referrals');
        return Array.isArray(res?.data) ? res.data : [];
    }

    // --- Rendering ---

    async function renderTable() {
        let items = await _getReferrals();
        const currentUser = (window.__CURRENT_USER__ || {});
        const isDivHead = currentUser.role === 'division-head';
        
        // Filter by division for Division Heads
        if (isDivHead && currentUser.division) {
            items = items.filter(ref => ref.referredToDivision === currentUser.division);
        }

        // Hide referrals that have already moved to the assessment stage
        items = items.filter(ref => !['Assessed', 'Evaluated', 'Draft', 'PAR Started'].includes(ref.status));

        if (items.length === 0) {
            referralTable.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">No referrals found for your division.</td></tr>`;
            if (countSpan) countSpan.textContent = 'Showing 0 entries';
            return;
        }

        referralTable.innerHTML = items.map(ref => `
            <tr>
                <td>
                    <div class="fw-bold text-dark">${ref.projectTitle}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">Implementing Agency: ${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(ref.agency) : (ref.agency || 'N/A')}</div>
                </td>
                <td>
                    <div class="fw-semibold">${ref.referrerName}</div>
                    <div class="text-muted small">${ref.referrerRole}</div>
                </td>
                <td>
                    <div class="fw-semibold">${ref.referredToDivision || 'N/A'}</div>
                    <div class="text-primary small fw-bold mt-1">
                        ${ref.assignedStaff ? `<i data-lucide="user" width="12" class="me-1"></i>${ref.assignedStaff}` : '<span class="text-warning">Pending Assignment</span>'}
                    </div>
                </td>
                <td>
                    <div class="small">${new Date(ref.referralDate).toLocaleDateString()}</div>
                    <div class="text-muted" style="font-size: 0.65rem;">Official Referral</div>
                </td>
                <td>${_statusBadge(ref.status)}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i data-lucide="more-vertical" width="16"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                            ${isDivHead ? `<li><a class="dropdown-item py-2 assign-staff-btn" href="#" data-id="${ref.id}" data-division="${ref.referredToDivision}"><i data-lucide="user-plus" width="14" class="me-2"></i>Assign Staff</a></li>` : ''}
                            ${ref.status === 'Pending PAR' ? `<li><a class="dropdown-item py-2 text-primary start-par-dynamic" href="#" data-title="${ref.projectTitle}" data-agency="${ref.agency}" data-cte-id="${ref.cteId || ''}"><i data-lucide="file-edit" width="14" class="me-2"></i>Start PAR</a></li>` : ''}
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger delete-referral" href="#" data-id="${ref.id}"><i data-lucide="trash-2" width="14" class="me-2"></i>Remove</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `).join('');

        if (countSpan) countSpan.textContent = `Showing 1 to ${items.length} of ${items.length} entries`;
        if (window.lucide) window.lucide.createIcons();
        _attachRowHandlers();
    }

    function _attachRowHandlers() {
        // Start PAR from Dynamic Rows
        document.querySelectorAll('.start-par-dynamic').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                // Ensure we are not in edit mode
                sessionStorage.removeItem('par_edit_id');
                // Set pre-fill data
                sessionStorage.setItem('par_prefill_title', btn.dataset.title);
                sessionStorage.setItem('par_prefill_agency', btn.dataset.agency);
                if (btn.dataset.cteId) sessionStorage.setItem('par_prefill_cte_id', btn.dataset.cteId);
                
                if (window.switchPage) window.switchPage('project-assessment-report-form');
            };
        });

        // Delete Referral
        document.querySelectorAll('.delete-referral').forEach(btn => {
            btn.onclick = async (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                await _api(`/referrals/${id}`, { method: 'DELETE' });
                await renderTable();
            };
        });

        // Assign Staff Trigger
        document.querySelectorAll('.assign-staff-btn').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                const division = btn.dataset.division;
                
                document.getElementById('assignReferralId').value = id;
                document.getElementById('assign-division-name').textContent = division;
                
                // Populate mock staff based on division
                const staffSelect = document.getElementById('assignStaffSelect');
                staffSelect.innerHTML = '<option value="">-- Select Staff member --</option>';
                
                _api(`/referrals/staff?division=${encodeURIComponent(division || '')}`)
                    .then(res => {
                        const list = Array.isArray(res?.data) ? res.data : [];
                        list.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.id;
                            opt.textContent = `${s.name}${s.email ? ' — ' + s.email : ''}`;
                            staffSelect.appendChild(opt);
                        });
                        if (!list.length) {
                            const opt = document.createElement('option');
                            opt.value = '';
                            opt.textContent = 'No staff found for division';
                            opt.disabled = true;
                            staffSelect.appendChild(opt);
                        }
                    })
                    .catch(() => {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'Unable to load staff';
                        opt.disabled = true;
                        staffSelect.appendChild(opt);
                    });

                const modal = new bootstrap.Modal(document.getElementById('assignStaffModal'));
                modal.show();
            };
        });

        const confirmAssignBtn = document.getElementById('confirmAssignBtn');
        if (confirmAssignBtn) {
            confirmAssignBtn.onclick = async () => {
                const referralId = document.getElementById('assignReferralId').value;
                const staffName = document.getElementById('assignStaffSelect').value;
                
                if (!staffName) {
                    if (window.showSimpleAlert) window.showSimpleAlert('Please select a staff member.', 'warning');
                    return;
                }

                await _api(`/referrals/${referralId}/assign-staff`, {
                    method: 'POST',
                    body: JSON.stringify({ staff_id: staffName }),
                });

                const modalEl = document.getElementById('assignStaffModal');
                bootstrap.Modal.getInstance(modalEl).hide();
                await renderTable();
                if (window.showSimpleAlert) window.showSimpleAlert('Project assignment updated.', 'success');
            };
        }
    }

    // --- Modal Logic ---

    async function _populateProjects() {
        if (!projectSelect) return;

        const res = await _api('/referrals/validated-projects');
        const finalCtes = Array.isArray(res?.data) ? res.data : [];

        projectSelect.innerHTML = '<option value="">-- Choose from Completeness Test Results --</option>';
        finalCtes.forEach(c => {
            const title = c.projectTitle || 'Untitled';
            const agency = c.agency || '';
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.dataset.projectTitle = title;
            opt.dataset.agency = agency;
            opt.textContent = `${title}${agency ? ' (' + agency + ')' : ''}`;
            projectSelect.appendChild(opt);
        });
    }

    // --- Events ---

    if (newReferralBtn) {
        newReferralBtn.onclick = async () => {
            await _populateProjects();
            const modal = new bootstrap.Modal(document.getElementById('newReferralModal'));
            modal.show();
        };
    }

    if (submitBtn) {
        submitBtn.onclick = async () => {
            const fd = new FormData(referralForm);
            
            if (!fd.get('projectSelect') || !fd.get('referredToDivision')) {
                if (window.showSimpleAlert) window.showSimpleAlert('Please select a project and target division.', 'warning');
                return;
            }

            await _api('/referrals', {
                method: 'POST',
                body: JSON.stringify({
                    projectSelect: fd.get('projectSelect'),
                    referredToDivision: fd.get('referredToDivision'),
                    referralNotes: fd.get('referralNotes') || '',
                }),
            });

            bootstrap.Modal.getInstance(document.getElementById('newReferralModal')).hide();
            referralForm.reset();
            await renderTable();
            if (window.showSimpleAlert) window.showSimpleAlert('Referral created successfully.', 'success');
        };
    }

    renderTable();
}
