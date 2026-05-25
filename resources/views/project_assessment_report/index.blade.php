@extends('layouts.app_v2')

@section('title', 'Project Assessment Reports')

@section('content')
<section class="page-content active container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-dark">Project Assessment Reports</h2>
            <p class="text-muted small mb-0">Manage technical evaluations and appraisal reports</p>
        </div>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-column" style="min-height: 300px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            @include('partials.table-header')

            <div class="table-responsive" style="border-radius: 8px;">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Project Title</th>
                            <th class="small text-secondary">Proponent</th>
                            <th class="small text-secondary">Assessment Status</th>
                            <th class="small text-secondary">Prepared By</th>
                            <th class="small text-secondary">Date Prepared</th>
                            <th class="small text-secondary" data-sort-skip="true">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="par-tbody">
                        @forelse($reports ?? [] as $report)
                            @php
                                $status = $report->status ?? 'Draft';
                                $badgeClass = 'bg-secondary';
                                if ($status === 'Review') $badgeClass = 'bg-indigo';
                                if ($status === 'Assessed') $badgeClass = 'bg-success';
                                if ($status === 'For Revision') $badgeClass = 'bg-warning text-dark';
                                if ($status === 'Sectoral Committee') $badgeClass = 'bg-primary';
                                if ($status === 'Evaluated') $badgeClass = 'bg-dark';
                                if ($status === 'Referred to PDIPBD') $badgeClass = 'bg-info text-white';
                                if ($status === 'Final') $badgeClass = 'bg-success';

                                $agency = optional($report->submission->user)->agency;
                                $proponent = $agency
                                    ? ($agency->agency_acronym ?? $agency->agency_name ?? ($agency->name ?? null))
                                    : (optional($report->submission->user)->name ?? '—');

                                $col4Content = $report->prepared_by ?? optional($report->assessor)->name ?? '—';
                                $datePrepared = optional($report->created_at)->format('F j, Y') ?? '—';
                            @endphp

                            <tr>
                                <td class="fw-bold text-dark">{{ optional($report->submission)->project_title ?? '—' }}</td>
                                <td>{{ $proponent }}</td>
                                <td><span class="badge {{ $badgeClass }} border border-white-subtle px-3 py-1 rounded-pill">{{ $status }}</span></td>
                                <td>{!! e($col4Content) !!}</td>
                                <td class="small text-muted">{{ $datePrepared }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                            <i data-lucide="more-vertical" width="16"></i>
                                        </button>
                                        <ul class="dropdown-menu border-0 shadow">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('project-assessment-reports.edit', $report->id) }}"><i data-lucide="edit" width="14" class="me-2 text-primary"></i>View / Edit</a>
                                            </li>
                                            <li>
                                                <form action="{{ route('project-assessment-reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Delete this PAR?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 text-danger"><i data-lucide="trash-2" width="14" class="me-2"></i>Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No assessments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.table-pagination', ['paginator' => $reports])
        </div>
    </div>
</section>
@endsection
