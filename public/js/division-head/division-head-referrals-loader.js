export async function initDivisionHeadReferralsLoader() {
    const referralTable = document.getElementById('dh-referral-tbody');
    const divisionLabel = document.getElementById('dh-division-label');
    const assignModalEl = document.getElementById('dhAssignStaffModal');
    const staffSelect = document.getElementById('dhAssignStaffSelect');
    const confirmAssignBtn = document.getElementById('dhConfirmAssignBtn');
    
    const currentUser = (window.__CURRENT_USER__ || {});
    if (!referralTable || !currentUser.division) return;

    if (divisionLabel) divisionLabel.textContent = currentUser.division;

    async function _getReferrals() {
        let items = await localforage.getItem('project_referrals');
        return Array.isArray(items) ? items : [];
    }

    async function _saveReferrals(items) {
        await localforage.setItem('project_referrals', items);
    }

    async function _getStaff() {
        let users = await localforage.getItem('system_users') || [];
        
        // Safety seed if empty (so Division Head always has someone to assign to in demo)
        if (users.length === 0) {
            users = [
                { name: 'Emmanuel Llaguno', email: 'emmanuel.llaguno@neda.gov.ph', role: 'staff', division: 'PMED', agency: 'NEDA 5' },
                { name: 'Arlene Posada', email: 'arlene.posada@neda.gov.ph', role: 'staff', division: 'PDIPBD', agency: 'NEDA 5' },
                { name: 'Technical Staff 1', email: 'staff1@neda.gov.ph', role: 'staff', division: 'PMED', agency: 'NEDA 5' },
                { name: 'Technical Staff 2', email: 'staff2@neda.gov.ph', role: 'staff', division: 'PDIPBD', agency: 'NEDA 5' }
            ];
            await localforage.setItem('system_users', users);
        }

        // Filter staff in the same division
        return users.filter(u => u.role === 'staff' && u.division === currentUser.division);
    }

    async function renderTable() {
        let items = await _getReferrals();
        // Filter for this division
        const myReferrals = items.filter(r => r.referredToDivision === currentUser.division);
        
        const countLabel = document.getElementById('dh-referral-count-label');
        if (countLabel) countLabel.textContent = `Showing ${myReferrals.length > 0 ? 1 : 0} to ${myReferrals.length} of ${myReferrals.length} entries`;

        if (myReferrals.length === 0) {
            referralTable.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">No referrals for ${currentUser.division} yet.</td></tr>`;
            return;
        }

        referralTable.innerHTML = myReferrals.map(ref => {
            const isAssigned = !!ref.assignedStaff;
            const statusBadge = isAssigned 
                ? `<span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1" style="font-size: 0.7rem;">Assigned</span>`
                : `<span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1" style="font-size: 0.7rem;">Pending Assignment</span>`;

            return `
                <tr>
                    <td class="py-3">
                        <div class="fw-bold text-dark d-flex align-items-center gap-1">
                            ${ref.projectTitle}
                            ${ref.referralNotes ? '<span data-bs-toggle="tooltip" data-bs-placement="top" title="Admin Notes available" style="cursor:help; display:inline-flex; align-items:center;"><i data-lucide="info" width="14" class="text-primary-rpts"></i></span>' : ''}
                        </div>
                        <div class="text-muted small" style="font-size: 0.7rem;">Agency: ${ref.agency || 'N/A'}</div>
                    </td>
                    <td class="py-3">
                        <div class="small fw-semibold">${ref.referrerName}</div>
                        <div class="text-muted" style="font-size: 0.65rem;">${ref.referrerRole}</div>
                    </td>
                    <td class="py-3">
                        <div class="small fw-semibold ${isAssigned ? 'text-dark' : 'text-danger italic'}">${ref.assignedStaff || 'Not Assigned'}</div>
                    </td>
                    <td class="py-3"><div class="small text-muted">${new Date(ref.referralDate).toLocaleDateString()}</div></td>
                    <td class="py-3">${statusBadge}</td>
                    <td class="py-3">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown">
                                <i data-lucide="more-vertical" width="20"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item view-notes-btn" href="#" data-id="${ref.id}" ${!ref.referralNotes ? 'style="display:none"' : ''}><i data-lucide="info" class="me-2" width="16"></i>View Admin Notes</a></li>
                                <li><a class="dropdown-item assign-btn" href="#" data-id="${ref.id}" ${isAssigned ? 'style="display:none"' : ''}><i data-lucide="user-plus" class="me-2" width="16"></i>Assign Staff</a></li>
                                <li><a class="dropdown-item text-danger reject-btn" href="#" data-id="${ref.id}"><i data-lucide="x-circle" class="me-2" width="16"></i>Reject Referral</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        if (window.lucide) window.lucide.createIcons();
        
        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(referralTable.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        _attachHandlers();
    }

    function _attachHandlers() {
        document.querySelectorAll('.view-notes-btn').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.dataset.id;
                const items = await _getReferrals();
                const ref = items.find(r => r.id === id);
                if (ref && ref.referralNotes) {
                    if (window.showInfoModal) {
                        window.showInfoModal({
                            title: 'Admin Instructions',
                            message: `<div class="p-3 bg-light rounded-12 text-secondary small text-start">${ref.referralNotes}</div>`
                        });
                    } else {
                        alert(`Referral Notes:\n\n${ref.referralNotes}`);
                    }
                }
            };
        });
        document.querySelectorAll('.assign-btn').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.dataset.id;
                document.getElementById('dhAssignReferralId').value = id;
                
                // Populate staff members
                const staffList = await _getStaff();
                if (staffList.length === 0) {
                    staffSelect.innerHTML = '<option value="">(No Staff found for your division)</option>';
                } else {
                    staffSelect.innerHTML = '<option value="">-- Select Staff member --</option>';
                    staffList.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.email;
                        opt.textContent = `${s.name || s.email} (${s.email})`;
                        staffSelect.appendChild(opt);
                    });
                }

                new bootstrap.Modal(assignModalEl).show();
            };
        });

        document.querySelectorAll('.reject-btn').forEach(btn => {
            btn.onclick = async () => {
                if (confirm('Are you sure you want to reject this referral? It will be removed from your list.')) {
                    const id = btn.dataset.id;
                    const all = await _getReferrals();
                    await _saveReferrals(all.filter(r => r.id !== id));
                    renderTable();
                }
            };
        });
    }

    if (confirmAssignBtn) {
        confirmAssignBtn.onclick = async () => {
            const id = document.getElementById('dhAssignReferralId').value;
            const staffEmail = staffSelect.value;
            if (!staffEmail) {
                if (window.showSimpleAlert) {
                    window.showSimpleAlert('Please select a staff member.', 'danger');
                } else {
                    alert('Please select a staff member.');
                }
                return;
            }

            const all = await _getReferrals();
            const idx = all.findIndex(r => r.id === id);
            if (idx >= 0) {
                all[idx].assignedStaff = staffEmail;
                all[idx].status = 'Assigned for PAR';
                await _saveReferrals(all);
            }

            bootstrap.Modal.getInstance(assignModalEl).hide();
            renderTable();
        };
    }

    renderTable();
}
