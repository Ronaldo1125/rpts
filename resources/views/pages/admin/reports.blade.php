<section id="reports" class="page-content container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="fw-bold">Reports</h2>
    </div>



    <!-- Report Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <!-- Downlods / Actions -->
            <div class="d-flex justify-content-end align-items-center flex-wrap gap-3 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-medium">Report Type:</span>
                    <select id="report-type-select" class="form-select form-select-sm border" style="width: 205px;">
                        <option value="">-- Select Report Type --</option>
                        <option value="rdip">RDIP</option>
                        <option value="aip">AIP</option>
                        <option value="custom">Custom Report</option>
                    </select>
                </div>
                <div id="year-select-container" class="d-none align-items-center gap-2">
                    <span class="text-secondary small fw-medium">Year:</span>
                    <select id="report-year-select" class="form-select form-select-sm border" style="width: 155px;">
                        <option value="">-- Select Year --</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                    </select>
                </div>
                <button id="btn-export-report" class="btn btn-primary text-white px-3 py-2 fw-medium rounded d-flex align-items-center gap-2" style="background-color: #154A9A; border-color: #154A9A;">
                    <i data-lucide="download" width="16"></i> Export Report
                </button>
            </div>

            <!-- Custom Report Column Selection (Hidden by default) -->
            <div id="custom-report-columns" class="d-none mb-4 p-3 rounded bg-light border shadow-sm">
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

            <!-- Table Controls -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm border" style="width: 90px;">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries per page</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <!-- Column Visibility Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <i data-lucide="eye" width="16"></i> Columns
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-3 shadow-lg" style="min-width: 250px;">
                            <li><h6 class="dropdown-header px-0 mb-2">Show/Hide Columns</h6></li>
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

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-secondary small">Search:</span>
                        <input type="text" class="form-control form-control-sm border" style="width: 200px;">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle border-top" id="reportTable" data-sortable="true">
                    <thead class="text-center align-middle">
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
                        <tr>
                            <td class="small col-title">BU Tabaco Campus Medical & Dental Clinic</td>
                            <td class="small col-desc">One building completed</td>
                            <td class="small col-ind">1 Unit</td>
                            <td class="small col-loc">Tabaco City, Albay</td>
                            <td class="small col-duty">Bicol University</td>
                            <td class="small col-fund">10.00</td>
                            <!-- Physical Target -->
                            <td class="small text-center col-physical">1</td>
                            <td class="small text-center col-physical">—</td>
                            <td class="small text-center col-physical">—</td>
                            <td class="small text-center col-physical">—</td>
                            <td class="small text-center col-physical">—</td>
                            <td class="small text-center col-physical">—</td>
                            <td class="small text-center col-physical">—</td>
                            <!-- Project Cost -->
                            <td class="small text-center col-cost">10.0</td>
                            <td class="small text-center col-cost">—</td>
                            <td class="small text-center col-cost">—</td>
                            <td class="small text-center col-cost">—</td>
                            <td class="small text-center col-cost">—</td>
                            <td class="small text-center col-cost">—</td>
                            <td class="small text-center col-cost">—</td>
                            <td class="small col-remarks">On-track</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="text-secondary small">Showing 1 to 2 of 2 entries</span>
                <nav>
                    <ul class="custom-pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&lsaquo;</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&rsaquo;</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>
                    </ul>
                </nav>
        </div>
    </div>
</section>
