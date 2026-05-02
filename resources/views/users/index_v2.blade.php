@extends('layouts.app_v2')

@section('content')
    <link rel="stylesheet" href="/css/users.css">
    <section id="users" class="page-content active container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0 text-dark">Manage Users</h2>
            </div>
            <div>
                <button class="btn btn-primary-rpts text-white px-4 py-2 fw-medium rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#addUserModal">
                    <i data-lucide="user-plus" class="me-1" width="18"></i> Create User
                </button>
            </div>
        </div>

        <!-- Add User Modal (Placeholder for now, keeping existing ID) -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg overflow-hidden rounded-16">
                    <div class="modal-accent-primary"></div>
                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                        <h5 class="modal-title fw-bold" id="addUserModalLabel">Add User</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4">
                        <form id="addUserForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Name</label>
                                <input type="text" name="name" id="userName" class="form-control rounded-12" placeholder="Enter Name"
                                    required>
                                <span class="text-danger mt-1 d-none" id="error-name" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Email</label>
                                <input type="email" name="email" id="userEmail" class="form-control rounded-12" placeholder="Enter Email"
                                    required>
                                <span class="text-danger mt-1 d-none" id="error-email" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Password</label>
                                <input type="password" name="password" id="userPassword" class="form-control rounded-12"
                                    placeholder="Password" required>
                                <span class="text-danger mt-1 d-none" id="error-password" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Confirm Password</label>
                                <input type="password" name="confirm-password" class="form-control rounded-12"
                                    placeholder="Confirm Password" required>
                                <span class="text-danger mt-1 d-none" id="error-confirm-password" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Role</label>
                                <select name="role" class="form-select rounded-12" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger mt-1 d-none" id="error-role" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Agency</label>
                                <select name="agency_id" class="form-select rounded-12" required>
                                    <option value="" disabled selected>Select Agency</option>
                                    @foreach($agencies as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger mt-1 d-none" id="error-agency_id" style="font-size: 0.75rem;"></span>
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none small"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="saveUserBtn" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12">
                                    <span class="btn-text">Save User</span>
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
                                <th class="small text-secondary ps-3">Name</th>
                                <th class="small text-secondary">Email Address</th>
                                <th class="small text-secondary">Agency</th>
                                <th class="small text-secondary">Roles</th>
                                <th class="small text-secondary">Joined</th>
                                <th class="small text-secondary text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="py-3 ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="fw-medium text-dark">{{ $user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-secondary">{{ $user->email }}</td>
                                    <td class="py-3">
                                        <span
                                            class="badge-soft-pill badge-soft-blue">{{ $user->agency->agency_acronym ?? 'N/A' }}</span>
                                    </td>
                                    <td class="py-3">
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-light text-dark border small fw-normal">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="py-3">
                                        <span class="text-secondary small">{{ $user->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td class="py-3 text-end pe-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown">
                                                <i data-lucide="more-vertical" width="20"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-12">
                                                <li><a class="dropdown-item py-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"><i data-lucide="edit-2" class="me-2 text-primary"
                                                            width="16"></i>Edit</a></li>
                                                <li><a class="dropdown-item py-2 text-danger delete-user-btn" href="javascript:void(0);" data-id="{{ $user->id }}" data-name="{{ $user->name }}"><i
                                                            data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                            </ul>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden text-start">
                                                    <div class="modal-accent-primary"></div>
                                                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                                                        <h5 class="modal-title fw-bold">Edit User</h5>
                                                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body px-4 text-start">
                                                        <form id="editUserForm{{ $user->id }}" class="edit-user-form" data-id="{{ $user->id }}">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Name</label>
                                                                <input type="text" name="name" class="form-control rounded-12" value="{{ $user->name }}" required>
                                                                <span class="text-danger mt-1 d-none error-name" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Email</label>
                                                                <input type="email" name="email" class="form-control rounded-12" value="{{ $user->email }}" required>
                                                                <span class="text-danger mt-1 d-none error-email" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Password (Leave blank to keep current)</label>
                                                                <input type="password" name="password" class="form-control rounded-12" placeholder="New Password">
                                                                <span class="text-danger mt-1 d-none error-password" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Confirm Password</label>
                                                                <input type="password" name="confirm-password" class="form-control rounded-12" placeholder="Confirm Password">
                                                                <span class="text-danger mt-1 d-none error-confirm-password" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Role</label>
                                                                <select name="role" class="form-select rounded-12" required>
                                                                    @foreach($roles as $role)
                                                                        <option value="{{ $role }}" {{ $user->hasRole($role) ? 'selected' : '' }}>{{ $role }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="text-danger mt-1 d-none error-role" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-semibold text-secondary d-block mb-1">Agency</label>
                                                                <select name="agency_id" class="form-select rounded-12" required>
                                                                    <option value="" disabled>Select Agency</option>
                                                                    @foreach($agencies as $id => $name)
                                                                        <option value="{{ $id }}" {{ $user->agency_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="text-danger mt-1 d-none error-agency_id" style="font-size: 0.75rem;"></span>
                                                            </div>
                                                            <div class="modal-footer border-0 px-0 pb-0">
                                                                <button type="button" class="btn btn-link text-secondary text-decoration-none small px-3" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12">Update User</button>
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
                                    <td colspan="6" class="text-center py-5 text-secondary">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('partials.table-pagination', ['paginator' => $users])
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const validateUrl = "{{ route('v2.users.validate') }}";
        
        RPTS.validation.attach(validateUrl, document.getElementById('userEmail'), 'email', document.getElementById('error-email'));

        document.querySelectorAll('.edit-user-form').forEach(form => {
            const id = form.getAttribute('data-id');
            const emailInput = form.querySelector('input[name="email"]');
            
            RPTS.validation.attach(validateUrl, emailInput, 'email', form.querySelector('.error-email'), id);
        });

        // Add User AJAX
        document.getElementById('addUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const saveBtn = document.getElementById('saveUserBtn');
            saveBtn.disabled = true;
            saveBtn.querySelector('.btn-text').classList.add('d-none');
            saveBtn.querySelector('.spinner-border').classList.remove('d-none');

            try {
                const response = await fetch("{{ route('v2.users.store') }}", {
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
                    bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                    RPTS.toast.show('User created successfully.', 'success');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                RPTS.toast.show('Error saving user.', 'warning');
            } finally {
                saveBtn.disabled = false;
                saveBtn.querySelector('.btn-text').classList.remove('d-none');
                saveBtn.querySelector('.spinner-border').classList.add('d-none');
            }
        });

        // Edit User AJAX
        document.querySelectorAll('.edit-user-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const submitBtn = this.closest('.modal-content').querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`/v2/users/${id}`, {
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
                        bootstrap.Modal.getInstance(document.getElementById(`editUserModal${id}`)).hide();
                        RPTS.toast.show('User updated successfully.', 'info');
                        setTimeout(() => window.location.reload(), 2000);
                    }
                } catch (error) {
                    RPTS.toast.show('Error updating user.', 'warning');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        });

        // Delete User Logic
        RPTS.delete.init(async (id) => {
            try {
                const response = await fetch(`/v2/users/${id}`, {
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
                    RPTS.toast.show('User deleted successfully.', 'danger');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                alert('Could not delete user.');
            }
        });

        document.querySelectorAll('.delete-user-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                RPTS.delete.confirm(this.getAttribute('data-id'), this.getAttribute('data-name'));
            });
        });
    });
</script>
@endsection