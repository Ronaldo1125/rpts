/**
 * Admin Dashboard — Revised Submissions Stage Loader
 */

export async function initRevisionStage() {
    const tbody = document.getElementById('revisedTableBody');
    const countBadge = document.getElementById('revised-list-count');
    const searchInput = document.getElementById('revisedSearchInput');

    if (!tbody) return;

    // ── Load Data ───────────────────────────────────────────────────────────
    async function _render() {
        const allSubmissions = (window.__ADMIN_DASHBOARD_SUBMISSIONS__ && window.__ADMIN_DASHBOARD_SUBMISSIONS__.length > 0)
            ? window.__ADMIN_DASHBOARD_SUBMISSIONS__
            : await localforage.getItem('cpp_submissions') || [];
            
        const allPars = (window.__ADMIN_DASHBOARD_EVALUATED_PARS__ && window.__ADMIN_DASHBOARD_EVALUATED_PARS__.length > 0)
            ? window.__ADMIN_DASHBOARD_EVALUATED_PARS__
            : await localforage.getItem('project_assessments') || [];
        
        // Filter for "Resubmitted" or "Revised"
        const rows = allSubmissions.filter(s => {
            const st = (s.submissionStatus || s.status || '').toLowerCase();
            const isRevised = st === 'revised';
            // Only show if it's revised and NOT yet referred to PDIPBD
            return isRevised && !s.referredToPdipb;
        });

        if (countBadge) countBadge.textContent = rows.length;
        const workflowBadge = document.getElementById('admin-dash-revised-count');
        if (workflowBadge) workflowBadge.textContent = rows.length;

        if (rows.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted small">
                        No revised submissions found.
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = rows.map(s => {
            const title  = s.title  || s.formData?.['f-title']  || 'Untitled';
            const agency = s.agency || s.formData?.['f-agency'] || '—';
            const stage  = _getManageProjectStage(s, allPars);
            const date   = s.resubmittedAt || s.updatedAt || s.dateSubmitted || '';
            const sid    = s.id || '';
            
            const abbr = window.getAgencyAbbreviation ? window.getAgencyAbbreviation(agency) : agency;

            return `
                <tr>
                    <td class="ps-4 py-3 fw-bold text-dark small" style="max-width:300px;">${title}</td>
                    <td class="small text-muted py-3">${abbr}</td>
                    <td class="small text-muted py-3">${stage}</td>
                    <td class="small text-muted py-3">${date ? new Date(date).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold refer-revised-btn shadow-sm"
                            data-sid="${sid}"
                            data-title="${title.replace(/"/g, '&quot;')}"
                            data-agency="${agency.replace(/"/g, '&quot;')}"
                            style="background:linear-gradient(135deg,#154A9A,#1e6fd9); border:none; font-size:0.75rem;">
                            <i data-lucide="send" width="13" class="me-1"></i> Refer to PDIPBD
                        </button>
                    </td>
                </tr>`;
        }).join('');

        if (window.lucide) window.lucide.createIcons();
        _attachHandlers();
    }

    function _getManageProjectStage(s, allPars) {
        const par = allPars.find(p => (p.connectedCteId === s.id || (s.cteId && p.connectedCteId === s.cteId)));
        const subStatus = (s.submissionStatus || s.status || '').trim();

        if (s.stage && s.stage.trim()) return s.stage;
        if (subStatus.toLowerCase() === 'draft') return 'Preparation';
        if (['Submitted','Incomplete','Validated'].includes(subStatus)) return 'Completeness Test and Validation';
        if (s.projectStatus === 'Sectoral Presentation' || par?.status === 'Sectoral Presentation') return 'Sectoral Committee';
        if (par?.status === 'Final' || subStatus === 'Final' || s.projectStatus === 'Technical Report Finalized') return 'Finalization';
        if (['Review','Approved','For Revision','Resubmitted','Revised','Referred to PDIPBD'].includes(subStatus) || par?.status === 'Reviewed') return 'Project Appraisal';
        return '—';
    }

    function _attachHandlers() {
        // Refer to PDIPBD handler
        tbody.querySelectorAll('.refer-revised-btn').forEach(btn => {
            btn.onclick = async (e) => {
                e.preventDefault();
                const sid = btn.dataset.sid;
                const title = btn.dataset.title;
                const agency = btn.dataset.agency;
                
                _showRevisionReferralModal(sid, title, agency);
            };
        });

        // Search handler
        if (searchInput) {
            searchInput.oninput = (e) => {
                const term = e.target.value.toLowerCase();
                tbody.querySelectorAll('tr').forEach(tr => {
                    tr.style.display = tr.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            };
        }
    }

    async function _loadPdipbStaff() {
        if (window.__ADMIN_DASHBOARD_USERS__ && window.__ADMIN_DASHBOARD_USERS__.length > 0) {
            return window.__ADMIN_DASHBOARD_USERS__;
        }
        const allUsers = await localforage.getItem('system_users') || [];
        return allUsers.filter(u =>
            u.role === 'staff' &&
            (u.division || '').toUpperCase() === 'PDIPBD'
        );
    }

    async function _showRevisionReferralModal(sid, title, agency) {
        // Create modal container if not exists
        let modalEl = document.getElementById('referRevisionToPdipbModal');
        if (modalEl) {
            bootstrap.Modal.getInstance(modalEl)?.dispose();
            modalEl.remove();
        }

        const staffList = await _loadPdipbStaff();
        const staffOptions = staffList.map(u => 
            `<option value="${u.id}" data-name="${u.name}">${u.name} — ${u.email || ''}</option>`
        ).join('') || '<option value="" disabled>No PDIPB staff registered</option>';

        const modalHtml = `
        <div class="modal fade" id="referRevisionToPdipbModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
                    <div style="height:5px; background:linear-gradient(90deg,#154A9A,#1e6fd9);"></div>
                    <div class="modal-header border-0 pt-4 px-4 pb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm"
                                style="width:40px;height:40px;background:linear-gradient(135deg,#154A9A,#1e6fd9);">
                                <i data-lucide="send" width="18" class="text-white"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" style="font-size:1rem;">Refer Revised Submission</h5>
                                <p class="mb-0 text-muted" style="font-size:0.75rem;">Forward revised CPP to PDIPB for review</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pb-3 pt-3">
                        <div class="p-3 rounded-3 mb-3 d-flex align-items-start gap-3" style="background:#f0f7ff; border:1px solid #bfdbfe;">
                            <i data-lucide="file-check" width="16" class="text-primary mt-1 flex-shrink-0"></i>
                            <div>
                                <div class="text-muted fw-bold text-uppercase mb-1" style="font-size:0.62rem;letter-spacing:.06em;">Revised CPP</div>
                                <div class="fw-bold text-dark lh-sm" style="font-size:0.85rem;">${title}</div>
                                <div class="text-muted mt-1" style="font-size:0.7rem;">${agency}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-semibold text-dark mb-2 d-block">Assign PDIPB Staff <span class="text-danger">*</span></label>
                            <select id="referRevisionStaff" class="form-select border-0 rounded-3 bg-light" style="font-size:0.85rem;">
                                <option value="">— Select staff member —</option>
                                ${staffOptions}
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="small fw-semibold text-dark mb-2 d-block">Revision Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea id="referRevisionNotes" rows="3" class="form-control border-0 rounded-3 bg-light" style="font-size:0.85rem; resize:none;" placeholder="Notes regarding the revision review..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm rounded-pill px-4 fw-medium bg-light text-muted border-0" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm rounded-pill px-5 fw-semibold text-white border-0" id="confirmReferRevisionBtn" style="background:linear-gradient(135deg,#154A9A,#1e6fd9);">
                            <i data-lucide="send" width="13" class="me-1"></i> Confirm Referral
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        if (window.lucide) window.lucide.createIcons();
        const modal = new bootstrap.Modal(document.getElementById('referRevisionToPdipbModal'));
        modal.show();

        document.getElementById('confirmReferRevisionBtn').onclick = async () => {
            const staffId = document.getElementById('referRevisionStaff').value;
            const staffName = document.getElementById('referRevisionStaff').selectedOptions[0]?.dataset.name;
            const notes = document.getElementById('referRevisionNotes').value;

            if (!staffId) {
                alert('Please select a staff member.');
                return;
            }

            const allSubs = await localforage.getItem('cpp_submissions') || [];
            const idx = allSubs.findIndex(s => s.id === sid);
            
            // Persist to Server
            try {
                const response = await fetch('/referrals', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        cipg_submission_id: sid,
                        to_user_id: staffId,
                        notes: notes
                    })
                });
                
                const result = await response.json();
                if (!result.success) {
                    alert('Failed to save referral: ' + (result.message || 'Unknown error'));
                    return;
                }
            } catch (err) {
                console.error('Error persisting referral:', err);
                alert('An error occurred while communicating with the server.');
                return;
            }

            if (idx >= 0) {
                allSubs[idx].referredToPdipb = true;
                allSubs[idx].assignedStaffId = staffId;
                allSubs[idx].assignedStaffName = staffName;
                allSubs[idx].referralNotes = notes;
                allSubs[idx].referralDate = new Date().toISOString();
                await localforage.setItem('cpp_submissions', allSubs);

                // Also update the associated Project Assessment Report (PAR)
                const assessments = await localforage.getItem('project_assessments') || [];
                // Find PAR connected to this submission (cppId/submissionId)
                const parIdx = assessments.findIndex(p => p.connectedCteId === allSubs[idx].cteId || p.connectedCteId === sid);
                if (parIdx >= 0) {
                    assessments[parIdx].isReferredToPdipbd = true;
                    assessments[parIdx].referredToPdipbdDate = new Date().toISOString();
                    assessments[parIdx].pdipbdAssignedStaffId = staffId;
                    assessments[parIdx].pdipbdAssignedStaffName = staffName;
                    // If it was already reviewed but referred again, keep status or move to Referred
                    if (assessments[parIdx].status !== 'Reviewed' && assessments[parIdx].status !== 'Final') {
                        assessments[parIdx].status = 'Referred to PDIPBD';
                    }
                    await localforage.setItem('project_assessments', assessments);
                }
            }

            modal.hide();
            if (window.showSimpleAlert) window.showSimpleAlert('Revised submission referred to PDIPB.', 'success');
            _render();
        };
    }

    await _render();
}
