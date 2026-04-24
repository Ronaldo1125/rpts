@extends('layouts.app_v2')
@section('content')

<section id="activity-logs" class="page-content active container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">User Activity Logs</h2>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @include('partials.table-header')

            <div class="table-responsive">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Date</th>
                            <th class="small text-secondary">Log Name</th>
                            <th class="small text-secondary">Event</th>
                            <th class="small text-secondary">Subject ID</th>
                            <th class="small text-secondary">Properties</th>
                            <th class="small text-secondary">Causer ID</th>
                            <th class="small text-secondary">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td class="small">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                            <td><span class="badge-soft-pill badge-soft-blue">{{ $activity->log_name }}</span></td>
                            <td><span class="badge-soft-pill badge-soft-{{ $activity->description == 'deleted' ? 'danger' : 'success' }}">{{ $activity->description }}</span></td>
                            <td class="small text-secondary">{{ $activity->subject_id }}</td>
                            <td class="small">
                                <button class="btn btn-sm btn-link p-0" onclick='console.log(@json($activity->properties))'>View</button>
                            </td>
                            <td class="small text-secondary">{{ $activity->causer_id }}</td>
                            <td class="small text-secondary">{{ $activity->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-4 text-muted">
                                <div class="d-flex flex-column align-items-center justify-content-center text-center">
                                    <i data-lucide="inbox" class="mb-2" width="32"></i>
                                    <p class="mb-0 small">No activity logs yet.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             @include('partials.table-pagination', ['paginator' => $activities])
        </div>
    </div>
</section>
@endsection