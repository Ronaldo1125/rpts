export function initAgencyDashboardLoader() {
    window.initAgencyDashboardLoader = initAgencyDashboardLoader;
    window._agencyCharts = window._agencyCharts || {};

    // Store submissions for use by toggleAgencyStage
    let _allSubmissions = [];

    async function updateDashboardCounts() {
        try {
            let submissions = await localforage.getItem('cpp_submissions') || [];
            
            // Fetch fresh data if needed, or simply always fetch to keep dashboard accurate
            try {
                const response = await fetch('/v2/cipg_submissions/fetch');
                if (response.ok) {
                    const serverSubs = await response.json();
                    
                    // Merge local true drafts
                    const trueDrafts = submissions.filter(s => String(s.id).startsWith('DRAFT-') || String(s.id).startsWith('CPP-Draft-'));
                    submissions = [...serverSubs, ...trueDrafts];
                    
                    // Update cache for other pages
                    await localforage.setItem('cpp_submissions', submissions);
                }
            } catch(e) {
                console.warn('Failed to fetch fresh submissions, using cache', e);
            }

            _allSubmissions = submissions;

            function _isWorkflowStatus(val) {
                return val === 'Draft' || val === 'Submitted' || val === 'Review' || val === 'Approved' || val === 'For Revision' || val === 'Resubmitted' || val === 'Rejected';
            }

            function _getSubmissionStatus(s) {
                if (s && typeof s.submissionStatus === 'string' && s.submissionStatus.trim()) return s.submissionStatus;
                if (s && typeof s.status === 'string' && _isWorkflowStatus(s.status)) return s.status;
                if (s && typeof s.status === 'string' && s.status.trim()) return s.status;
                if (s && typeof s.projectStatus === 'string' && s.projectStatus.trim()) return s.projectStatus;
                if (s && typeof s.cppStatus === 'string' && s.cppStatus.trim()) return s.cppStatus;
                if (s && s.id && String(s.id).startsWith('DRAFT-')) return 'Draft';
                if (s && s.id && String(s.id).startsWith('CPP-Draft-')) return 'Draft';
                return 'Submitted';
            }

            function _getProjectStatus(s) {
                if (s && typeof s.projectStatus === 'string' && s.projectStatus.trim()) return s.projectStatus;
                const fromForm = s?.formData?.['project-status'];
                if (typeof fromForm === 'string' && fromForm.trim()) return fromForm;
                if (s && typeof s.status === 'string' && s.status.trim() && !_isWorkflowStatus(s.status)) return s.status;
                return '';
            }

            const totalCount    = submissions.length;
            const draftsCount   = submissions.filter(s => _getSubmissionStatus(s) === 'Draft').length;
            const revisionCount = submissions.filter(s => _getSubmissionStatus(s) === 'For Revision').length;
            const incompleteCount = submissions.filter(s => _getSubmissionStatus(s) === 'Incomplete').length;
            const submittedCount = submissions.filter(s => !['Draft','For Revision','Incomplete'].includes(_getSubmissionStatus(s))).length;

            // Stats for chart — individual status buckets
            const cntOngoing    = submissions.filter(s => _getProjectStatus(s) === 'Ongoing').length;
            const cntApproved   = submissions.filter(s => _getProjectStatus(s) === 'Approved').length;
            const cntCompleted  = submissions.filter(s => _getProjectStatus(s) === 'Completed').length;
            const cntProposed   = submissions.filter(s => _getProjectStatus(s) === 'Proposed').length;
            const cntSuspended  = submissions.filter(s => ['Suspended', 'Pipeline'].includes(_getProjectStatus(s))).length;
            const cntDropped    = submissions.filter(s => _getProjectStatus(s) === 'Dropped').length;
            const cntTerminated = submissions.filter(s => _getProjectStatus(s) === 'Terminated').length;

    // Pipeline stage counts — uses adminStage field first, falls back to status mapping
            function _getAdminStage(s) {
                // Prefer granular adminStage set by admin workflow
                if (s.adminStage) return s.adminStage;
                // Fallback: derive from submission status
                const st = _getSubmissionStatus(s);
                if (st === 'Draft')           return null;          // pre-pipeline
                if (st === 'Submitted')       return 'submission';
                if (st === 'Review')          return 'referred';    // admin has at least referred it
                if (st === 'For Revision')    return 'revised';
                if (st === 'Resubmitted')     return 'referred';    // back in queue
                if (st === 'Approved')        return 'approved';
                if (st === 'Rejected')        return null;          // outside forward flow
                return 'submission';
            }

            const stageCounts = {
                submission:  submissions.filter(s => _getAdminStage(s) === 'submission').length,
                referred:    submissions.filter(s => _getAdminStage(s) === 'referred').length,
                evaluation:  submissions.filter(s => _getAdminStage(s) === 'evaluation').length,
                sectoral:    submissions.filter(s => _getAdminStage(s) === 'sectoral').length,
                findings:    submissions.filter(s => _getAdminStage(s) === 'findings').length,
                revised:     submissions.filter(s => _getAdminStage(s) === 'revised').length,
                'rdc-pres':  submissions.filter(s => _getAdminStage(s) === 'rdc-pres').length,
                approved:    submissions.filter(s => _getAdminStage(s) === 'approved').length,
            };

            // Update dashboard counter elements
            const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            setEl('total-projects-count', totalCount);
            setEl('drafts-count',         draftsCount);
            setEl('revision-count',       revisionCount);
            setEl('incomplete-count',     incompleteCount);
            setEl('submitted-count',      submittedCount);

            // Pipeline total badge
            setEl('agency-pipeline-total', `${totalCount} Submission${totalCount !== 1 ? 's' : ''}`);

            // Pipeline stage count badges
            Object.entries(stageCounts).forEach(([key, cnt]) => setEl(`agency-stage-count-${key}`, cnt));

            // Legend numbers
            const setLeg = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            setLeg('legend-ongoing',    cntOngoing);
            setLeg('legend-completed',  cntCompleted);
            setLeg('legend-proposed',   cntProposed);
            setLeg('legend-suspended',  cntSuspended);
            setLeg('legend-dropped',    cntDropped);
            setLeg('legend-terminated', cntTerminated);

            // Initialize Charts
            _initCharts({ cntOngoing, cntCompleted, cntProposed, cntSuspended, cntDropped, cntTerminated, submissions });

            if (window.lucide) window.lucide.createIcons();

        } catch (err) {
            console.error('Error updating dashboard counts:', err);
        }
    }

    // ── Pipeline Toggle ──────────────────────────────────────────────────────
    let _activeStage = null;

    const STAGE_META = {
        submission:  { label: 'Initial Submission',     color: '#154A9A', step: 1 },
        referred:    { label: 'Referred to Division',   color: '#2563eb', step: 2 },
        evaluation:  { label: 'Project Assessment',     color: '#0ea5e9', step: 3 },
        sectoral:    { label: 'SecCom Presentation',    color: '#6366f1', step: 4 },
        findings:    { label: 'Findings & Recs',        color: '#8b5cf6', step: 5 },
        revised:     { label: 'Revised Submissions',    color: '#f59e0b', step: 6 },
        'rdc-pres':  { label: 'RDC Presentation',       color: '#f97316', step: 7 },
        approved:    { label: 'RDC Approved',           color: '#10b981', step: 8 },
    };

    function _statusPill(st) {
        const map = {
            'Draft':        ['#f1f5f9','#64748b'],
            'Submitted':    ['#ede9fe','#5b21b6'],
            'Review':       ['#dbeafe','#1d4ed8'],
            'For Revision': ['#fef3c7','#92400e'],
            'Resubmitted':  ['#e0e7ff','#3730a3'],
            'Approved':     ['#d1fae5','#065f46'],
            'Rejected':     ['#fee2e2','#991b1b'],
        };
        const [bg, col] = map[st] || ['#f1f5f9','#334155'];
        return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${col};font-size:0.7rem;padding:0.35em 0.8em;">${st}</span>`;
    }

    window.toggleAgencyStage = function(stageKey) {
        const panel     = document.getElementById('agency-stage-panel');
        const tbody     = document.getElementById('agency-stage-table-body');
        const titleEl   = document.getElementById('agency-stage-panel-title');

        if (!panel || !tbody) return;

        // Deactivate all boxes
        document.querySelectorAll('.agency-stage-box').forEach(b => b.classList.remove('is-active'));

        // Close: null key or same stage re-clicked
        if (!stageKey || stageKey === _activeStage) {
            _activeStage = null;
            panel.className = 'agency-stage-panel-hidden';
            panel.style.borderTop = '1px solid #e2e8f0';
            return;
        }

        _activeStage = stageKey;

        // Highlight active box
        const activeBox = document.querySelector(`.agency-stage-box[data-stage="${stageKey}"]`);
        if (activeBox) activeBox.classList.add('is-active');

        const meta = STAGE_META[stageKey];
        if (!meta) return;

        // Update panel title
        if (titleEl) {
            titleEl.innerHTML = `
                <span class="p-1 px-2 rounded text-white" style="font-size:0.7rem;background:${meta.color};">STEP ${meta.step}</span>
                ${meta.label} — Your Projects
            `;
        }

        // Filter submissions by admin stage
        function _getSt(s) {
            if (s?.submissionStatus?.trim()) return s.submissionStatus;
            if (['Draft','Submitted','Review','Approved','For Revision','Resubmitted','Rejected'].includes(s?.status)) return s.status;
            if (String(s?.id||'').startsWith('DRAFT-') || String(s?.id||'').startsWith('CPP-Draft-')) return 'Draft';
            return 'Submitted';
        }
        function _getStageKey(s) {
            if (s.adminStage) return s.adminStage;
            const st = _getSt(s);
            if (st === 'Submitted')    return 'submission';
            if (st === 'Review')       return 'referred';
            if (st === 'For Revision') return 'revised';
            if (st === 'Resubmitted')  return 'referred';
            if (st === 'Approved')     return 'approved';
            return null;
        }

        const filtered = _allSubmissions.filter(s => _getStageKey(s) === stageKey);

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted small">
                <i data-lucide="inbox" width="28" class="d-block mx-auto mb-2 opacity-40"></i>
                No projects in this stage
            </td></tr>`;
        } else {
            tbody.innerHTML = filtered.map(s => {
                const title  = s.title || s.formData?.['f-title'] || 'Untitled';
                const sector = s.sector || s.formData?.['f-sector'] || '—';
                const sub    = s.formData?.['f-sub-sector'] || '—';
                const date   = s.date ? new Date(s.date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '—';
                const st     = _getSt(s);
                return `<tr>
                    <td class="fw-medium small py-3 ps-3" style="max-width:280px;">${title}</td>
                    <td class="small text-muted py-3 fw-semibold">${sector}</td>
                    <td class="small text-muted py-3">${sub}</td>
                    <td class="small text-muted py-3">${date}</td>
                    <td class="py-3">${_statusPill(st)}</td>
                    <td class="py-3 text-center">
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3"
                            style="font-size:0.72rem;"
                            onclick="sessionStorage.setItem('cpp_view_id','${s.id}'); if(window.switchPage) window.switchPage('cpp-form');">
                            View
                        </button>
                    </td>
                </tr>`;
            }).join('');
        }

        // Show panel with animation
        panel.className = 'agency-stage-panel-show';
        panel.style.borderTop = '1px solid #e2e8f0';

        if (window.lucide) window.lucide.createIcons();

        // Smooth scroll to panel
        setTimeout(() => panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 50);
    };

    // ────────────────────────────────────────────────────────────────────────
    function _initCharts({ cntOngoing, cntCompleted, cntProposed, cntSuspended, cntDropped, cntTerminated, submissions }) {
        if (!window.Chart) return;
        
        // Cleanup
        Object.values(window._agencyCharts).forEach(c => c?.destroy());
        window._agencyCharts = {};

        // 1. Status Pie Chart
        const ctxStatus = document.getElementById('agency-status-chart')?.getContext('2d');
        if (ctxStatus) {
            const rawData = [
                { label: 'Ongoing',    count: cntOngoing,    color: '#4c1d95' }, // Deep Purple
                { label: 'Approved',   count: cntApproved,   color: '#5b21b6' },
                { label: 'Completed',  count: cntCompleted,  color: '#6d28d9' },
                { label: 'Proposed',   count: cntProposed,   color: '#7c3aed' }, // Purple
                { label: 'Suspended',  count: cntSuspended,  color: '#8b5cf6' }, // Light Purple
                { label: 'Dropped',    count: cntDropped,    color: '#a78bfa' },
                { label: 'Terminated', count: cntTerminated, color: '#c4b5fd' }
            ];

            rawData.forEach(item => {
                const el = document.getElementById('legend-' + item.label.toLowerCase());
                if (el) el.textContent = item.count;
            });

            const filtered = rawData.filter(d => d.count > 0);
            const counts   = filtered.map(d => d.count);
            const labels   = filtered.map(d => d.label);
            const colors   = filtered.map(d => d.color);
            const hasData  = counts.length > 0;

            window._agencyCharts.status = new window.Chart(ctxStatus, {
                type: 'pie',
                data: {
                    labels: hasData ? labels : ['No Data'],
                    datasets: [{
                        data: hasData ? counts : [1],
                        backgroundColor: hasData ? colors : ['#e2e8f0'],
                        borderWidth: hasData ? 2 : 0,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: ctx => {
                                    if (!hasData) return ' No project data yet';
                                    const total = counts.reduce((a,b)=>a+b,0);
                                    return ` ${ctx.label}: ${ctx.parsed} (${total > 0 ? Math.round((ctx.parsed/total)*100) : 0}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Pulse Chart (Bar)
        const ctxPulse = document.getElementById('agency-pulse-chart')?.getContext('2d');
        if (ctxPulse) {
            const dayCounts = [0, 0, 0, 0, 0, 0, 0];
            submissions.forEach(s => {
                const d = new Date(s.date || Date.now());
                let day = d.getDay();
                let idx = day === 0 ? 6 : day - 1;
                dayCounts[idx]++;
            });
            const hasData = dayCounts.some(c => c > 0);
            const displayData = hasData ? dayCounts : [2, 5, 3, 8, 4, 1, 2];

            window._agencyCharts.pulse = new Chart(ctxPulse, {
                type: 'bar',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        data: displayData,
                        backgroundColor: '#154A9A',
                        borderRadius: 8,
                        barThickness: 'flex'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { display: false, beginAtZero: true },
                        x: { grid: { display: false }, border: { display: false } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
            const weeklyAvgEl = document.getElementById('weekly-avg');
            if (weeklyAvgEl) {
                const total = displayData.reduce((a,b)=>a+b,0);
                weeklyAvgEl.textContent = (total / 7).toFixed(1) + ' submissions/day average';
            }
        }

        // 3. MTIP Horizon (Line Chart)
        const ctxMtip = document.getElementById('agency-mtip-chart')?.getContext('2d');
        if (ctxMtip) {
            window._agencyCharts.mtip = new Chart(ctxMtip, {
                type: 'line',
                data: {
                    labels: ['2023', '2024', '2025', '2026', '2027', '2028'],
                    datasets: [{
                        label: 'Project Count',
                        data: [12, 19, 24, 32, 18, 14],
                        borderColor: '#154A9A',
                        backgroundColor: 'rgba(21, 74, 154, 0.05)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#154A9A',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#64748b', font: { size: 11 } },
                            grid: { color: 'rgba(0,0,0,0.03)' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { size: 11 } },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1e293b', padding: 12 }
                    }
                }
            });
            const mtipTotalEl = document.getElementById('mtip-total');
            if (mtipTotalEl) {
                mtipTotalEl.textContent = (submissions ? submissions.length : 0) + ' Total Managed Projects';
            }
        }
    }

    updateDashboardCounts();
}

