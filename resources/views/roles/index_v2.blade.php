@extends('layouts.app_v2')
@section('content')

    <link rel="stylesheet" href="/css/roles.css">
    <section id="roles" class="page-content active container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0 text-dark">Manage Roles</h2>
            </div>
            <div>
                <button class="btn btn-primary-rpts text-white px-4 py-2 fw-medium rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#addRoleModal">
                    <i data-lucide="plus" class="me-1" width="18"></i> Create Role
                </button>
            </div>
        </div>

        <!-- Add Role Modal -->
        <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg overflow-hidden rounded-16">
                    <div class="modal-accent-primary"></div>
                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                        <h5 class="modal-title fw-bold" id="addRoleModalLabel">Add Role</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 text-start">
                        <form id="addRoleForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Role Name</label>
                                <input type="text" name="name" id="roleName" class="form-control rounded-12" placeholder="Enter Role Name"
                                    required>
                                <span class="text-danger mt-1 d-none" id="error-name" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Permissions</label>
                                <div class="border rounded-12 p-3" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($permissions as $permission)
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" name="permission[]"
                                                value="{{ $permission }}" id="perm_{{ $loop->index }}">
                                            <label class="form-check-label small" for="perm_{{ $loop->index }}">
                                                {{ $permission }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <span class="text-danger mt-1 d-none" id="error-permission" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none small"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="saveRoleBtn" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12">
                                    <span class="btn-text">Save Role</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column">
                @include('partials.table-header')

                <div class="table-responsive table-overflow-visible">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-secondary ps-3">Role Name</th>
                                <th class="small text-secondary">Permissions</th>
                                <th class="small text-secondary">Created At</th>
                                <th class="small text-secondary text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td class="py-3 ps-3 fw-medium text-dark">{{ $role->name }}</td>
                                    <td class="py-3">
                                        <div class="d-flex flex-wrap gap-1" style="max-width: 400px;">
                                            @foreach($role->permissions as $perm)
                                                <span class="badge bg-light text-dark border fw-normal"
                                                    style="font-size: 0.7rem;">{{ $perm->name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-3 text-secondary small">
                                        {{ $role->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-3 text-end pe-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown">
                                                <i data-lucide="more-vertical" width="20"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-12">
                                                <li><a class="dropdown-item py-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editRoleModal{{ $role->id }}"><i data-lucide="edit-2" class="me-2 text-primary"
                                                            width="16"></i>Edit</a></li>
                                                <li><a class="dropdown-item py-2 text-danger delete-role-btn" href="javascript:void(0);" data-id="{{ $role->id }}" data-name="{{ $role->name }}"><i
                                                            data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                            </ul>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editRoleModal{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden text-start">
                                                    <div class="modal-accent-primary"></div>
                                                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                                                        <h5 class="modal-title fw-bold">Edit Role</h5>
                                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body px-4 text-start">
                                                        <form id="editRoleForm{{ $role->id }}" class="edit-role-form" data-id="{{ $role->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Role Name</label>
                                                                <input type="text" name="name" class="form-control rounded-12" value="{{ $role->name }}" required>
                                                                <span class="text-danger mt-1 d-none error-name" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Permissions</label>
                                                                <div class="border rounded-12 p-3 text-start" style="max-height: 200px; overflow-y: auto;">
                                                                    @foreach($permissions as $permission)
                                                                        <div class="form-check mb-1">
                                                                            <input class="form-check-input" type="checkbox" name="permission[]"
                                                                                value="{{ $permission }}" id="edit_perm_{{ $role->id }}_{{ $loop->index }}"
                                                                                {{ $role->hasPermissionTo($permission) ? 'checked' : '' }}>
                                                                            <label class="form-check-label small" for="edit_perm_{{ $role->id }}_{{ $loop->index }}">
                                                                                {{ $permission }}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <span class="text-danger mt-1 d-none error-permission" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="modal-footer border-0 px-0 pb-0">
                                                                <button type="button" class="btn btn-link text-secondary text-decoration-none small px-3" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12">Update Role</button>
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
                                    <td colspan="4" class="text-center py-5 text-secondary">No roles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('partials.table-pagination', ['paginator' => $roles])
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const validateUrl = "{{ route('v2.roles.validate') }}";
        
        RPTS.validation.attach(validateUrl, document.getElementById('roleName'), 'name', document.getElementById('error-name'));

        document.querySelectorAll('.edit-role-form').forEach(form => {
            const id = form.getAttribute('data-id');
            const nameInput = form.querySelector('input[name="name"]');
            
            RPTS.validation.attach(validateUrl, nameInput, 'name', form.querySelector('.error-name'), id);
        });

        // Add Role AJAX
        document.getElementById('addRoleForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const saveBtn = document.getElementById('saveRoleBtn');
            saveBtn.disabled = true;
            saveBtn.querySelector('.btn-text').classList.add('d-none');
            saveBtn.querySelector('.spinner-border').classList.remove('d-none');

            try {
                const response = await fetch("{{ route('v2.roles.store') }}", {
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
                    bootstrap.Modal.getInstance(document.getElementById('addRoleModal')).hide();
                    RPTS.toast.show('Role created successfully.', 'success');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                RPTS.toast.show('Error saving role.', 'warning');
            } finally {
                saveBtn.disabled = false;
                saveBtn.querySelector('.btn-text').classList.remove('d-none');
                saveBtn.querySelector('.spinner-border').classList.add('d-none');
            }
        });

        // Edit Role AJAX
        document.querySelectorAll('.edit-role-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const submitBtn = this.closest('.modal-content').querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`/v2/roles/${id}`, {
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
                        bootstrap.Modal.getInstance(document.getElementById(`editRoleModal${id}`)).hide();
                        RPTS.toast.show('Role updated successfully.', 'info');
                        setTimeout(() => window.location.reload(), 2000);
                    }
                } catch (error) {
                    RPTS.toast.show('Error updating role.', 'warning');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        });

        // Delete Role Logic
        RPTS.delete.init(async (id) => {
            try {
                const response = await fetch(`/v2/roles/${id}`, {
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
                    RPTS.toast.show('Role deleted successfully.', 'danger');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                alert('Could not delete role.');
            }
        });

        document.querySelectorAll('.delete-role-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                RPTS.delete.confirm(this.getAttribute('data-id'), this.getAttribute('data-name'));
            });
        });
    });
</script>
@endsection