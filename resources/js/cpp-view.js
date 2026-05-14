/**
 * cpp-view.js
 * Handles rendering of a single CPP submission in a read-only view.
 */

export async function initCppView() {
    window.initCppView = initCppView;
    const container = document.getElementById('cpp-view-container');
    if (!container) return;

    const submissionId = sessionStorage.getItem('cpp_view_id');
    console.log('[cpp-view] Initializing view for ID:', submissionId);

    if (!submissionId) {
        container.innerHTML = `<div class="alert alert-warning">No submission selected for viewing.</div>`;
        return;
    }

    try {
        const submissions = await localforage.getItem('cpp_submissions') || [];
        const submission = submissions.find(s => s.id === submissionId);
        console.log('[cpp-view] Found submission:', submission);

        if (!submission || !submission.formData) {
            console.warn('[cpp-view] Submission or formData missing.');
            container.innerHTML = `
                <div class="alert alert-danger m-4">
                    <h5 class="fw-bold">Submission data not found</h5>
                    <p class="small mb-0">This could happen if the submission was made before the "View" feature was enabled.</p>
                </div>`;
            return;
        }

        // Update Header Dynamically
        const titleEl = document.getElementById('cpp-view-title');
        const actionEl = document.getElementById('cpp-view-actions');

        if (titleEl) {
            titleEl.textContent = submission.status === 'Draft' ? 'Draft Project Profile' : 'Project Profile View';
        }

        if (actionEl) {
            const isDraft = submission.status === 'Draft';

            const curRole = JSON.parse(localStorage.getItem('currentUser') || '{}').role;
            // Check if we came from the PDIPB CTE tab (pdipb-cte-loader sets this flag)
            const returnOverride = sessionStorage.getItem('cpp_view_return_page');
            sessionStorage.removeItem('cpp_view_return_page'); // clear after reading

            const isDashboard = window.location.pathname.includes('dashboard.html');
            const hasAdminLink = document.querySelector('[data-page="manage-submissions"]');
            const returnPage = returnOverride
                || ((curRole === 'admin' || isDashboard || hasAdminLink) ? 'manage-submissions' : 'submissions');

            const returnLabel = returnOverride === 'test-and-evaluation'
                ? 'Back to Validation List'
                : 'Back to Submissions';

            actionEl.innerHTML = `
                ${!isDraft ? `
            <button class="btn btn-primary shadow-sm btn-sm px-4 rounded-pill me-2" onclick="window.print()" title="Use the browser's native 'Save as PDF' via Print for 100% accurate formatting">
                <i data-lucide="printer" width="16" class="me-1"></i> Print / Save PDF
            </button>` : ''}
                <button class="btn-back-dash" data-page="${returnPage}">
                    <i data-lucide="chevron-left" width="16"></i> ${returnLabel}
                </button>
            `;

            if (isDraft) {
                const editBtn = document.createElement('button');
                editBtn.className = "btn btn-primary btn-sm px-4 rounded-pill me-1";
                editBtn.innerHTML = '<i data-lucide="edit-3" width="16" class="me-1"></i> Edit Draft';
                editBtn.onclick = () => {
                    sessionStorage.setItem('cpp_edit_id', submission.id);
                    const cppContainer = document.getElementById('cpp-steps-container');
                    if (cppContainer) {
                        cppContainer.innerHTML = '';
                        delete cppContainer.dataset.loaded;
                    }
                    // switchPage triggers initCppForm via cipg-main.js patch — don't call both
                    if (window.switchPage) window.switchPage('cpp-form');
                };
                actionEl.prepend(editBtn);
            }
            if (window.lucide) window.lucide.createIcons();
        }

        renderSubmission(container, submission);
    } catch (err) {
        console.error('[cpp-view] Error loading submission:', err);
        container.innerHTML = `<div class="alert alert-danger">Error: ${err.message}</div>`;
    }
}

function renderSubmission(container, submission) {
    const d = submission.formData;
    const g = (key) => (d[key] !== undefined && d[key] !== null && d[key] !== '') ? String(d[key]) : '—';
    const gSig = (key) => (d[key] && String(d[key]).startsWith('data:')) ? d[key] : '';

    const renderCheckboxGroup = (options, activeValues) => {
        if (!Array.isArray(activeValues)) activeValues = activeValues ? [activeValues] : [];
        return options.map(opt => `
            <div class="d-flex align-items-center gap-2 mb-1">
                <div style="width:14px; height:14px; border:1px solid #000; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold; background:#fff;">
                    ${activeValues.includes(opt) ? '✓' : ''}
                </div>
                <span style="font-size:0.72rem;">${opt}</span>
            </div>
        `).join('');
    };

    const summaryHtml = `
        <div id="printable-cpp" class="page-container animate__animated animate__fadeIn" style="max-width:950px; margin: 0 auto; background:#fff; padding:3rem; box-shadow:0 0 40px rgba(0,0,0,0.1); color:#000; font-family:'Times New Roman', serif;">
            
            <!-- HEADER SECTION -->
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:60px; height:60px; border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:5px; background:#fff;">
                        <img src="assets/images/rdc.png" style="max-width:100%; max-height:100%;" alt="RDC Logo" onerror="this.style.display='none'">
                    </div>
                    <div style="width:60px; height:60px; border:1px solid #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:5px; background:#fff;">
                        <img src="assets/images/rnp.png" style="max-width:100%; max-height:100%;" alt="RNP Logo" onerror="this.style.display='none'">
                    </div>
                    <div>
                        <p class="mb-0 fw-bold" style="font-size:0.75rem;">REPUBLIC OF THE PHILIPPINES</p>
                        <p class="mb-0 fw-bold" style="font-size:0.75rem; color:#108543;">REGIONAL DEVELOPMENT COUNCIL</p>
                        <p class="mb-0 fw-bold" style="font-size:0.75rem; color:#154A9A;">BICOL REGION</p>
                    </div>
                </div>
                <div class="text-end" style="font-size:0.6rem; color:#475569; line-height:1.4;">
                    <p class="mb-0">FM-PDI-01 | CPP Form | Revision No. 01</p>
                    <p class="mb-0">Effectivity Date: August 1, 2025</p>
                    <p class="mb-0 mt-3 fw-bold" style="font-size:0.7rem;">Annex C</p>
                    <p class="mb-0 mt-1">Submission ID: <span class="text-dark fw-bold">${submission.id}</span></p>
                </div>
            </div>

            <!-- BLUE TITLE BAR -->
            <div style="background:#154A9A; color:#fff; text-align:center; font-weight:bold; padding:6px; margin-top:20px;">
                COMPREHENSIVE PROJECT PROFILE
            </div>

            <!-- AGENCY & SECTOR BOXES -->
            <div class="row g-0 border-top border-bottom border-dark mt-2">
                <div class="col-8 border-end border-dark p-2 d-flex align-items-center gap-2">
                    <span class="fw-bold" style="font-size:0.7rem; width:50px;">Agency:</span>
                    <div style="border:1px solid #000; font-size:0.82rem; min-height:20px; padding-left:2px; flex-grow:1;">${g('f-agency')}</div>
                </div>
                <div class="col-4 p-2 d-flex align-items-center gap-2">
                    <span class="fw-bold" style="font-size:0.7rem; width:45px;">Sector:</span>
                    <div style="border:1px solid #000; font-size:0.82rem; min-height:20px; padding-left:2px; flex-grow:1;">${g('f-sector')}</div>
                </div>
            </div>

            <!-- I. PROJECT INFORMATION -->
            <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">I. PROJECT INFORMATION</div>
            <div class="ps-3">
                <div class="mb-2">
                    <span style="font-size:0.72rem; display:block; font-weight:bold;">1. Project Title:</span>
                    <div style="border:1px solid #000; font-size:0.82rem; min-height:40px; padding:2px; line-height:1.2; font-weight:bold;">${g('f-title')}</div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">2. Project Type:</span>
                        <div class="ms-2">
                            ${renderCheckboxGroup(['Capital Outlay', 'Technical Assistance'], d['project-type'])}
                        </div>
                    </div>
                    <div class="col-6">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">3. Project Components:</span>
                        <div style="border:1px solid #000; font-size:0.82rem; min-height:36px; padding:2px;">${g('f-components')}</div>
                    </div>
                </div>
                <div class="mt-2">
                    <span style="font-size:0.72rem; display:block; font-weight:bold;">4. Project Location:</span>
                    <div class="row gx-1 mt-1">
                        <div class="col-6 d-flex align-items-center gap-2">
                            <span style="font-size:0.65rem; width:60px; text-align:right;">Province:</span>
                            <div style="border:1px solid #000; font-size:0.82rem; min-height:20px; padding-left:2px; flex-grow:1;">${g('f-province') || g('f-provinces')}</div>
                        </div>
                        <div class="col-6 d-flex align-items-center gap-2">
                            <span style="font-size:0.65rem; width:70px; text-align:right;">Municipality:</span>
                            <div style="border:1px solid #000; font-size:0.82rem; min-height:20px; padding-left:2px; flex-grow:1;">${g('f-municipality')}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- II. PROJECT STATUS -->
            <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">II. PROJECT STATUS</div>
            <div class="ps-3">
                <div class="row g-0">
                    <div class="col-4">
                        ${renderCheckboxGroup(['Ongoing', 'Pipeline', 'Proposed'], d['project-status'])}
                    </div>
                    <div class="col-8">
                        <p class="mb-1" style="font-size:0.65rem; font-weight:bold;">Preparatory Works:</p>
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:14px; height:14px; border:1px solid #000; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">${d['prep-site'] ? '✓' : ''}</div>
                                <span style="font-size:0.65rem;">Site is readily available</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:14px; height:14px; border:1px solid #000; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">${d['prep-row'] ? '✓' : ''}</div>
                                <span style="font-size:0.65rem;">No issue on right-of-way</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:14px; height:14px; border:1px solid #000; display:inline-flex; align-items:center; justify-content:center; font-size:10px; font-weight:bold;">${d['prep-ded'] ? '✓' : ''}</div>
                                <span style="font-size:0.65rem;">Detailed Engineering Design was prepared</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- III. ENDORSEMENTS -->
            <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">III. ENDORSEMENTS</div>
            <div class="ps-3 mb-3">
                <table class="table table-bordered table-sm mb-0" style="border-color:#000 !important; font-size:0.75rem;">
                    <thead>
                        <tr class="text-center">
                            <th style="width:40%; border-color:#000 !important;">Resolution / Letter</th>
                            <th style="width:30%; border-color:#000 !important;">Reference No.</th>
                            <th style="width:30%; border-color:#000 !important;">Date of Issuance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td style="border-color:#000 !important;">Sangguniang Panlalawigan</td><td style="border-color:#000 !important;">${g('f-sp-res')}</td><td style="border-color:#000 !important;">${g('f-sp-date')}</td></tr>
                        <tr><td style="border-color:#000 !important;">Sangguniang Bayan</td><td style="border-color:#000 !important;">${g('f-sb-res')}</td><td style="border-color:#000 !important;">${g('f-sb-date')}</td></tr>
                        <tr><td style="border-color:#000 !important;">Letter Request to SP / SB</td><td style="border-color:#000 !important;">${g('f-letter-req')}</td><td style="border-color:#000 !important;">${g('f-letter-date')}</td></tr>
                        <tr><td style="border-color:#000 !important;">BOR / BOT Resolution</td><td style="border-color:#000 !important;">${g('f-bor-res')}</td><td style="border-color:#000 !important;">${g('f-bor-date')}</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- IV. PROJECT JUSTIFICATION -->
            <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">IV. PROJECT JUSTIFICATION</div>
            <div class="ps-3 mb-3">
                <div class="mb-2">
                    <span style="font-size:0.72rem; display:block; font-weight:bold;">Alignment to SDG 2030 and RDP 2023-2028:</span>
                    <div style="border:1px solid #000; font-size:0.65rem; min-height:40px; padding:2px;">${g('f-alignment')}</div>
                </div>
                <div class="mb-2">
                    <span style="font-size:0.72rem; display:block; font-weight:bold;">1. Background / Demand for the Project:</span>
                    <div style="border:1px solid #000; font-size:0.65rem; min-height:60px; padding:2px;">${g('f-background')}</div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">2. Goal:</span>
                        <div style="border:1px solid #000; font-size:0.65rem; min-height:40px; padding:2px;">${g('f-goal')}</div>
                    </div>
                    <div class="col-6">
                        <span style="font-size:0.72rem; display:block; font-weight:bold;">3. Purpose:</span>
                        <div style="border:1px solid #000; font-size:0.65rem; min-height:40px; padding:2px;">${g('f-purpose')}</div>
                    </div>
                </div>
            </div>

            <!-- VIII. LOGICAL FRAMEWORK -->
            <div style="font-weight:bold; font-size:0.85rem; margin-top:15px; margin-bottom:5px;">VIII. PROJECT LOGICAL FRAMEWORK</div>
            <div class="ps-3 mb-3">
                <table class="table table-bordered table-sm mb-0" style="border-color:#000 !important; font-size:0.75rem;">
                    <thead class="text-center">
                        <tr>
                            <th style="width:15%; border-color:#000 !important;">Hierarchy</th>
                            <th style="width:25%; border-color:#000 !important;">Narrative Summary</th>
                            <th style="width:30%; border-color:#000 !important;">Obj. Verifiable Indicators</th>
                            <th style="width:30%; border-color:#000 !important;">Means of Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="fw-bold" style="border-color:#000 !important;">Goal</td><td style="border-color:#000 !important;">${g('lf-goal-narrative')}</td><td style="border-color:#000 !important;">${g('lf-goal-indicators')}</td><td style="border-color:#000 !important;">${g('lf-goal-verification')}</td></tr>
                        <tr><td class="fw-bold" style="border-color:#000 !important;">Purpose</td><td style="border-color:#000 !important;">${g('lf-purpose-narrative')}</td><td style="border-color:#000 !important;">${g('lf-purpose-indicators')}</td><td style="border-color:#000 !important;">${g('lf-purpose-verification')}</td></tr>
                        <tr><td class="fw-bold" style="border-color:#000 !important;">Outputs</td><td style="border-color:#000 !important;">${g('lf-outputs-narrative')}</td><td style="border-color:#000 !important;">${g('lf-outputs-indicators')}</td><td style="border-color:#000 !important;">${g('lf-outputs-verification')}</td></tr>
                        <tr><td class="fw-bold" style="border-color:#000 !important;">Inputs</td><td style="border-color:#000 !important;">${g('lf-inputs-narrative')}</td><td style="border-color:#000 !important;">${g('lf-inputs-indicators')}</td><td style="border-color:#000 !important;">${g('lf-inputs-verification')}</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- IX. MAP & SIGNATURES -->
            <div class="row g-4 mt-4 mb-5">
                <div class="col-6">
                    <div style="font-weight:bold; font-size:0.85rem; margin-bottom:5px;">IX. GEOTAGGED PHOTO / MAP</div>
                    <div style="height:200px; background:#f8fafc; border:1px dashed #000; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
                        <i data-lucide="map" width="30" class="mb-1 text-muted"></i>
                        <span class="text-muted" style="font-size:0.6rem;">Satellite Map Verification Overlay</span>
                    </div>
                </div>
                <div class="col-6">
                    <div style="font-weight:bold; font-size:0.85rem; margin-bottom:5px;">X. GEOLOCATION COORDINATES</div>
                    <div class="row g-2">
                        <div class="col-12">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">Beginning:</span>
                            <div style="border:1px solid #000; font-size:0.75rem; min-height:20px; padding:2px; font-family:monospace;">${g('f-geo-start-lat')}, ${g('f-geo-start-lng')}</div>
                        </div>
                        <div class="col-12 mt-2">
                            <span style="font-size:0.72rem; display:block; font-weight:bold;">End:</span>
                            <div style="border:1px solid #000; font-size:0.75rem; min-height:20px; padding:2px; font-family:monospace;">${g('f-geo-end-lat') || 'N/A'}, ${g('f-geo-end-lng') || 'N/A'}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4">
                <div class="row gx-5">
                    <div class="col-6">
                        <div style="background:#154A9A; color:#fff; padding:3px 12px; font-size:0.75rem; font-weight:bold;">Prepared by:</div>
                        <div class="text-center mt-3">
                            ${gSig('sig-prep-data') ? `<img src="${gSig('sig-prep-data')}" style="max-height:70px; max-width:180px; margin-bottom:-10px;">` : '<div style="height:50px;"></div>'}
                            <p class="mb-0 fw-bold" style="border-bottom:2px solid #000; display:inline-block; padding:0 2rem 2px 2rem;">${g('f-prep-name').toUpperCase()}</p>
                            <p class="mb-0 small" style="font-size:0.7rem;">${g('f-prep-position')}</p>
                            <p class="mb-0 small mt-1" style="font-size:0.68rem; color:#334155;">${g('f-prep-date') || '\u2014'}</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#154A9A; color:#fff; padding:3px 12px; font-size:0.75rem; font-weight:bold;">Noted by:</div>
                        <div class="text-center mt-3">
                            ${gSig('sig-noted-data') ? `<img src="${gSig('sig-noted-data')}" style="max-height:70px; max-width:180px; margin-bottom:-10px;">` : '<div style="height:50px;"></div>'}
                            <p class="mb-0 fw-bold" style="border-bottom:2px solid #000; display:inline-block; padding:0 2rem 2px 2rem;">${g('f-noted-name').toUpperCase()}</p>
                            <p class="mb-0 small" style="font-size:0.7rem;">${g('f-noted-position')}</p>
                            <p class="mb-0 small mt-1" style="font-size:0.68rem; color:#334155;">${g('f-noted-date') || '\u2014'}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 text-end border-top" style="font-size:0.6rem; color:#64748b;">
                Page 1 of 1 — Viewed via RPTS Portals
            </div>

        </div>
    `;

    container.innerHTML = `
        <style>
            @media print {
                @page { margin: 0.5in; size: letter portrait; }
                body { background: #fff !important; margin: 0 !important; padding: 0 !important; }
                .no-print, .btn-back-dash, #sidebarMenu, .topnav-container, .navbar { display: none !important; }
                #cpp-view { padding: 0 !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
                #printable-cpp { 
                    padding: 0 !important; 
                    margin: 0 !important; 
                    width: 100% !important; 
                    box-shadow: none !important; 
                    border: none !important; 
                    background: #fff !important;
                    font-size: 11pt !important;
                }
                .card { border: none !important; box-shadow: none !important; }
                /* Ensure tables look clean in print */
                .table { border-collapse: collapse !important; width: 100% !important; }
                .table td, .table th { border: 1px solid #000 !important; padding: 4px 8px !important; }
                .bg-light { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
            }
        </style>
        ${summaryHtml}
    `;
    if (window.lucide) window.lucide.createIcons();
}
