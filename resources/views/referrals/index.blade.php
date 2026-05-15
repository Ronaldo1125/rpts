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
        <div class="d-flex gap-2">
            <button class="btn btn-primary-rpts text-white px-4 py-2 fw-semibold rounded-pill" id="newReferralBtn">
                <i data-lucide="plus-circle" class="me-2" width="16"></i> New Referral
            </button>
        </div>
    </div>

    <!-- New Referral Modal -->
    <div class="modal fade" id="newReferralModal" tabindex="-1" aria-labelledby="newReferralModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                <div class="modal-accent-primary"></div>
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" id="newReferralModalLabel">Create New Project Referral</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="newReferralForm">
                        <div class="row g-4">
                            <!-- Project Selection -->
                            <div class="col-12">
                                <label class="form-label small fw-bold text-secondary">A. Select Validated Project</label>
                                <select class="form-select bg-light border-0 py-2 rounded-12" name="projectSelect" id="referralProjectSelect" required>
                                    <option value="">-- Choose from Completeness Test Results --</option>
                                    <!-- Dynamic Options from CTE -->
                                </select>
                                <div class="form-text mt-1 small">Only projects with "Final" validation status are listed here.</div>
                            </div>

                            <!-- Sender Info (Now Automated) -->
                            <div class="col-12 pt-1">
                                <p class="text-muted small italic px-1"><i data-lucide="info" width="14" class="me-1"></i> Referrer identity will be automatically recorded as the currently logged-in user.</p>
                            </div>

                            <!-- Recipient Info -->
                            <div class="col-md-6 pt-2">
                                <label class="form-label small fw-bold text-secondary">C. Refer To (Division)</label>
                                <select class="form-select bg-light border-0 py-2 rounded-12" name="to_division_id" required>
                                    <option value="">-- Select Recipient Division --</option>
                                    <option value="2">PMED</option>
                                    <option value="3">PFPD</option>
                                    <option value="4">DRD</option>
                                </select>
                            </div>

                            <!-- Notes -->
                            <div class="col-12 pt-2">
                                <label class="form-label small fw-bold text-secondary">D. Additional Instructions/Notes</label>
                                <textarea class="form-control bg-light border-0 rounded-12" name="referralNotes" rows="3" placeholder="Provide context or specific areas of concern for the PAR assessment..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary-rpts text-white px-5 rounded-pill" id="submitReferralBtn">
                        <i data-lucide="send" class="me-2" width="16"></i> Create Referral
                    </button>
                </div>
            </div>
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
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light entries-select" id="referralEntriesSelect">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-bold">Search:</span>
                    <div class="input-group input-group-sm search-input-group" id="referralSearchGroup">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" id="referralSearchInput" placeholder="Search referrals...">
                    </div>
                </div>
            </div>

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

            <div class="d-flex justify-content-between align-items-center mt-auto py-3">
                <span id="referral-count" class="text-muted small">Showing {{ $referrals->count() }} of {{ $referrals->count() }} entries</span>
                <nav>
                    <ul class="custom-pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>
                    </ul>
                </nav>
            </div>
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

        if (window.lucide) window.lucide.createIcons();
    });
</script>

@endsection

