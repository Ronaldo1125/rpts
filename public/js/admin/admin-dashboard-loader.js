import { initSubmissionsLoader } from '../agency/submissions-loader.js';

export async function initAdminDashboard() {
    try {
        // ── Current User ──
        let currentUser = {};
        try {
            currentUser = (window.__CURRENT_USER__ || {});
        } catch (e) {
            console.error('[AdminDashboard] LocalStorage parse error:', e);
        }
        const usernameEl = document.getElementById('admin-dash-username');
        if (usernameEl) usernameEl.textContent = currentUser.name || currentUser.email || 'Admin';

        // ── Helpers ──
        function _isWorkflowStatus(val) {
            return ['Draft','Submitted','Review','Approved','Validated','For Revision','Resubmitted','Revised','Incomplete','Rejected'].includes(val);
        }
        function _getSubmissionStatus(s) {
            if (s?.submissionStatus?.trim()) return s.submissionStatus;
            if (typeof s?.status === 'string' && _isWorkflowStatus(s.status)) return s.status;
            if (typeof s?.status === 'string' && s.status.trim()) return s.status;
            if (s?.projectStatus?.trim()) return s.projectStatus;
            if (s?.cppStatus?.trim()) return s.cppStatus;
            if (String(s?.id || '').startsWith('DRAFT-') || String(s?.id || '').startsWith('CPP-Draft-')) return 'Draft';
            return 'Submitted';
        }
        function _normalizeStatus(val) {
            return String(val || '').trim().toLowerCase();
        }
        function _hasSubmissionStatus(s, candidateStatuses) {
            const candidates = new Set(
                (candidateStatuses || []).map(_normalizeStatus).filter(Boolean)
            );
            const statusValues = [
                s?.submissionStatus,
                s?.status,
                s?.projectStatus,
                s?.cppStatus
            ];
            return statusValues.some(v => candidates.has(_normalizeStatus(v)));
        }
        function _isSectoralStageSubmission(s) {
            if (_hasSubmissionStatus(s, ['Sectoral Presentation', 'SecCom Presentation'])) return true;
            const stageValues = [s?.stage, s?.cppStage].map(_normalizeStatus);
            return stageValues.some(v => v.includes('sectoral') || v.includes('seccom'));
        }
        function _isRdcPresentationSubmission(s) {
            if (_hasSubmissionStatus(s, ['RDC Approved'])) return false;
            if (_hasSubmissionStatus(s, ['RDC Presentation'])) return true;
            const stageValues = [s?.stage, s?.cppStage, s?.projectStage].map(_normalizeStatus);
            return stageValues.some(v => v === 'rdc');
        }
        function _setReferPdipbStage(stageKey) {
            const modalEl = document.getElementById('referPdipbModal');
            if (!modalEl) return;
            let stageInput = document.getElementById('referPdipbStage');
            if (!stageInput) {
                stageInput = document.createElement('input');
                stageInput.type = 'hidden';
                stageInput.id = 'referPdipbStage';
                modalEl.appendChild(stageInput);
            }
            stageInput.value = stageKey || '';
        }
        function setEl(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        }

        async function _getAdminSubmissions() {
            const local = await localforage.getItem('cpp_submissions') || [];
            const serverSubmissions = Array.isArray(window.__ADMIN_DASHBOARD_SUBMISSIONS__) 
                ? window.__ADMIN_DASHBOARD_SUBMISSIONS__ 
                : [];

            if (serverSubmissions.length > 0) {
                // Merge: prioritizes local changes (like optimistic referral updates) 
                // over static server-injected data until the page is fully refreshed.
                const merged = new Map();
                serverSubmissions.forEach(s => merged.set(String(s.id), s));
                local.forEach(s => {
                    // Only merge if the record exists in server data (avoid orphaned drafts)
                    // and keep local version because it contains the new 'referred' flags
                    if (merged.has(String(s.id))) {
                        merged.set(String(s.id), s);
                    }
                });
                const finalSubmissions = Array.from(merged.values());
                
                // Keep localforage in sync (optional, but good for persistence)
                // await localforage.setItem('cpp_submissions', finalSubmissions); 
                return finalSubmissions;
            }

            return local;
        }

        async function _getPdipbStaff() {
            const serverUsers = Array.isArray(window.__ADMIN_DASHBOARD_USERS__)
                ? window.__ADMIN_DASHBOARD_USERS__
                : [];

            if (serverUsers.length) {
                return serverUsers.filter(u =>
                    String(u.role || '').toLowerCase() === 'staff' &&
                    String(u.division || '').toUpperCase() === 'PDIPBD'
                );
            }

            const allUsers = await localforage.getItem('system_users') || [];
            return allUsers.filter(u =>
                u.role === 'staff' &&
                (u.division || '').toUpperCase() === 'PDIPBD'
            );
        }



        // ── Fetch all submissions ONCE ──
        const allSubmissions = await _getAdminSubmissions();
        const nonDrafts = allSubmissions.filter(s => _getSubmissionStatus(s) !== 'Draft');

        console.log(`[AdminDashboard] Loaded ${allSubmissions.length} total, ${nonDrafts.length} non-drafts`);

        // ── KPI counts ──
        const reviewCount      = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Review').length;
        const revisionCount    = nonDrafts.filter(s => _getSubmissionStatus(s) === 'For Revision').length;
        const resubmittedCount = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Resubmitted' || _getSubmissionStatus(s) === 'Revised').length;
        const approvedCount    = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Approved').length;
        const rdcApprovedCount = nonDrafts.filter(s => _getSubmissionStatus(s) === 'RDC Approved').length;
        const rejectedCount    = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Rejected').length;

        setEl('admin-dash-proj-total',     allSubmissions.length);
        setEl('admin-dash-sub-review',     reviewCount);
        setEl('admin-dash-sub-approved',   approvedCount);
        setEl('admin-dash-sub-rejected',   rejectedCount);
        setEl('admin-dash-sub-revision',   revisionCount);
        
        // Get evaluated/reviewed PARs from global variables or localforage
        const serverEvaluatedPars = Array.isArray(window.__ADMIN_DASHBOARD_EVALUATED_PARS__)
            ? window.__ADMIN_DASHBOARD_EVALUATED_PARS__
            : [];
        const serverReviewedPars = Array.isArray(window.__ADMIN_DASHBOARD_REVIEWED_PARS__)
            ? window.__ADMIN_DASHBOARD_REVIEWED_PARS__
            : [];
        const localPARs = await localforage.getItem('project_assessments') || [];
        const allPARs = (serverEvaluatedPars.length > 0 || serverReviewedPars.length > 0)
            ? [...serverEvaluatedPars, ...serverReviewedPars]
            : localPARs;
        const evaluatedParsCount = allPARs.filter(p => p.status === 'Evaluated' && !p.isReferredToPdipbd).length;
        const reviewedParsCount = allPARs.filter(p => p.status === 'Reviewed').length;
        setEl('admin-dash-eval-count',     evaluatedParsCount + reviewedParsCount);
        
        // Initial count: real count of items waiting for PDIPB validation (Submitted, Resubmitted)
        const initialCount = nonDrafts.filter(s => {
            const st = _getSubmissionStatus(s);
            const stage = s.stage || s.cppStage || s.projectStatus;
            if (st === 'Submitted' && !s.referredToPdipb) return true;
            if (st === 'Resubmitted' && stage === 'Completeness Test and Validation') return true;
            return false;
        }).length;
        setEl('admin-dash-initial-count',  initialCount);
        
        // Referred count: match the table KPI for pending validated submissions
        const referredCount = nonDrafts.filter(s => {
            const status = _getSubmissionStatus(s);
            const stage = s.stage || s.cppStage || s.projectStatus;
            return status === 'Validated' && stage !== 'Project Appraisal';
        }).length;
        setEl('admin-dash-referred-count', referredCount);

        const allReferrals = await localforage.getItem('project_referrals') || [];
        
        const sectoralCount = nonDrafts.filter(s => {
            const stage = _normalizeStatus(s.stage || s.cppStage);
            const status = _normalizeStatus(_getSubmissionStatus(s));
            if (stage === 'sectoral committee' && status === 'sectoral presentation') {
                const refs = Array.isArray(s.referrals) ? s.referrals : [];
                const combinedRefs = [...refs, ...allReferrals.filter(r => r.submissionId == s.id || r.cteId == s.id)];
                const hasReferral = combinedRefs.some(r =>
                    _normalizeStatus(r.stage) === 'sectoral committee' &&
                    _normalizeStatus(r.status) === 'sectoral presentation review'
                );
                return !hasReferral;
            }
            return false;
        }).length;
        const rdcCount = nonDrafts.filter(s => {
            if (!_isRdcPresentationSubmission(s)) return false;
            
            const refs = Array.isArray(s.referrals) ? s.referrals : [];
            const combinedRefs = [...refs, ...allReferrals.filter(r => r.submissionId == s.id || r.cteId == s.id)];
            
            const hasReferral = combinedRefs.some(r =>
                _normalizeStatus(r.stage) === 'rdc' &&
                _normalizeStatus(r.status) === 'rdc presentation review'
            );
            return !hasReferral;
        }).length;

        setEl('admin-dash-sectoral-count', sectoralCount);
        setEl('admin-dash-findings-count', 0);
        // Revised count: match the revised submissions table KPI
        const revisedCount = nonDrafts.filter(s => {
            const status = _getSubmissionStatus(s).toLowerCase();
            return status === 'revised' && !s.referredToPdipb;
        }).length;
        setEl('admin-dash-revised-count', revisedCount);
        setEl('admin-dash-rdc-pres-count', rdcCount);
        setEl('admin-dash-approved-count', rdcApprovedCount);

        if (window.lucide) window.lucide.createIcons();


        // ── CIPG Table Renderer (self-contained, uses pre-fetched data or re-fetches for filter) ──
        async function renderCipgTable(sectorFilter) {
            sectorFilter = sectorFilter || 'all';

            // Always re-find the DOM elements (they may have been re-injected by toggleStepDetails)
            const tbody      = document.getElementById('cipgTableBody');
            const countBadge = document.getElementById('cipg-list-count');
            const theadRow   = tbody?.closest('table')?.querySelector('thead tr');

            if (!tbody) {
                console.warn('[AdminDashboard] #cipgTableBody not found — retrying in 200ms');
                setTimeout(() => renderCipgTable(sectorFilter), 200);
                return;
            }

            // ── Dynamic thead: show Sector column only when 'All' is active ──
            const showSector = sectorFilter === 'all';
            if (theadRow) {
                theadRow.innerHTML = showSector
                    ? `<th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                       <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                       <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Sector</th>
                       <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Sub-Sector</th>
                       <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Status</th>
                       <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>`
                    : `<th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                       <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                       <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Sub-Sector</th>
                       <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Status</th>
                       <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>`;
            }
            const colSpan = showSector ? 6 : 5;

            // Re-fetch to get freshest data each time
            const subs = await _getAdminSubmissions();
            // Only show freshly 'Submitted' or 'Resubmitted' projects that have NOT yet been referred
            let rows = subs.filter(s => {
                const st = _getSubmissionStatus(s);
                const stage = s.stage || s.cppStage || s.projectStatus;
                if (st === 'Submitted' && !s.referredToPdipb) return true;
                if (st === 'Resubmitted' && stage === 'Completeness Test and Validation') return true;
                return false;
            });

            if (sectorFilter !== 'all') {
                rows = rows.filter(s => {
                    const sec = (s.sector || s.formData?.['f-sector'] || '').toLowerCase();
                    if (sectorFilter === 'social')   return sec.includes('social');
                    if (sectorFilter === 'economic') return sec.includes('economic');
                    if (sectorFilter === 'infra')    return sec.includes('infra') || sec.includes('physical');
                    if (sectorFilter === 'insti')    return sec.includes('insti') || sec.includes('devt') || sec.includes('admin');
                    return true;
                });
            }

            console.log(`[AdminDashboard] Rendering table: ${rows.length} rows (filter: ${sectorFilter})`);
            if (countBadge) countBadge.textContent = rows.length;
            setEl('admin-dash-initial-count', rows.length);

            if (rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${colSpan}" class="text-center py-5"><p class="text-muted mb-1">No pending submissions</p><span class="small text-muted">All submitted CPP forms are currently under PDIPB review.</span></td></tr>`;
                return;
            }

            // ── Sector badge helper ──
            function _secBadge(raw) {
                const s = (raw || '').toLowerCase();
                let bg = '#f1f5f9', color = '#64748b';
                if (s.includes('social'))                              { bg = '#dbeafe'; color = '#1e3a8a'; }
                else if (s.includes('economic'))                       { bg = '#bfdbfe'; color = '#1d4ed8'; }
                else if (s.includes('infra') || s.includes('physical')){ bg = '#e0f2fe'; color = '#0369a1'; }
                else if (s.includes('insti') || s.includes('devt') || s.includes('admin')) { bg = '#ede9fe'; color = '#5b21b6'; }
                return `<span class="badge fw-semibold" style="background:${bg};color:${color};font-size:0.68rem;padding:0.3em 0.75em;border-radius:999px;">${raw || '—'}</span>`;
            }

            tbody.innerHTML = rows.map(s => {
                const title  = s.title  || s.formData?.['f-title']  || 'Untitled';
                const agency = s.agency || s.formData?.['f-agency'] || '—';
                const sec    = s.sector || s.formData?.['f-sector'] || '—';
                const abbr   = window.getAgencyAbbreviation ? window.getAgencyAbbreviation(agency) : agency;
                const sub    = s.formData?.['f-sub-sector']         || '—';
                const sid    = s.id || '';
                const st     = _getSubmissionStatus(s);

                return `<tr data-sid="${sid}">
                    <td class="fw-medium small py-3 ps-4" style="max-width:300px;">${title}</td>
                    <td class="small text-muted py-3">${abbr}</td>
                    ${showSector ? `<td class="py-3">${_secBadge(sec)}</td>` : ''}
                    <td class="small text-muted py-3">${sub}</td>
                    <td class="py-3"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 small">${st}</span></td>
                    <td class="text-center py-3 pe-4">
                        <button class="btn btn-sm rounded-pill fw-semibold px-3 cipg-refer-btn"
                            data-sid="${sid}"
                            data-title="${title.replace(/"/g, '&quot;')}"
                            data-agency="${agency.replace(/"/g, '&quot;')}"
                            style="background:#154A9A;color:#fff;border:none;font-size:0.75rem;">
                            <i data-lucide="send" width="13" class="me-1"></i>Refer
                        </button>
                    </td>
                </tr>`;
            }).join('');

            if (window.lucide) window.lucide.createIcons();

            // ── Wire "Refer" buttons → open referPdipbModal ──────────────────
            tbody.querySelectorAll('.cipg-refer-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    _setReferPdipbStage('submission');
                    // Populate basic modal fields
                    const titleEl  = document.getElementById('referPdipb-project-title');
                    const agencyEl = document.getElementById('referPdipb-agency');
                    const sidEl    = document.getElementById('referPdipbSid');
                    const notesEl  = document.getElementById('referPdipbNotes');
                    const staffSel = document.getElementById('referPdipbStaff');
                    const errEl    = document.getElementById('referPdipbStaffErr');

                    if (titleEl)  titleEl.textContent  = btn.dataset.title  || 'Untitled';
                    if (agencyEl) agencyEl.textContent = btn.dataset.agency || '—';
                    if (sidEl)    sidEl.value           = btn.dataset.sid   || '';
                    if (notesEl)  notesEl.value         = '';
                    if (errEl)    errEl.style.display   = 'none';

                    // Populate the PDIPB staff dropdown
                    if (staffSel) {
                        staffSel.innerHTML = '<option value="">— Select staff member —</option>';
                        const pdipbStaff = await _getPdipbStaff();

                        if (pdipbStaff.length === 0) {
                            staffSel.innerHTML = '<option value="" disabled>No PDIPB staff registered yet.</option>';
                        } else {
                            pdipbStaff.forEach(u => {
                                const opt = document.createElement('option');
                                opt.value         = u.id;
                                opt.dataset.name  = u.name;
                                opt.dataset.email = u.email || '';
                                opt.textContent   = `${u.name}${u.email ? ' — ' + u.email : ''}`;
                                staffSel.appendChild(opt);
                            });
                        }
                    }

                    const modalEl = document.getElementById('referPdipbModal');
                    if (modalEl) {
                        if (window.lucide) window.lucide.createIcons();
                        new bootstrap.Modal(modalEl).show();
                    }
                });
            });

            // ── Wire Confirm Referral button once for the shared refer modal ──
            _wireConfirmReferPdipbBtn();

            // Wire search input (re-bind every render to avoid stale elements)
            const searchInput = document.getElementById('cipgSearchInput');
            if (searchInput) {
                // Remove old listener by cloning
                const fresh = searchInput.cloneNode(true);
                searchInput.parentNode.replaceChild(fresh, searchInput);
                fresh.addEventListener('input', e => {
                    const term = e.target.value.toLowerCase();
                    document.querySelectorAll('#cipgTableBody tr').forEach(r => {
                        r.style.display = r.textContent.toLowerCase().includes(term) ? '' : 'none';
                    });
                });
            }
        }

        // ── Expose for sector pill buttons (switchSector in ui-utils.js calls this) ──
        // ── Expose for sector pill buttons (switchSector in ui-utils.js calls this) ──
        window.filterAdminCipgTable = renderCipgTable;

        async function renderSectoralStage() {
            const tbody = document.getElementById('sectoralTableBody');
            const countBadge = document.getElementById('sectoral-list-count');
            if (!tbody) {
                setTimeout(() => renderSectoralStage(), 200);
                return;
            }

            const subs = await _getAdminSubmissions();
            const allReferrals = await localforage.getItem('project_referrals') || [];
            const rows = subs.filter(s => {
                const stage = _normalizeStatus(s.stage || s.cppStage);
                const status = _normalizeStatus(_getSubmissionStatus(s));
                if (stage === 'sectoral committee' && status === 'sectoral presentation') {
                    const refs = Array.isArray(s.referrals) ? s.referrals : [];
                    const combinedRefs = [...refs, ...allReferrals.filter(r => r.submissionId == s.id || r.cteId == s.id)];
                    const hasReferral = combinedRefs.some(r =>
                        _normalizeStatus(r.stage) === 'sectoral committee' &&
                        _normalizeStatus(r.status) === 'sectoral presentation review'
                    );
                    return !hasReferral;
                }
                return false;
            });

            if (countBadge) countBadge.textContent = rows.length;
            if (rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted small">No sectoral presentation CPPs are currently pending referral to PDIPBD staff.</td></tr>`;
                return;
            }

            tbody.innerHTML = rows.map(s => {
                const title  = s.title || s.formData?.['f-title'] || 'Untitled';
                const agency = s.agency || s.formData?.['f-agency'] || '—';
                const sector = s.sector || s.formData?.['f-sector'] || '—';
                const status = _getSubmissionStatus(s);
                const sid    = s.id || '';

                return `
                    <tr data-sid="${sid}">
                        <td class="fw-medium small py-3 ps-4" style="max-width:280px;">${title}</td>
                        <td class="small text-muted py-3">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(agency) : agency}</td>
                        <td class="small py-3">${sector}</td>
                        <td class="py-3"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 small">${status}</span></td>
                        <td class="text-center py-3 pe-4">
                            <button class="btn btn-sm rounded-pill fw-semibold px-3 sectoral-refer-btn"
                                data-sid="${sid}"
                                data-title="${title.replace(/"/g, '&quot;')}"
                                data-agency="${agency.replace(/"/g, '&quot;')}"
                                style="background:#154A9A;color:#fff;border:none;font-size:0.75rem;">
                                <i data-lucide="send" width="13" class="me-1"></i>Refer
                            </button>
                        </td>
                    </tr>`;
            }).join('');

            if (window.lucide) window.lucide.createIcons();

            tbody.querySelectorAll('.sectoral-refer-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    _setReferPdipbStage('sectoral');
                    const titleEl  = document.getElementById('referPdipb-project-title');
                    const agencyEl = document.getElementById('referPdipb-agency');
                    const sidEl    = document.getElementById('referPdipbSid');
                    const notesEl  = document.getElementById('referPdipbNotes');
                    const staffSel = document.getElementById('referPdipbStaff');
                    const errEl    = document.getElementById('referPdipbStaffErr');

                    if (titleEl)  titleEl.textContent  = btn.dataset.title  || 'Untitled';
                    if (agencyEl) agencyEl.textContent = btn.dataset.agency || '—';
                    if (sidEl)    sidEl.value           = btn.dataset.sid   || '';
                    if (notesEl)  notesEl.value         = '';
                    if (errEl)    errEl.style.display   = 'none';

                    if (staffSel) {
                        staffSel.innerHTML = '<option value="">— Select staff member —</option>';
                        const pdipbStaff = await _getPdipbStaff();
                        if (pdipbStaff.length === 0) {
                            staffSel.innerHTML = '<option value="" disabled>No PDIPB staff registered yet.</option>';
                        } else {
                            pdipbStaff.forEach(u => {
                                const opt = document.createElement('option');
                                opt.value         = u.id;
                                opt.dataset.name  = u.name;
                                opt.dataset.email = u.email || '';
                                opt.textContent   = `${u.name}${u.email ? ' — ' + u.email : ''}`;
                                staffSel.appendChild(opt);
                            });
                        }
                    }

                    const modalEl = document.getElementById('referPdipbModal');
                    if (modalEl) {
                        if (window.lucide) window.lucide.createIcons();
                        new bootstrap.Modal(modalEl).show();
                    }
                });
            });

            const searchInput = document.getElementById('sectoralSearchInput');
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

        async function renderRdcPresentationStage() {
            const tbody = document.getElementById('rdcPresTableBody');
            const countBadge = document.getElementById('rdc-pres-list-count');
            if (!tbody) {
                setTimeout(() => renderRdcPresentationStage(), 200);
                return;
            }

            const subs = await _getAdminSubmissions();
            const allReferrals = await localforage.getItem('project_referrals') || [];
            const rows = subs.filter(s => {
                if (!_isRdcPresentationSubmission(s)) return false;
                
                const refs = Array.isArray(s.referrals) ? s.referrals : [];
                const combinedRefs = [...refs, ...allReferrals.filter(r => r.submissionId == s.id || r.cteId == s.id)];
                
                const hasReferral = combinedRefs.some(r =>
                    _normalizeStatus(r.stage) === 'rdc' &&
                    _normalizeStatus(r.status) === 'rdc presentation review'
                );
                return !hasReferral;
            });

            if (countBadge) countBadge.textContent = rows.length;
            if (rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted small">No RDC Presentation submissions are currently available.</td></tr>`;
                return;
            }

            tbody.innerHTML = rows.map(s => {
                const title  = s.title || s.formData?.['f-title'] || 'Untitled';
                const agency = s.agency || s.formData?.['f-agency'] || '—';
                const stage  = s.stage || s.cppStage || s.projectStatus || 'RDC';
                const status = 'RDC Presentation';
                const updated = s.updatedAt ? new Date(s.updatedAt).toLocaleDateString() : '—';
                return `
                    <tr data-sid="${s.id || ''}">
                        <td class="fw-medium small py-3 ps-4" style="max-width:280px;">${title}</td>
                        <td class="small text-muted py-3">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(agency) : agency}</td>
                        <td class="small py-3">${stage}</td>
                        <td class="small py-3"><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 small">${status}</span></td>
                        <td class="small text-muted py-3">${updated}</td>
                        <td class="text-center py-3 pe-4">
                            <button class="btn btn-sm rounded-pill fw-semibold px-3 rdc-presentation-refer-btn"
                                data-sid="${s.id || ''}"
                                data-title="${(title||'').replace(/"/g, '&quot;')}"
                                data-agency="${(agency||'').replace(/"/g, '&quot;')}"
                                style="background:#154A9A;color:#fff;border:none;font-size:0.75rem;">
                                <i data-lucide="send" width="13" class="me-1"></i>Refer
                            </button>
                        </td>
                    </tr>`;
            }).join('');

            if (window.lucide) window.lucide.createIcons();

            tbody.querySelectorAll('.rdc-presentation-refer-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    _setReferPdipbStage('rdc-pres');
                    const titleEl  = document.getElementById('referPdipb-project-title');
                    const agencyEl = document.getElementById('referPdipb-agency');
                    const sidEl    = document.getElementById('referPdipbSid');
                    const notesEl  = document.getElementById('referPdipbNotes');
                    const staffSel = document.getElementById('referPdipbStaff');
                    const errEl    = document.getElementById('referPdipbStaffErr');

                    if (titleEl)  titleEl.textContent  = btn.dataset.title  || 'Untitled';
                    if (agencyEl) agencyEl.textContent = btn.dataset.agency || '—';
                    if (sidEl)    sidEl.value           = btn.dataset.sid   || '';
                    if (notesEl)  notesEl.value         = '';
                    if (errEl)    errEl.style.display   = 'none';

                    if (staffSel) {
                        staffSel.innerHTML = '<option value="">— Select staff member —</option>';
                        const pdipbStaff = await _getPdipbStaff();

                        if (pdipbStaff.length === 0) {
                            staffSel.innerHTML = '<option value="" disabled>No PDIPB staff registered yet.</option>';
                        } else {
                            pdipbStaff.forEach(u => {
                                const opt = document.createElement('option');
                                opt.value         = u.id;
                                opt.dataset.name  = u.name;
                                opt.dataset.email = u.email || '';
                                opt.textContent   = `${u.name}${u.email ? ' — ' + u.email : ''}`;
                                staffSel.appendChild(opt);
                            });
                        }
                    }

                    const modalEl = document.getElementById('referPdipbModal');
                    if (modalEl) {
                        if (window.lucide) window.lucide.createIcons();
                        new bootstrap.Modal(modalEl).show();
                    }
                });
            });

            const searchInput = document.getElementById('rdcPresSearchInput');
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

        async function renderRdcApprovedStage() {
            const tbody = document.getElementById('rdcApprovedTableBody');
            const countBadge = document.getElementById('rdc-approved-list-count');
            if (!tbody) {
                setTimeout(() => renderRdcApprovedStage(), 200);
                return;
            }

            const subs = await _getAdminSubmissions();
            const rows = subs.filter(s => _getSubmissionStatus(s) === 'RDC Approved');

            if (countBadge) countBadge.textContent = rows.length;
            if (rows.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted small">No RDC Approved submissions are currently available.</td></tr>`;
                return;
            }

            tbody.innerHTML = rows.map(s => {
                const title  = s.title || s.formData?.['f-title'] || 'Untitled';
                const agency = s.agency || s.formData?.['f-agency'] || '—';
                const stage  = s.stage || s.cppStage || s.projectStatus || 'RDC';
                const status = _getSubmissionStatus(s);
                const updated = s.updatedAt ? new Date(s.updatedAt).toLocaleDateString() : '—';
                return `
                    <tr data-sid="${s.id || ''}">
                        <td class="fw-medium small py-3 ps-4" style="max-width:280px;">${title}</td>
                        <td class="small text-muted py-3">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(agency) : agency}</td>
                        <td class="small py-3">${stage}</td>
                        <td class="small py-3"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small">${status}</span></td>
                        <td class="small text-muted py-3">${updated}</td>
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

        function _wireConfirmReferPdipbBtn() {
            const confirmBtn = document.getElementById('confirmReferPdipbBtn');
            if (!confirmBtn) return;

            confirmBtn.onclick = async () => {
                const sid      = document.getElementById('referPdipbSid')?.value || '';
                const notes    = document.getElementById('referPdipbNotes')?.value.trim() || '';
                const referralStage = document.getElementById('referPdipbStage')?.value || '';
                const staffSel = document.getElementById('referPdipbStaff');
                const errEl    = document.getElementById('referPdipbStaffErr');
                if (!sid) return;

                if (!staffSel?.value) {
                    if (errEl) errEl.style.display = '';
                    staffSel?.focus();
                    return;
                }
                if (errEl) errEl.style.display = 'none';

                const selectedOpt  = staffSel.options[staffSel.selectedIndex];
                const staffName    = selectedOpt?.dataset.name  || selectedOpt?.text || '';

                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Referring...';

                try {
                    const response = await fetch('/referrals', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            cipg_submission_id: sid,
                            to_user_id: staffSel.value,
                            stage: referralStage,
                            notes: notes
                        })
                    });

                    if (!response.ok) throw new Error('Referral submission failed');

                    // Sync local state for UI consistency
                    const allSubs = await _getAdminSubmissions();
                    const sub = allSubs.find(s => String(s.id) === String(sid));
                    if (sub) {
                        sub.referredToPdipb = true;
                        sub.assignedStaffId = staffSel.value;
                        sub.referralStage   = referralStage;
                        if (!['sectoral', 'rdc-pres'].includes(referralStage)) {
                            sub.stage = 'Completeness Test and Validation';
                            sub.status = 'Review';
                            sub.submissionStatus = 'Review';
                        }
                        await localforage.setItem('cpp_submissions', allSubs);
                    }

                    // Save referral to project_referrals so all table filters pick it up immediately
                    // Each table's filter checks specific stage+status values:
                    //   Sectoral:        stage='sectoral committee', status='sectoral presentation review'
                    //   RDC Pres:        stage='rdc',                status='rdc presentation review'
                    //   Submissions/CTE: just checks submissionId
                    const _stageMap = {
                        'sectoral':   { stage: 'sectoral committee',    status: 'sectoral presentation review' },
                        'rdc-pres':   { stage: 'rdc',                   status: 'rdc presentation review' },
                        'submission': { stage: 'Completeness Test',     status: 'Pending PAR' },
                    };
                    const _refStageInfo = _stageMap[referralStage] || { stage: referralStage, status: 'Assigned' };
                    const _existingRefs = await localforage.getItem('project_referrals') || [];
                    _existingRefs.unshift({
                        id: `REF-LOCAL-${Date.now()}`,
                        submissionId: sid,
                        cteId: sid,
                        stage: _refStageInfo.stage,
                        status: _refStageInfo.status,
                        toUserId: staffSel.value,
                        toUserName: staffName,
                        referralDate: new Date().toISOString(),
                        notes: notes
                    });
                    await localforage.setItem('project_referrals', _existingRefs);

                    const modalEl = document.getElementById('referPdipbModal');
                    bootstrap.Modal.getOrCreateInstance(modalEl)?.hide();

                    if (window.showSimpleAlert) {
                        window.showSimpleAlert(
                            `Submission has been referred to <strong>${staffName}</strong> (PDIPB) for completeness review.`,
                            'success'
                        );
                    }

                    // Refresh ALL table views now that localforage is consistent
                    renderCipgTable('all');
                    if (window.initAdminSectoralStage) window.initAdminSectoralStage();
                    if (window.initRdcPresentationStage) window.initRdcPresentationStage();
                    if (window.initEvaluationStage) window.initEvaluationStage();
                    if (window.initTestAndEvaluationLoader) window.initTestAndEvaluationLoader();
                    
                } catch (err) {
                    console.error('[AdminDashboard] Referral error:', err);
                    if (window.showSimpleAlert) window.showSimpleAlert('Failed to refer project. Please try again.', 'danger');
                } finally {
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = '<i data-lucide="send" width="13" class="me-1"></i> Refer to PDIPB';
                    if (window.lucide) window.lucide.createIcons({ node: confirmBtn });
                }
            };
        }

        window.initAdminSectoralStage = async function() {
            await renderSectoralStage();
        };
        window.initRdcPresentationStage = async function() {
            await renderRdcPresentationStage();
        };
        window.initRdcApprovedStage = async function() {
            await renderRdcApprovedStage();
        };

        // ── Also expose initAdminCipgTable for toggleStepDetails (ui-utils.js line 582) ──
        window.initAdminCipgTable = async function() {
            await renderCipgTable('all');
        };

        // ── Step 1: Open the submission workflow panel ──
        // toggleStepDetails rewrites .workflow-details-inner, so we must render the table
        // AFTER it finishes (it's synchronous DOM manipulation + 100ms inner timeout)
        if (typeof window.toggleStepDetails === 'function') {
            window.toggleStepDetails(null, 'submission');
        }

        // ── Step 2: Render the table after toggleStepDetails' inner 100ms timeout fires ──
        // Use 250ms to safely clear both the 100ms toggleStepDetails timeout AND
        // the agency initSubmissionsLoader it calls, so we get the last word on the DOM.
        setTimeout(async () => {
            await renderCipgTable('all');
            // Also fire charts
            if (typeof window.initStageCharts === 'function') {
                window.initStageCharts('submission');
            }
        }, 250);

        // Wire the shared PDIPB referral confirm button immediately.
        _wireConfirmReferPdipbBtn();

    } catch (globalErr) {
        console.error('[AdminDashboard] Fatal initialization error:', globalErr);
    }
}
