@extends('layouts.app_v2')
@section('content')

<link rel="stylesheet" href="/css/referrals.css">
<style>
    .fade-out {
        opacity: 0;
        transform: translateX(20px);
        transition: all 0.3s ease;
    }
</style>
<section id="referrals" class="page-content active container-fluid py-4 text-dark">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Project Referrals</h2>
            <p class="text-muted small mb-0">List of projects referred from Completeness Test for PAR Assessment</p>
        </div>
    </div>

    <!-- Assign Staff Modal -->
    <div class="modal fade" id="assignStaffModal" tabindex="-1" aria-labelledby="assignStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-16 overflow-hidden">
                <div class="modal-accent-primary"></div>
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="assignStaffModalLabel">Assign Technical Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Select a staff member from <span id="assign-division-name" class="fw-bold text-dark"></span> to conduct the PAR assessment.</p>
                    <form id="assignStaffForm">
                        <input type="hidden" name="referralId" id="assignReferralId">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Technical Staff</label>
                            <select class="form-select bg-light border-0 py-2 rounded-12" name="staffName" id="assignStaffSelect" required>
                                <option value="">-- Select Staff member --</option>
                                <!-- Dynamic Options -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-rpts text-white px-5 rounded-pill" id="confirmAssignBtn">
                        Confirm Assignment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-16">
        <div class="card-body d-flex flex-column" style="min-height: 400px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            @include('partials.table-header')

            <div class="table-responsive table-overflow-visible">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary table-title-col">Project Title</th>
                            <th class="small text-secondary">Stage</th>
                            <th class="small text-secondary">Referrer</th>
                            <th class="small text-secondary">Target Division</th>
                            <th class="small text-secondary">Referral Date</th>
                            <th class="small text-secondary text-center">Status</th>
                            <th class="small text-secondary table-actions-col">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="referral-tbody">
                        @forelse($referrals as $ref)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark lh-sm">{{ $ref->project_title }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Agency: {{ $ref->agency ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 small fw-bold text-uppercase" style="font-size: 0.65rem;">
                                        {{ $ref->stage ?? 'Initial' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ optional($ref->referrer)->name ?? 'System' }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">
                                        @if($ref->referrer && $ref->referrer->roles->isNotEmpty())
                                            {{ ucwords(str_replace(['_', '-'], ' ', $ref->referrer->roles->first()->name)) }}
                                        @else
                                            Referrer
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary small">{{ $ref->toDivision->name ?? 'N/A' }}</div>
                                    <div class="text-muted mt-1" style="font-size: 0.68rem;">
                                        @if($ref->assignedStaff)
                                            <i data-lucide="user" width="10" class="me-1"></i>{{ $ref->assignedStaff->name }}
                                        @else
                                            <span class="text-warning italic">Unassigned</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-dark">{{ $ref->referral_date?->format('M d, Y') ?? $ref->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">Official Referral</div>
                                </td>
                                <td class="text-center">
                                    @if($ref->status === 'Assigned')
                                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">Assigned</span>
                                    @elseif($ref->status === 'Returned')
                                        <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1">Returned to Agency</span>
                                    @elseif($ref->status === 'Validated')
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1">Validated</span>
                                    @elseif($ref->status === 'Referred to Division')
                                        <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1">Referred to Division</span>
                                    @elseif($ref->status === 'Referred to Staff')
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-1">Referred to Staff</span>
                                    @else
                                        <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                            <i data-lucide="more-vertical" width="16"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                            @if(auth()->user()?->hasAnyRole(['division_head', 'chief']))
                                                <li><a class="dropdown-item py-2 assign-staff-btn" href="#" data-id="{{ $ref->id }}" data-division="{{ $ref->referred_to_division }}"><i data-lucide="user-plus" width="14" class="me-2"></i>Assign Staff</a></li>
                                                <li><a class="dropdown-item py-2 text-danger reject-referral-btn" href="#" data-sub-id="{{ $ref->cpp_submission_id ?? $ref->cipg_submission_id ?? '' }}" data-context="division" data-title="{{ str_replace('"', '&quot;', $ref->project_title) }}"><i data-lucide="x-circle" width="14" class="me-2"></i>Reject</a></li>
                                            @endif
                                            @if($ref->status === 'Pending PAR')
                                                <li><a class="dropdown-item py-2 text-primary start-par-dynamic" href="#" data-title="{{ $ref->project_title }}" data-agency="{{ $ref->agency }}" data-cte-id="{{ $ref->cpp_submission_id ?? '' }}"><i data-lucide="file-edit" width="14" class="me-2"></i>Start PAR</a></li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item py-2 text-danger delete-referral" href="#" data-id="{{ $ref->id }}"><i data-lucide="trash-2" width="14" class="me-2"></i>Remove</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No referrals found for your division.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.table-pagination', ['paginator' => $referrals])
        </div>
    </div>
</section>

<script type="module">
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    async function _api(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                ...(options.headers || {}),
            },
            ...options,
        });

        let data = null;
        try {
            data = await response.json();
        } catch (_) {
            data = null;
        }

        if (!response.ok) {
            const msg = data?.message || 'Request failed';
            throw new Error(msg);
        }

        return data;
    }

    function _statusBadge(status) {
        if (status === 'Accepted') return '<span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1">Accepted</span>';
        if (status === 'Processed' || status === 'Resolved') return '<span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1">Processed</span>';
        if (status === 'Assigned') return '<span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">Assigned</span>';
        return '<span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1">Awaiting Assignment</span>';
    }

    async function reloadTable() {
        location.reload();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const newReferralBtn = document.getElementById('newReferralBtn');
        const submitBtn = document.getElementById('submitReferralBtn');
        const projectSelect = document.getElementById('referralProjectSelect');
        const referralForm = document.getElementById('newReferralForm');

        async function _populateProjects() {
            if (!projectSelect) return;
            const res = await _api('/referrals/validated-projects');
            const finalCtes = Array.isArray(res?.data) ? res.data : [];
            projectSelect.innerHTML = '<option value="">-- Choose from Completeness Test Results --</option>';
            finalCtes.forEach(c => {
                const title = c.projectTitle || 'Untitled';
                const agency = c.agency || '';
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.dataset.projectTitle = title;
                opt.dataset.agency = agency;
                opt.textContent = `${title}${agency ? ' (' + agency + ')' : ''}`;
                projectSelect.appendChild(opt);
            });
        }

        if (newReferralBtn) {
            newReferralBtn.onclick = async () => {
                await _populateProjects();
                const modal = new bootstrap.Modal(document.getElementById('newReferralModal'));
                modal.show();
            };
        }

        if (submitBtn) {
            submitBtn.onclick = async () => {
                const fd = new FormData(referralForm);
                if (!fd.get('projectSelect') || !fd.get('referredToDivision')) {
                    if (window.showSimpleAlert) window.showSimpleAlert('Please select a project and target division.', 'warning');
                    return;
                }
                await _api('/referrals', {
                    method: 'POST',
                    body: JSON.stringify({
                        cipg_submission_id: fd.get('projectSelect'),
                        to_division_id: fd.get('to_division_id'),
                        notes: fd.get('referralNotes') || '',
                    }),
                });
                bootstrap.Modal.getInstance(document.getElementById('newReferralModal')).hide();
                referralForm.reset();
                if (window.showSimpleAlert) window.showSimpleAlert('Referral created successfully.', 'success');
                setTimeout(() => reloadTable(), 1500);
            };
        }

        // Delete Referral
        document.querySelectorAll('.delete-referral').forEach(btn => {
            btn.onclick = async (e) => {
                e.preventDefault();
                window.showConfirmModal({
                    title: 'Remove Referral',
                    message: 'Are you sure you want to remove this referral?',
                    confirmClass: 'btn-danger',
                    onConfirm: async () => {
                        const id = btn.dataset.id;
                        try {
                            await _api(`/referrals/${id}`, { method: 'DELETE' });
                            if (window.showSimpleAlert) window.showSimpleAlert('Referral removed successfully.', 'success');
                            
                            // Immediately remove row from DOM
                            const row = btn.closest('tr');
                            if (row) {
                                row.classList.add('fade-out');
                                setTimeout(() => {
                                    row.remove();
                                    // Update count
                                    const countEl = document.getElementById('referral-count');
                                    if (countEl) {
                                        const current = document.querySelectorAll('#referral-tbody tr').length;
                                        countEl.textContent = `Showing ${current} of ${current} entries`;
                                    }
                                }, 300);
                            }
                        } catch (err) {
                            if (window.showSimpleAlert) window.showSimpleAlert('Failed to remove referral.', 'danger');
                        }
                    }
                });
            };
        });

        // Assign Staff Trigger
        document.querySelectorAll('.assign-staff-btn').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                const division = btn.dataset.division;
                document.getElementById('assignReferralId').value = id;
                document.getElementById('assign-division-name').textContent = division;
                const staffSelect = document.getElementById('assignStaffSelect');
                staffSelect.innerHTML = '<option value="">-- Select Staff member --</option>';
                _api(`/referrals/staff?division=${encodeURIComponent(division || '')}`)
                    .then(res => {
                        const list = Array.isArray(res?.data) ? res.data : [];
                        list.forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.id;
                            opt.textContent = `${s.name}${s.email ? ' — ' + s.email : ''}`;
                            staffSelect.appendChild(opt);
                        });
                        if (!list.length) {
                            const opt = document.createElement('option');
                            opt.value = '';
                            opt.textContent = 'No staff found for division';
                            opt.disabled = true;
                            staffSelect.appendChild(opt);
                        }
                    })
                    .catch(() => {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = 'Unable to load staff';
                        opt.disabled = true;
                        staffSelect.appendChild(opt);
                    });
                const modal = new bootstrap.Modal(document.getElementById('assignStaffModal'));
                modal.show();
            };
        });

        const confirmAssignBtn = document.getElementById('confirmAssignBtn');
        if (confirmAssignBtn) {
            confirmAssignBtn.onclick = async () => {
                const referralId = document.getElementById('assignReferralId').value;
                const staffId = document.getElementById('assignStaffSelect').value;
                if (!staffId) {
                    if (window.showSimpleAlert) window.showSimpleAlert('Please select a staff member.', 'warning');
                    return;
                }
                try {
                    const res = await _api(`/referrals/${referralId}/assign-staff`, {
                        method: 'POST',
                        body: JSON.stringify({ staff_id: staffId }),
                    });
                    const modalEl = document.getElementById('assignStaffModal');
                    bootstrap.Modal.getInstance(modalEl).hide();
                    if (window.showSimpleAlert) window.showSimpleAlert('Project assignment updated.', 'success');
                    
                    // Immediately remove row from DOM
                    const row = document.querySelector(`.assign-staff-btn[data-id="${referralId}"]`).closest('tr');
                    if (row) {
                        row.classList.add('fade-out');
                        setTimeout(() => {
                            row.remove();
                            // Update count
                            const countEl = document.getElementById('referral-count');
                            if (countEl) {
                                const current = document.querySelectorAll('#referral-tbody tr').length;
                                countEl.textContent = `Showing ${current} of ${current} entries`;
                            }
                        }, 300);
                    }
                } catch (err) {
                    if (window.showSimpleAlert) window.showSimpleAlert('Failed to assign staff.', 'danger');
                }
            };
        }

        // Start PAR from Dynamic Rows
        document.querySelectorAll('.start-par-dynamic').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                sessionStorage.removeItem('par_edit_id');
                sessionStorage.setItem('par_prefill_title', btn.dataset.title);
                sessionStorage.setItem('par_prefill_agency', btn.dataset.agency);
                if (btn.dataset.cteId) sessionStorage.setItem('par_prefill_cte_id', btn.dataset.cteId);
                if (window.switchPage) window.switchPage('project-assessment-report-form');
            };
        });

        // Reject Referral Logic
        document.querySelectorAll('.reject-referral-btn').forEach(btn => {
            btn.onclick = (e) => {
                e.preventDefault();
                const subId = btn.dataset.subId;
                const title = btn.dataset.title;
                const context = btn.dataset.context;
                _showRejectModal(subId, title, context, btn);
            };
        });

        function _showRejectModal(subId, title, context, triggerBtn) {
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
                        body: JSON.stringify({ context, notes }),
                    });
                    const data = await res.json();
                    modal.remove();
                    if (data.success) {
                        if (window.showSimpleAlert) window.showSimpleAlert(data.message, 'success');
                        const row = triggerBtn.closest('tr');
                        if (row) { row.style.opacity = '0'; row.style.transition = 'opacity 0.3s'; setTimeout(() => row.remove(), 300); }
                        // Also decrement count
                        const countEl = document.getElementById('referral-count');
                        if (countEl) {
                            const currentRows = document.querySelectorAll('#referral-tbody tr:not(.fade-out)').length - 1; // -1 for the one being removed
                            if(currentRows >= 0) countEl.textContent = `Showing ${currentRows} of ${currentRows} entries`;
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

        if (window.lucide) window.lucide.createIcons();
    });
</script>

@endsection

