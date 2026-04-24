@extends('layouts.app_v2')

@section('content')




<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Component Projects</h3>
         <p class="text-end">
          <a href="{{ route('components.create', 0)}}"><button class="btn btn-success btn-sm"><i class="icon-base bx bx-bell-plus icon-sm"></i>Create Component Project</button></a>
          {{-- <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addProject">Create Project</button> --}}
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTableProject">
                <thead>
                    <tr>
                        <th>Component Title</th>
                        <th>Sub Project Title</th>
                        <th>Description</th>
                        <th>Funding Req't</th>
                        <th>Status</th>
                        <th>Funding Category</th>
                        <th>Document</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @php
                    $component_name = '';
                  @endphp
                  @foreach($projects as $project)
                    <tr>
                        <td>
                          @if($component_name != $project->component_project->component_project_title)
                          {{$project->component_project->component_project_title }}
                          @endif
                          
                        </td>
                        <td>
                          {{ $project->project_title}}
                        </td>
                        <td>
                          {{ $project->description }}
                        </td>
                        @php
                          $funding_requirement = $project->project_cost_target?->cost_year_2023 + $project->project_cost_target?->cost_year_2024 
                                                   + $project->project_cost_target?->cost_year_2025 + $project->project_cost_target?->cost_year_2026 
                                                   + $project->project_cost_target?->cost_year_2027 + $project->project_cost_target?->cost_year_2028 
                                                   +  $project->project_cost_target?->cost_succeeding_years;
                        @endphp
                        <td>{{ number_format($funding_requirement, 2) }}</td>
                        <td>
                          {{ $project->status }}
                        </td>
                        <td>
                          {{ $project->funding_category }}
                        </td>
                        <td>

                        </td>
                        <td>
                            {{ $project->created_at->diffForHumans() }}
                        </td>

                        <td>
                           @if($component_name != $project->component_project->component_project_title)
                          <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="{{ route('components.create', $project->component_project->id ) }}"
                                ><i class="icon-base bx bx-shield-plus me-1"></i> Add</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{ route('components.destroy', $project->component_project_id) }}" data-confirm-delete="true"
                                ><i class="icon-base bx bx-trash me-1"></i> Delete</a
                              >
                            </div>
                          </div>
                          @endif
                        </td>
                      </tr>
                          @php
                            $component_name =  $project->component_project->component_project_title;
                          @endphp
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Hoverable Table rows -->
    </div>
</div>

@endsection

@section('jsvalidator')

{!! JsValidator::formRequest('App\Http\Requests\StoreProjectRequest') !!}

@endsection

@section('script')

<script type="text/javascript">


$(document).ready(function() {

  $('#myTableProject').DataTable({
    autoWidth: false,
    columns: [
        { width: '12%' },
        { width: '12%' },
        { width: '16%' },
        { width: '10%' },
        { width: '10%' },
        { width: '10%' },
        { width: '10%' },
        { width: '10%' },
        { width: '10%' },
    ],
    "ordering": false, // Disables all user sorting
    "order": [],        // Clears any default sort
    columnDefs: [
        {targets: [1, 2, 3, 4, 5], searchable: false}
    ],
  });
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

</script>
@endsection