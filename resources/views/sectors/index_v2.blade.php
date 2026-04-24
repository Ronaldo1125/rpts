@extends('layouts.app_v2')

@section('content')

    <section id="sector" class="page-content active container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Manage Sectors</h2>
            </div>
            <div>
                <button class="btn btn-primary text-white px-4 py-2 fw-medium rounded-pill"
                    style="background-color: #154A9A; border-color: #154A9A;" data-bs-toggle="modal"
                    data-bs-target="#addSectorModal">
                    <i data-lucide="plus" class="me-1" width="18"></i> Create Sector
                </button>
            </div>
        </div>

        <!-- Add Sector Modal -->
        <div class="modal fade" id="addSectorModal" tabindex="-1" aria-labelledby="addSectorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1rem;">
                    <!-- Header Accent -->
                    <div style="height: 4px; background-color: #154A9A;"></div>

                    <div class="modal-header border-0 pt-4 px-4 pb-1">
                        <h5 class="modal-title fw-bold" id="addSectorModalLabel">Add Sector</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4">
                        <form id="addSectorForm">
                            <div class="mb-4">
                                <label for="sectorName" class="form-label small fw-semibold text-secondary mb-1">Sector
                                    Name</label>
                                <input type="text" class="form-control" id="sectorName" placeholder="Enter Sector Name"
                                    style="border-radius: 0.75rem;" required>
                            </div>
                            <div class="mb-0">
                                <label for="sectorAcronym" class="form-label small fw-semibold text-secondary mb-1">Sector
                                    Acronym</label>
                                <input type="text" class="form-control" id="sectorAcronym"
                                    placeholder="Enter Sector Acronym" style="border-radius: 0.75rem;" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" form="addSectorForm" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm"
                            style="background-color: #0248D4; border-color: #0248D4; border-radius: 0.75rem !important;">Save</button>
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
                                <th class="small text-secondary">Sector Name</th>
                                <th class="small text-secondary">Created At</th>
                                <th class="small text-secondary" data-sort-skip="true">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sectors as $sector)
                                <tr>
                                    <td>{{ $sector->sector_name }}</td>
                                    <td><span class="badge rounded-pill fw-medium small px-3 py-2"
                                            style="background-color: #e8f0fe; color: #0032A6;">{{ $sector->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown position-static">
                                            <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown"
                                                data-bs-boundary="viewport" aria-expanded="false">
                                                <i data-lucide="more-vertical" width="20"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-12">
                                                <li><a class="dropdown-item py-2" href="javascript:void(0);"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editSectorModal{{ $sector->id }}"><i
                                                            data-lucide="edit-2" class="me-2 text-primary"
                                                            width="16"></i>Edit</a></li>
                                                <li><a class="dropdown-item py-2 text-danger" href="javascript:void(0);"><i
                                                            data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i data-lucide="inbox" class="mb-2" width="32"></i>
                                        <p class="mb-0 small">No sectors found. Add a new one to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @include('partials.table-pagination', ['paginator' => $sectors])
            </div>
        </div>
    </section>
@endsection