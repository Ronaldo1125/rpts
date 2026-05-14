export async function initStaffReferralsLoader() {
    const referralTable = document.getElementById('staff-referral-tbody');
    const currentUser = (window.__CURRENT_USER__ || {});

    if (!referralTable || !currentUser.email) return;

    async function _getReferrals() {
        let items = await localforage.getItem('project_referrals');
        return Array.isArray(items) ? items : [];
    }

    async function _saveReferrals(items) {
        await localforage.setItem('project_referrals', items);
    }

    function _cppIsRdcApprovedOrBeyond(sub) {
        if (!sub) return false;
        const v = x => String(x || '').trim().toLowerCase();
        return [sub.submissionStatus, sub.status, sub.projectStatus, sub.cppStatus].some(
            s => v(s) === 'rdc approved'
        );
    }

    function _findLinkedCppSubmission(subs, ref) {
        if (!ref) return null;
        const sid = ref.submissionId || ref.cipg_submission_id || ref.cpp_submission_id;
        const cte = ref.cteId || ref.cipg_submission_id || ref.cpp_submission_id;
        return subs.find(s =>
            (sid && s.id === sid) ||
            (cte && (s.cteId === cte || s.id === cte))
        ) || null;
    }

    /** True if a PAR record already exists for this referral (any status, including Draft). */
    function _referralHasExistingPar(pars, ref) {
        if (!ref || !Array.isArray(pars) || pars.length === 0) return false;
        const cte = String(ref.submissionId || ref.cteId || ref.cipg_submission_id || '').trim();
        const subId = String(ref.submissionId || ref.cipg_submission_id || '').trim();
        return pars.some(p => {
            const cid = String(p.connectedCteId || '').trim();
            if (!cid) return false;
            if (cte && cid === cte) return true;
            if (subId && cid === subId) return true;
            return false;
        });
    }

    async function renderTable() {
        let items = await _getReferrals();
        const subs = await localforage.getItem('cpp_submissions') || [];
        const pars = await localforage.getItem('project_assessments') || [];
        const excludedReferralStatuses = [
            'Assessed', 'Evaluated', 'Draft', 'PAR Started',
            'RDC Approved'
        ];
        // Filter for this staff specifically - only show projects strictly awaiting a PAR
        const myAssignments = items.filter(r => {
            const assigned = (r.assignedStaff || r.assignedStaffId || r.toUserId || '').toLowerCase();
            if (assigned !== currentUser.email && assigned !== String(currentUser.id)) return false;
            
            const st = (r.status || '').trim();
            if (excludedReferralStatuses.includes(st)) return false;
            if (_referralHasExistingPar(pars, r)) return false;
            const linked = _findLinkedCppSubmission(subs, r);
            if (_cppIsRdcApprovedOrBeyond(linked)) return false;
            return true;
        });
        
        const countLabel = document.getElementById('staff-referral-count-label');
        if (countLabel) countLabel.textContent = `Showing ${myAssignments.length > 0 ? 1 : 0} to ${myAssignments.length} of ${myAssignments.length} entries`;

        if (myAssignments.length === 0) {
            referralTable.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted">You have no active PAR assignments.</td></tr>`;
            return;
        }

        referralTable.innerHTML = myAssignments.map(ref => `
            <tr>
                <td class="py-3">
                    <div class="fw-bold text-dark d-flex align-items-center gap-1">
                        ${ref.projectTitle}
                        ${ref.referralNotes ? '<span data-bs-toggle="tooltip" data-bs-placement="top" title="Admin Instructions" style="cursor:help; display:inline-flex; align-items:center;"><i data-lucide="info" width="14" class="text-primary-rpts"></i></span>' : ''}
                    </div>
                    <div class="text-muted small" style="font-size: 0.7rem;">Agency: ${ref.agency || 'N/A'}</div>
                </td>
                <td class="py-3">
                    <div class="small fw-semibold text-primary px-2 py-1 bg-primary-subtle rounded d-inline-block" style="font-size: 0.75rem;">${ref.toDivisionName || ref.referredToDivision || 'N/A'}</div>
                </td>
                <td class="py-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success" style="width: 8px; height: 8px;"></div>
                        <span class="small text-muted fw-medium">Assigned to Me</span>
                    </div>
                </td>
                <td class="py-3">
                    <div class="small text-muted">${new Date(ref.referredAt || ref.referralDate).toLocaleDateString()}</div>
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
                            <li><a class="dropdown-item view-notes-btn" href="#" data-id="${ref.id}" ${!ref.notes && !ref.referralNotes ? 'style="display:none"' : ''}><i data-lucide="info" class="me-2" width="16"></i>View Admin Instructions</a></li>
                            <li><a class="dropdown-item start-par-btn" href="#" data-id="${ref.id}" data-project="${ref.submissionTitle || ref.projectTitle}" data-agency="${ref.agency || ''}" data-cte-id="${ref.submissionId || ref.cteId || ref.cipg_submission_id || ''}"><i data-lucide="play" class="me-2" width="16"></i>Start PAR</a></li>
                            <li><a class="dropdown-item text-danger reject-staff-btn" href="#" data-id="${ref.id}"><i data-lucide="x-circle" class="me-2" width="16"></i>Reject Assignment</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `).join('');

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
                if (ref && (ref.notes || ref.referralNotes)) {
                    if (window.showInfoModal) {
                        window.showInfoModal({
                            title: 'Admin Instructions',
                            message: `<div class="p-3 bg-light rounded-12 text-secondary small text-start">${ref.notes || ref.referralNotes}</div>`
                        });
                    } else {
                        alert(`Admin Instructions:\n\n${ref.referralNotes}`);
                    }
                }
            };
        });
        document.querySelectorAll('.start-par-btn').forEach(btn => {
            btn.onclick = () => {
                const id = btn.dataset.id;
                const title = btn.dataset.project;
                const cteId = btn.dataset.cteId;
                const agency = btn.dataset.agency;
                
                // Store context for the PAR form (aligned with project-assessment-loader expectations)
                sessionStorage.removeItem('par_edit_id'); // Ensure it's a new entry
                sessionStorage.setItem('par_prefill_cte_id', cteId);
                sessionStorage.setItem('par_prefill_title', title);
                sessionStorage.setItem('par_prefill_agency', agency);
                sessionStorage.setItem('current_par_referral_id', id);
                
                if (window.switchPage) {
                    window.switchPage('project-assessment-report-form');
                }
            };
        });

        document.querySelectorAll('.reject-staff-btn').forEach(btn => {
            btn.onclick = async () => {
                if (confirm('Review REJECTION - Are you sure you want to reject this assignment? It will return to your Division Head for re-assignment.')) {
                    const id = btn.dataset.id;
                    const all = await _getReferrals();
                    const idx = all.findIndex(r => r.id === id);
                    if (idx >= 0) {
                        all[idx].assignedStaff = '';
                        all[idx].status = 'Pending PAR (Re-assignment)';
                        await _saveReferrals(all);
                    }
                    renderTable();
                }
            };
        });
    }

    renderTable();
}
