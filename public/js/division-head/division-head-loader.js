export async function initDivisionHeadDashboard() {
    const currentUser = (window.__CURRENT_USER__ || {});
    const ChartJS = window.Chart;

    const display = document.getElementById('active-division-display');
    if (display) display.textContent = currentUser.division || currentUser.name || 'Division Head';

    function _isWorkflowStatus(val) {
        return ['Draft','Submitted','Review','Approved','For Revision','Resubmitted'].includes(val);
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
        let submissions = await localforage.getItem('cpp_submissions') || [];
        const serverSubs = Array.isArray(window.__ADMIN_DASHBOARD_SUBMISSIONS__) ? window.__ADMIN_DASHBOARD_SUBMISSIONS__ : [];
        if (serverSubs.length > 0) {
            const merged = new Map();
            submissions.forEach(s => merged.set(String(s.id || ''), s));
            serverSubs.forEach(s => merged.set(String(s.id || ''), s));
            submissions = Array.from(merged.values());
        }

        const nonDrafts = submissions.filter(s => _getSubmissionStatus(s) !== 'Draft');

        const ongoing    = submissions.filter(s => _getProjectStatus(s) === 'Ongoing').length;
        const proposed   = submissions.filter(s => _getProjectStatus(s) === 'Proposed').length;
        const completed  = submissions.filter(s => _getProjectStatus(s) === 'Completed').length;
        const terminated = submissions.filter(s => _getProjectStatus(s) === 'Terminated').length;
        const suspended  = submissions.filter(s => _getProjectStatus(s) === 'Suspended').length;
        const dropped    = submissions.filter(s => _getProjectStatus(s) === 'Dropped').length;

        setEl('dh-dash-proj-total',      submissions.length);
        setEl('dh-dash-proj-ongoing',    ongoing);
        setEl('dh-dash-proj-proposed',   proposed);
        setEl('dh-dash-proj-completed',  completed);
        setEl('dh-dash-proj-terminated', terminated);
        setEl('dh-dash-proj-suspended',  suspended);
        setEl('dh-dash-proj-dropped',    dropped);

        // ── CIPG Submission Overview ──
        const submittedCount   = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Submitted').length;
        const revisionCount    = nonDrafts.filter(s => _getSubmissionStatus(s) === 'For Revision').length;
        const resubmittedCount = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Resubmitted').length;
        const reviewCount      = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Review').length;
        const approvedCount    = nonDrafts.filter(s => _getSubmissionStatus(s) === 'Approved').length;

        setEl('dh-dash-total-count', nonDrafts.length);

        // ── Division Head Exclusive ──
        const pars = await localforage.getItem('project_assessments') || [];
        setEl('dh-dash-par-count', pars.length);

        // Fetch live referrals from backend
        let referrals = [];
        try {
            const refRes = await fetch('/referrals', { headers: { 'Accept': 'application/json' } });
            if (refRes.ok) {
                const refJson = await refRes.json();
                referrals = refJson.data.map(r => ({
                    id: r.id,
                    submissionId: r.submissionId,
                    projectTitle: r.submissionTitle,
                    agency: r.fromDivisionName || '—',
                    referralDate: r.referredAt,
                    status: r.status,
                    assignedStaff: r.toUserId ? r.toUserName : null,
                    referredToDivision: r.toDivisionName
                }));
            }
        } catch (e) {
            console.error('Failed to fetch referrals', e);
            referrals = await localforage.getItem('project_referrals') || [];
        }
        
        setEl('dh-dash-referral-count', referrals.filter(r => !r.assignedStaff).length);

        const crs = await localforage.getItem('comments_recommendations') || [];
        setEl('dh-dash-cr-count', crs.length);

        // C&R progress bar (relative to total pars)
        const crBar = document.getElementById('dh-cr-bar');
        if (crBar && pars.length > 0) {
            const pct = Math.min(100, Math.round((crs.length / pars.length) * 100));
            crBar.style.width = pct + '%';
        }



        // ── CHART 1: Project Status Donut ──
        const statusCtx = document.getElementById('dh-status-chart');
        if (statusCtx && ChartJS) {
            destroyChart('dh-status-chart');
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
        const subCtx = document.getElementById('dh-submission-chart');
        if (subCtx && ChartJS) {
            destroyChart('dh-submission-chart');
            new ChartJS(subCtx, {
                type: 'bar',
                data: {
                    labels: ['Submitted','For Revision','Resubmitted','Under Review','Approved'],
                    datasets: [{
                        label: 'Submissions',
                        data: [submittedCount, revisionCount, resubmittedCount, reviewCount, approvedCount],
                        backgroundColor: [
                            'rgba(99,102,241,0.85)',
                            'rgba(245,158,11,0.85)',
                            'rgba(124,58,237,0.85)',
                            'rgba(14,165,233,0.85)',
                            'rgba(16,185,129,0.85)'
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


        // ── Recent Referrals Table Event Wiring ──
        const referralTbody = document.getElementById('dh-dash-referrals-tbody');
        if (referralTbody) {
            // Since we now render the table via Blade, we only need to wire the buttons
            const wireAssignButtons = () => {
                referralTbody.querySelectorAll('.dash-assign-btn').forEach(btn => {
                    if (btn._wired) return;
                    btn._wired = true;
                    btn.onclick = async () => {
                        const rid = btn.dataset.id;
                        const modalEl = document.getElementById('dhAssignStaffModal');
                        const staffSel = document.getElementById('dhAssignStaffSelect');
                        document.getElementById('dhAssignReferralId').value = rid;

                        // Load division staff from API
                        try {
                            const staffRes = await fetch(`/referrals/staff?division_id=${currentUser.division_id}`, {
                                headers: { 'Accept': 'application/json' }
                            });
                            if (staffRes.ok) {
                                const staffJson = await staffRes.json();
                                if (staffSel) {
                                    staffSel.innerHTML = '<option value="">-- Select Staff member --</option>';
                                    staffJson.data.forEach(s => {
                                        const opt = document.createElement('option');
                                        opt.value = s.id;
                                        opt.textContent = `${s.name || s.email} (${s.email})`;
                                        staffSel.appendChild(opt);
                                    });
                                }
                            }
                        } catch (e) {
                            console.error('Failed to load division staff', e);
                            // Fallback
                            const allUsers = await localforage.getItem('system_users') || [];
                            const myStaff = allUsers.filter(u => u.role === 'staff' && (u.division || '').toUpperCase() === myDivision);
                            if (staffSel) {
                                staffSel.innerHTML = '<option value="">-- Select Staff member --</option>';
                                myStaff.forEach(s => {
                                    const opt = document.createElement('option');
                                    opt.value = s.id || s.email;
                                    opt.textContent = `${s.name || s.email} (${s.email})`;
                                    staffSel.appendChild(opt);
                                });
                            }
                        }

                        if (modalEl) new bootstrap.Modal(modalEl).show();
                    };
                });
            };
            wireAssignButtons();
        }

        // ── Assessed Reports Table (Handled via Blade) ──
        // Overwriting this with localforage data is no longer necessary as we use real database records now.
        // If we want to add dynamic behavior later, we can wire events here.

        // ── Wire assignment confirmation button on dashboard ──
        const confirmBtn = document.getElementById('dhConfirmAssignBtn');
        if (confirmBtn && !confirmBtn._dashWired) {
            confirmBtn._dashWired = true;
            confirmBtn.onclick = async () => {
                const rid = document.getElementById('dhAssignReferralId').value;
                const staffId = document.getElementById('dhAssignStaffSelect').value;
                if (!staffId) return;

                try {
                    const res = await fetch(`/referrals/${rid}/assign-staff`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ to_user_id: staffId })
                    });

                    if (res.ok) {
                        // Success - optimistically update localforage for other parts of the dashboard that might use it
                        const allRefs = await localforage.getItem('project_referrals') || [];
                        const idx = allRefs.findIndex(r => r.id === rid);
                        const staffText = document.getElementById('dhAssignStaffSelect').options[document.getElementById('dhAssignStaffSelect').selectedIndex].text;
                        
                        if (idx >= 0) {
                            allRefs[idx].assignedStaff = staffText;
                            allRefs[idx].status = 'Assigned';
                            await localforage.setItem('project_referrals', allRefs);
                        }

                        // Manually remove the row from the DOM since it's a Blade-rendered table
                        const assignBtn = document.querySelector(`.dash-assign-btn[data-id="${rid}"]`);
                        if (assignBtn) {
                            const tr = assignBtn.closest('tr');
                            if (tr) tr.remove();

                            const tbody = document.getElementById('dh-dash-referrals-tbody');
                            if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted small">No active referrals for your division</td></tr>`;
                            }
                        }

                        const modalEl = document.getElementById('dhAssignStaffModal');
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                        
                        if (window.showSimpleAlert) window.showSimpleAlert('Staff assigned successfully.', 'success');
                        
                        // Update counts via re-initialization
                        initDivisionHeadDashboard();
                    } else {
                        const err = await res.json();
                        alert('Assignment failed: ' + (err.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('Failed to assign staff', e);
                    alert('An error occurred during assignment.');
                }
            };
        }

    } catch (err) {
        console.error('[DivisionHeadDashboard]', err);
    }

    if (window.lucide) window.lucide.createIcons();
}
