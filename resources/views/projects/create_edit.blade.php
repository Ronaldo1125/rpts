@extends('layouts.app_v2')

@php
    $isView = isset($viewMode) && $viewMode;
    $isEdit = isset($project) && !$isView;
    $isCreate = !isset($project);
@endphp

@section('content')
<section class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('v2.projects.index') }}" class="btn btn-white btn-sm rounded-pill shadow-sm px-3 py-2 border d-flex align-items-center gap-2 text-secondary">
            <i data-lucide="chevron-left" width="16"></i>
            <span class="fw-medium smaller">Back to Projects</span>
        </a>
        <h4 class="fw-bold mb-0">{{ $isView ? '' : ($isEdit ? 'Editing: ' : 'New Project: ') }}{{ $project->project_title ?? 'New Project' }}</h4>
    </div>

    <form id="projectForm" action="{{ $isEdit ? route('v2.projects.update', $project->id) : route('v2.projects.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit) @method('PUT') @endif

        @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-12 mb-4 d-flex align-items-start gap-3">
            <i data-lucide="alert-circle" width="24" class="mt-1"></i>
            <div>
                <h6 class="fw-bold mb-1">Please correct the following errors:</h6>
                <ul class="mb-0 smaller ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-9">
                @include('partials.v2.project_form', [
                    'project' => $project ?? null,
                    'isReadOnly' => $isView,
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
                    'locationSpecific' => $project->project_location_specific ?? null
                ])
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sticky-top" style="top: 20px; z-index: 10;">
                    <!-- Quick Actions Card -->
                        @include('partials.v2.workspace_actions', [
                            'isReadOnly' => $isView,
                            'submitLabel' => $isEdit ? 'Update Project' : 'Publish Project',
                            'submitIcon' => $isEdit ? 'save' : 'check',
                            'cancelUrl' => route('v2.projects.index'),
                            'editUrl' => $isView ? route('v2.projects.edit', $project->id) : null,
                            'editLabel' => 'Edit This Project',
                            'deleteBtnId' => $isView ? 'deleteProjectBtn' : null,
                            'deleteLabel' => 'Delete Project'
                        ])

                        @include('partials.v2.metadata_card', ['project' => $project ?? null])

                        @if(isset($project) && isset($project->component_project))
                            @include('partials.v2.sub_projects_list', ['component_project' => $project->component_project, 'project' => $project])
                        @endif
                    </div>
                </div>
                </div>
            </div>
        </div>
    </form>
</section>

    @include('partials.v2.indicator_modal')

@if(isset($project))
<form id="deleteProjectForm" action="{{ route('v2.projects.destroy', $project->id) }}" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endif

@endsection

@section('scripts')
@include('partials.v2.project_scripts', ['isReadOnly' => $isView])
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Delete Project Confirmation
        const deleteBtn = document.getElementById('deleteProjectBtn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
                    document.getElementById('deleteProjectForm').submit();
                }
            });
        }
    });
</script>
@endsection
