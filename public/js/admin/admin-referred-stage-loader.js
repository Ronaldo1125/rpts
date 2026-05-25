/**
 * admin-referred-stage-loader.js
 * Powers the "Referral to Division" stage panel in the admin dashboard.
 *
 * Reads project_referrals from localforage:
 *   - status === 'Validated'   → awaiting admin referral to a division
 *   - status === 'Referred'    → already referred; counted per division in KPI cards
 */

window.initReferredStage = async function () {
    const tbody       = document.getElementById('ref-validated-tbody');
    const countBadge  = document.getElementById('ref-list-count');
    const kpiPfpd     = document.getElementById('ref-kpi-pfpd');
    const kpiPmed     = document.getElementById('ref-kpi-pmed');
    const kpiDrd      = document.getElementById('ref-kpi-drd');
    const kpiPending  = document.getElementById('ref-kpi-pending');
    const searchInput = document.getElementById('refSearchInput');

    if (!tbody) return;

    // ── Helpers ────────────────────────────────────────────────────────────────
    function _timeAgo(iso) {
        if (!iso) return '—';
        const diff = Math.floor((Date.now() - new Date(iso)) / 1000);
        if (diff < 60)    return 'Just now';
        if (diff < 3600)  return `${Math.floor(diff / 60)}m ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
        return new Date(iso).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function _divBadge(div) {
        const map = {
            'PFPD': ['#dbeafe', '#1e40af'],
            'PMED': ['#bfdbfe', '#1d4ed8'],
            'DRD':  ['#e0f2fe', '#0369a1'],
        };
        const [bg, color] = map[div] || ['#f1f5f9', '#334155'];
        return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.7rem;padding:0.3em 0.75em;">${div || '—'}</span>`;
    }

    // ── Render ─────────────────────────────────────────────────────────────────
    async function render(filterTerm = '') {
        const all = await localforage.getItem('project_referrals') || [];
        
        let subs = await localforage.getItem('cpp_submissions') || [];
        const serverSubs = Array.isArray(window.__ADMIN_DASHBOARD_SUBMISSIONS__) ? window.__ADMIN_DASHBOARD_SUBMISSIONS__ : [];
        // Merge: Local updates in localforage must prioritize over stale server-side data
        if (serverSubs.length > 0) {
            const merged = new Map();
            // Load server data first
            serverSubs.forEach(s => merged.set(String(s.id || ''), s));
            // Overwrite with local updates (optimistic UI changes)
            subs.forEach(s => merged.set(String(s.id || ''), s));
            subs = Array.from(merged.values());
        }

        // KPI: count per division (status === 'Referred' or already has a division)
        const referred = all.filter(r => r.status === 'Referred' || (r.referredToDivision && r.status !== 'Validated'));
        const pfpdCount = referred.filter(r => r.referredToDivision === 'PFPD').length;
        const pmedCount = referred.filter(r => r.referredToDivision === 'PMED').length;
        const drdCount  = referred.filter(r => r.referredToDivision === 'DRD').length;

        // Pending = based on CIPG submission status being 'Validated'
        // We also check 'all' (project_referrals) to see if it was just referred locally
        const pendingSubs = subs.filter(s => {
            const status = (s.status || '').toLowerCase();
            const stage = (s.stage || '').toLowerCase();

            // Must be validated first
            if (status !== 'validated') return false;

            const isReferredLocally = all.some(r => String(r.submissionId || r.cteId) === String(s.id) && r.status === 'Referred');
            if (isReferredLocally) return false;
            
            const sReferrals = Array.isArray(s.referrals) ? s.referrals : [];
            const allRefsForSub = [...sReferrals, ...all.filter(r => String(r.submissionId || r.cteId) === String(s.id))];
            
            const divRefs = allRefsForSub.filter(r => {
                const rStage = (r.stage || '').toLowerCase();
                const rStatus = (r.status || '').toLowerCase();
                return rStage === 'project appraisal' || rStatus === 'referred to division' || rStatus === 'referred';
            });
            const hasActiveDivRef = divRefs.some(r => (r.status || '').toLowerCase() !== 'rejected');
            
            if (hasActiveDivRef) return false;
            
            const hasRejectedDivRef = divRefs.some(r => (r.status || '').toLowerCase() === 'rejected');
            if (hasRejectedDivRef) return true;

            if (stage === 'project appraisal') return false;

            return true;
        });

        // Map submissions to the table row structure expected
        let rows = pendingSubs.map(s => {
            const ref = all.find(r => String(r.submissionId || r.cteId) === String(s.id) && r.status !== 'Rejected') || {};
            const sReferrals = Array.isArray(s.referrals) ? s.referrals : [];
            const rejectedRef = sReferrals.find(r => r.status === 'Rejected' && r.stage === 'Project Appraisal') ||
                                all.find(r => String(r.submissionId || r.cteId) === String(s.id) && r.status === 'Rejected');

            let rejectText = '';
            if (rejectedRef) {
                let rejecterName = rejectedRef.to_user_name || rejectedRef.toUserName || 'Division Head';
                let rawNotes = rejectedRef.notes || '';
                const match = rawNotes.match(/\[Rejected(?: by (.*?))?\]/);
                if (match) {
                    if (match[1]) rejecterName = match[1].trim();
                    rawNotes = rawNotes.replace(match[0], '').trim();
                }
                rejectText = `Rejected by ${rejecterName}: ${rawNotes.replace(/"/g, '&quot;')}`;
            }

            return {
                id: ref.id || s.id, // Fallback to submission ID if no referral exists
                submissionId: s.id,
                projectTitle: s.title || s.formData?.['f-title'] || s.project_title || 'Untitled',
                agency: s.agency || s.formData?.['f-agency'] || (s.user?.agency?.agency_name) || '—',
                sector: s.sector || s.formData?.['f-sector'] || '—',
                referrerName: ref.referrerName || 'System',
                referrerRole: ref.referrerRole || 'PDIPB Staff',
                referralDate: ref.referralDate || s.updated_at || s.date || new Date().toISOString(),
                rejectText: rejectText
            };
        });

        if (kpiPfpd)    kpiPfpd.textContent    = pfpdCount;
        if (kpiPmed)    kpiPmed.textContent    = pmedCount;
        if (kpiDrd)     kpiDrd.textContent     = drdCount;
        if (kpiPending) kpiPending.textContent = rows.length;

        // Also update the workflow step badge to show actionable work (Validated/pending referrals)
        const stepBadge = document.getElementById('admin-dash-referred-count');
        if (stepBadge) stepBadge.textContent = rows.length;

        // Table: validated CPPs awaiting referral
        if (filterTerm) {
            const t = filterTerm.toLowerCase();
            rows = rows.filter(r =>
                (r.projectTitle || '').toLowerCase().includes(t) ||
                (r.agency       || '').toLowerCase().includes(t) ||
                (r.referrerName || '').toLowerCase().includes(t) ||
                (r.sector       || '').toLowerCase().includes(t)
            );
        }

        if (countBadge) countBadge.textContent = rows.length;

        if (rows.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="text-muted">
                            <i data-lucide="inbox" width="38" class="mb-3 opacity-25 d-block mx-auto"></i>
                            <p class="mb-1 fw-medium">No validated CPPs awaiting referral.</p>
                            <p class="small mb-0">Once PDIPB staff mark a CPP as complete, it will appear here for division referral.</p>
                        </div>
                    </td>
                </tr>`;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        tbody.innerHTML = rows.map(ref => {
            const rejectionIcon = ref.rejectText
                ? `<span class="rejection-info-icon me-2"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="${ref.rejectText}"
                        style="cursor:pointer;color:#f59e0b;vertical-align:middle;"
                    ><i data-lucide="info" width="14" height="14"></i></span>`
                : '';
                
            return `
            <tr data-ref-id="${ref.id}">
                <td class="py-3 ps-4" style="max-width:270px;">
                    <div class="fw-semibold small text-dark">${ref.projectTitle || '—'}</div>
                    <div class="text-muted" style="font-size:0.7rem;">ID: ${ref.submissionId || ref.id}</div>
                </td>
                <td class="small text-muted py-3">${ref.agency || '—'}</td>
                <td class="py-3">
                    <div class="small fw-semibold text-dark">${ref.referrerName || '—'}</div>
                    <div class="text-muted" style="font-size:0.68rem;">${ref.referrerRole || 'PDIPB Staff'}</div>
                </td>
                <td class="py-3">
                    <span class="badge rounded-pill fw-medium" 
                          style="background:#e8f0fe;color:#0032A6;font-size:0.7rem;padding:0.35em 0.8em;">
                        ${ref.sector}
                    </span>
                </td>
                <td class="py-3 pe-4 text-end">
                    ${rejectionIcon}
                    <button class="btn btn-sm rounded-pill fw-semibold px-3 btn-refer-modal shadow-sm d-inline-flex align-items-center justify-content-center mx-1"
                        style="background:#154A9A;color:#fff;border:none;font-size:0.75rem;"
                        data-ref-id="${ref.id}" data-title="${ref.projectTitle || ''}">
                        <i data-lucide="send" width="13" class="me-1"></i>Refer to Division
                    </button>
                </td>
            </tr>`;
        }).join('');

        if (window.lucide) window.lucide.createIcons();
        
        // Initialize Bootstrap tooltips for rejection info icons
        tbody.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            if (window.bootstrap?.Tooltip) {
                bootstrap.Tooltip.getInstance(el)?.dispose();
                new bootstrap.Tooltip(el, { trigger: 'hover', html: false });
            }
        });

        _attachHandlers();
    }

    // ── Action Handlers ────────────────────────────────────────────────────────
    function _attachHandlers() {
        document.querySelectorAll('.btn-refer-modal').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const refId = btn.dataset.refId;
                const title = btn.dataset.title;
                _showReferDivisionModal(refId, title);
            });
        });
    }

    // ── Validation/Referral Modal ──────────────────────────────────────────────
    function _showReferDivisionModal(refId, title) {
        const existing = document.getElementById('admin-refer-division-modal');
        if (existing) {
            try { bootstrap.Modal.getInstance(existing)?.dispose(); } catch (_) {}
            existing.remove();
        }

        document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="admin-refer-division-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                    <div style="height: 6px; background: linear-gradient(90deg, #154A9A, #1e6fd9);"></div>
                    <div class="modal-header border-0 pb-0 pt-4 px-4 text-center d-block">
                        <i data-lucide="send" width="36" class="text-primary mb-3 mx-auto opacity-75"></i>
                        <h5 class="modal-title fw-bold mb-1">Refer to Division</h5>
                        <p class="text-muted small mb-0 px-2">Assign this validated project to a division for the Project Assessment Report (PAR).</p>
                    </div>
                    <div class="modal-body p-4">
                        <div class="card bg-light border-0 rounded-12 mb-3">
                            <div class="card-body p-3 text-center">
                                <div class="small text-muted fw-semibold mb-1">Project Title</div>
                                <div class="fw-bold text-dark fs-0-85 text-truncate" title="${title}">${title}</div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label small fw-bold text-secondary">Target Division <span class="text-danger">*</span></label>
                            <select class="form-select border-0 shadow-sm rounded-12" id="adminDashReferTarget" style="font-size:0.9rem; padding:0.6rem 1rem;">
                                <option value="">-- Select Division --</option>
                                <option value="PFPD">PFPD (Project Formatting & Preparation Division)</option>
                                <option value="PMED">PMED (Project Monitoring & Evaluation Division)</option>
                                <option value="DRD">DRD (Development Research Division)</option>
                            </select>
                            <div id="adminDashReferTargetErr" class="text-danger small mt-1" style="display:none;">Please select a division.</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label small fw-bold text-secondary">Additional Notes <span class="text-muted fw-normal">(Optional)</span></label>
                            <textarea class="form-control border-0 shadow-sm rounded-12" id="adminDashReferNotes" rows="2" style="font-size:0.85rem; padding:0.6rem 1rem;" placeholder="Any specific instructions for the assessors..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-1 pb-4 px-4 d-flex justify-content-center gap-2 bg-white">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-muted" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn text-white rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2" id="btn-admin-confirm-referral" style="background:#154A9A;">
                            <i data-lucide="check" width="16"></i>Confirm Referral
                        </button>
                    </div>
                </div>
            </div>
        </div>`);

        if (window.lucide) window.lucide.createIcons();
        const modalEl = document.getElementById('admin-refer-division-modal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        document.getElementById('btn-admin-confirm-referral').addEventListener('click', async () => {
            const selectEl = document.getElementById('adminDashReferTarget');
            const errEl = document.getElementById('adminDashReferTargetErr');
            const notesEl = document.getElementById('adminDashReferNotes');
            
            if (!selectEl.value) {
                errEl.style.display = 'block';
                return;
            }
            errEl.style.display = 'none';

            const div = selectEl.value;
            const notes = notesEl.value.trim();

            const btn = document.getElementById('btn-admin-confirm-referral');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            btn.disabled = true;

            try {
                const response = await fetch('/referrals', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        cipg_submission_id: refId,
                        division_name: div,
                        notes: notes
                    })
                });

                if (!response.ok) {
                    throw new Error('Failed to submit referral to the server.');
                }
                
                // Optimistically update the UI
                const all = await localforage.getItem('project_referrals') || [];
                const idx = all.findIndex(r => String(r.submissionId || r.cteId) === String(refId));
                const currentUser = (window.__CURRENT_USER__ || {});
                
                if (idx >= 0) {
                    all[idx].referredToDivision = div;
                    all[idx].status             = 'Referred';
                    all[idx].assignedBy         = currentUser.name || currentUser.email || 'Admin';
                    all[idx].assignedAt         = new Date().toISOString();
                    
                    if (notes) {
                        all[idx].referralNotes = (all[idx].referralNotes ? all[idx].referralNotes + '\n\nAdmin Note: ' : 'Admin Note: ') + notes;
                    }
                } else {
                    all.push({
                        id: 'ref_' + Date.now(),
                        submissionId: refId,
                        projectTitle: title,
                        referredToDivision: div,
                        status: 'Referred',
                        assignedBy: currentUser.name || currentUser.email || 'Admin',
                        assignedAt: new Date().toISOString(),
                        referralNotes: notes ? 'Admin Note: ' + notes : '',
                        referralDate: new Date().toISOString()
                    });
                }
                
                await localforage.setItem('project_referrals', all);

                // Update submission stage
                const subs = await localforage.getItem('cpp_submissions') || [];
                const sIdx = subs.findIndex(s => String(s.id) === String(refId) || String(s.cteId) === String(refId));
                if (sIdx >= 0) {
                    subs[sIdx].stage = 'Project Appraisal';
                    await localforage.setItem('cpp_submissions', subs);
                }
                
                if (window.showSimpleAlert) {
                    window.showSimpleAlert(`"${title}" has been successfully referred to <strong>${div}</strong>. Stage updated to Project Appraisal.`, 'success');
                }
            } catch (error) {
                console.error('Referral Error:', error);
                if (window.showSimpleAlert) {
                    window.showSimpleAlert('An error occurred while referring the project. Please try again.', 'error');
                }
                btn.innerHTML = '<i data-lucide="check" width="16" class="me-2"></i>Confirm Referral';
                btn.disabled = false;
                if (window.lucide) window.lucide.createIcons();
                return;
            }
            
            setTimeout(() => {
                modal.hide();
                render(document.getElementById('refSearchInput')?.value || '');
            }, 300);
        });

        modalEl.addEventListener('hidden.bs.modal', () => modalEl.remove());
    }

    // ── Search ─────────────────────────────────────────────────────────────────
    if (searchInput) {
        const fresh = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(fresh, searchInput);
        fresh.addEventListener('input', e => render(e.target.value));
    }

    // ── Initial render ─────────────────────────────────────────────────────────
    render();
};
