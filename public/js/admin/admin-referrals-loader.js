export async function initAdminReferralsLoader() {
    const referralTable = document.getElementById('admin-referral-tbody');
    const newReferralBtn = document.getElementById('adminNewReferralBtn');
    const submitBtn = document.getElementById('adminSubmitReferralBtn');
    const projectSelect = document.getElementById('adminReferralProjectSelect');
    const referralForm = document.getElementById('adminNewReferralForm');

    if (!referralTable) return;

    async function _getReferrals() {
        try {
            const res = await fetch('/referrals', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                const json = await res.json();
                return json.data.map(r => ({
                    id: r.id,
                    cteId: r.submissionId,
                    submissionId: r.submissionId,
                    projectTitle: r.submissionTitle,
                    agency: r.fromDivisionName || '—',
                    referrerName: r.fromUserName || 'System',
                    referredToDivision: r.toDivisionName || '—',
                    referralDate: r.referredAt,
                    status: r.status,
                    assignedStaff: r.toUserName
                }));
            }
        } catch (e) {
            console.error('Failed to fetch referrals', e);
        }
        return [];
    }

    async function renderTable() {
        let items = await _getReferrals();
        const countLabel = document.getElementById('admin-referral-count');
        if (countLabel) countLabel.textContent = `Showing ${items.length > 0 ? 1 : 0} to ${items.length} of ${items.length} entries`;
        
        if (items.length === 0) {
            referralTable.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">No referrals found.</td></tr>`;
            return;
        }

        referralTable.innerHTML = items.map(ref => `
            <tr>
                <td class="py-3">
                    <div class="fw-bold text-dark">${ref.projectTitle}</div>
                    <div class="text-muted" style="font-size: 0.70rem;">Agency: ${ref.agency || 'N/A'}</div>
                </td>
                <td class="py-3">
                    <div class="fw-semibold small">${ref.referrerName}</div>
                </td>
                <td class="py-3">
                    <div class="fw-semibold small">${ref.referredToDivision || 'N/A'}</div>
                </td>
                <td class="py-3">
                    <div class="small text-muted">${new Date(ref.referralDate).toLocaleDateString()}</div>
                </td>
                <td class="py-3">
                    <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1" style="font-size: 0.7rem;">${ref.status}</span>
                </td>
                <td class="py-3">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown">
                            <i data-lucide="more-vertical" width="20"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item text-danger delete-referral" href="#" data-id="${ref.id}"><i data-lucide="trash-2" class="me-2" width="16"></i>Cancel</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `).join('');

        if (window.lucide) window.lucide.createIcons();
        _attachHandlers();
    }

    function _attachHandlers() {
        document.querySelectorAll('.delete-referral').forEach(btn => {
            btn.onclick = async () => {
                if (confirm('Are you sure you want to remove this referral?')) {
                    const id = btn.dataset.id;
                    const all = await _getReferrals();
                    await _saveReferrals(all.filter(r => r.id !== id));
                    renderTable();
                }
            };
        });
    }

    async function _populateProjects() {
        if (!projectSelect) return;
        let ctes = await localforage.getItem('cte_validations') || [];
        const finalCtes = ctes.filter(c => c.status === 'Final');
        projectSelect.innerHTML = '<option value="">-- Choose Project --</option>';
        finalCtes.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.dataset.projectTitle = c.formData?.projectTitle || 'Untitled';
            opt.dataset.agency = c.formData?.implementingAgency || '';
            opt.textContent = `${opt.dataset.projectTitle} (${opt.dataset.agency})`;
            projectSelect.appendChild(opt);
        });
    }

    if (newReferralBtn) {
        newReferralBtn.onclick = () => {
            _populateProjects();
            new bootstrap.Modal(document.getElementById('adminNewReferralModal')).show();
        };
    }

    if (submitBtn) {
        submitBtn.onclick = async () => {
            const fd = new FormData(referralForm);
            const selectedOpt = projectSelect.options[projectSelect.selectedIndex];
            const currentUser = (window.__CURRENT_USER__ || {});
            
            if (!fd.get('projectSelect') || !fd.get('referredToDivision')) {
                if (window.showSimpleAlert) {
                    window.showSimpleAlert('Please fill in all required fields.', 'danger');
                }
                return;
            }

            const newRef = {
                id: `REF-${Date.now()}`,
                cteId: fd.get('projectSelect'),
                projectTitle: selectedOpt.dataset.projectTitle,
                agency: selectedOpt.dataset.agency,
                referrerName: currentUser.email || 'Admin',
                referrerRole: 'Admin',
                referredToDivision: fd.get('referredToDivision'),
                referralDate: new Date().toISOString(),
                referralNotes: fd.get('referralNotes'),
                status: 'Pending PAR',
                assignedStaff: ''
            };

            const all = await _getReferrals();
            all.unshift(newRef);
            await _saveReferrals(all);

            bootstrap.Modal.getInstance(document.getElementById('adminNewReferralModal')).hide();
            referralForm.reset();
            renderTable();
        };
    }

    renderTable();
}
