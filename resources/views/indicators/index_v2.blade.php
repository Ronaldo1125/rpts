@extends('layouts.app_v2')
@section('content')

    <section id="indicator" class="page-content active container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Manage Indicators</h2>
            </div>
            <div>
                <button class="btn btn-primary text-white px-4 py-2 fw-medium rounded-pill"
                    style="background-color: #154A9A; border-color: #154A9A;" data-bs-toggle="modal"
                    data-bs-target="#addIndicatorModal">
                    <i data-lucide="plus" class="me-1" width="18"></i> Create Indicator
                </button>
            </div>
        </div>



        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @include('partials.table-header')

                <div class="table-responsive">
                    <table class="table table-hover align-middle" data-sortable="true">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-secondary">Indicator Name</th>
                                <th class="small text-secondary">Created At</th>
                                <th class="small text-secondary" data-sort-skip="true">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($indicators as $indicator)
                                <tr>
                                    <td style="color: inherit !important;">{{ $indicator->indicator_name }}</td>
                                    <td><span class="badge rounded-pill fw-medium small px-3 py-2"
                                            style="background-color: #e8f0fe; color: #0032A6;">{{ $indicator->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <div class="dropdown position-static">
                                            <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown"
                                                data-bs-boundary="viewport" aria-expanded="false">
                                                <i data-lucide="more-vertical" width="20"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-12">
                                                <li><a class="dropdown-item py-2" href="javascript:void(0);"><i
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
                                        <p class="mb-0 small">No indicators found. Add a new one to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @include('partials.table-pagination', ['paginator' => $indicators])
            </div>
        </div>
    </section>
    <style>
        .bg-indigo-50 {
            background-color: #e8f0fe !important;
        }

        .text-indigo-700 {
            color: #0032A6 !important;
        }
    </style>
@endsection