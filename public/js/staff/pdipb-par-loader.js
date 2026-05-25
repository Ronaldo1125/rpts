/**
 * PDIPB Staff — Referred & Reviewed PARs Dashboard Loader
 */

export async function initPdipbParLoader(
    pendingTbodyId = 'dashboard-par-tbody',
    reviewedTbodyId = 'dashboard-reviewed-par-tbody',
    revisionTbodyId = 'dashboard-revision-tbody',
    sectoralTbodyId = 'dashboard-sectoral-presentation-tbody',
    rdcTbodyId = 'dashboard-rdc-presentation-tbody'
) {
    // Disable JS rendering for tables now handled by Blade
    const pendingTbody = document.getElementById(pendingTbodyId);
    const reviewedTbody = document.getElementById(reviewedTbodyId);

    const revisionTbody = document.getElementById(revisionTbodyId);
    const sectoralTbody = document.getElementById(sectoralTbodyId);
    const rdcTbody = document.getElementById(rdcTbodyId);

    // Proceed even if tbodys are null because we need to calculate and set the KPI badges on initial load.

    // ── Load & Render ─────────────────────────────────────────────────────────
    async function render() {
        console.log('[PDIPB-PAR-Loader] Starting render...');

        // Merge DB and Local assessments
        const dbAssessments = window.__EVALUATED_REPORTS__ || [];
        const localAssessments = await localforage.getItem('project_assessments') || [];

        // Use Map to deduplicate by ID, DB takes precedence
        const assessmentsMap = new Map();
        localAssessments.forEach(p => assessmentsMap.set(String(p.id), p));
        dbAssessments.forEach(p => assessmentsMap.set(String(p.id), p));

        const allAssessments = Array.from(assessmentsMap.values());

        console.log(`[PDIPB-PAR-Loader] Total unique assessments: ${allAssessments.length}`);

        const referrals = await localforage.getItem('project_referrals') || [];

        const allSubs = (window.__ADMIN_DASHBOARD_SUBMISSIONS__ && window.__ADMIN_DASHBOARD_SUBMISSIONS__.length > 0)
            ? window.__ADMIN_DASHBOARD_SUBMISSIONS__
            : await localforage.getItem('cpp_submissions') || [];

        // 1. Technical Reports (PARs)
        let allReferred = allAssessments.filter(p =>
            p.isReferredToPdipbd === true ||
            ['Evaluated', 'Reviewed', 'Final', 'Sectoral Presentation'].includes(p.status)
        );

        function _linkedSubmissionForPar(par) {
            if (!par?.connectedCteId) return null;
            const ref = referrals.find(r => (r.submissionId === par.connectedCteId || r.cteId === par.connectedCteId));
            const sid = ref?.submissionId || ref?.cteId;
            if (sid) return allSubs.find(s => s.id === sid) || null;
            return allSubs.find(s => s.id === par.connectedCteId || s.cteId === par.connectedCteId) || null;
        }

        function _norm(x) {
            return String(x || '').trim().toLowerCase();
        }

        /** CPP must explicitly be in Project Appraisal (stage / cppStage). */
        function _submissionIsProjectAppraisalStage(sub) {
            if (!sub) return false;
            const stages = [sub.stage, sub.cppStage, sub.projectStage].map(_norm);
            return stages.some(s => s === 'project appraisal');
        }

        /**
         * "Technical Reports for Review": PAR referred to PDIPBD AND linked CPP is in Project Appraisal.
         */
        /**
         * "Technical Reports for Review": PAR referred to PDIPBD OR in 'Evaluated' status.
         */
        function _eligibleTechnicalReportPending(par) {
            const parSt = String(par.status || '').trim();

            // If it's Evaluated, it's definitely for review
            if (parSt === 'Evaluated') return true;

            // Legacy/Referral fallback
            if (par.isReferredToPdipbd === true) {
                if (['Reviewed', 'Final', 'Approved'].includes(parSt)) return false;
                if (['Sectoral Presentation', 'Sectoral Committee'].includes(parSt)) return false;
                return true;
            }

            return false;
        }

        function _linkedSubmissionForPar(par) {
            const sid = par.cpp_submission_id || par.connectedCteId;
            if (!sid) return null;
            return allSubs.find(s => s.id == sid) || null;
        }

        const dbPending = dbAssessments.filter(p => p.status === 'Evaluated' && p.isReferredToPdipbd === true);
        const legacyPending = allAssessments.filter(p => _eligibleTechnicalReportPending(p) && p.status !== 'Evaluated');

        const pendingRows = [...dbPending, ...legacyPending];
        const reviewedRows = allReferred.filter(p => p.status === 'Reviewed');

        console.log(`[PDIPB-PAR-Loader] Pending rows for review: ${pendingRows.length}`);
        console.log(`[PDIPB-PAR-Loader] Reviewed rows for final: ${reviewedRows.length}`);

        const evalWorkflowBadge = document.getElementById('staff-dash-eval-count');
        if (evalWorkflowBadge) evalWorkflowBadge.textContent = pendingRows.length + reviewedRows.length;

        renderTable(pendingTbody, pendingRows, 'No referred technical reports yet.', referrals);
        renderTable(reviewedTbody, reviewedRows, 'No technical reports ready for finalization.', referrals);

        // 2. Revision Submissions (Submissions referred by Admin)
        const currentUser = (window.__CURRENT_USER__ || {});
        const revisionRows = allSubs.filter(s => {
            const st = String(s.status || s.projectStatus || '').toLowerCase();
            const isRevised = st === 'revised';
            if (!isRevised) return false;

            // Check if there's an active referral for revision review assigned to me
            const hasActiveRevisionReferral = (s.referrals || []).some(r => {
                const isCorrectStatus = r.status === 'For Revision Review';
                const isNotResolved = !r.resolved_at;
                const isAssignedToMe = String(r.to_user_id) === String(currentUser.id) || 
                                       (r.to_user_email || '').toLowerCase() === (currentUser.email || '').toLowerCase();
                return isCorrectStatus && isNotResolved && isAssignedToMe;
            });

            return hasActiveRevisionReferral;
        });

        renderRevisionTable(revisionTbody, revisionRows, 'No revised submissions referred by admin yet.', referrals, allAssessments);
        const revisionWorkflowBadge = document.getElementById('staff-dash-revised-count');
        if (revisionWorkflowBadge) revisionWorkflowBadge.textContent = revisionRows.length;

        // 3. Sectoral Presentation Referrals from Admin
        const mySectoralRows = allSubs.filter(s => {
            const statusNorm = String(
                s.projectStatus || s.status || s.submissionStatus || s.cppStatus || ''
            ).trim().toLowerCase();
            if (statusNorm !== 'sectoral presentation' && statusNorm !== 'seccom presentation') return false;
            
            const refs = Array.isArray(s.referrals) ? s.referrals : [];
            const combinedRefs = [...refs, ...referrals.filter(r => r.submissionId == s.id || r.cteId == s.id)];

            // Check if there's an active referral for Sectoral Presentation Review assigned to me
            const hasActiveSectoralReferral = combinedRefs.some(r => {
                const isCorrectStage = String(r.stage || '').toLowerCase() === 'sectoral committee';
                const isCorrectStatus = r.status === 'Sectoral Presentation Review';
                const isNotResolved = !r.resolved_at;
                const isAssignedToMe = String(r.to_user_id) === String(currentUser.id) || 
                                       (r.to_user_email || '').toLowerCase() === (currentUser.email || '').toLowerCase();
                return isCorrectStage && isCorrectStatus && isNotResolved && isAssignedToMe;
            });

            return hasActiveSectoralReferral;
        });

        renderSectoralPresentationTable(sectoralTbody, mySectoralRows, 'No sectoral presentation referrals found.', referrals);
        const sectoralWorkflowBadge = document.getElementById('staff-dash-sectoral-count');
        if (sectoralWorkflowBadge) sectoralWorkflowBadge.textContent = mySectoralRows.length;

        const myRdcRows = allSubs.filter(s => {
            const stageNorm = String(s.stage || s.cppStage || s.projectStage || '').trim().toLowerCase();
            const statusNorm = String(s.projectStatus || s.status || s.submissionStatus || s.cppStatus || '').trim().toLowerCase();
            if (stageNorm !== 'rdc') return false;
            if (statusNorm !== 'rdc presentation') return false;

            const refs = Array.isArray(s.referrals) ? s.referrals : [];
            const combinedRefs = [...refs, ...referrals.filter(r => r.submissionId == s.id || r.cteId == s.id)];

            // Check if there's an active RDC Presentation referral assigned to me
            const hasActiveRdcReferral = combinedRefs.some(r => {
                const isCorrectStatus = r.status === 'RDC Presentation Review';
                const isNotResolved = !r.resolved_at;
                const isAssignedToMe = String(r.to_user_id) === String(currentUser.id) ||
                                       (r.to_user_email || '').toLowerCase() === (currentUser.email || '').toLowerCase();
                return isCorrectStatus && isNotResolved && isAssignedToMe;
            });

            return hasActiveRdcReferral;
        });

        renderRdcPresentationTable(rdcTbody, myRdcRows, 'No RDC presentation referrals found.', referrals);
        const rdcWorkflowBadge = document.getElementById('staff-dash-rdc-pres-count');
        if (rdcWorkflowBadge) rdcWorkflowBadge.textContent = myRdcRows.length;

        if (window.lucide) window.lucide.createIcons();
        _attachHandlers();
    }

    function renderSectoralPresentationTable(tbody, rows, emptyMsg, referrals = []) {
        if (!tbody) return;
        if (rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small">${emptyMsg}</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(s => {
            const ref = referrals.find(r => r.submissionId === s.id || (s.cteId && r.cteId === s.cteId));
            const date = ref ? (ref.referredToPdipbdDate || ref.dateReferred || ref.referralDate) : (s.referralDate || s.dateReferred || s.date);
            const statusBadge = _statusPill('Sectoral Presentation', '#eff6ff', '#1d4ed8');

            return `<tr>
                <td class="ps-4 py-3 fw-bold text-dark" style="max-width:240px; font-size:0.8rem;">${s.title || s.formData?.['f-title'] || 'Untitled'}</td>
                <td class="small text-muted">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(s.agency || s.formData?.['f-agency']) : (s.agency || s.formData?.['f-agency'] || '—')}</td>
                <td>${statusBadge}</td>
                <td class="small text-muted">${date ? new Date(date).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' }) : '—'}</td>
                <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold sectoral-presentation-view"
                            data-id="${s.id}"
                            style="color:#154A9A; border:none; font-size:0.7rem;">
                            View
                        </button>
                        <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold referral-reject-btn"
                            data-sub-id="${s.id}"
                            data-context="sectoral"
                            data-title="${(s.title || s.formData?.['f-title'] || 'Untitled').replace(/"/g, '&quot;')}"
                            style="font-size:0.7rem;">
                            Reject
                        </button>
                </td>
            </tr>`;
        }).join('');
    }

    function _statusPill(text, bg, color) {
        return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.65rem;padding:0.3em 0.75em;">${text}</span>`;
    }

    function renderRdcPresentationTable(tbody, rows, emptyMsg, referrals = []) {
        if (!tbody) return;
        if (rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small">${emptyMsg}</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(s => {
            const ref = referrals.find(r => r.submissionId === s.id || (s.cteId && r.cteId === s.cteId));
            const date = ref ? (ref.referredToPdipbdDate || ref.dateReferred || ref.referralDate) : (s.referralDate || s.dateReferred || s.date);
            const statusBadge = _statusPill('RDC Presentation', '#fee2e2', '#b91c1c');

            return `<tr>
                <td class="ps-4 py-3 fw-bold text-dark" style="max-width:240px; font-size:0.8rem;">${s.title || s.formData?.['f-title'] || 'Untitled'}</td>
                <td class="small text-muted">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(s.agency || s.formData?.['f-agency']) : (s.agency || s.formData?.['f-agency'] || '—')}</td>
                <td>${statusBadge}</td>
                <td class="small text-muted">${date ? new Date(date).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' }) : '—'}</td>
                <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold rdc-presentation-view"
                            data-id="${s.id}"
                            style="color:#7f1d1d; border:none; font-size:0.7rem;">
                            View
                        </button>
                        <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold referral-reject-btn"
                            data-sub-id="${s.id}"
                            data-context="rdc"
                            data-title="${(s.title || s.formData?.['f-title'] || 'Untitled').replace(/"/g, '&quot;')}"
                            style="font-size:0.7rem;">
                            Reject
                        </button>
                </td>
            </tr>`;
        }).join('');
    }

    function renderRevisionTable(tbody, rows, emptyMsg, referrals, assessments) {
        if (!tbody) return;
        if (rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small">${emptyMsg}</td></tr>`;
            return;
        }

        tbody.innerHTML = rows.map(s => {
            const ref = referrals.find(r => (r.submissionId === s.id || (s.cteId && r.cteId === s.cteId)));
            const par = assessments.find(p => p.connectedCteId === s.id || (s.cteId && p.connectedCteId === s.cteId));
            const date = ref ? (ref.referredToPdipbdDate || ref.dateReferred) : (s.dateRevised || s.date);

            const stageValue = s.cppStage || s.stage || s.projectStage || s.projectStatus || s.status || '—';
            let stageHtml = _statusPill(stageValue, '#f1f5f9', '#334155');
            if (stageValue === 'Sectoral Committee') stageHtml = _statusPill('Sectoral Committee', '#eff6ff', '#1d4ed8');
            if (stageValue === 'Finalization') stageHtml = _statusPill('Finalization', '#dcfce7', '#166534');
            if (stageValue === 'Project Appraisal') stageHtml = _statusPill('Project Appraisal', '#e0f2fe', '#0369a1');

            return `<tr>
                <td class="ps-4 py-3 fw-bold text-dark" style="max-width:240px; font-size:0.8rem;">${s.title || s.formData?.['f-title'] || 'Untitled'}</td>
                <td class="small text-muted">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(s.agency || s.formData?.['f-agency']) : (s.agency || s.formData?.['f-agency'] || '—')}</td>
                <td>${stageHtml}</td>
                <td class="small text-muted">${date ? new Date(date).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' }) : '—'}</td>
                <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold revision-dashboard-view"
                            data-id="${s.id}"
                            style="color:#154A9A; border:none; font-size:0.7rem;">
                            View
                        </button>
                        <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold referral-reject-btn"
                            data-sub-id="${s.id}"
                            data-context="revised"
                            data-title="${(s.title || s.formData?.['f-title'] || 'Untitled').replace(/"/g, '&quot;')}"
                            style="font-size:0.7rem;">
                            Reject
                        </button>
                </td>
            </tr>`;
        }).join('');
    }

    function renderTable(tbody, rows, emptyMsg, referrals = []) {
        if (!tbody) return;
        if (rows.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small">${emptyMsg}</td></tr>`;
            return;
        }

        function _divBadge(div) {
            const map = { 'PFPD': ['#dbeafe', '#1e40af'], 'PMED': ['#bfdbfe', '#1d4ed8'], 'DRD': ['#e0f2fe', '#0369a1'] };
            const [bg, color] = map[div] || ['#f1f5f9', '#334155'];
            return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.65rem;padding:0.3em 0.75em;">${div || '—'}</span>`;
        }

        tbody.innerHTML = rows.slice(0, 10).map(p => {
            const parId = p.id || '';
            const title = p.submission?.project_title || p.projectTitle || 'Untitled';
            const agency = p.submission?.user?.agency?.agency_name || p.proponent || '—';

            // Format date (use created_at for database records if status-specific date missing)
            let dateVal = p.status === 'Reviewed' ? (p.dateReviewed || p.datePrepared) : (p.referredToPdipbdDate || p.datePrepared || p.created_at || '');
            const dateStr = dateVal ? new Date(dateVal).toLocaleDateString('en-PH', { month: 'short', day: 'numeric' }) : '—';

            const ref = referrals.find(r => r.cteId === p.connectedCteId || (p.cpp_submission_id && r.submissionId == p.cpp_submission_id));
            const division = ref ? ref.referredToDivision : (p.evaluatingDivision || '—');

            return `<tr>
                <td class="ps-4 py-3 fw-bold text-dark" style="max-width:240px; font-size:0.8rem;">
                    <div class="text-truncate" title="${title}">${title}</div>
                </td>
                <td class="small text-muted">${agency}</td>
                <td>${p.status === 'Reviewed' ? '<span class="badge bg-success-subtle text-success border-success-subtle px-2 py-1 rounded-pill" style="font-size:0.65rem;">Reviewed</span>' : _divBadge(division)}</td>
                <td class="small text-muted">${dateStr}</td>
                <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold par-dashboard-view"
                            data-id="${parId}"
                            style="color:#154A9A; border:none; font-size:0.7rem;">
                            View
                        </button>
                        <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold referral-reject-btn"
                            data-sub-id="${p.cpp_submission_id || ''}"
                            data-context="par"
                            data-title="${title.replace(/"/g, '&quot;')}"
                            style="font-size:0.7rem;">
                            Reject
                        </button>
                        ${(() => {
                            const attachments = Array.isArray(p.submission?.media || p.media) ? (p.submission?.media || p.media).map(m => ({
                                name: m.file_name,
                                url: m.original_url || m.url,
                                type: m.custom_properties?.type,
                                label: m.custom_properties?.label
                            })) : [];
                            return `<button class="btn btn-sm btn-light rounded-pill px-3 fw-bold attachments-sub" 
                                data-id="${parId}" 
                                data-title="${title.replace(/"/g, '&quot;')}"
                                data-attachments='${JSON.stringify(attachments).replace(/'/g, "&apos;")}'
                                style="color:#64748b; border:none; font-size:0.7rem;">
                                <i data-lucide="paperclip" width="12"></i></button>`;
                        })()}
                </td>
            </tr>`;
        }).join('');
    }

    function _attachHandlers() {
        document.querySelectorAll('.par-dashboard-view').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                sessionStorage.setItem('par_edit_id', btn.dataset.id);
                sessionStorage.setItem('pdipb_hide_submit_comments', '1');
                if (window.switchPage) window.switchPage('project-assessment-report-form');
            });
        });

        document.querySelectorAll('.revision-dashboard-view').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const sid = btn.dataset.id;
                window.location.href = `/v2/cipg_submissions/${sid}/show`;
            });
        });

        document.querySelectorAll('.sectoral-presentation-view').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const sid = btn.dataset.id;
                window.location.href = `/v2/cipg_submissions/${sid}/show`;
            });
        });

        document.querySelectorAll('.rdc-presentation-view').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const sid = btn.dataset.id;
                window.location.href = `/v2/cipg_submissions/${sid}/show`;
            });
        });

        // ── Reject handler for all four stages ───────────────────────────────
        document.querySelectorAll('.referral-reject-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const subId   = btn.dataset.subId;
                const context = btn.dataset.context;
                const title   = btn.dataset.title || 'this submission';
                _showRejectModal(subId, context, title, btn);
            });
        });
    }

    function _showRejectModal(subId, context, title, triggerBtn) {
        const existingModal = document.getElementById('referral-reject-modal');
        if (existingModal) existingModal.remove();

        const contextLabels = { par: 'PAR Assessment', revised: 'Revised Submission', sectoral: 'SecCom Presentation', rdc: 'RDC Presentation' };
        const label = contextLabels[context] || context;

        const modal = document.createElement('div');
        modal.id = 'referral-reject-modal';
        modal.innerHTML = `
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:9999;display:flex;align-items:center;justify-content:center;">
          <div class="bg-white rounded-4 shadow-lg p-4" style="width:100%;max-width:480px;">
            <div class="d-flex align-items-center gap-2 mb-3">
              <div class="bg-danger bg-opacity-10 p-2 rounded-3">
                <i data-lucide="x-circle" width="20" style="color:#dc2626;"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-0 text-dark">Reject — ${label}</h6>
                <p class="text-muted small mb-0" style="font-size:0.75rem;">"${title}"</p>
              </div>
            </div>
            <div class="alert alert-warning border-0 rounded-3 small py-2 px-3 mb-3" style="background:#fffbeb;color:#92400e;">
              This will mark the referral as <strong>Rejected</strong> and return the submission for referral again.
            </div>
            <label class="small fw-semibold text-secondary text-uppercase mb-1" style="font-size:0.65rem;letter-spacing:0.05em;">Reason for Rejection (optional)</label>
            <textarea id="reject-reason-input" class="form-control border-0 bg-light rounded-3 mb-3" rows="3" placeholder="Enter reason or leave blank..."></textarea>
            <div class="d-flex gap-2 justify-content-end">
              <button id="reject-cancel-btn" class="btn btn-light rounded-pill px-4 fw-semibold small">Cancel</button>
              <button id="reject-confirm-btn" class="btn btn-danger rounded-pill px-4 fw-bold small">Confirm Rejection</button>
            </div>
          </div>
        </div>`;
        document.body.appendChild(modal);
        if (window.lucide) window.lucide.createIcons({ nodes: [modal] });

        modal.querySelector('#reject-cancel-btn').onclick = () => modal.remove();
        modal.querySelector('#reject-confirm-btn').onclick = async () => {
            const notes = modal.querySelector('#reject-reason-input').value.trim();
            const confirmBtn = modal.querySelector('#reject-confirm-btn');
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Rejecting...';

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
                const res = await fetch(`/referrals/reject/${subId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ context, notes }),
                });
                const data = await res.json();
                modal.remove();
                if (data.success) {
                    if (window.showSimpleAlert) window.showSimpleAlert(data.message, 'success');
                    // Remove the row from the table with a fade animation
                    const row = triggerBtn.closest('tr');
                    if (row) { row.style.opacity = '0'; row.style.transition = 'opacity 0.3s'; setTimeout(() => row.remove(), 300); }
                    // Decrement the KPI badge for the relevant workflow stage
                    const badgeIdMap = {
                        par:      'staff-dash-eval-count',
                        revised:  'staff-dash-revised-count',
                        sectoral: 'staff-dash-sectoral-count',
                        rdc:      'staff-dash-rdc-pres-count',
                    };
                    const badgeId = badgeIdMap[context];
                    if (badgeId) {
                        const badge = document.getElementById(badgeId);
                        if (badge) {
                            const current = parseInt(badge.textContent, 10) || 0;
                            badge.textContent = Math.max(0, current - 1);
                        }
                    }
                } else {
                    if (window.showSimpleAlert) window.showSimpleAlert(data.message || 'Failed to reject referral.', 'danger');
                }
            } catch (err) {
                modal.remove();
                if (window.showSimpleAlert) window.showSimpleAlert('An error occurred. Please try again.', 'danger');
            }
        };
    }

    render();
}

export async function initPdipbApprovedLoader() {
    const tbody = document.getElementById('rdcApprovedTableBody');
    const countBadge = document.getElementById('rdc-approved-list-count');
    if (!tbody) {
        setTimeout(() => initPdipbApprovedLoader(), 200);
        return;
    }

    const subs = (window.__ADMIN_DASHBOARD_SUBMISSIONS__ && window.__ADMIN_DASHBOARD_SUBMISSIONS__.length > 0)
        ? window.__ADMIN_DASHBOARD_SUBMISSIONS__
        : await window.localforage.getItem('cpp_submissions') || [];
        
    function _getSubmissionStatus(s) {
        return s.status || s.submissionStatus || s.cppStatus || s.projectStatus || 'Draft';
    }

    const currentUser = window.__CURRENT_USER__ || {};
    const userEmail = (currentUser.email || '').toLowerCase();
    
    const myApprovedRows = subs.filter(s => {
        if (!_getSubmissionStatus(s).includes('Approved')) return false;
        
        const refs = Array.isArray(s.referrals) ? s.referrals : [];
        const isAssigned = refs.some(r => String(r.to_user_id) === String(currentUser.id) || 
                               (r.to_user_email || '').toLowerCase() === userEmail);
                               
        return isAssigned;
    });

    if (countBadge) countBadge.textContent = myApprovedRows.length;
    if (myApprovedRows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted small">No RDC Approved submissions are currently assigned to you.</td></tr>`;
        return;
    }

    tbody.innerHTML = myApprovedRows.map(s => {
        const title  = s.title || s.formData?.['f-title'] || 'Untitled';
        const agency = s.agency || s.formData?.['f-agency'] || '—';
        const stage  = s.stage || s.cppStage || s.projectStatus || 'RDC';
        const status = _getSubmissionStatus(s);
        const updated = s.updatedAt || s.submittedAt ? new Date(s.updatedAt || s.submittedAt).toLocaleDateString() : '—';
        return `
            <tr data-sid="${s.id || ''}">
                <td class="fw-medium small py-3 ps-4" style="max-width:280px;">${title}</td>
                <td class="small text-muted py-3">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(agency) : agency}</td>
                <td class="small py-3">${stage}</td>
                <td class="small py-3"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small">${status}</span></td>
                <td class="small text-muted py-3 pe-4">${updated}</td>
            </tr>`;
    }).join('');

    if (window.lucide) window.lucide.createIcons();

    const searchInput = document.getElementById('rdcApprovedSearchInput');
    if (searchInput) {
        const fresh = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(fresh, searchInput);
        fresh.addEventListener('input', e => {
            const term = e.target.value.toLowerCase();
            tbody.querySelectorAll('tr').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });
    }
}
