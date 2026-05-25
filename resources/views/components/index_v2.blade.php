@extends('layouts.app_v2')

@section('content')
  <section id="component-projects" class="page-content active container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Component Projects</h2>
        </div>
        @can('project-create')
          <div>
            <a href="{{ route('v2.components.create', ['component_id' => 0]) }}" class="btn btn-primary text-white px-4 py-2 fw-medium rounded-pill" style="background-color: #154A9A; border-color: #154A9A;">
                <i data-lucide="plus" class="me-1" width="18"></i> Create Component
            </a>
          </div>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @include('partials.table-header')

            <div class="table-responsive" style="overflow: visible;">
                <table class="table align-middle custom-project-table">
                    <thead>
                        <tr>
                            <th class="text-secondary">Component Title</th>
                            <th class="text-secondary">Sub Project Title</th>
                            <th class="text-secondary text-center">Status</th>
                            <th class="text-secondary">Agency</th>
                            <th class="text-secondary">Funding</th>
                            <th class="text-secondary">Last Updated</th>
                            <th class="text-secondary text-end" data-sort-skip="true">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects->groupBy('component_project_id') as $component_id => $group)
                            @foreach($group as $index => $project)
                            <tr class="project-row">
                                @if($index == 0)
                                <td rowspan="{{ $group->count() }}" class="align-top py-3" style="width: 25%;">
                                    <div class="project-title" title="{{ $project->component_project->component_project_title ?? 'N/A' }}">{{ $project->component_project->component_project_title ?? 'N/A' }}</div>
                                </td>
                                @endif
                                <td class="project-info-col" style="width: 30%;">
                                    <div class="project-title" title="{{ $project->project_title }}">{{ $project->project_title }}</div>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusStyles = match (strtolower($project->status)) {
                                            'completed' => 'background: #e6fcf5; color: #087f5b;',
                                            'ongoing' => 'background: #fff9db; color: #f08c00;',
                                            'delayed' => 'background: #fff5f5; color: #c92a2a;',
                                            'proposed' => 'background: #e7f5ff; color: #1971c2;',
                                            default => 'background: #f8f9fa; color: #495057;'
                                        };
                                    @endphp
                                    <span class="badge rounded-pill fw-medium"
                                        style="{{ $statusStyles }}; font-size: 0.75rem; padding: 4px 12px;">
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td class="small text-secondary">
                                    {{ $project->agency->agency_acronym ?? 'N/A' }}
                                </td>
                                <td class="small text-secondary">
                                    {{ $project->funding_category ?? 'N/A' }}
                                    <div class="smaller fw-semibold text-dark">{{ number_format($project->funding_requirement ?? 0, 2) }} M</div>
                                </td>
                                <td class="project-date-col py-3">
                                    <span class="badge bg-light text-primary rounded-pill px-3 py-2 small fw-normal">
                                        {{ $project->updated_at ? $project->updated_at->diffForHumans() : $project->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-link text-dark p-0 border-0" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                            <i data-lucide="more-vertical" style="width: 20px; height: 20px;"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            @can('project-view')
                                              <li><a class="dropdown-item small" href="{{ route('v2.components.showSubProject', ['component_id' => $project->component_project_id, 'id' => $project->id]) }}"><i data-lucide="eye" class="me-2" width="14"></i> View</a></li>
                                            @endcan
                                            @can('project-create')
                                              <li><a class="dropdown-item small" href="{{ route('v2.components.create', ['component_id' => $project->component_project_id]) }}"><i data-lucide="plus" class="me-2" width="14"></i> Add Sub-Project</a></li>
                                            @endcan
                                            @can('project-edit')
                                              <li><a class="dropdown-item small" href="{{ route('v2.components.editSubProject', ['component_id' => $project->component_project_id, 'id' => $project->id]) }}"><i data-lucide="edit-3" class="me-2" width="14"></i> Edit</a></li>
                                            @endcan
                                            @can('project-delete')
                                              <li><hr class="dropdown-divider"></li>
                                              <li><a class="dropdown-item small text-danger" href="javascript:void(0)" onclick="event.preventDefault(); if(confirm('Delete this sub-project?')) document.getElementById('delete-sub-{{ $project->id }}').submit();"><i data-lucide="trash-2" class="me-2" width="14"></i> Delete</a></li>
                                            @endcan
                                        </ul>
                                    </div>
                                    <form id="delete-sub-{{ $project->id }}" action="{{ route('v2.components.subProjectDestroy', ['component_id' => $project->component_project_id, 'id' => $project->id]) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i data-lucide="inbox" class="mb-2" width="32"></i>
                                <p class="mb-0 small">No component projects found. Add a new one to get started.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.table-pagination', ['paginator' => $projects])
        </div>
    </div>
  </section>

  <style>
    .custom-project-table {
      border-collapse: collapse;
      width: 100%;
    }

    .custom-project-table thead {
      display: table-header-group;
      background-color: #f8f9fa;
    }

    .custom-project-table thead th {
      border: none;
      padding: 12px 15px;
      font-weight: 600;
      font-size: 0.8rem;
      color: #64748b !important;
    }

    .project-row {
      background-color: #fff;
      border-bottom: 1px solid #f1f5f9;
      transition: all 0.2s ease;
    }

    .project-row:hover {
      background-color: #f8fafc !important;
    }

    .project-row td {
      border: none !important;
      padding: 16px 15px !important;
      vertical-align: middle;
    }

    .project-title {
      font-size: 0.9rem;
      font-weight: 600;
      color: #1e293b;
      line-height: 1.4;
    }
  </style>
@endsection
