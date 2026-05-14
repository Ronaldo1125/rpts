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

    if (!pendingTbody && !reviewedTbody && !revisionTbody && !sectoralTbody && !rdcTbody) {
        // Still run the other parts if available
        if (!revisionTbody && !sectoralTbody && !rdcTbody) return;
    }

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
                // Opened from Technical/Reviewed PAR queues: hide agency-submit in comments modal.
                sessionStorage.setItem('pdipb_hide_submit_comments', '1');
                if (window.switchPage) window.switchPage('project-assessment-report-form');
            });
        });

        document.querySelectorAll('.revision-dashboard-view').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const sid = btn.dataset.id;
                // Redirect to the cpp-view page for the revised submission
                // Pass view_context parameter to show PDIPBD staff buttons
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
    }

    render();
}
