@extends('layouts.app_v2')
@section('content')
    <section id="permissions" class="page-content active container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Manage Permissions</h2>
            </div>
            <div>
                <button class="btn btn-primary text-white px-4 py-2 fw-medium rounded-pill"
                    style="background-color: #154A9A; border-color: #154A9A;" data-bs-toggle="modal"
                    data-bs-target="#addPermissionModal">
                    <i data-lucide="plus" class="me-1" width="18"></i> Create Permission
                </button>
            </div>
        </div>

        <!-- Add Permission Modal -->
        <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg overflow-hidden rounded-16">
                    <div class="modal-accent-primary"></div>
                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                        <h5 class="modal-title fw-bold" id="addPermissionModalLabel">Add Permission</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 text-start">
                        <form id="addPermissionForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Permission Name</label>
                                <input type="text" name="name" id="permissionName" class="form-control rounded-12"
                                    placeholder="Enter Permission Name" required>
                                <span class="text-danger mt-1 d-none" id="error-name" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none small"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="savePermissionBtn" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12">
                                    <span class="btn-text">Save Permission</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-16">
            <div class="card-body d-flex flex-column">
                @include('partials.table-header')

                <div class="table-responsive table-overflow-visible">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-secondary ps-3">Permission Name</th>
                                <th class="small text-secondary">Created At</th>
                                <th class="small text-secondary text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $permission)
                                <tr>
                                    <td class="py-3 ps-3 fw-medium text-dark">{{ $permission->name }}</td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill fw-medium small px-3 py-2 badge-soft-blue">
                                            {{ $permission->created_at->diffForHumans() }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-end pe-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown">
                                                <i data-lucide="more-vertical" width="20"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-12">
                                                <li><a class="dropdown-item py-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editPermissionModal{{ $permission->id }}"><i data-lucide="edit-2" class="me-2 text-primary"
                                                            width="16"></i>Edit</a></li>
                                                <li><a class="dropdown-item py-2 text-danger delete-permission-btn" href="javascript:void(0);" data-id="{{ $permission->id }}" data-name="{{ $permission->name }}"><i
                                                            data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                            </ul>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editPermissionModal{{ $permission->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden text-start">
                                                    <div class="modal-accent-primary"></div>
                                                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                                                        <h5 class="modal-title fw-bold">Edit Permission</h5>
                                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body px-4 text-start">
                                                        <form id="editPermissionForm{{ $permission->id }}" class="edit-permission-form" data-id="{{ $permission->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Permission Name</label>
                                                                <input type="text" name="name" class="form-control rounded-12" value="{{ $permission->name }}" required>
                                                                <span class="text-danger mt-1 d-none error-name" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="modal-footer border-0 px-0 pb-0">
                                                                <button type="button" class="btn btn-link text-secondary text-decoration-none small px-3" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12">Update Permission</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-secondary">No permissions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('partials.table-pagination', ['paginator' => $permissions])
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const validateUrl = "{{ route('v2.permissions.validate') }}";
        
        RPTS.validation.attach(validateUrl, document.getElementById('permissionName'), 'name', document.getElementById('error-name'));

        document.querySelectorAll('.edit-permission-form').forEach(form => {
            const id = form.getAttribute('data-id');
            const nameInput = form.querySelector('input[name="name"]');
            
            RPTS.validation.attach(validateUrl, nameInput, 'name', form.querySelector('.error-name'), id);
        });

        // Add Permission AJAX
        document.getElementById('addPermissionForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const saveBtn = document.getElementById('savePermissionBtn');
            saveBtn.disabled = true;
            saveBtn.querySelector('.btn-text').classList.add('d-none');
            saveBtn.querySelector('.spinner-border').classList.remove('d-none');

            try {
                const response = await fetch("{{ route('v2.permissions.store') }}", {
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
                    bootstrap.Modal.getInstance(document.getElementById('addPermissionModal')).hide();
                    RPTS.toast.show('Permission created successfully.', 'success');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                RPTS.toast.show('Error saving permission.', 'warning');
            } finally {
                saveBtn.disabled = false;
                saveBtn.querySelector('.btn-text').classList.remove('d-none');
                saveBtn.querySelector('.spinner-border').classList.add('d-none');
            }
        });

        // Edit Permission AJAX
        document.querySelectorAll('.edit-permission-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const submitBtn = this.closest('.modal-content').querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`/v2/permissions/${id}`, {
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
                        bootstrap.Modal.getInstance(document.getElementById(`editPermissionModal${id}`)).hide();
                        RPTS.toast.show('Permission updated successfully.', 'info');
                        setTimeout(() => window.location.reload(), 2000);
                    }
                } catch (error) {
                    RPTS.toast.show('Error updating permission.', 'warning');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        });

        // Delete Permission Logic
        RPTS.delete.init(async (id) => {
            try {
                const response = await fetch(`/v2/permissions/${id}`, {
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
                    RPTS.toast.show('Permission deleted successfully.', 'danger');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                alert('Could not delete permission.');
            }
        });

        document.querySelectorAll('.delete-permission-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                RPTS.delete.confirm(this.getAttribute('data-id'), this.getAttribute('data-name'));
            });
        });
    });
</script>
@endsection