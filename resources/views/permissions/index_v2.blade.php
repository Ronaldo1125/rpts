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
                    <!-- Header Accent -->
                    <div style="height: 4px; background-color: #154A9A;"></div>

                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                        <h5 class="modal-title fw-bold" id="addPermissionModalLabel">Add Permission</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4">
                        <form action="{{ route('permissions.store') }}" method="POST" id="addPermissionForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Permission Name</label>
                                <input type="text" name="name" class="form-control rounded-12"
                                    placeholder="Enter Permission Name" required>
                            </div>
                            <div class="modal-footer border-0 px-0 pb-0">
                                <button type="button" class="btn btn-link text-secondary text-decoration-none small"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold rounded-12"
                                    style="background-color: #154A9A; border-color: #154A9A;">Save Permission</button>
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
                                                <li><a class="dropdown-item" href="#"><i data-lucide="edit-2" class="me-2"
                                                            width="16"></i>Edit</a></li>
                                                <li>
                                                    <form action="{{ route('permissions.destroy', $permission->id) }}"
                                                        method="POST" onsubmit="return confirm('Delete this permission?')">
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