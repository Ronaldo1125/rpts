/**
 * UI Utilities for RPTS
 * Shared alerts and confirmation modals
 */

export const showSimpleAlert = (message, type = 'success') => {
    const id = 'alert-' + Date.now();
    const alertHtml = `
        <div id="${id}" class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow" role="alert" style="z-index: 10000; min-width: 300px;">
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="${type === 'success' ? 'check-circle' : 'alert-circle'}" width="20"></i>
                <span>${message}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    if (window.lucide) window.lucide.createIcons();

    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        const alertEl = document.getElementById(id);
        if (alertEl) {
            try {
                const bsAlert = new bootstrap.Alert(alertEl);
                bsAlert.close();
            } catch (e) {
                alertEl.remove();
            }
        }
    }, 3000);
};

export const showConfirmModal = (options) => {
    // Clean up existing modal instance if it exists
    const existing = document.getElementById('rpts-confirm-modal');
    if (existing) {
        const oldInstance = bootstrap.Modal.getInstance(existing);
        if (oldInstance) oldInstance.dispose();
        existing.remove();
    }

    const modalHtml = `
        <div class="modal fade" id="rpts-confirm-modal" tabindex="-1" aria-hidden="true" style="z-index: 10050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3 text-danger">
                            <i data-lucide="alert-triangle" width="48" height="48"></i>
                        </div>
                        <h5 class="fw-bold mb-2">${options.title || 'Are you sure?'}</h5>
                        <div class="text-muted mb-4">${options.message || 'Do you really want to proceed?'}</div>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-light px-4 py-2 fw-medium" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn ${options.confirmClass || 'btn-primary'} px-4 py-2 fw-medium" id="modal-confirm-action" data-bs-dismiss="modal">
                                ${options.confirmText || 'Confirm'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    if (window.lucide) window.lucide.createIcons();

    const modalEl = document.getElementById('rpts-confirm-modal');
    const bsModal = new bootstrap.Modal(modalEl);

    const confirmBtn = modalEl.querySelector('#modal-confirm-action');
    confirmBtn.onclick = () => {
        if (options.onConfirm) {
            try {
                options.onConfirm();
            } catch (err) {
                console.error("Error in confirmation callback:", err);
            }
        }
    };

    bsModal.show();
};

export const showInfoModal = (options) => {
    // Clean up existing modal instance if it exists
    const existing = document.getElementById('rpts-info-modal');
    if (existing) {
        const oldInstance = bootstrap.Modal.getInstance(existing);
        if (oldInstance) oldInstance.dispose();
        existing.remove();
    }

    const modalHtml = `
        <div class="modal fade" id="rpts-info-modal" tabindex="-1" aria-hidden="true" style="z-index: 10050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3 text-primary">
                            <i data-lucide="info" width="48" height="48"></i>
                        </div>
                        <h5 class="fw-bold mb-2">${options.title || 'Information'}</h5>
                        <div class="text-muted mb-4">${options.message || ''}</div>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-primary-rpts px-5 py-2 fw-medium rounded-pill text-white" data-bs-dismiss="modal">
                                ${options.closeText || 'Close'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    if (window.lucide) window.lucide.createIcons();

    const modalEl = document.getElementById('rpts-info-modal');
    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();
};

// Expose to window for global access
window.showSimpleAlert = showSimpleAlert;
window.showConfirmModal = showConfirmModal;
window.showInfoModal = showInfoModal;

/**
 * Extracts abbreviation from agency string like "Department of Health (DOH)"
 * @param {string} agencyStr 
 * @returns {string} The abbreviation or original string if not found
 */
export const getAgencyAbbreviation = (agencyStr) => {
    if (!agencyStr || agencyStr === '—' || agencyStr === '\u2014' || agencyStr === '&mdash;') return '—';
    // Try to match text inside parentheses
    const match = agencyStr.match(/\(([^)]+)\)/);
    if (match && match[1]) return match[1];
    return agencyStr;
};
window.getAgencyAbbreviation = getAgencyAbbreviation;
/**
 * Populates a select element with agencies.
 * @param {HTMLSelectElement} selectEl 
 * @param {string} selectedValue optional value to pre-select
 */
export const populateAgencyDropdown = async (selectEl, selectedValue = '') => {
    if (!selectEl) return;

    // Default agencies (aligned with agency.html and existing list)
    const defaults = [
        "Bicol University (BU)",
        "Commission on Population and Development (CPD)",
        "Department of Economy, Planning, and Development 5 (DEPDev 5)",
        "Department of Health (DOH)",
        "Department of Agriculture (DA)",
        "Department of Education (DepEd)",
        "Department of Public Works and Highways (DPWH)",
        "Department of Science and Technology (DOST)",
        "Department of Social Welfare and Development (DSWD)",
        "Department of Trade and Industry (DTI)",
        "Department of Transportation (DOTr)",
        "National Economic and Development Authority (NEDA)",
        "National Irrigation Administration (NIA)",
        "Philippine Coconut Authority (PCA)",
        "Technical Education and Skills Development Authority (TESDA)",
        "Local Government Unit (LGU)"
    ];

    let agencies = [...defaults];

    // Try to get from localforage 'agencies' (from Admin page)
    try {
        const saved = await localforage.getItem('agencies');
        if (Array.isArray(saved)) agencies = [...agencies, ...saved];
    } catch (e) { }

    // Also pick up any agencies mentioned in submissions
    try {
        const cppSubmissions = await localforage.getItem('cpp_submissions') || [];
        const oldSubmissions = await localforage.getItem('submissions') || [];

        const subAgencies = [
            ...cppSubmissions.map(s => s.agency || s.formData?.['f-agency']),
            ...oldSubmissions.map(s => s.agency || s.formData?.['f-agency'])
        ].filter(Boolean);

        subAgencies.forEach(a => {
            if (!agencies.some(x => x.toLowerCase() === a.toLowerCase())) {
                agencies.push(a);
            }
        });
    } catch (e) { }

    // Sort uniquely
    const allAgencies = [...new Set(agencies)].sort((a, b) => a.localeCompare(b));

    // Determine value to pre-select
    const currentVal = (selectedValue || selectEl.value || '').trim().toLowerCase();

    // Clear and add placeholder
    selectEl.innerHTML = '<option value="">-- Select Agency --</option>';

    allAgencies.forEach(agency => {
        const opt = document.createElement('option');
        opt.value = agency;
        opt.textContent = agency;
        // Case-insensitive match for pre-fill
        if (agency.toLowerCase() === currentVal) opt.selected = true;
        selectEl.appendChild(opt);
    });

    // Final attempt to set value if not caught by loop (e.g. dynamic value not in list)
    if (selectedValue && !selectEl.value) {
        const opt = document.createElement('option');
        opt.value = selectedValue;
        opt.textContent = selectedValue;
        opt.selected = true;
        selectEl.appendChild(opt);
    }
};
window.populateAgencyDropdown = populateAgencyDropdown;

/**
 * Workflow Stage Analytics Data & Handler (Global for Admin Dashboard)
 */
const stageDetails = {
    'submission': {
        title: 'Submission',
        headerRight: `
            <div class="nav nav-pills small gap-1 p-1 bg-light rounded-3" id="sector-pills" role="tablist">
                <button class="nav-link active px-3 border-0 rounded-2" onclick="switchSector(event, 'all')" style="font-size:0.75rem;font-weight:600;padding-top:0.4rem;padding-bottom:0.4rem;">All</button>
                <button class="nav-link px-3 border-0 rounded-2" onclick="switchSector(event, 'social')" style="font-size:0.75rem;font-weight:600;padding-top:0.4rem;padding-bottom:0.4rem;">Social</button>
                <button class="nav-link px-3 border-0 rounded-2" onclick="switchSector(event, 'economic')" style="font-size:0.75rem;font-weight:600;padding-top:0.4rem;padding-bottom:0.4rem;">Economic</button>
                <button class="nav-link px-3 border-0 rounded-2" onclick="switchSector(event, 'infra')" style="font-size:0.75rem;font-weight:600;padding-top:0.4rem;padding-bottom:0.4rem;">Infrastructure</button>
                <button class="nav-link px-3 border-0 rounded-2" onclick="switchSector(event, 'insti')" style="font-size:0.75rem;font-weight:600;padding-top:0.4rem;padding-bottom:0.4rem;">Devt. Ad</button>
            </div>`,
        html: `
            <div class="row g-4 mb-4">
                <div class="col-md-5">
                    <div class="bg-white p-3 rounded-4 shadow-sm border h-100">
                         <div class="d-flex justify-content-between mb-3 text-uppercase" style="letter-spacing:0.03em;">
                            <span class="small fw-bold text-muted" id="sector-dist-title">Sectorial Distribution</span>
                            <i data-lucide="pie-chart" width="14" class="text-primary"></i>
                         </div>
                         <div class="d-flex align-items-center justify-content-center py-2">
                            <div style="width:180px; height:180px; position:relative;">
                                <canvas id="chart-sector-dist"></canvas>
                            </div>
                         </div>
                         <div class="mt-3 px-1 d-flex flex-wrap justify-content-center column-gap-3 row-gap-2" id="sector-legend-container" style="font-size: 0.7rem;">
                            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#1e3a8a;"></div><span class="text-muted fw-medium">Social</span> <span class="fw-bold text-dark" id="wf-legend-count-social">0</span></div>
                            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#2563eb;"></div><span class="text-muted fw-medium">Economic</span> <span class="fw-bold text-dark" id="wf-legend-count-economic">0</span></div>
                            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#60a5fa;"></div><span class="text-muted fw-medium">Infra</span> <span class="fw-bold text-dark" id="wf-legend-count-infra">0</span></div>
                            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#bfdbfe;"></div><span class="text-muted fw-medium">Devt. Ad</span> <span class="fw-bold text-dark" id="wf-legend-count-insti">0</span></div>
                         </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="bg-white p-3 rounded-4 shadow-sm border h-100">
                        <div class="d-flex justify-content-between mb-4 text-uppercase" style="letter-spacing:0.03em;">
                            <span class="small fw-bold text-muted">Agency Submissions <span id="active-sector-label" class="text-primary">(All)</span></span>
                            <span class="badge bg-primary bg-opacity-10 text-primary small" id="chart-total-label">0 Projects</span>
                        </div>
                        <div style="height:250px; width:100%; margin-bottom:1rem;">
                            <canvas id="chart-agency-submissions"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CPP Submissions List -->
            <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
                <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="list" width="16" class="text-primary"></i>
                        CPP Submissions
                        <span id="cipg-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
                    </h6>
                    <div class="input-group input-group-sm" style="max-width:220px;">
                        <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="cipgSearchInput" placeholder="Search...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Sub-Sector</th>
                                <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cipgTableBody">
                            <!-- Populated by admin-dashboard-loader.js -->
                        </tbody>
                    </table>
                </div>
            </div>
            `
    },
    'referred': {
        title: 'Referral to Division',
        html: `
            <!-- Division KPI cards — populated by initReferredStage() -->
            <div class="mb-4 d-flex gap-3 overflow-x-auto pb-3 justify-content-center" id="ref-div-kpi-row">
                <div class="p-3 bg-white rounded-4 shadow-sm border text-center flex-grow-1" style="min-width:140px; border-bottom:4px solid #1e3a8a;">
                    <span class="text-muted small fw-bold d-block mb-1 text-uppercase letter-spacing-05">PFPD</span>
                    <h3 class="fw-bold mb-0 text-primary" id="ref-kpi-pfpd">—</h3>
                    <span class="small text-muted">referred</span>
                </div>
                <div class="p-3 bg-white rounded-4 shadow-sm border text-center flex-grow-1" style="min-width:140px; border-bottom:4px solid #2563eb;">
                    <span class="text-muted small fw-bold d-block mb-1 text-uppercase letter-spacing-05">PMED</span>
                    <h3 class="fw-bold mb-0 text-primary" id="ref-kpi-pmed">—</h3>
                    <span class="small text-muted">referred</span>
                </div>
                <div class="p-3 bg-white rounded-4 shadow-sm border text-center flex-grow-1" style="min-width:140px; border-bottom:4px solid #93c5fd;">
                    <span class="text-muted small fw-bold d-block mb-1 text-uppercase letter-spacing-05">DRD</span>
                    <h3 class="fw-bold mb-0 text-primary" id="ref-kpi-drd">—</h3>
                    <span class="small text-muted">referred</span>
                </div>
                <div class="p-3 bg-white rounded-4 shadow-sm border text-center flex-grow-1" style="min-width:140px; border-bottom:4px solid #6366f1;">
                    <span class="text-muted small fw-bold d-block mb-1 text-uppercase letter-spacing-05">Pending</span>
                    <h3 class="fw-bold mb-0 text-indigo" id="ref-kpi-pending" style="color:#6366f1;">—</h3>
                    <span class="small text-muted">awaiting referral</span>
                </div>
            </div>

            <!-- Validated CPPs list — populated by initReferredStage() -->
            <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
                <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="send" width="16" class="text-primary"></i>
                        Validated CPPs — Awaiting Division Referral
                        <span id="ref-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
                    </h6>
                    <input type="text" class="form-control form-control-sm bg-light border-0 rounded-pill px-3"
                        id="refSearchInput" placeholder="Search..." style="max-width:200px;">
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="small text-secondary fw-semibold ps-4">Project Title</th>
                                <th class="small text-secondary fw-semibold">Agency</th>
                                <th class="small text-secondary fw-semibold">Validated By</th>
                                <th class="small text-secondary fw-semibold">Validated On</th>
                                <th class="small text-secondary fw-semibold">Status</th>
                                <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                            </tr>
                        </thead>
                        <tbody id="ref-validated-tbody">
                            <tr><td colspan="6" class="text-center py-4 text-muted small">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading...
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>`
    },
    'evaluation': {
        title: 'Project Assessment Report',
        html: `
            <div class="row g-4 mb-4">
                <div class="col-md-7">
                    <div class="bg-white p-4 rounded-4 shadow-sm border mb-3">
                        <span class="small fw-bold d-block mb-4">DIVISION ASSESSMENT WORKLOAD</span>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width:24px; height:24px; font-size:0.6rem;">PFPD</div>
                                <div class="flex-grow-1"><div class="progress" style="height:6px;"><div class="progress-bar" style="width:85%; background:#154A9A"></div></div></div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-info fw-bold" style="width:24px; height:24px; font-size:0.6rem;">PMED</div>
                                <div class="flex-grow-1"><div class="progress" style="height:6px;"><div class="progress-bar" style="width:60%; background:#60a5fa"></div></div></div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-success fw-bold" style="width:24px; height:24px; font-size:0.6rem;">DRD</div>
                                <div class="flex-grow-1"><div class="progress" style="height:6px;"><div class="progress-bar" style="width:95%; background:#34d399"></div></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                     <div class="bg-white p-4 rounded-4 shadow-sm border h-100">
                        <span class="small fw-bold d-block mb-3">STATUS BREAKDOWN</span>
                        <div class="d-flex flex-column gap-2">
                            <div class="p-2 rounded bg-light border-start border-4 border-warning d-flex justify-content-between text-dark"><span>Pending Assessment</span><span class="fw-bold" id="eval-kpi-pending">0</span></div>
                            <div class="p-2 rounded bg-light border-start border-4 border-success d-flex justify-content-between text-dark"><span>Assessed</span><span class="fw-bold" id="eval-kpi-assessed">0</span></div>
                            <div class="p-2 rounded bg-light border-start border-4 border-primary d-flex justify-content-between text-dark"><span>Evaluated</span><span class="fw-bold" id="eval-kpi-evaluated">0</span></div>
                        </div>
                     </div>
                </div>
            </div>

            <!-- PAR Projects List -->
            <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
                <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="file-check" width="16" class="text-primary"></i>
                        Evaluated Reports
                        <span id="eval-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
                    </h6>
                    <div class="input-group input-group-sm" style="max-width:220px;">
                        <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="evalSearchInput" placeholder="Search...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Evaluating Division</th>
                                <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                            </tr>
                        </thead>
                        <tbody id="evalTableBody">
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    <span class="text-muted">Loading candidates...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>`
    },
    'sectoral': {
        title: 'SecCom Presentation',
        html: `
            <div class="row g-3 mb-4">
                <div class="col-md-3"><div class="p-3 text-white border-0 rounded shadow-sm text-center" style="background:#154A9A;"><span class="small fw-bold opacity-75">EDC</span><h3 class="mt-1 mb-0 fw-bold">12</h3></div></div>
                <div class="col-md-3"><div class="p-3 text-white border-0 rounded shadow-sm text-center" style="background:#0ea5e9;"><span class="small fw-bold opacity-75">IDD</span><h3 class="mt-1 mb-0 fw-bold">8</h3></div></div>
                <div class="col-md-3"><div class="p-3 text-white border-0 rounded shadow-sm text-center" style="background:#10b981;"><span class="small fw-bold opacity-75">DAC</span><h3 class="mt-1 mb-0 fw-bold">15</h3></div></div>
                <div class="col-md-3"><div class="p-3 text-white border-0 rounded shadow-sm text-center" style="background:#6366f1;"><span class="small fw-bold opacity-75">SDC</span><h3 class="mt-1 mb-0 fw-bold">9</h3></div></div>
            </div>

            <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
                <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i data-lucide="list" width="16" class="text-primary"></i>
                        Sectoral Presentation CPPs
                        <span id="sectoral-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
                    </h6>
                    <div class="input-group input-group-sm" style="max-width:220px;">
                        <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="sectoralSearchInput" placeholder="Search...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                                <th class="small text-secondary fw-semibold">Agency</th>
                                <th class="small text-secondary fw-semibold">Sector</th>
                                <th class="small text-secondary fw-semibold">Status</th>
                                <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                            </tr>
                        </thead>
                        <tbody id="sectoralTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">
                                    Loading sectoral presentation CPPs…
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>`
    },
    'findings': {
        title: 'Findings & Recommendations',
        html: `
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="list-group list-group-flush border shadow-sm rounded-4 overflow-hidden">
                        <div class="list-group-item active bg-primary border-0 p-3"><span class="small fw-bold">EDC COMMITTEE</span></div>
                        <div class="list-group-item border-0 p-3"><span class="small fw-bold">IDC COMMITTEE</span></div>
                        <div class="list-group-item border-0 p-3"><span class="small fw-bold">DAC COMMITTEE</span></div>
                        <div class="list-group-item border-0 p-3"><span class="small fw-bold">SDC COMMITTEE</span></div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="bg-white p-4 rounded-4 shadow-sm border h-100">
                        <div class="d-flex justify-content-between mb-4"><span class="small fw-bold uppercase">Technical Recommendations</span></div>
                        <div class="p-3 bg-light rounded-3 mb-3 border-start border-4 border-navy">
                            <p class="small mb-0 italic text-muted">"Recommended for approval with provisions for climate resilience mapping integration."</p>
                        </div>
                        <div class="row g-2">
                            <div class="col-6"><div class="p-2 border rounded text-center"><span class="d-block x-small text-muted mb-1">Response Time (Avg)</span><span class="fw-bold">4.2 Days</span></div></div>
                            <div class="col-6"><div class="p-2 border rounded text-center"><span class="d-block x-small text-muted mb-1">Key Compliance Rate</span><span class="fw-bold">92%</span></div></div>
                        </div>
                    </div>
                </div>
            </div>`
    },
    'revised': {
        title: 'Revised Submissions',
        html: `
            <div class="bg-white p-4 rounded-4 shadow-sm border animate-popup">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05 d-flex align-items-center gap-2">
                        <i data-lucide="refresh-cw" width="16" class="text-primary"></i>
                        Revised Projects
                        <span id="revised-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
                    </h6>
                    <div class="input-group input-group-sm" style="max-width:220px;">
                        <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="revisedSearchInput" placeholder="Search...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Stage</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Revised Date</th>
                                <th class="small text-secondary fw-semibold text-center pe-4" data-sort-skip="true">Action</th>
                            </tr>
                        </thead>
                        <tbody id="revisedTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    <span class="text-muted">Loading revised submissions...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>`
    },
    'rdc-pres': {
        title: 'RDC Presentation',
        html: `
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-4 shadow-sm border h-100 text-center">
                        <span class="small fw-bold d-block mb-3">RDIP DECISION FUNNEL</span>
                        <div class="funnel-container d-flex flex-column align-items-center gap-1">
                            <div style="width:100%; height:25px; background:#154A9A; border-radius:15px 15px 2px 2px;"></div>
                            <div style="width:85%; height:25px; background:#3b82f6; border-radius:2px;"></div>
                            <div style="width:65%; height:25px; background:#f97316; border-radius:2px;"></div>
                            <div style="width:40%; height:25px; background:#10b981; border-radius:2px 2px 15px 15px;"></div>
                        </div>
                        <div class="mt-3 small text-muted">Final Approval Funnel (RDIP)</div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="bg-white p-4 rounded-4 shadow-sm border mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary px-3">READY FOR RDC: 24</span>
                            <span class="badge bg-danger px-3">DEFERRED: 11</span>
                        </div>
                        <div class="p-3 bg-light rounded text-muted small">
                            <div class="d-flex justify-content-between border-bottom pb-1 mb-1"><span>Priority Projects</span><span class="fw-bold text-dark">18</span></div>
                            <div class="d-flex justify-content-between"><span>RDC Agenda Items</span><span class="fw-bold text-dark">6</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-4 shadow-sm border animate-popup">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05 d-flex align-items-center gap-2">
                        <i data-lucide="arrow-right-circle" width="16" class="text-primary"></i>
                        RDC Presentation Projects
                        <span id="rdc-pres-list-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">0</span>
                    </h6>
                    <div class="input-group input-group-sm" style="max-width:240px;">
                        <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="rdcPresSearchInput" placeholder="Search RDC items...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Stage</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Status</th>
                                <th class="small text-secondary fw-semibold text-end pe-4" style="white-space:nowrap;">Updated</th>
                            </tr>
                        </thead>
                        <tbody id="rdcPresTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                    <span class="text-muted">Loading RDC presentation submissions...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>`
    },
    'approved': {
        title: 'RDC Approved',
        html: `
            <div class="p-4 bg-white rounded-4 shadow-sm border text-center mb-4">
                <div class="row g-4">
                    <div class="col-md-6 border-end text-center">
                        <h2 class="fw-bold text-success mb-1">18</h2>
                        <span class="small text-muted uppercase fw-bold">Approved Projects</span>
                    </div>
                    <div class="col-md-6 text-center">
                        <h2 class="fw-bold text-primary mb-1">5</h2>
                        <span class="small text-muted uppercase fw-bold">Resolutions Issued</span>
                    </div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-4 shadow-sm border mb-3 animate-popup">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 small text-uppercase letter-spacing-05 d-flex align-items-center gap-2">
                        <i data-lucide="award" width="16" class="text-success"></i>
                        RDC Approved Projects
                        <span id="rdc-approved-list-count" class="badge bg-success bg-opacity-10 text-success fw-bold ms-1" style="font-size:0.7rem;">0</span>
                    </h6>
                    <div class="input-group input-group-sm" style="max-width:240px;">
                        <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="rdcApprovedSearchInput" placeholder="Search RDC approved...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="small text-secondary fw-semibold ps-4" style="white-space:nowrap;">Project Title</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Agency</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Stage</th>
                                <th class="small text-secondary fw-semibold" style="white-space:nowrap;">Status</th>
                                <th class="small text-secondary fw-semibold text-end pe-4" style="white-space:nowrap;">Updated</th>
                            </tr>
                        </thead>
                        <tbody id="rdcApprovedTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                                    <span class="text-muted">Loading RDC approved submissions...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-light p-3 rounded-4 border">
                <span class="small fw-bold d-block mb-3">LATEST RDC RESOLUTIONS</span>
                <ul class="list-unstyled mb-0 small text-muted">
                    <li class="mb-2 d-flex gap-2"><i data-lucide="file-check" width="14" class="text-success"></i> Resolution No. 42 s. 2024 - Project Alpha Approval</li>
                    <li class="mb-2 d-flex gap-2"><i data-lucide="file-check" width="14" class="text-success"></i> Resolution No. 43 s. 2024 - RDP Sectoral Update</li>
                    <li class="d-flex gap-2"><i data-lucide="file-check" width="14" class="text-success"></i> Resolution No. 45 s. 2024 - Infrastructure Funding</li>
                </ul>
            </div>`
    }
};

window.toggleStepDetails = function (event, id) {
    const panel = document.getElementById('workflow-details-panel');
    const contentEl = document.querySelector('.workflow-details-inner');
    if (!panel || !contentEl) return;

    const stepData = stageDetails[id];
    if (!stepData) return;

    // If the same tab is already active, do nothing — panel stays open
    if (panel.classList.contains('show') && contentEl.dataset.activeStage === id) {
        return;
    }

    // Update selection state
    document.querySelectorAll('.process-step').forEach(s => s.classList.remove('active'));
    
    // Find based on ID or use target from event
    let stepEl = null;
    if (event && event.currentTarget) {
        stepEl = event.currentTarget;
    } else {
        // Find stage element by checking onclick or just common class prefixes
        stepEl = document.querySelector(`.process-step[onclick*="'${id}'"]`) || 
                 document.querySelector(`.process-step.step-1`);
    }

    if (stepEl) {
        stepEl.classList.add('active');
    }

    // Update Content
    contentEl.innerHTML = `
        <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <span class="p-1 px-2 rounded bg-primary text-white" style="font-size:0.7rem;">STAGE</span>
                ${stepData.title} Analytics
            </h5>
            ${stepData.headerRight || ''}
        </div>
        <div class="animate-popup">
            ${stepData.html}
        </div>
    `;
    contentEl.dataset.activeStage = id;
    panel.classList.add('show');
    if (window.lucide) window.lucide.createIcons();

    // Initialize charts and tables after content injection
    setTimeout(() => { 
        if (typeof window.initStageCharts === 'function') window.initStageCharts(id);
        if (id === 'submission') {
            // Prefer admin table renderer; fall back to agency loader
            if (typeof window.initAdminCipgTable === 'function') {
                window.initAdminCipgTable();
            } else if (typeof window.initSubmissionsLoader === 'function') {
                window.initSubmissionsLoader();
            }
        }
        if (id === 'referred') {
            if (typeof window.initReferredStage === 'function') {
                window.initReferredStage();
            }
        }
        if (id === 'evaluation') {
            if (typeof window.initEvaluationStage === 'function') {
                window.initEvaluationStage();
            }
        }
        if (id === 'revised') {
            import('../admin/admin-revision-stage-loader.js').then(m => {
                m.initRevisionStage();
            });
        }
        if (id === 'sectoral') {
            if (typeof window.initAdminSectoralStage === 'function') {
                window.initAdminSectoralStage();
            }
        }
        if (id === 'rdc-pres') {
            if (typeof window.initRdcPresentationStage === 'function') {
                window.initRdcPresentationStage();
            }
        }
        if (id === 'approved') {
            if (typeof window.initRdcApprovedStage === 'function') {
                window.initRdcApprovedStage();
            }
        }
    }, 100);
};

window._dashboardCharts = window._dashboardCharts || {};

window.switchSector = function (event, sectorId) {
    const pills = document.querySelectorAll('#sector-pills .nav-link');
    
    // Update Tab UI
    pills.forEach(p => p.classList.remove('active'));
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }

    // Update Label
    const label = document.getElementById('active-sector-label');
    if (label) {
        label.textContent = `(${sectorId.charAt(0).toUpperCase() + sectorId.slice(1)})`;
    }

    // Update Chart
    if (window.initStageCharts) {
        window.initStageCharts('submission', sectorId);
    }

    // Delegate to the table filter — prefer admin, fall back to agency
    if (typeof window.filterAdminCipgTable === 'function') {
        window.filterAdminCipgTable(sectorId);
    } else if (window.filterSubmissionsTableBySector) {
        window.filterSubmissionsTableBySector(sectorId);
    }
};

window.initStageCharts = function(stageKey, forceSector = 'all') {
    if (!window.Chart) return;

    // Cleanup existing charts
    if (window._dashboardCharts) {
        Object.values(window._dashboardCharts).forEach(c => c?.destroy());
    }
    window._dashboardCharts = {};

    if (stageKey === 'submission') {
        localforage.getItem('cpp_submissions').then(submissions => {
            const list = submissions || [];
            const nonDrafts = list.filter(s => {
                const st = (s?.submissionStatus || s?.status || '').trim();
                const isPending = (st === 'Submitted' || st === 'Resubmitted');
                return isPending && !s.referredToPdipb;
            });

            // 1. Sector Pie Logic
            const sCounts = { social: 0, economic: 0, infra: 0, insti: 0 };
            const subsectorCounts = {}; // Key: subsector name string, Value: count
            
            nonDrafts.forEach(s => {
                const sec = (s.sector || s.formData?.['f-sector'] || '').toLowerCase();
                const subsec = s.subsector || s.formData?.['f-sub-sector'] || 'Uncategorized';
                
                if (sec.includes('social')) sCounts.social++;
                else if (sec.includes('economic')) sCounts.economic++;
                else if (sec.includes('infra') || sec.includes('physical')) sCounts.infra++;
                else if (sec.includes('insti') || sec.includes('devt') || sec.includes('admin')) sCounts.insti++;

                // Track subsector only for the active filter (if not 'all')
                if (forceSector !== 'all') {
                    let match = false;
                    if (forceSector === 'social' && sec.includes('social')) match = true;
                    if (forceSector === 'economic' && sec.includes('economic')) match = true;
                    if (forceSector === 'infra' && (sec.includes('infra') || sec.includes('physical'))) match = true;
                    if (forceSector === 'insti' && (sec.includes('insti') || sec.includes('devt') || sec.includes('admin'))) match = true;
                    
                    if (match) {
                        subsectorCounts[subsec] = (subsectorCounts[subsec] || 0) + 1;
                    }
                }
            });

            const updateLegend = (prefix) => {
                const els = ['social','economic','infra','insti'];
                els.forEach(k => {
                    const el = document.getElementById(`${prefix}legend-count-${k}`);
                    if (el) el.textContent = sCounts[k === 'insti' ? 'insti' : k];
                });
            };
            updateLegend('dash-');

            // Dynamic title update
            const titleEl = document.getElementById('sector-dist-title');
            if (titleEl) {
                titleEl.textContent = forceSector === 'all' ? 'Sectorial Distribution' : 'Subsector Distribution';
            }

            const ctxPie = document.getElementById('chart-sector-dist')?.getContext('2d');
            if (ctxPie) {
                let pieLabels = [];
                let pieData = [];
                let pieColors = [];

                const legendContainer = document.getElementById('sector-legend-container');

                if (forceSector === 'all') {
                    pieLabels = ['Social', 'Economic', 'Infra', 'Devt. Ad'];
                    pieData = [sCounts.social, sCounts.economic, sCounts.infra, sCounts.insti];
                    pieColors = ['#1e3a8a', '#2563eb', '#60a5fa', '#bfdbfe'];
                    
                    if (legendContainer) {
                        legendContainer.innerHTML = `
                            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#1e3a8a;"></div><span class="text-muted fw-medium">Social</span> <span class="fw-bold text-dark">${sCounts.social}</span></div>
                            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#2563eb;"></div><span class="text-muted fw-medium">Economic</span> <span class="fw-bold text-dark">${sCounts.economic}</span></div>
                            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#60a5fa;"></div><span class="text-muted fw-medium">Infra</span> <span class="fw-bold text-dark">${sCounts.infra}</span></div>
                            <div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:#bfdbfe;"></div><span class="text-muted fw-medium">Devt. Ad</span> <span class="fw-bold text-dark">${sCounts.insti}</span></div>
                        `;
                    }
                } else {
                    pieLabels = Object.keys(subsectorCounts);
                    pieData = Object.values(subsectorCounts);
                    // generate palette for subsectors
                    const palettes = {
                        social: ['#1e3a8a', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'],
                        economic: ['#14532d', '#166534', '#15803d', '#16a34a', '#22c55e', '#4ade80', '#86efac', '#bbf7d0'],
                        infra: ['#7c2d12', '#9a3412', '#c2410c', '#ea580c', '#f97316', '#fb923c', '#fdba74', '#fed7aa', '#ffedd5'],
                        insti: ['#4c1d95', '#5b21b6', '#6d28d9', '#7c3aed', '#8b5cf6', '#a78bfa', '#c4b5fd', '#ddd6fe', '#ede9fe', '#f5f3ff']
                    };
                    const baseColors = palettes[forceSector] || palettes.social;
                    pieLabels.forEach((_, i) => {
                        pieColors.push(baseColors[i % baseColors.length]);
                    });

                    // Build Subsector Legend
                    if (legendContainer) {
                        if (pieLabels.length === 0) {
                            legendContainer.innerHTML = `<div class="d-flex align-items-center gap-2"><span class="text-muted fw-medium small">No subsector data</span></div>`;
                        } else {
                            legendContainer.innerHTML = pieLabels.map((lbl, idx) => {
                                return `<div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:2px;background:${pieColors[idx]};"></div><span class="text-muted fw-medium">${lbl}</span> <span class="fw-bold text-dark">${pieData[idx]}</span></div>`;
                            }).join('');
                        }
                    }

                    if (pieData.length === 0) {
                        pieLabels = ['No Data'];
                        pieData = [1];
                        pieColors = ['#f1f5f9'];
                    }
                }

                window._dashboardCharts.sectorDist = new Chart(ctxPie, {
                    type: 'pie',
                    data: {
                        labels: pieLabels,
                        datasets: [{
                            data: pieData,
                            backgroundColor: pieColors
                        }]
                    },
                    plugins: [ChartDataLabels],
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        plugins: { 
                            legend: { display: false }, 
                            datalabels: { 
                                color: forceSector === 'all' ? '#fff' : (ctx) => {
                                    // if subsectors have no data (placeholder value 1), hide label
                                    if(pieLabels[0] === 'No Data') return 'transparent';
                                    return '#fff';
                                }, 
                                formatter: (v, ctx) => {
                                    if(pieLabels[0] === 'No Data') return '';
                                    return v || '';
                                } 
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (pieLabels[0] === 'No Data') return ' No data';
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        return ` ${label}: ${value}`;
                                    }
                                }
                            }
                        } 
                    }
                });
            }

            // 2. Agency Bar Logic
            const filtered = forceSector === 'all' ? nonDrafts : nonDrafts.filter(s => {
                const sec = (s.sector || s.formData?.['f-sector'] || '').toLowerCase();
                if (forceSector === 'social') return sec.includes('social');
                if (forceSector === 'economic') return sec.includes('economic');
                if (forceSector === 'infra') return sec.includes('infra') || sec.includes('physical');
                if (forceSector === 'insti') return sec.includes('insti') || sec.includes('devt');
                return true;
            });

            const agencyMap = {};
            filtered.forEach(s => {
                const raw = s.agency || s.formData?.['f-agency'] || 'Unknown';
                const abb = window.getAgencyAbbreviation ? window.getAgencyAbbreviation(raw) : raw;
                agencyMap[abb] = (agencyMap[abb] || 0) + 1;
            });

            const labels = Object.keys(agencyMap);
            const data = Object.values(agencyMap);
            const totalLabel = document.getElementById('chart-total-label');
            if (totalLabel) totalLabel.textContent = `${filtered.length} Projects`;

            const ctxBar = document.getElementById('chart-agency-submissions')?.getContext('2d');
            if (ctxBar) {
                window._dashboardCharts.agencySub = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: labels.length ? labels : ['No Data'],
                        datasets: [{
                            label: 'Projects',
                            data: data.length ? data : [0],
                            backgroundColor: '#154A9A',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } },
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
    }
};
