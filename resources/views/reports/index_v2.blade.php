@extends('layouts.app_v2')

@section('content')

<section id="reports" class="page-content active container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold">Reports</h2>
    </div>



    <!-- Report Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <!-- Filters / Actions -->
    <!-- Report Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <!-- Top Actions & Primary Filters -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom">
                <form action="{{ route('v2.reports.index') }}" method="GET" id="filterForm" class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05rem;">Funding:</label>
                        <select name="funding_category" class="form-select form-select-sm border-0 bg-light rounded-pill px-3" style="min-width: 160px;" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($funding_categories as $category)
                                <option value="{{ $category->value }}" {{ $selectedFundingCategory == $category->value ? 'selected' : '' }}>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05rem;">Status:</label>
                        <select name="status" class="form-select form-select-sm border-0 bg-light rounded-pill px-3" style="min-width: 140px;" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" {{ $selectedStatus == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('per_page'))
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif
                </form>

                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-secondary small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05rem;">Type:</span>
                        <select id="report-type-select" class="form-select form-select-sm border-0 bg-light rounded-pill px-3" style="width: 130px;">
                            <option value="" selected disabled>-- Select --</option>
                            <option value="rdip">RDIP</option>
                            <option value="aip">AIP</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <button id="btn-export-report" class="btn btn-primary text-white px-4 py-1.5 fw-bold rounded-pill d-flex align-items-center gap-2 shadow-sm" style="background-color: #154A9A; border-color: #154A9A; font-size: 0.8rem;">
                        <i data-lucide="download" width="14"></i> EXPORT
                    </button>
                </div>
            </div>

            <!-- Custom Report Column Selection (Initially Hidden) -->
            <div id="custom-report-columns" class="mb-4 p-3 rounded bg-light border shadow-sm d-none">
                <div class="d-flex flex-column gap-2">
                    <span class="text-secondary small fw-bold text-uppercase mb-2"><i data-lucide="check-square" width="14" class="me-1"></i> Select Columns for Custom Report:</span>
                    <div class="d-flex flex-wrap gap-x-4 gap-y-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-col-chk" type="checkbox" value="col-desc" id="cust-col-desc" checked>
                            <label class="form-check-label small" for="cust-col-desc">Description</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-col-chk" type="checkbox" value="col-ind" id="cust-col-ind" checked>
                            <label class="form-check-label small" for="cust-col-ind">Indicators</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-col-chk" type="checkbox" value="col-loc" id="cust-col-loc" checked>
                            <label class="form-check-label small" for="cust-col-loc">Location</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-col-chk" type="checkbox" value="col-duty" id="cust-col-duty" checked>
                            <label class="form-check-label small" for="cust-col-duty">Duty Bearers</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-col-chk" type="checkbox" value="col-fund" id="cust-col-fund" checked>
                            <label class="form-check-label small" for="cust-col-fund">Funding Req.</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-col-chk" type="checkbox" value="col-physical" id="cust-col-physical" checked>
                            <label class="form-check-label small" for="cust-col-physical">Physical Target</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-col-chk" type="checkbox" value="col-cost" id="cust-col-cost" checked>
                            <label class="form-check-label small" for="cust-col-cost">Project Cost</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-col-chk" type="checkbox" value="col-remarks" id="cust-col-remarks" checked>
                            <label class="form-check-label small" for="cust-col-remarks">Remarks</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Controls (Reusing Utils) -->
            <div class="position-relative mb-2">
                @include('partials.table-header')
                
                <!-- Inject Columns Dropdown into Header Area -->
                <div style="position: absolute; right: 270px; top: 2px;">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2 border-0 bg-light rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <i data-lucide="eye" width="16"></i> <span class="small fw-bold text-uppercase" style="font-size: 0.65rem;">Visibility</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-3 shadow-lg" style="min-width: 250px;">
                            <li><h6 class="dropdown-header px-0 mb-2 font-weight-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05rem;">Show/Hide Columns</h6></li>
                            <li>
                                <div class="form-check mb-2">
                                    <input class="form-check-input col-toggle-chk" type="checkbox" value="col-desc" id="chk-col-desc" checked>
                                    <label class="form-check-label small" for="chk-col-desc">Description</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check mb-2">
                                    <input class="form-check-input col-toggle-chk" type="checkbox" value="col-ind" id="chk-col-ind" checked>
                                    <label class="form-check-label small" for="chk-col-ind">Indicator/s</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check mb-2">
                                    <input class="form-check-input col-toggle-chk" type="checkbox" value="col-loc" id="chk-col-loc" checked>
                                    <label class="form-check-label small" for="chk-col-loc">Location</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check mb-2">
                                    <input class="form-check-input col-toggle-chk" type="checkbox" value="col-duty" id="chk-col-duty" checked>
                                    <label class="form-check-label small" for="chk-col-duty">Duty Bearer/s</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check mb-2">
                                    <input class="form-check-input col-toggle-chk" type="checkbox" value="col-fund" id="chk-col-fund" checked>
                                    <label class="form-check-label small" for="chk-col-fund">Funding Req.</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check mb-2">
                                    <input class="form-check-input col-toggle-chk" type="checkbox" value="col-physical" id="chk-col-physical" checked>
                                    <label class="form-check-label small font-weight-bold" for="chk-col-physical">Physical Target (All)</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check mb-2">
                                    <input class="form-check-input col-toggle-chk" type="checkbox" value="col-cost" id="chk-col-cost" checked>
                                    <label class="form-check-label small font-weight-bold" for="chk-col-cost">Project Cost (All)</label>
                                </div>
                            </li>
                            <li>
                                <div class="form-check mb-0">
                                    <input class="form-check-input col-toggle-chk" type="checkbox" value="col-remarks" id="chk-col-remarks" checked>
                                    <label class="form-check-label small" for="chk-col-remarks">Remarks</label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle border-top" id="reportTable" data-sortable="true">
                    <thead class="text-center align-middle bg-light">
                        <tr>
                            <th rowspan="2" class="small text-secondary py-3 col-title" style="min-width:200px;">Project Title</th>
                            <th rowspan="2" class="small text-secondary py-3 col-desc" style="min-width:200px;">Description</th>
                            <th rowspan="2" class="small text-secondary py-3 col-ind">Indicator/s</th>
                            <th rowspan="2" class="small text-secondary py-3 col-loc">Location</th>
                            <th rowspan="2" class="small text-secondary py-3 col-duty">Duty Bearer/s</th>
                            <th rowspan="2" class="small text-secondary py-3 col-fund">Funding Req. (PhP M)</th>
                            <th colspan="7" class="small text-secondary py-3 border-bottom col-physical">Physical Target</th>
                            <th colspan="7" class="small text-secondary py-3 border-bottom col-cost">Project Cost (PhP M)</th>
                            <th rowspan="2" class="small text-secondary py-3 col-remarks" style="min-width:150px;">Remarks</th>
                        </tr>
                        <tr>
                            <!-- Physical Target Years -->
                            <th class="small text-secondary py-2 border-start col-physical">2023</th>
                            <th class="small text-secondary py-2 col-physical">2024</th>
                            <th class="small text-secondary py-2 col-physical">2025</th>
                            <th class="small text-secondary py-2 col-physical">2026</th>
                            <th class="small text-secondary py-2 col-physical">2027</th>
                            <th class="small text-secondary py-2 col-physical">2028</th>
                            <th class="small text-secondary py-2 border-end col-physical">Succeeding Years</th>
                            <!-- Project Cost Years -->
                            <th class="small text-secondary py-2 col-cost">2023</th>
                            <th class="small text-secondary py-2 col-cost">2024</th>
                            <th class="small text-secondary py-2 col-cost">2025</th>
                            <th class="small text-secondary py-2 col-cost">2026</th>
                            <th class="small text-secondary py-2 col-cost">2027</th>
                            <th class="small text-secondary py-2 col-cost">2028</th>
                            <th class="small text-secondary py-2 col-cost">Succeeding Years</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        @php
                            $target = $project->project_cost_target;
                            $fundingReq = ($target->cost_year_2023 ?? 0) + ($target->cost_year_2024 ?? 0) + ($target->cost_year_2025 ?? 0) + ($target->cost_year_2026 ?? 0) + ($target->cost_year_2027 ?? 0) + ($target->cost_year_2028 ?? 0) + ($target->cost_succeeding_years ?? 0);
                            
                            $indicators = $project->project_indicator->map(function($pi) {
                                return ($pi->indicator_quantity ?? '') . ' ' . ($pi->indicator->indicator_name ?? '');
                            })->implode(', ');

                            $location = $project->location; 
                        @endphp
                        <tr>
                            <td class="small col-title">{{ $project->project_title }}</td>
                            <td class="small col-desc">{{ Str::limit($project->description, 100) }}</td>
                            <td class="small col-ind">{{ $indicators ?: '—' }}</td>
                            <td class="small col-loc">{{ $location }}</td>
                            <td class="small col-duty">{{ $project->agency->agency_acronym ?? '—' }}</td>
                            <td class="small text-end col-fund">{{ number_format($fundingReq, 2) }}</td>
                            
                            <!-- Physical Target -->
                            <td class="small text-center col-physical">{{ $target->target_year_2023 ?? '—' }}</td>
                            <td class="small text-center col-physical">{{ $target->target_year_2024 ?? '—' }}</td>
                            <td class="small text-center col-physical">{{ $target->target_year_2025 ?? '—' }}</td>
                            <td class="small text-center col-physical">{{ $target->target_year_2026 ?? '—' }}</td>
                            <td class="small text-center col-physical">{{ $target->target_year_2027 ?? '—' }}</td>
                            <td class="small text-center col-physical">{{ $target->target_year_2028 ?? '—' }}</td>
                            <td class="small text-center col-physical">{{ $target->target_succeeding_years ?? '—' }}</td>
                            
                            <!-- Project Cost -->
                            <td class="small text-center col-cost">{{ number_format($target->cost_year_2023 ?? 0, 1) }}</td>
                            <td class="small text-center col-cost">{{ number_format($target->cost_year_2024 ?? 0, 1) }}</td>
                            <td class="small text-center col-cost">{{ number_format($target->cost_year_2025 ?? 0, 1) }}</td>
                            <td class="small text-center col-cost">{{ number_format($target->cost_year_2026 ?? 0, 1) }}</td>
                            <td class="small text-center col-cost">{{ number_format($target->cost_year_2027 ?? 0, 1) }}</td>
                            <td class="small text-center col-cost">{{ number_format($target->cost_year_2028 ?? 0, 1) }}</td>
                            <td class="small text-center col-cost">{{ number_format($target->cost_succeeding_years ?? 0, 1) }}</td>
                            
                            <td class="small col-remarks">{{ $project->status }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="22" class="text-center py-5 text-muted">
                                <i data-lucide="info" class="d-block mx-auto mb-2 opacity-50" width="32"></i>
                                No projects found matching the criteria.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('partials.table-pagination', ['paginator' => $projects])
    </div>
</section>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Column Visibility Logic
    const toggleCheckboxes = document.querySelectorAll('.col-toggle-chk');
    const table = document.getElementById('reportTable');

    const updateColumnVisibility = () => {
        toggleCheckboxes.forEach(chk => {
            const colClass = chk.value;
            const elements = table.querySelectorAll('.' + colClass);
            elements.forEach(el => {
                if (chk.checked) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });
        });
    };

    toggleCheckboxes.forEach(chk => {
        chk.addEventListener('change', updateColumnVisibility);
    });

    // 2. Report Type Logic
    const reportTypeSelect = document.getElementById('report-type-select');
    const customColPanel = document.getElementById('custom-report-columns');

    if (reportTypeSelect) {
        reportTypeSelect.addEventListener('change', () => {
            if (reportTypeSelect.value === 'custom') {
                customColPanel.classList.remove('d-none');
            } else {
                customColPanel.classList.add('d-none');
                // Optional: reset columns for RDIP/AIP
            }
        });
    }

    // 3. Local Filter Logic (Optional helper for row filtering)
    const localSearchInput = document.getElementById('globalSearchInput'); // Reuse the utils search input
    if (localSearchInput) {
        localSearchInput.addEventListener('input', () => {
            const filter = localSearchInput.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr:not(.empty-row)');

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    }

    // 4. Export Logic
    const exportBtn = document.getElementById('btn-export-report');
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            const fundingCategory = document.querySelector('select[name="funding_category"]').value;
            const status = document.querySelector('select[name="status"]').value;
            
            // Build export URL
            let url = "{{ route('v2.reports.generateExcel') }}";
            url += `?funding_category=${fundingCategory}&status=${status}`;
            
            window.location.href = url;
        });
    }

    // Initialize Visibility
    updateColumnVisibility();
    if (window.lucide) window.lucide.createIcons();
});
</script>
<style>
    .status-badge-v3 {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        text-transform: uppercase;
        display: inline-block;
    }
    .status-badge-v3[data-status="ongoing"] { background: #e0f2fe; color: #0369a1; }
    .status-badge-v3[data-status="proposed"] { background: #fef3c7; color: #b45309; }
    .status-badge-v3[data-status="completed"] { background: #dcfce7; color: #15803d; }
    .status-badge-v3[data-status="dropped"] { background: #f1f5f9; color: #475569; }
    .status-badge-v3[data-status="suspended"] { background: #ffedd5; color: #c2410c; }
    .status-badge-v3[data-status="terminated"] { background: #fee2e2; color: #b91c1c; }
</style>
@endsection