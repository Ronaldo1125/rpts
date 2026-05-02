<!-- Project Metadata Card -->
@php $metaProject = $project ?? null; @endphp
@if(isset($metaProject) && is_object($metaProject) && isset($metaProject->id))
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body p-3">
        <h6 class="fw-bold smaller text-dark mb-3">Information</h6>
        <div class="mb-2 d-flex flex-column">
            <span class="text-muted smaller">Created By</span>
            <span class="fw-medium text-dark smaller text-truncate" title="{{ $metaProject->user->name ?? 'System' }}">{{ $metaProject->user->name ?? 'System' }}</span>
        </div>
        <div class="mb-2 d-flex flex-column">
            <span class="text-muted smaller">Created At</span>
            <span class="fw-medium text-dark smaller">{{ $metaProject->created_at ? $metaProject->created_at->format('M d, Y h:i A') : 'N/A' }}</span>
        </div>
        <div class="mb-0 d-flex flex-column">
            <span class="text-muted smaller">Updated At</span>
            <span class="fw-medium text-dark smaller">{{ $metaProject->updated_at ? $metaProject->updated_at->format('M d, Y h:i A') : 'N/A' }}</span>
        </div>
    </div>
</div>
@endif
