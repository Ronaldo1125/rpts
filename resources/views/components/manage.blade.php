@extends('layouts.app_v2')

@php
    $isEditSubProject = isset($sub_project) && !isset($isView);
    $isViewSubProject = isset($sub_project) && isset($isView);
    $isCreateComponent = $component_project_id == 0;
    $isManageComponent = !$isEditSubProject && !$isViewSubProject && !$isCreateComponent;
    $isReadOnly = $isViewSubProject;
@endphp

@section('content')
<section class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('v2.components.index') }}" class="btn btn-white btn-sm rounded-pill shadow-sm px-3 py-2 border d-flex align-items-center gap-2 text-secondary">
                <i data-lucide="chevron-left" width="16"></i>
                <span class="fw-medium small">Back to List</span>
            </a>
            <h4 class="fw-bold mb-0">
                @if($isViewSubProject)
                    View Sub-Project: {{ $sub_project->project_title }}
                @elseif($isEditSubProject)
                    Edit Sub-Project: {{ $sub_project->project_title }}
                @elseif($isCreateComponent)
                    New Component Project
                @else
                    Manage Component: {{ $component_project->component_project_title }}
                @endif
            </h4>
        </div>
    </div>



    <!-- Main Form -->
    <form id="componentForm" action="{{ $isEditSubProject ? route('v2.components.updateSubProject', $sub_project->id) : route('v2.components.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEditSubProject) @method('PUT') @endif
        <input type="hidden" name="component_project_id" value="{{ $component_project_id }}">

        @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-12 mb-4 d-flex align-items-start gap-3">
            <i data-lucide="alert-circle" width="24" class="mt-1"></i>
            <div>
                <h6 class="fw-bold mb-1">Please correct the following errors:</h6>
                <ul class="mb-0 small ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-9">
                <!-- Component Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-primary text-uppercase smaller ls-1 mb-4">Component Information</h6>
                        <div class="mb-0">
                            <label class="form-label fw-medium small text-dark">Component Title <span class="text-danger">*</span></label>
                                <input type="text" name="component_project_title" class="form-control @error('component_project_title') is-invalid @enderror" 
                                    value="{{ old('component_project_title', $component_project->component_project_title ?? '') }}" 
                                    placeholder="e.g. Regional Health Infrastructure Program" required {{ $isReadOnly ? 'disabled' : '' }}>
                                @error('component_project_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    @include('partials.v2.project_form', [
                        'project' => $sub_project ?? null,
                        'isReadOnly' => $isReadOnly,
                        'indicators' => $indicators,
                        'agencies' => $agencies,
                        'sectors' => $sectors,
                        'statuses' => $statuses,
                        'funding_categories' => $funding_categories,
                        'fund_sources' => $fund_sources,
                        'endorse_years' => $endorse_years,
                        'chapters' => $chapters,
                        'project_location_types' => $project_location_types,
                        'provinces' => $provinces,
                        'locationSpecific' => $locationSpecific ?? ($sub_project->project_location_specific ?? null)
                    ])
                </div>

                <div class="col-lg-3">
                    <div class="sticky-top" style="top: 20px;">
                        @include('partials.v2.workspace_actions', [
                            'isReadOnly' => $isReadOnly,
                            'submitLabel' => $isEditSubProject ? 'Update Sub-Project' : ($isCreateComponent ? 'Initialize Component' : 'Add Sub-Project'),
                            'submitIcon' => $isEditSubProject ? 'save' : 'check',
                            'cancelUrl' => route('v2.components.index'),
                            'editUrl' => $isViewSubProject ? route('v2.components.editSubProject', ['component_id' => $component_project_id, 'id' => $sub_project->id]) : null,
                            'editLabel' => 'Edit This Project',
                            'deleteBtnId' => $isViewSubProject ? 'deleteSubProjectBtn' : null,
                            'deleteLabel' => 'Delete Sub-Project'
                        ])

                        @include('partials.v2.metadata_card', ['project' => $sub_project ?? $component_project ?? null])

                        @include('partials.v2.sub_projects_list', ['component_project' => $component_project, 'project' => $sub_project ?? null])
                    </div>
                </div>
            </div>
    </form>

    @if(!$isCreateComponent && isset($component_project))
    @foreach($component_project->project as $project)
    <form id="delete-sidebar-sub-{{ $project->id }}" action="{{ route('v2.components.subProjectDestroy', ['component_id' => $component_project_id, 'id' => $project->id]) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    @endforeach
    @endif
</section>

    @include('partials.v2.indicator_modal')

@endsection

@section('scripts')
<style>
    .form-select, .form-check-input, .cursor-pointer {
        cursor: pointer;
    }
    .spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    /* Typography Consistency */
    .smaller { font-size: 0.8rem !important; }
    .badge { font-size: 0.8rem !important; font-weight: 500 !important; }
    .dropdown-item { font-size: 0.85rem !important; }
    .form-control, .form-select { font-size: 0.9rem !important; }

    /* Handle long tag names */
    #selected-indicators-tags .badge,
    #selected-chapters-tags .badge {
        max-width: 100%;
        display: inline-flex !important;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem !important;
    }

    #selected-indicators-tags .badge span,
    #selected-chapters-tags .badge span {
        max-width: 450px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: inline-block;
    }

    /* Wrap container properly */
    .form-control.d-flex.flex-wrap {
        height: auto !important;
        min-height: 42px;
        padding: 0.4rem 0.5rem !important;
    }

    /* Modal Styling */
    .rounded-20 { border-radius: 20px !important; }
    .rounded-12 { border-radius: 12px !important; }
    .modal-accent-primary {
        height: 4px;
        background: linear-gradient(90deg, #154A9A, #4facfe);
    }

    .custom-project-table thead th {
        border: none;
        padding: 12px 15px;
        font-weight: 600;
        font-size: 0.8rem;
        color: #64748b !important;
    }
    .custom-project-table tr {
        border-bottom: 1px solid #f1f5f9;
    }
    .custom-project-table td {
        padding: 16px 15px !important;
    }
</style>
@include('partials.v2.project_scripts')
<style>
    .spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endsection
