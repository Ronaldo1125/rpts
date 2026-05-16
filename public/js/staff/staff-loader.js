export async function initStaffDashboard() {
    const currentUser = (window.__CURRENT_USER__ || {});
    const userEmail = (currentUser.email || '').toLowerCase();
    const userDivision = (currentUser.division || '').toUpperCase();
    const ChartJS = window.Chart;
    const { initPdipbCteLoader } = await import('./pdipb-cte-loader.js');
    const { initPdipbParLoader } = await import('./pdipb-par-loader.js');

    const usernameEl = document.getElementById('staff-dash-username');
    if (usernameEl) usernameEl.textContent = currentUser.name || currentUser.email || 'Staff';

    // ── PDIPBD STAFF EXCLUSIVE: Hide Assignments & Show Completeness Test Table ──
    const pdipbCteSection = document.getElementById('pdipb-dashboard-cte-section');
    const pdipbParSection = document.getElementById('pdipb-dashboard-par-section');
    const isPdipbdPortal = (window.APP_CONFIG?.sidebarPath || '').includes('pdipbd-staff-sidebar');
    const isPdipbUser = userDivision.includes('PDIPBD') || userDivision.includes('PDIPB') || isPdipbdPortal;
    
    console.log('[StaffLoader] User context:', { userDivision, isPdipbdPortal, isPdipbUser });

    if (isPdipbUser) {
        console.log('[StaffLoader] Initializing PDIPBD dashboard components...');
        // Show the workflow pipeline
        const pipelineSection = document.getElementById('pdipb-dashboard-pipeline-section');
        if (pipelineSection) pipelineSection.style.display = 'block';

        const myAssignments = Array.isArray(window.__STAFF_ASSIGNMENTS__) ? window.__STAFF_ASSIGNMENTS__ : [];
        setEl('staff-dash-referred-count', myAssignments.length);
        setEl('staff-dash-referral-count', myAssignments.length);

        if (pdipbCteSection) {
            pdipbCteSection.style.display = 'block';
            initPdipbCteLoader('dashboard-cte-tbody');
        }
        const revisionSection = document.getElementById('pdipb-dashboard-revision-section');
        const sectoralSection = document.getElementById('pdipb-dashboard-sectoral-presentation-section');
        const rdcSection = document.getElementById('pdipb-dashboard-rdc-presentation-section');
        if (revisionSection) revisionSection.style.display = 'block';
        if (sectoralSection) sectoralSection.style.display = 'block';
        if (rdcSection) rdcSection.style.display = 'block';
        
        initPdipbParLoader(
            'evalTableBody',
            'dashboard-reviewed-par-tbody',
            'dashboard-revision-tbody',
            'dashboard-sectoral-presentation-tbody',
            'dashboard-rdc-presentation-tbody'
        );

        // Hide Recent Assignments specifically for PDIPBD staff as requested
        const assignmentsKpi = document.getElementById('staff-assignments-kpi');
        const assignmentsSection = document.getElementById('staff-recent-assignments-section');
        if (assignmentsKpi) assignmentsKpi.style.display = 'none';
        if (assignmentsSection) assignmentsSection.style.display = 'none';
        
        // Adjust KPI card widths for PDIPBD (from col-xl-3 to col-xl-4)
        document.querySelectorAll('.col-xl-3:not([style*="display: none"])').forEach(el => {
            el.classList.replace('col-xl-3', 'col-xl-4');
        });

        // Populate pipeline step counts — ONLY for submissions assigned to THIS staff member
        setTimeout(async () => {
            try {
                const allSubs      = await localforage.getItem('cpp_submissions') || [];
                const allReferrals = await localforage.getItem('project_referrals') || [];

                // Find referrals assigned to the current staff user (by email or name)
                const myReferrals = allReferrals.filter(r => {
                    const assignee = (r.assignedTo || r.staffEmail || r.staff || '').toLowerCase();
                    return assignee && (
                        assignee === userEmail ||
                        assignee === (currentUser.name || '').toLowerCase()
                    );
                });

                // Collect submission IDs assigned to this staff member
                const myAssignedIds = new Set(myReferrals.map(r => r.submissionId || r.cppId || r.id).filter(Boolean));

                // If the user has assigned submissions, filter to them; otherwise fall back to all non-drafts
                const submissions = window.__ADMIN_DASHBOARD_SUBMISSIONS__ && window.__ADMIN_DASHBOARD_SUBMISSIONS__.length > 0
                    ? window.__ADMIN_DASHBOARD_SUBMISSIONS__
                    : await localforage.getItem('cpp_submissions') || [];

                const nonDrafts = submissions.filter(s => _getSubmissionStatus(s) !== 'Draft');
                const cteRows = submissions.filter(s => {
                    const st = _getSubmissionStatus(s);
                    const stage = (s.stage || '').trim();
                    const isTarget = ['Review', 'Submitted', 'Resubmitted'].includes(st) && stage === 'Completeness Test and Validation';
                    if (!isTarget) return false;

                    const rawEmail = s.assignedStaffEmail || s.assigned_staff_email || '';
                    const assignedEmail = rawEmail ? String(rawEmail).toLowerCase() : '';
                    const rawId = s.assignedStaffId || s.assigned_staff_id || s.assigned_to_user_id;
                    const assignedId = rawId ? String(rawId) : '';
                    const assignedStaffId = s.assignedStaff?.id ? String(s.assignedStaff.id) : assignedId;

                    return (assignedId && (assignedId === currentUser.id || assignedStaffId === currentUser.id)) ||
                           (assignedEmail && assignedEmail === userEmail) ||
                           (s.referredToPdipb && !assignedId);
                });
                
                // Pipeline counts based on STAGE or STATUS
                const initialCount   = cteRows.length;
                const referredCount  = myAssignments.length;
                const evalCount      = nonDrafts.filter(s => _getSubmissionStage(s).includes('Assessment') || _getSubmissionStage(s).includes('Test')).length;
                const findingsCount  = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Validated').length;
                const revisedCount   = nonDrafts.filter(s => ['For Revision', 'Resubmitted'].includes(_getSubmissionStatus(s))).length;
                const sectoralCount  = nonDrafts.filter(s => _getSubmissionStatus(s).includes('Sectoral')).length;
                const rdcPresCount   = nonDrafts.filter(s => _getSubmissionStatus(s).includes('RDC Presentation')).length;
                const approvedCount  = nonDrafts.filter(s => _getSubmissionStatus(s).includes('Approved')).length;

                // initial-count is handled by pdipb-cte-loader
                setEl('staff-dash-referred-count', referredCount);
                // eval-count, revised-count, sectoral-count, rdc-pres-count are handled by pdipb-par-loader

                // Approved count logic for staff
                const myApprovedRows = nonDrafts.filter(s => {
                    if (!_getSubmissionStatus(s).includes('Approved')) return false;
                    const refs = Array.isArray(s.referrals) ? s.referrals : [];
                    return refs.some(r => String(r.to_user_id) === String(currentUser.id) || 
                                           (r.to_user_email || '').toLowerCase() === userEmail);
                });
                setEl('staff-dash-approved-count', myApprovedRows.length);
            } catch(e) { console.error('[StaffDashboard-Pipeline]', e); }

            // Open Submission stage by default
            if (typeof window.toggleStepDetails === 'function') {
                window.toggleStepDetails(null, 'submission');
                // Wait for DOM injection before loading table
                setTimeout(() => {
                    import('../agency/submissions-loader.js').then(m => m.initSubmissionsLoader());
                }, 300);
            }
        }, 400);
    }

    function _isWorkflowStatus(val) {
        return ['Draft','Submitted','Review','Approved','Validated','For Revision','Resubmitted','Revised','Incomplete','PDIPB Review','Processed'].includes(val);
    }
    function _getSubmissionStatus(s) {
        if (!s) return 'Unknown';
        // Priority: explicitly passed status > model status > metadata status
        if (s.status?.trim()) return s.status;
        if (s.submissionStatus?.trim()) return s.submissionStatus;
        if (s.projectStatus?.trim()) return s.projectStatus;
        if (s.cppStatus?.trim()) return s.cppStatus;
        if (String(s.id || '').startsWith('DRAFT-')) return 'Draft';
        return 'Submitted';
    }
    function _getSubmissionStage(s) {
        if (!s) return 'Initial';
        return s.stage || s.currentStage || 'Initial';
    }
    function _getProjectStatus(s) {
        if (s?.projectStatus?.trim()) return s.projectStatus;
        const fromForm = s?.formData?.['project-status'];
        if (typeof fromForm === 'string' && fromForm.trim()) return fromForm;
        if (typeof s?.status === 'string' && s.status.trim() && !_isWorkflowStatus(s.status)) return s.status;
        return '';
    }
    function setEl(id, val) { const el = document.getElementById(id); if (el) el.textContent = val; }

    // Destroy any existing chart instances to avoid "canvas in use" errors
    function destroyChart(id) {
        if (!ChartJS) return;
        const existing = ChartJS.getChart(id);
        if (existing) existing.destroy();
    }

    try {
        const submissions = window.__ADMIN_DASHBOARD_SUBMISSIONS__ && window.__ADMIN_DASHBOARD_SUBMISSIONS__.length > 0
            ? window.__ADMIN_DASHBOARD_SUBMISSIONS__
            : await localforage.getItem('cpp_submissions') || [];
        
        const nonDrafts = submissions.filter(s => _getSubmissionStatus(s) !== 'Draft');

        const ongoing    = submissions.filter(s => _getProjectStatus(s) === 'Ongoing').length;
        const proposed   = submissions.filter(s => _getProjectStatus(s) === 'Proposed').length;
        const completed  = submissions.filter(s => _getProjectStatus(s) === 'Completed').length;
        const terminated = submissions.filter(s => _getProjectStatus(s) === 'Terminated').length;
        const suspended  = submissions.filter(s => _getProjectStatus(s) === 'Suspended').length;
        const dropped    = submissions.filter(s => _getProjectStatus(s) === 'Dropped').length;

        setEl('staff-dash-proj-total',      submissions.length);
        setEl('staff-dash-proj-ongoing',    ongoing);
        setEl('staff-dash-proj-proposed',   proposed);
        setEl('staff-dash-proj-completed',  completed);
        setEl('staff-dash-proj-terminated', terminated);
        setEl('staff-dash-proj-suspended',  suspended);
        setEl('staff-dash-proj-dropped',    dropped);

        // ── CIPG Submission Overview ──
        const submittedCount   = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Submitted').length;
        const revisionCount    = nonDrafts.filter(s => _getSubmissionStatus(s) === 'For Revision').length;
        const resubmittedCount = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Resubmitted').length;
        const incompleteCount  = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Incomplete').length;
        const revisedCount     = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Revised').length;
        const reviewCount      = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Review').length;
        const validatedCount   = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Validated').length;
        const approvedCount    = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Approved').length;

        setEl('staff-dash-total-count', nonDrafts.length);

        // ── Staff Exclusive ──
        const pars = await localforage.getItem('project_assessments') || [];
        const myPars = pars.filter(p => (p.preparedBy || '').toLowerCase() === userEmail);
        setEl('staff-dash-par-count', myPars.length);

        const crs = await localforage.getItem('comments_recommendations') || [];
        const myCrs = crs.filter(c => (c.preparedBy || '').toLowerCase() === userEmail);
        setEl('staff-dash-cr-count', myCrs.length);

        // ── CHART 1: Project Status Donut ──
        const statusCtx = document.getElementById('staff-status-chart');
        if (statusCtx && ChartJS) {
            destroyChart('staff-status-chart');
            const total = ongoing + proposed + completed + terminated + suspended + dropped;
            const hasData = total > 0;
            new ChartJS(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: hasData ? ['Ongoing','Proposed','Completed','Terminated','Suspended','Dropped'] : ['No Data'],
                    datasets: [{
                        data: hasData ? [ongoing, proposed, completed, terminated, suspended, dropped] : [1],
                        backgroundColor: hasData ? ['#3b82f6','#a78bfa','#10b981','#ef4444','#f59e0b','#94a3b8'] : ['#e2e8f0'],
                        borderWidth: hasData ? 2 : 0,
                        borderColor: '#fff',
                        hoverOffset: hasData ? 8 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => hasData ? ` ${ctx.label}: ${ctx.parsed} (${Math.round((ctx.parsed/total)*100)}%)` : ' No project data yet'
                            }
                        }
                    }
                }
            });
        }

        // ── CHART 2: CIPG Submission Pipeline Bar ──
        const subCtx = document.getElementById('staff-submission-chart');
        if (subCtx && ChartJS) {
            destroyChart('staff-submission-chart');
            new ChartJS(subCtx, {
                type: 'bar',
                data: {
                    labels: ['Submitted','For Revision','Incomplete','Revised','Resubmitted','Under Review','Validated','Approved'],
                    datasets: [{
                        label: 'Submissions',
                        data: [submittedCount, revisionCount, incompleteCount, revisedCount, resubmittedCount, reviewCount, validatedCount, approvedCount],
                        backgroundColor: [
                            'rgba(99,102,241,0.85)',
                            'rgba(245,158,11,0.85)',
                            'rgba(244,63,94,0.85)',
                            'rgba(147,51,234,0.85)',
                            'rgba(124,58,237,0.85)',
                            'rgba(14,165,233,0.85)',
                            'rgba(16,185,129,0.85)',
                            'rgba(34,197,94,0.85)'
                        ],
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, font: { size: 11 } },
                            grid: { color: 'rgba(0,0,0,0.04)' }
                        },
                        x: {
                            ticks: { font: { size: 10 } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    } catch (err) {
        console.error('[StaffDashboard]', err);
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
        return subs.find(s =>
            (ref.submissionId && s.id === ref.submissionId) ||
            (ref.cteId && (s.cteId === ref.cteId || s.id === ref.cteId))
        ) || null;
    }

    /** True if a PAR record already exists for this referral (any status, including Draft). */
    function _referralHasExistingPar(pars, ref) {
        if (!ref || !Array.isArray(pars) || pars.length === 0) return false;
        const cte = String(ref.cteId || '').trim();
        const subId = String(ref.submissionId || '').trim();
        return pars.some(p => {
            const cid = String(p.connectedCteId || '').trim();
            if (!cid) return false;
            if (cte && cid === cte) return true;
            if (subId && cid === subId) return true;
            return false;
        });
    }

    // ── My Assignments Table Event Wiring (Only for Division Staff) ──
    const referralTbody = document.getElementById('staff-dash-referrals-tbody');
    if (referralTbody && !isPdipbUser) {
        const myAssignments = Array.isArray(window.__STAFF_ASSIGNMENTS__) ? window.__STAFF_ASSIGNMENTS__ : [];
        setEl('staff-dash-referral-count', myAssignments.length);
    }

    if (window.lucide) window.lucide.createIcons();
}
