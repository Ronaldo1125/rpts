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

        const masterStatuses = window.__MASTER_PROJECT_STATUSES__ || {
            ongoing: 0, proposed: 0, completed: 0, terminated: 0, suspended: 0, dropped: 0
        };

        const ongoing    = masterStatuses.ongoing;
        const proposed   = masterStatuses.proposed;
        const completed  = masterStatuses.completed;
        const terminated = masterStatuses.terminated;
        const suspended  = masterStatuses.suspended;
        const dropped    = masterStatuses.dropped;

        setEl('dh-dash-proj-ongoing',    ongoing);
        setEl('dh-dash-proj-proposed',   proposed);
        setEl('dh-dash-proj-completed',  completed);
        setEl('dh-dash-proj-terminated', terminated);
        setEl('dh-dash-proj-suspended',  suspended);
        setEl('dh-dash-proj-dropped',    dropped);

        // ── CIPG Submission Overview ──
        const pipeCounts = window.__SUBMISSION_PIPELINE_COUNTS__ || {
            submitted: 0, for_revision: 0, resubmitted: 0, incomplete: 0, revised: 0, validated: 0, seccom: 0, rdc: 0, approved: 0
        };
        const submittedCount   = pipeCounts.submitted;
        const revisionCount    = pipeCounts.for_revision;
        const resubmittedCount = pipeCounts.resubmitted;
        const incompleteCount  = pipeCounts.incomplete;
        const revisedCount     = pipeCounts.revised;
        const validatedCount   = pipeCounts.validated;
        const seccomCount      = pipeCounts.seccom;
        const rdcCount         = pipeCounts.rdc;
        const approvedCount    = pipeCounts.approved;

        // ── Division Head Exclusive ──
        const pars = await localforage.getItem('project_assessments') || [];
        // PAR count is now rendered directly by Blade

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
                    labels: ['Submitted','For Revision','Incomplete','Revised','Resubmitted','Validated','SecCom','RDC','Approved'],
                    datasets: [{
                        label: 'Submissions',
                        data: [submittedCount, revisionCount, incompleteCount, revisedCount, resubmittedCount, validatedCount, seccomCount, rdcCount, approvedCount],
                        backgroundColor: [
                            'rgba(99,102,241,0.85)',
                            'rgba(245,158,11,0.85)',
                            'rgba(244,63,94,0.85)',
                            'rgba(147,51,234,0.85)',
                            'rgba(124,58,237,0.85)',
                            'rgba(14,165,233,0.85)',
                            'rgba(236,72,153,0.85)',
                            'rgba(20,184,166,0.85)',
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

                referralTbody.querySelectorAll('.dash-reject-btn').forEach(btn => {
                    if (btn._wiredReject) return;
                    btn._wiredReject = true;
                    btn.onclick = async (e) => {
                        e.preventDefault();
                        const subId = btn.dataset.subId;
                        const title = btn.dataset.title;

                        const modalId = `reject-modal-${subId}`;
                        let modal = document.getElementById(modalId);
                        if (modal) modal.remove();

                        modal = document.createElement('div');
                        modal.id = modalId;
                        modal.className = 'position-fixed w-100 h-100 top-0 left-0 d-flex align-items-center justify-content-center';
                        modal.style.background = 'rgba(0,0,0,0.5)';
                        modal.style.zIndex = '9999';
                        modal.innerHTML = `
                        <div class="bg-white rounded-4 shadow-lg overflow-hidden" style="width: 400px; max-width: 90vw;">
                          <div class="p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                              <div class="bg-danger bg-opacity-10 p-2 rounded-3">
                                <i data-lucide="x-circle" width="20" style="color:#dc2626;"></i>
                              </div>
                              <div>
                                <h6 class="fw-bold mb-0 text-dark">Reject — Division Referral</h6>
                                <p class="text-muted small mb-0" style="font-size:0.75rem;">"${title}"</p>
                              </div>
                            </div>
                            <div class="alert alert-warning border-0 rounded-3 small py-2 px-3 mb-3" style="background:#fffbeb;color:#92400e;">
                              This will mark the referral as <strong>Rejected</strong> and return the submission.
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
                                    body: JSON.stringify({ context: 'division', notes }),
                                });
                                const data = await res.json();
                                modal.remove();
                                if (data.success) {
                                    if (window.showSimpleAlert) window.showSimpleAlert(data.message, 'success');
                                    const row = btn.closest('tr');
                                    if (row) { 
                                        row.style.opacity = '0'; 
                                        row.style.transition = 'opacity 0.3s'; 
                                        setTimeout(() => {
                                            row.remove();
                                            const tbodyCheck = document.getElementById('dh-dash-referrals-tbody');
                                            if (tbodyCheck && tbodyCheck.querySelectorAll('tr').length === 0) {
                                                tbodyCheck.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted small">No active referrals for your division</td></tr>`;
                                            }
                                        }, 300); 
                                    }
                                } else {
                                    if (window.showSimpleAlert) window.showSimpleAlert(data.message || 'Failed to reject referral.', 'danger');
                                }
                            } catch (err) {
                                modal.remove();
                                if (window.showSimpleAlert) window.showSimpleAlert('An error occurred. Please try again.', 'danger');
                            }
                        };
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
