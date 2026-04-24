@extends('layouts.app_v2')

@section('content')

  <section id="projects" class="page-content active container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="fw-bold mb-0">Manage Projects</h2>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('projects.create')}}"
          class="btn btn-primary create-project-btn text-white px-4 py-2 fw-medium rounded-pill"
          style="background-color: #154A9A; border-color: #154A9A;">
          <i data-lucide="plus" class="me-1" width="18"></i> Create Project
        </a>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        @include('partials.table-header')

        <div class="table-responsive">
          <table class="table align-middle custom-project-table">
            <thead>
              <tr>
                <th class="text-secondary">Project Title</th>
                <th class="text-secondary">Agency</th>
                <th class="text-secondary">Location</th>
                <th class="text-secondary">Funding</th>
                <th class="text-secondary text-center">Status</th>
                <th class="text-secondary">Last Updated</th>
                <th class="text-secondary text-end" data-sort-skip="true">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($projects as $project)
                <tr class="project-row">
                  <td class="project-info-col" style="width: 35%;">
                    <div class="project-title">{{ $project->project_title }}</div>
                  </td>
                  <td class="small text-secondary">
                    {{ $project->agency->agency_acronym ?? 'N/A' }}
                  </td>
                  <td class="small text-secondary">
                    {{ $project->location ?? 'N/A' }}
                  </td>
                  <td class="small text-secondary">
                    {{ $project->funding_category ?? 'N/A' }}
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
                  <td class="project-date-col py-3">
                    <span class="badge bg-light text-primary rounded-pill px-3 py-2 small fw-normal">
                      {{ $project->updated_at ? $project->updated_at->diffForHumans() : $project->created_at->diffForHumans() }}
                    </span>
                  </td>
                  <td class="text-end">
                    <div class="dropdown">
                      <button class="btn btn-link text-dark p-0 border-0" type="button" data-bs-toggle="dropdown">
                        <i data-lucide="more-vertical" style="width: 20px; height: 20px;"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item small" href="{{ route('projects.edit', $project->id) }}"><i
                              data-lucide="eye" class="me-2" style="width: 14px; height: 14px;"></i> View</a></li>
                        @can('project-update')
                          <li><a class="dropdown-item small" href="{{ route('projects.edit', $project->id) }}"><i
                                data-lucide="edit-2" class="me-2" style="width: 14px; height: 14px;"></i> Edit</a></li>
                        @endcan
                        @can('project-delete')
                          <li>
                            <hr class="dropdown-divider">
                          </li>
                          <li>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST"
                              onsubmit="return confirm('Are you sure?');">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="dropdown-item text-danger small"><i data-lucide="trash-2"
                                  class="me-2" style="width: 14px; height: 14px;"></i>
                                Delete</button>
                            </form>
                          </li>
                        @endcan
                      </ul>
                    </div>
                  </td>
                </tr>
              @endforeach
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
      /* Show headers as seen in the new image */
      background-color: #f8f9fa;
    }

    .custom-project-table thead th {
      border: none;
      padding: 12px 15px;
      font-weight: 600;
      font-size: 0.8rem;
      color: #64748b !important;
      text-transform: none;
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

    .btn-icon:hover {
      background-color: #e9ecef !important;
    }

    /* Custom Paginator Design */
    .pagination {
      gap: 5px;
    }

    .pagination .page-item .page-link {
      border: none;
      border-radius: 8px !important;
      padding: 8px 14px;
      color: #64748b;
      background-color: #f8f9fa;
      font-weight: 500;
      font-size: 0.85rem;
      transition: all 0.2s ease;
    }

    .pagination .page-item.active .page-link {
      background-color: #154A9A !important;
      color: white !important;
      box-shadow: 0 4px 6px -1px rgba(21, 74, 154, 0.2);
    }

    .pagination .page-item:not(.active):hover .page-link {
      background-color: #e2e8f0;
      color: #1e293b;
    }

    .pagination .page-item.disabled .page-link {
      background-color: transparent;
      color: #cbd5e1;
    }

    /* Hide the default Laravel 'Showing X to Y' text inside the pagination nav */
    nav .flex.items-center.justify-between div:first-child,
    nav .d-none.flex-1.items-center.justify-between div:first-child,
    nav .small.text-muted {
      display: none !important;
    }
  </style>

@endsection