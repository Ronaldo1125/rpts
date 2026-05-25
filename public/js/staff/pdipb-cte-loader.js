/**
 * PDIPB Staff — Completeness Test and Validation Intake Loader
 *
 * Shows all submitted (non-draft) CPP submissions automatically.
 * Staff can flag a submission as "Complete" (passes to next stage)
 * or "Send Feedback" (pushes it back to the agency with a message).
 */

export async function initPdipbCteLoader(targetTbodyId = 'cte-tbody') {
    const tbody = document.getElementById(targetTbodyId);
    const countSpan = document.getElementById('cte-count');
    const searchInput = document.getElementById('cteSearchInput');

    if (!tbody) return;

    // ── Helpers ──────────────────────────────────────────────────────────────
    function _statusBadge(st) {
        const map = {
            'Submitted':    ['#dbeafe', '#1e40af'],
            'Review':       ['#e0f2fe', '#0369a1'],
            'Approved':     ['#dcfce7', '#15803d'],
            'Validated':    ['#f0fdf4', '#166534'],
            'Incomplete':   ['#fff1f2', '#e11d48'],
            'Revised':      ['#f5f3ff', '#5b21b6'],
            'For Revision': ['#fef3c7', '#92400e'],
            'Resubmitted':  ['#ede9fe', '#5b21b6'],
            'Rejected':     ['#fee2e2', '#991b1b'],
        };
        const [bg, color] = map[st] || ['#f1f5f9', '#334155'];
        return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.7rem;padding:0.3em 0.75em;">${st || 'Submitted'}</span>`;
    }

    function _getStatus(s) {
        if (!s) return 'Unknown';
        if (s.status?.trim()) return s.status;
        if (s.submissionStatus?.trim()) return s.submissionStatus;
        if (s.projectStatus?.trim()) return s.projectStatus;
        if (s.cppStatus?.trim()) return s.cppStatus;
        return 'Submitted';
    }

    function _timeAgo(iso) {
        if (!iso) return '—';
        const diff = Math.floor((Date.now() - new Date(iso)) / 1000);
        if (diff < 60)   return 'Just now';
        if (diff < 3600) return `${Math.floor(diff/60)}m ago`;
        if (diff < 86400)return `${Math.floor(diff/3600)}h ago`;
        return new Date(iso).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // ── Load & Render ─────────────────────────────────────────────────────────
    async function render(filterTerm = '') {
        const all = window.__ADMIN_DASHBOARD_SUBMISSIONS__ && window.__ADMIN_DASHBOARD_SUBMISSIONS__.length > 0
            ? window.__ADMIN_DASHBOARD_SUBMISSIONS__
            : await localforage.getItem('cpp_submissions') || [];

        const currentUser = (window.__CURRENT_USER__ || {});
        const currentEmail = String(currentUser.email || '').toLowerCase();
        const currentId = String(currentUser.id || '');

        // Show only submissions that are pending PDIPB review:
        let rows = all.filter(s => {
            const st = _getStatus(s);
            const stage = (s.stage || '').trim();

            // Status can be 'Submitted', 'Resubmitted' or 'Review', but stage must be Completeness Test and Validation
            const isTarget = ['Review', 'Submitted', 'Resubmitted'].includes(st) && stage === 'Completeness Test and Validation';
            if (!isTarget) return false;

            // Only show rows assigned to the currently logged-in PDIPBD staff.
            const rawEmail = s.assignedStaffEmail || s.assigned_staff_email || '';
            const assignedEmail = rawEmail ? String(rawEmail).toLowerCase() : '';
            
            const rawId = s.assignedStaffId || s.assigned_staff_id || s.assigned_to_user_id;
            const assignedId = rawId ? String(rawId) : '';
            
            // If it's from the window data, it might have assignedStaff object
            const assignedStaffId = s.assignedStaff?.id ? String(s.assignedStaff.id) : assignedId;
            
            return (assignedId && (assignedId === currentId || assignedStaffId === currentId)) || 
                   (assignedEmail && assignedEmail === currentEmail) ||
                   (s.referredToPdipb && !assignedId); // Show unassigned to all PDIPBD staff in intake
        });

        if (filterTerm) {
            const term = filterTerm.toLowerCase();
            rows = rows.filter(s => {
                const title  = (s.title  || s.formData?.['f-title']  || '').toLowerCase();
                const agency = (s.agency || s.formData?.['f-agency'] || '').toLowerCase();
                return title.includes(term) || agency.includes(term);
            });
        }

        if (countSpan) countSpan.textContent = `Showing ${rows.length > 0 ? 1 : 0} to ${rows.length} of ${rows.length} entries`;
        const workflowBadge = document.getElementById('staff-dash-initial-count');
        if (workflowBadge) workflowBadge.textContent = rows.length;

        if (rows.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i data-lucide="inbox" width="40" class="mb-3 opacity-25 d-block mx-auto"></i>
                            <p class="mb-1 fw-medium">No submitted forms yet.</p>
                            <p class="small mb-0">CPP submissions from agencies will appear here for review.</p>
                        </div>
                    </td>
                </tr>`;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        tbody.innerHTML = rows.map(s => {
            const sid    = s.id || '';
            const title  = s.title  || s.formData?.['f-title']  || 'Untitled';
            const agency = s.agency || s.formData?.['f-agency'] || '—';
            const abbr   = window.getAgencyAbbreviation ? window.getAgencyAbbreviation(agency) : agency;
            const sector = s.sector || s.formData?.['f-sector'] || '—';
            const st     = _getStatus(s);
            const date   = s.submittedAt || s.updatedAt || s.date || '';

            return `<tr data-sid="${sid}">
                <td class="py-3 ps-3" style="max-width:260px;">
                    <div class="fw-semibold small text-dark">${title}</div>
                    <div class="text-muted" style="font-size:0.7rem;">${sector}</div>
                </td>
                <td class="small text-muted py-3">${abbr}</td>
                <td class="py-3">${_statusBadge(st)}</td>
                <td class="small text-muted py-3">${_timeAgo(date)}</td>
                <td class="py-3 pe-3 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold cte-view-cpp"
                            data-sid="${sid}"
                            style="color:#0369a1; border:none; font-size:0.75rem;">
                            View
                        </button>
                        <button class="btn btn-sm btn-danger rounded-pill px-3 fw-bold referral-reject-btn"
                            data-sub-id="${sid}"
                            data-context="cte"
                            data-title="${title.replace(/"/g, '&quot;')}"
                            style="font-size:0.75rem;">
                            Reject
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');

        if (window.lucide) window.lucide.createIcons();
        _attachHandlers();
    }

    function _showRejectModal(subId, title, context, triggerBtn) {
        const modalId = `reject-modal-${subId}`;
        let modal = document.getElementById(modalId);
        if (modal) modal.remove();

        const labelMap = {
            par: 'PAR Draft',
            revised: 'Revised Submission',
            sectoral: 'Sectoral Presentation',
            rdc: 'RDC Presentation',
            cte: 'Completeness Test'
        };
        const label = labelMap[context] || 'Referral';

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
                    const row = triggerBtn.closest('tr');
                    if (row) { row.style.opacity = '0'; row.style.transition = 'opacity 0.3s'; setTimeout(() => row.remove(), 300); }
                    const badge = document.getElementById('staff-dash-initial-count');
                    if (badge) {
                        const current = parseInt(badge.textContent, 10) || 0;
                        badge.textContent = Math.max(0, current - 1);
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

    // ── Action Handlers ───────────────────────────────────────────────────────
    function _attachHandlers() {
        // View CPP: navigate to the server-rendered CPP view page
        tbody.querySelectorAll('.cte-view-cpp').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const sid = btn.dataset.sid;
                // Navigate to the Laravel show route — the blade renders the full CPP
                window.location.href = `/v2/cipg_submissions/${sid}/show`;
            });
        });

        // Reject handler
        tbody.querySelectorAll('.referral-reject-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const sid = btn.dataset.subId;
                const title = btn.dataset.title;
                const context = btn.dataset.context;
                _showRejectModal(sid, title, context, btn);
            });
        });

        // Feedback handler
        tbody.querySelectorAll('.cte-feedback-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const sid = btn.dataset.sid;
                const title = btn.dataset.title;
                _showFeedbackModal(title, async (msg) => {
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.content;
                        const res = await fetch(`/referrals/staff-action/${sid}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ action: 'feedback', notes: msg })
                        });
                        const data = await res.json();
                        if (data.success) {
                            if (window.showSimpleAlert) window.showSimpleAlert(data.message || `Feedback sent for "${title}".`, 'success');
                            
                            // Proactively update local state to reflect the change
                            if (window.__ADMIN_DASHBOARD_SUBMISSIONS__) {
                                window.__ADMIN_DASHBOARD_SUBMISSIONS__ = window.__ADMIN_DASHBOARD_SUBMISSIONS__.filter(s => String(s.id) !== String(sid));
                            }
                            // Also update localforage
                            const all = await localforage.getItem('cpp_submissions') || [];
                            const updated = all.filter(s => String(s.id) !== String(sid));
                            await localforage.setItem('cpp_submissions', updated);

                            // Re-render immediately
                            render();
                        } else {
                            if (window.showSimpleAlert) window.showSimpleAlert(data.message || 'Error sending feedback.', 'error');
                        }
                    } catch (err) {
                        console.error(err);
                        if (window.showSimpleAlert) window.showSimpleAlert('Network error while sending feedback.', 'error');
                    }
                });
            });
        });

        // Complete handler
        tbody.querySelectorAll('.cte-complete-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const sid = btn.dataset.sid;
                const title = btn.dataset.title;
                const agency = btn.dataset.agency;
                
                // For simplicity, we'll use the one defined in cpp-form.js if available
                // or a basic confirmation if not. 
                // But the user said "put back the popup modal" which usually implies the checklist.
                if (window._showPdipbCteModal) {
                    window._showPdipbCteModal(title, agency, sid, async (formData) => {
                        await _handleComplete(sid, title, agency, formData);
                    });
                } else {
                    // Fallback to basic confirm or we should have duplicated the function
                    if (confirm(`Mark "${title}" as Complete?`)) {
                        _handleComplete(sid, title, agency, { authorizedOfficial: 'N/A' });
                    }
                }
            });
        });
    }

    async function _handleComplete(sid, title, agency, formData) {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/referrals/staff-action/${sid}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: 'complete', formData: formData })
            });
            const data = await res.json();
            if (data.success) {
                if (window.showSimpleAlert) window.showSimpleAlert(data.message || `"${title}" validated and forwarded.`, 'success');
                
                // Proactively update local state to reflect the change
                if (window.__ADMIN_DASHBOARD_SUBMISSIONS__) {
                    window.__ADMIN_DASHBOARD_SUBMISSIONS__ = window.__ADMIN_DASHBOARD_SUBMISSIONS__.filter(s => String(s.id) !== String(sid));
                }
                // Also update localforage
                const all = await localforage.getItem('cpp_submissions') || [];
                const updated = all.filter(s => String(s.id) !== String(sid));
                await localforage.setItem('cpp_submissions', updated);

                // Re-render immediately
                render();
            } else {
                if (window.showSimpleAlert) window.showSimpleAlert(data.message || 'Error completing submission.', 'error');
            }
        } catch (err) {
            console.error(err);
            if (window.showSimpleAlert) window.showSimpleAlert('Network error while completing submission.', 'error');
        }
    }

    // ── Feedback Modal ────────────────────────────────────────────────────────
    function _showFeedbackModal(title, onSend) {
        const existing = document.getElementById('pdipb-feedback-modal');
        if (existing) {
            const old = bootstrap.Modal.getInstance(existing);
            if (old) old.dispose();
            existing.remove();
        }

        const html = `
        <div class="modal fade" id="pdipb-feedback-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
                    <div class="modal-header border-0" style="background:#1e40af;color:#fff;padding:1.25rem 1.5rem;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center justify-content-center rounded-2"
                                style="width:32px;height:32px;background:rgba(255,255,255,0.15);">
                                <i data-lucide="message-circle" width="15" class="text-white"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0" style="font-size:0.95rem;">Send Feedback</h6>
                                <p class="mb-0 text-white opacity-75" style="font-size:0.72rem;">Submission will be returned to agency for revision</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="px-4 py-3" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                            <div class="x-small text-muted fw-bold text-uppercase mb-1" style="font-size:0.65rem;letter-spacing:.06em;">Submission</div>
                            <div class="fw-bold text-dark lh-sm" style="font-size:0.875rem;">${title}</div>
                        </div>
                        <div class="px-4 pt-3 pb-4">
                            <label class="small fw-semibold text-dark mb-2 d-block">
                                Feedback / Instructions <span class="text-danger">*</span>
                            </label>
                            <textarea id="pdipb-feedback-text" rows="4"
                                class="form-control border-0 rounded-3"
                                style="background:#f1f5f9;resize:none;font-size:0.85rem;"
                                placeholder="Describe what needs to be revised or corrected..."></textarea>
                            <div id="pdipb-feedback-err" class="text-danger small mt-1" style="display:none;">Please enter feedback before sending.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top d-flex gap-2 justify-content-end" style="padding:0.875rem 1.5rem;background:#f8fafc;">
                        <button type="button" class="btn btn-sm rounded-pill px-4 fw-medium"
                            style="background:#e2e8f0;color:#475569;border:none;" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm rounded-pill px-4 fw-semibold text-white" id="pdipb-feedback-send"
                            style="background:#1e40af;border:none;">
                            <i data-lucide="send" width="13" class="me-1"></i>Send Feedback
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', html);
        if (window.lucide) window.lucide.createIcons();

        const modalEl = document.getElementById('pdipb-feedback-modal');
        const modal   = new bootstrap.Modal(modalEl);
        modal.show();

        document.getElementById('pdipb-feedback-send').addEventListener('click', () => {
            const msg = document.getElementById('pdipb-feedback-text').value.trim();
            const err = document.getElementById('pdipb-feedback-err');
            if (!msg) { if (err) err.style.display = ''; return; }
            modal.hide();
            modalEl.addEventListener('hidden.bs.modal', () => {
                modalEl.remove();
                onSend(msg);
            }, { once: true });
        });
    }

    // ── Search ────────────────────────────────────────────────────────────────
    if (searchInput) {
        const fresh = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(fresh, searchInput);
        fresh.addEventListener('input', e => render(e.target.value));
    }

    // ── Swap table header for PDIPB staff view ────────────────────────────────
    const thead = tbody.closest('table')?.querySelector('thead tr');
    if (thead) {
        thead.innerHTML = `
            <th class="small text-secondary ps-3">Project Title</th>
            <th class="small text-secondary">Agency</th>
            <th class="small text-secondary">Status</th>
            <th class="small text-secondary">Submitted</th>
            <th class="small text-secondary text-end pe-3" data-sort-skip="true">Actions</th>`;
    }

    // Also hide the "New Validation" button — PDIPB staff doesn't manually create CTEs
    const newBtn = document.getElementById('newCteBtn');
    if (newBtn) newBtn.style.display = 'none';

    // Initial render
    render();
}
