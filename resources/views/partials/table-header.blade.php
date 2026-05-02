<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
    <div class="d-flex align-items-center gap-2">
        <span class="text-secondary small">Show</span>
        <select class="form-select form-select-sm border-0 bg-light entries-select" 
                style="width: 70px;"
                onchange="window.location.href = window.location.pathname + '?per_page=' + this.value">
            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
        </select>
        <span class="text-secondary small">entries</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="text-secondary small fw-bold">Search:</span>
        <form method="GET" action="{{ url()->current() }}" class="m-0 p-0">
            @foreach(request()->except('search', 'page') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" name="search" id="globalSearchInput" value="{{ request('search') }}" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search...">
            </div>
        </form>
    </div>
</div>
