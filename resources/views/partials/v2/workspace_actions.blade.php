<!-- Workspace Actions Card -->
@if(
    auth()->user()->can('project-update') ||
    auth()->user()->can('project-delete')
)
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <h6 class="fw-bold smaller text-dark mb-3">Workspace Actions</h6>
        
        @if(!$isReadOnly)
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 mb-2" style="background-color: #154A9A; border-color: #154A9A;">
                <i data-lucide="{{ $submitIcon ?? 'check' }}" width="18"></i>
                {{ $submitLabel ?? 'Save Changes' }}
            </button>
            <a href="{{ $cancelUrl ?? '#' }}" class="btn btn-white w-100 py-2 fw-semibold border text-dark">
                Cancel
            </a>
        @else
            @if(isset($editUrl))
                <a href="{{ $editUrl }}" class="btn btn-primary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2 mb-2" style="background-color: #154A9A; border-color: #154A9A;">
                    <i data-lucide="edit-3" width="18"></i>
                    {{ $editLabel ?? 'Edit' }}
                </a>
            @endif

            @if(isset($deleteBtnId))
                <button type="button" class="btn btn-outline-danger w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2" id="{{ $deleteBtnId }}">
                    <i data-lucide="trash-2" width="18"></i>
                    {{ $deleteLabel ?? 'Delete' }}
                </button>
            @endif
        @endif
    </div>
</div>
@endif