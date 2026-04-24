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
                    <div class="modal-body px-4">
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Role Name</label>
                                <input type="text" name="name" class="form-control rounded-12" placeholder="Enter Role Name"
                                    required>
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
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none small"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary-rpts px-4 py-2 fw-semibold rounded-12">Save
                                    Role</button>
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
                                                <li><a class="dropdown-item" href="#"><i data-lucide="edit-2" class="me-2"
                                                            width="16"></i>Edit</a></li>
                                                <li>
                                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                        onsubmit="return confirm('Delete this role?')">
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