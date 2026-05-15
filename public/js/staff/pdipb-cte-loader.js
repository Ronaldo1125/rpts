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
                        <button class="btn btn-sm rounded-pill px-3 fw-semibold cte-view-cpp"
                            data-sid="${sid}"
                            style="background:#e0f2fe;color:#0369a1;border:none;font-size:0.75rem;">
                            <i data-lucide="eye" width="13" class="me-1"></i>View CPP
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');

        if (window.lucide) window.lucide.createIcons();
        _attachHandlers();
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
