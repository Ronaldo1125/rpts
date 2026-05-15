/**
 * admin-evaluation-stage-loader.js
 * Powers the "Project Assessment Report" (Step 3) stage panel in the admin dashboard.
 */

window.initEvaluationStage = async function () {
    const tbody       = document.getElementById('evalTableBody');
    const countBadge  = document.getElementById('eval-list-count');
    const kpiPending  = document.getElementById('eval-kpi-pending');
    const kpiAssessed = document.getElementById('eval-kpi-assessed');
    const kpiEval     = document.getElementById('eval-kpi-evaluated');
    const searchInput = document.getElementById('evalSearchInput');

    if (!tbody) return;

    async function render(filterTerm = '') {
        const reviewedTbody = document.getElementById('dashboard-reviewed-par-tbody');
        const reviewedCountBadge = document.getElementById('reviewed-list-count');

        // Fetch evaluated PARs from the global variable or localforage
        const serverEvaluatedPars = Array.isArray(window.__ADMIN_DASHBOARD_EVALUATED_PARS__)
            ? window.__ADMIN_DASHBOARD_EVALUATED_PARS__
            : (Array.isArray(window.__EVALUATED_REPORTS__) ? window.__EVALUATED_REPORTS__ : []);
        const serverReviewedPars = Array.isArray(window.__ADMIN_DASHBOARD_REVIEWED_PARS__)
            ? window.__ADMIN_DASHBOARD_REVIEWED_PARS__
            : [];
        const localEvaluatedPars = await localforage.getItem('project_assessments') || [];

        // Combine and deduplicate by ID
        const allParsMap = new Map();
        localEvaluatedPars.forEach(p => allParsMap.set(String(p.id || p.parId), p));
        serverEvaluatedPars.forEach(p => allParsMap.set(String(p.id || p.parId), p));
        serverReviewedPars.forEach(p => allParsMap.set(String(p.id || p.parId), p));
        let allPars = Array.from(allParsMap.values());

        // Get local referrals to check which PARs have already been referred
        const localReferrals = await localforage.getItem('project_referrals') || [];

        const currentUser = (window.__CURRENT_USER__ || {});
        const isPdipbdStaff = (currentUser.division || '').toUpperCase().includes('PDIPB');
        const isAdmin = (currentUser.role || '').toLowerCase().includes('admin');

        let pendingPars = [];
        let reviewedPars = [];

        if (isAdmin) {
            // ADMIN VIEW:
            // 1. Pending Referral (Status Evaluated and not referred)
            pendingPars = allPars.filter(p => {
                if (p.status !== 'Evaluated') return false;
                if (p.isReferredToPdipbd) return false;

                const alreadyReferred = localReferrals.some(ref =>
                    ref.stage === 'Project Appraisal' &&
                    (String(ref.parId) === String(p.id) || String(ref.submissionId) === String(p.id))
                );
                return !alreadyReferred;
            });

            // 2. Reviewed (Ready for Finalization)
            reviewedPars = allPars.filter(p => p.status === 'Reviewed');
        } else if (isPdipbdStaff) {
            // STAFF VIEW: Show only PARs referred to PDIPBD
            pendingPars = allPars.filter(p => {
                return p.isReferredToPdipbd === true || p.status === 'Referred to PDIPBD';
            });
        }

        // --- Helper to map rows ---
        const mapToRow = (p) => {
            const title = p.title || p.projectTitle || (p.submission ? (p.submission.project_title || p.submission.title) : null) || 'Untitled';
            const agency = p.agency || p.proponent || (p.submission?.user?.agency ? p.submission.user.agency.agency_name : null) || '—';
            const division = p.division || p.referredToDivision || p.evaluatingDivision || 
                             (p.referral?.toDivision ? p.referral.toDivision.name : null) || 
                             (p.referral?.to_division ? p.referral.to_division.name : null) || 
                             (p.assessor?.division ? p.assessor.division.name : null) || '—';
            const dateVal = p.status === 'Reviewed' ? (p.dateReviewed || p.updated_at || p.created_at) : (p.referredToPdipbdDate || p.created_at);
            const dateStr = dateVal ? new Date(dateVal).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';

            return {
                id: p.id || p.parId,
                title: title,
                agency: agency,
                division: division,
                status: p.status,
                date: dateStr,
                type: p.status === 'Reviewed' ? 'reviewed' : 'evaluated'
            };
        };

        let pendingRows = pendingPars.map(mapToRow);
        let reviewedRows = reviewedPars.map(mapToRow);

        if (filterTerm) {
            const t = filterTerm.toLowerCase();
            const filterFn = r => (r.title || '').toLowerCase().includes(t) || (r.agency || '').toLowerCase().includes(t);
            pendingRows = pendingRows.filter(filterFn);
            reviewedRows = reviewedRows.filter(filterFn);
        }

        // Update counts
        if (countBadge) countBadge.textContent = pendingRows.length;
        if (reviewedCountBadge) reviewedCountBadge.textContent = reviewedRows.length;
        
        const totalCount = pendingRows.length + reviewedRows.length;
        if (kpiEval) kpiEval.textContent = totalCount;
        
        const globalBadge = document.getElementById('admin-dash-eval-count');
        if (globalBadge) globalBadge.textContent = totalCount;
        const workflowBadge = document.getElementById('admin-dash-eval-count');
        if (workflowBadge) workflowBadge.textContent = totalCount;

        // Populate Pending Table
        if (pendingRows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-5 text-muted small">No evaluated reports found.</td></tr>`;
        } else {
            tbody.innerHTML = pendingRows.map(r => `
                <tr>
                    <td class="ps-4 py-3 fw-medium small" style="max-width:300px;">${r.title}</td>
                    <td class="small text-muted">${r.agency || '—'}</td>
                    <td>${_divBadge(r.division)}</td>
                    <td class="text-center pe-4">
                        ${isPdipbdStaff ? 
                            `<a href="/project-assessment-reports/${r.id}/edit" class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-sm" style="color:#154A9A; border:none; font-size:0.7rem;">View</a>` :
                            `<button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold refer-pdipbd-btn shadow-sm" data-id="${r.id}" data-title="${r.title}" data-agency="${r.agency}" style="background:linear-gradient(135deg,#154A9A,#1e6fd9); border:none;"><i data-lucide="send" width="13" class="me-1"></i> Refer to PDIPBD</button>`
                        }
                    </td>
                </tr>
            `).join('');
        }

        // Populate Reviewed Table (Admins only)
        if (reviewedTbody) {
            if (reviewedRows.length === 0) {
                reviewedTbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted small">No reviewed reports ready for finalization.</td></tr>`;
            } else {
                reviewedTbody.innerHTML = reviewedRows.map(r => `
                    <tr>
                        <td class="ps-4 py-3 fw-medium small" style="max-width:300px;">${r.title}</td>
                        <td class="small text-muted">${r.agency || '—'}</td>
                        <td>${_divBadge(r.division)}</td>
                        <td class="text-end pe-4">
                            <a href="/project-assessment-reports/${r.id}/edit" class="btn btn-sm btn-light rounded-pill px-3 fw-bold shadow-sm" 
                                style="color:#154A9A; border:none; font-size:0.7rem;">
                                Finalize
                            </a>
                        </td>
                    </tr>
                `).join('');
            }
        }

        function _divBadge(div) {
            const map = { 'PFPD': ['#dbeafe', '#1e40af'], 'PMED': ['#bfdbfe', '#1d4ed8'], 'DRD':  ['#e0f2fe', '#0369a1'] };
            const [bg, color] = map[div] || ['#f1f5f9', '#334155'];
            return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.7rem;padding:0.3em 0.75em;">${div || '—'}</span>`;
        }

        if (window.lucide) window.lucide.createIcons();
        _attachHandlers();
    }

    async function _loadPdipbStaff() {
        // Try to get staff from the global variable first
        const serverUsers = Array.isArray(window.__ADMIN_DASHBOARD_USERS__)
            ? window.__ADMIN_DASHBOARD_USERS__
            : [];

        if (serverUsers.length) {
            return serverUsers.filter(u =>
                String(u.role || '').toLowerCase() === 'staff' &&
                String(u.division || '').toUpperCase() === 'PDIPBD'
            );
        }

        // Fallback to localforage
        const allUsers = await localforage.getItem('system_users') || [];
        return allUsers.filter(u =>
            u.role === 'staff' &&
            (u.division || '').toUpperCase().includes('PDIP')
        );
    }

    function _attachHandlers() {
        tbody.querySelectorAll('.refer-pdipbd-btn').forEach(btn => {
            btn.onclick = async () => {
                const id = btn.dataset.id;
                const title = btn.dataset.title;
                const agency = btn.dataset.agency;
                
                // Show the modal (similar to Step 1)
                _showParReferralModal(id, title, agency);
            };
        });
    }

    async function _showParReferralModal(parId, title, agency) {
        // Clean up existing
        const old = document.getElementById('referParToPdipbModal');
        if (old) {
            bootstrap.Modal.getInstance(old)?.dispose();
            old.remove();
        }

        const staffList = await _loadPdipbStaff();
        const staffOptions = staffList.map(u => 
            `<option value="${u.id}" data-name="${u.name}">${u.name} — ${u.email || ''}</option>`
        ).join('') || '<option value="" disabled>No PDIPB staff registered</option>';

        const modalHtml = `
        <div class="modal fade" id="referParToPdipbModal" tabindex="-1" aria-hidden="true">
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
                                <h5 class="modal-title fw-bold mb-0" style="font-size:1rem;">Refer Evaluated PAR</h5>
                                <p class="mb-0 text-muted" style="font-size:0.75rem;">Forward assessment results to PDIPBD</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4 pb-3 pt-3">
                        <div class="p-3 rounded-3 mb-3 d-flex align-items-start gap-3" style="background:#f0f7ff; border:1px solid #bfdbfe;">
                            <i data-lucide="file-check" width="16" class="text-primary mt-1 flex-shrink-0"></i>
                            <div>
                                <div class="text-muted fw-bold text-uppercase mb-1" style="font-size:0.62rem;letter-spacing:.06em;">Evaluated PAR</div>
                                <div class="fw-bold text-dark lh-sm" style="font-size:0.85rem;">${title}</div>
                                <div class="text-muted mt-1" style="font-size:0.7rem;">${agency}</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-semibold text-dark mb-2 d-block">Assign PDIPB Staff <span class="text-danger">*</span></label>
                            <select id="referParPdipbStaff" class="form-select border-0 rounded-3 bg-light" style="font-size:0.85rem;">
                                <option value="">— Select staff member —</option>
                                ${staffOptions}
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="small fw-semibold text-dark mb-2 d-block">Integration Notes <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea id="referParPdipbNotes" rows="3" class="form-control border-0 rounded-3 bg-light" style="font-size:0.85rem; resize:none;" placeholder="Notes for SecCom presentation preparation..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-2 d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm rounded-pill px-4 fw-medium bg-light text-muted border-0" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm rounded-pill px-5 fw-semibold text-white border-0" id="confirmReferParBtn" style="background:linear-gradient(135deg,#154A9A,#1e6fd9);">
                            <i data-lucide="send" width="13" class="me-1"></i> Confirm Referral
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        if (window.lucide) window.lucide.createIcons();
        const modal = new bootstrap.Modal(document.getElementById('referParToPdipbModal'));
        modal.show();

            document.getElementById('confirmReferParBtn').onclick = async () => {
            const staffId = document.getElementById('referParPdipbStaff').value;
            const staffName = document.getElementById('referParPdipbStaff').selectedOptions[0]?.dataset.name;
            const notes = document.getElementById('referParPdipbNotes').value;

            if (!staffId) {
                alert('Please select a staff member.');
                return;
            }

                const assessments = await localforage.getItem('project_assessments') || [];
                const idx = assessments.findIndex(p => p.id === parId);
                let linkedSubmissionId = null;
                if (idx >= 0) {
                    // assessments[idx].status = 'Referred to PDIPBD'; // User requested NOT to change status
                    assessments[idx].isReferredToPdipbd = true;
                    assessments[idx].referredToPdipbdDate = new Date().toISOString();
                    assessments[idx].pdipbdAssignedStaffId = staffId;
                    assessments[idx].pdipbdAssignedStaffName = staffName;
                    assessments[idx].pdipbdNotes = notes;
                    // try to capture connected submission id if present on PAR record
                    linkedSubmissionId = assessments[idx].connectedCteId || assessments[idx].submissionId || assessments[idx].cppSubmissionId || null;
                    await localforage.setItem('project_assessments', assessments);
                }

                // Also save a referral record locally using the same structure as other referral loaders
                try {
                    const currentUser = (window.__CURRENT_USER__ || {});
                    const referrals = await (localforage.getItem('project_referrals') || []);
                    const newLocalRef = {
                        id: `REF-${Date.now()}`,
                        parId: parId,
                        cteId: linkedSubmissionId || parId,
                        submissionId: linkedSubmissionId || parId,
                        submissionTitle: title,
                        projectTitle: title,
                        agency: agency || (assessments[idx] && (assessments[idx].proponent || assessments[idx].agency)) || '—',
                        fromUserName: currentUser.email || currentUser.name || 'Admin',
                        fromDivisionName: currentUser.division || 'Admin',
                        referredToDivision: 'PDIPBD',
                        referralDate: new Date().toISOString(),
                        status: 'Assigned',
                        stage: 'Project Appraisal',
                        toUserName: staffName || '',
                        toUserId: staffId || null,
                        notes: notes || ''
                    };

                    referrals.unshift(newLocalRef);
                    await localforage.setItem('project_referrals', referrals);
                } catch (e) {
                    console.error('Failed to save local referral', e);
                }
                    // Also POST referral to server so it is persisted in DB — but only if we can resolve a cpp submission id
                    try {
                        let submissionIdToSend = linkedSubmissionId || null;

                        if (!submissionIdToSend) {
                            // Try to find a matching cpp_submission locally by title or project_title
                            const cppSubs = await localforage.getItem('cpp_submissions') || [];
                            const t = (title || '').toLowerCase().trim();
                            const found = cppSubs.find(s => {
                                const sTitle = (s.project_title || s.title || (s.formData && s.formData.projectTitle) || '').toLowerCase();
                                return t && sTitle && sTitle.includes(t);
                            });
                            if (found) submissionIdToSend = found.id;
                        }

                        if (!submissionIdToSend) {
                            const msg = 'The selected cipg submission id is invalid; referral saved locally only.';
                            if (window.showSimpleAlert) window.showSimpleAlert(msg, 'warning'); else alert(msg);
                            console.warn('Skipping server POST: no submission id found for PAR', parId, title);
                        } else {
                            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            const payload = {
                                cipg_submission_id: submissionIdToSend,
                                to_user_id: staffId,
                                notes: notes
                            };

                            const resp = await fetch('/referrals', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify(payload)
                            });

                            const result = await resp.json().catch(() => ({}));
                            if (!resp.ok || !result.success) {
                                const msg = result.message || 'Failed to create referral on server.';
                                if (window.showSimpleAlert) window.showSimpleAlert(msg, 'error'); else alert(msg);
                            } else {
                                // Merge server response into local referral (update temp id with server id)
                                try {
                                    const referrals = await localforage.getItem('project_referrals') || [];
                                    const idx = referrals.findIndex(r => r.id === newLocalRef.id);
                                    if (idx >= 0 && result.id) {
                                        referrals[idx].id = String(result.id);
                                        await localforage.setItem('project_referrals', referrals);
                                    }
                                } catch (e) {
                                    console.error('Failed to update local referral id', e);
                                }

                                if (window.showSimpleAlert) window.showSimpleAlert('PAR successfully referred to PDIPBD.', 'success');
                            }
                        }
                    } catch (err) {
                        console.error('Referral POST failed', err);
                        if (window.showSimpleAlert) window.showSimpleAlert('Failed to persist referral on server.', 'error');
                    }

                    modal.hide();
                    render();
        };
    }

    if (searchInput) {
        searchInput.oninput = (e) => render(e.target.value);
    }

    render();
};
