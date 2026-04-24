<div class="d-flex justify-content-between align-items-center mt-auto py-3 px-2">
    <span class="text-muted small">
        Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() ?? 0 }} entries
    </span>
    <nav>
        {{ $paginator->links('vendor.pagination.custom') }}
    </nav>
</div>
