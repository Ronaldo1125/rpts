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
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Name</label>
                                <input type="text" name="name" class="form-control rounded-12" placeholder="Enter Name"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Email</label>
                                <input type="email" name="email" class="form-control rounded-12" placeholder="Enter Email"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Password</label>
                                <input type="password" name="password" class="form-control rounded-12"
                                    placeholder="Password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Role</label>
                                <select name="role" class="form-select rounded-12" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Agency</label>
                                <select name="agency_id" class="form-select rounded-12">
                                    <option value="">No Agency</option>
                                    @foreach($agencies as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none small"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12">Save
                                    User</button>
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
                                            <img src="https://api.dicebear.com/7.x/initials/svg?seed={{ $user->name }}"
                                                class="rounded-circle" width="32" height="32" alt="Avatar">
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
                                                <li><a class="dropdown-item" href="#"><i data-lucide="edit-2" class="me-2"
                                                            width="16"></i>Edit</a></li>
                                                <li>
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                        onsubmit="return confirm('Delete this user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"><i
                                                                data-lucide="trash-2" class="me-2"
                                                                width="16"></i>Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
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