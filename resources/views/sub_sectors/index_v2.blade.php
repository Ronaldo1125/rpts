@extends('layouts.app_v2')

@section('content')
    <section id="sub-sector" class="page-content active container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Manage Sub-Sectors</h2>
            </div>
            <div>
                <button class="btn btn-primary text-white px-4 py-2 fw-medium rounded-pill"
                    style="background-color: #154A9A; border-color: #154A9A;" data-bs-toggle="modal"
                    data-bs-target="#addSubSectorModal">
                    <i data-lucide="plus" class="me-1" width="18"></i> Create Sub-Sector
                </button>
            </div>
        </div>

        <!-- Add Sub-Sector Modal -->
        <div class="modal fade" id="addSubSectorModal" tabindex="-1" aria-labelledby="addSubSectorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                    <div class="modal-accent-primary"></div>
                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                        <h5 class="modal-title fw-bold" id="addSubSectorModalLabel">Add Sub-Sector</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 text-start">
                        <form id="addSubSectorForm">
                            @csrf
                            <div class="mb-4">
                                <label for="subSectorName" class="form-label small fw-semibold text-secondary mb-1">Sub-Sector Name</label>
                                <input type="text" name="subsector_name" class="form-control rounded-12" id="subSectorName" placeholder="Enter Sub-Sector Name" required>
                                <span class="text-danger mt-1 d-none" id="error-subsector_name" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="mb-0">
                                <label for="parentSector" class="form-label small fw-semibold text-secondary mb-1">Sector:</label>
                                <select name="sector_id" class="form-select rounded-12" id="parentSector" required>
                                    <option value="" selected disabled>Select sector ...</option>
                                    @foreach($sectors as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger mt-1 d-none" id="error-sector_id" style="font-size: 0.75rem;"></span>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="saveSubSectorBtn" form="addSubSectorForm" class="btn btn-primary px-4 py-2 fw-semibold rounded-12 shadow-sm" style="background-color: #154A9A; border-color: #154A9A;">
                            <span class="btn-text">Save Sub-Sector</span>
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @include('partials.table-header')

                <div class="table-responsive">
                    <table class="table table-hover align-middle" data-sortable="true">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-secondary">Sub-Sector Name</th>
                                <th class="small text-secondary">Created At</th>
                                <th class="small text-secondary" data-sort-skip="true">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sub_sectors as $sub_sector)
                                <tr>
                                    <td style="color: inherit !important;">{{ $sub_sector->subsector_name }}</td>
                                    <td><span class="badge rounded-pill fw-medium small px-3 py-2"
                                            style="background-color: #e8f0fe; color: #0032A6;">{{ $sub_sector->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown"
                                                data-bs-boundary="viewport" aria-expanded="false">
                                                <i data-lucide="more-vertical" width="20"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-12">
                                                <li><a class="dropdown-item py-2" href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editSubSectorModal{{ $sub_sector->id }}"><i
                                                            data-lucide="edit-2" class="me-2 text-primary"
                                                            width="16"></i>Edit</a></li>
                                                <li><a class="dropdown-item py-2 text-danger delete-sub-sector-btn" href="javascript:void(0);" data-id="{{ $sub_sector->id }}" data-name="{{ $sub_sector->subsector_name }}"><i
                                                            data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                            </ul>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editSubSectorModal{{ $sub_sector->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                                                    <div class="modal-accent-primary"></div>
                                                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                                                        <h5 class="modal-title fw-bold">Edit Sub-Sector</h5>
                                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body px-4 text-start">
                                                        <form id="editSubSectorForm{{ $sub_sector->id }}" class="edit-sub-sector-form" data-id="{{ $sub_sector->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-4">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Sub-Sector Name</label>
                                                                <input type="text" name="subsector_name" class="form-control rounded-12" value="{{ $sub_sector->subsector_name }}" required>
                                                                <span class="text-danger mt-1 d-none error-subsector_name" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Sector:</label>
                                                                <select name="sector_id" class="form-select rounded-12" required>
                                                                    <option value="" disabled>Select sector ...</option>
                                                                    @foreach($sectors as $id => $name)
                                                                        <option value="{{ $id }}" {{ $sub_sector->sector_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="text-danger mt-1 d-none error-sector_id" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer border-0 px-4 pb-4">
                                                        <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" form="editSubSectorForm{{ $sub_sector->id }}" class="btn btn-primary px-4 py-2 fw-semibold rounded-12 shadow-sm" style="background-color: #154A9A; border-color: #154A9A;">Update Sub-Sector</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i data-lucide="inbox" class="mb-2" width="32"></i>
                                        <p class="mb-0 small">No sub-sectors found. Add a new one to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @include('partials.table-pagination', ['paginator' => $sub_sectors])
            </div>
        </div>
    </section>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const validateUrl = "{{ route('v2.sub_sectors.validate') }}";
        
        RPTS.validation.attach(validateUrl, document.getElementById('subSectorName'), 'subsector_name', document.getElementById('error-subsector_name'));

        document.querySelectorAll('.edit-sub-sector-form').forEach(form => {
            const id = form.getAttribute('data-id');
            const nameInput = form.querySelector('input[name="subsector_name"]');
            
            RPTS.validation.attach(validateUrl, nameInput, 'subsector_name', form.querySelector('.error-subsector_name'), id);
        });

        // Add Sub-Sector AJAX
        document.getElementById('addSubSectorForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const saveBtn = document.getElementById('saveSubSectorBtn');
            saveBtn.disabled = true;
            saveBtn.querySelector('.btn-text').classList.add('d-none');
            saveBtn.querySelector('.spinner-border').classList.remove('d-none');

            try {
                const response = await fetch("{{ route('v2.sub_sectors.store') }}", {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.status === 422) {
                    const data = await response.json();
                    for (const [key, messages] of Object.entries(data.errors)) {
                        const errorEl = document.getElementById(`error-${key}`);
                        if (errorEl) { errorEl.textContent = messages[0]; errorEl.classList.remove('d-none'); }
                    }
                    return;
                }

                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addSubSectorModal')).hide();
                    RPTS.toast.show('Sub-Sector created successfully.', 'success');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                RPTS.toast.show('Error saving sub-sector.', 'warning');
            } finally {
                saveBtn.disabled = false;
                saveBtn.querySelector('.btn-text').classList.remove('d-none');
                saveBtn.querySelector('.spinner-border').classList.add('d-none');
            }
        });

        // Edit Sub-Sector AJAX
        document.querySelectorAll('.edit-sub-sector-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const submitBtn = this.closest('.modal-content').querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`/v2/sub_sectors/${id}`, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-HTTP-Method-Override': 'PUT' }
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        for (const [key, messages] of Object.entries(data.errors)) {
                            const errorEl = this.querySelector(`.error-${key}`);
                            if (errorEl) { errorEl.textContent = messages[0]; errorEl.classList.remove('d-none'); }
                        }
                        return;
                    }

                    const data = await response.json();
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById(`editSubSectorModal${id}`)).hide();
                        RPTS.toast.show('Sub-Sector updated successfully.', 'info');
                        setTimeout(() => window.location.reload(), 2000);
                    }
                } catch (error) {
                    RPTS.toast.show('Error updating sub-sector.', 'warning');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        });

        // Delete Sub-Sector Logic
        RPTS.delete.init(async (id) => {
            try {
                const response = await fetch(`/v2/sub_sectors/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-HTTP-Method-Override': 'DELETE'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    RPTS.delete.hide();
                    RPTS.toast.show('Sub-Sector deleted successfully.', 'danger');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                alert('Could not delete sub-sector.');
            }
        });

        document.querySelectorAll('.delete-sub-sector-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                RPTS.delete.confirm(this.getAttribute('data-id'), this.getAttribute('data-name'));
            });
        });
    });
</script>
@endsection