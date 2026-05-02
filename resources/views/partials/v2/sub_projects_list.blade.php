<!-- Sub-Projects Card -->
@if(isset($component_project) && is_object($component_project))
<div class="card border-0 shadow-sm mt-3">
    <div class="card-body p-0">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light-subtle rounded-top">
            <h6 class="fw-bold smaller text-dark mb-0">Sub-Projects</h6>
            <a href="{{ route('v2.components.create', ['component_id' => $component_project->id]) }}" class="text-primary smaller text-decoration-none fw-bold">
                <i data-lucide="plus" width="12"></i> Add
            </a>
        </div>
        <div class="list-group list-group-flush">
            @forelse($component_project->project as $p)
            @php
                $isActive = isset($project) && $p->id == $project->id;
            @endphp
            <div class="list-group-item list-group-item-action border-0 py-2 px-3 {{ $isActive ? 'bg-primary-subtle border-start border-primary border-4' : '' }}">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <a href="{{ route('v2.components.showSubProject', ['component_id' => $component_project->id, 'id' => $p->id]) }}" 
                       class="text-decoration-none text-dark small fw-medium d-block text-truncate" title="{{ $p->project_title }}">
                        {{ $p->project_title }}
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-link btn-sm text-dark p-0 border-0 shadow-none" data-bs-toggle="dropdown">
                            <i data-lucide="more-horizontal" width="14"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item small" href="{{ route('v2.components.showSubProject', ['component_id' => $component_project->id, 'id' => $p->id]) }}"><i data-lucide="eye" class="me-2" width="14"></i> View</a></li>
                            <li><a class="dropdown-item small" href="{{ route('v2.components.editSubProject', ['component_id' => $component_project->id, 'id' => $p->id]) }}"><i data-lucide="edit-3" class="me-2" width="14"></i> Edit</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="button" class="dropdown-item small text-danger delete-sub-project-btn" data-id="{{ $p->id }}">
                                    <i data-lucide="trash-2" class="me-2" width="14"></i> Delete
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center">
                <p class="text-muted smaller mb-0">No sub-projects found</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endif
