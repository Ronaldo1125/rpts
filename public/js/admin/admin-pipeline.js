/**
 * admin-pipeline.js
 * Dedicated CIPG Evaluation Workflow pipeline handler for the Admin dashboard.
 * Completely separate from the Staff's toggleStepDetails.
 * Admin sees ALL submissions + has the "Refer to PDIPB Staff" action.
 */

function _getSubStatus(s) {
    const WORKFLOW = ['Draft','Submitted','Review','Approved','Validated','For Revision',
                      'Resubmitted','Revised','Incomplete','Rejected',
                      'Sectoral Presentation','RDC Presentation','RDC Approved'];
    if (s?.submissionStatus?.trim()) return s.submissionStatus;
    if (typeof s?.status === 'string' && WORKFLOW.includes(s.status)) return s.status;
    if (typeof s?.status === 'string' && s.status.trim()) return s.status;
    if (s?.projectStatus?.trim()) return s.projectStatus;
    if (String(s?.id || '').startsWith('DRAFT-') || String(s?.id || '').startsWith('CPP-Draft-')) return 'Draft';
    return 'Submitted';
}

function _statusPill(text) {
    const MAP = {
        'Submitted':    ['#e0f2fe','#075985'],
        'Review':       ['#eff6ff','#1d4ed8'],
        'Validated':    ['#f0fdf4','#166534'],
        'Approved':     ['#dcfce7','#15803d'],
        'For Revision': ['#fff7ed','#9a3412'],
        'Resubmitted':  ['#e0e7ff','#3730a3'],
        'Revised':      ['#f5f3ff','#5b21b6'],
        'Incomplete':   ['#fff1f2','#e11d48'],
        'Rejected':     ['#fee2e2','#991b1b'],
        'Sectoral Presentation': ['#eff6ff','#1d4ed8'],
        'RDC Presentation':      ['#eff6ff','#1d4ed8'],
        'RDC Approved':          ['#dcfce7','#15803d'],
        'Draft':        ['#f1f5f9','#475569'],
    };
    const [bg, color] = MAP[text] || ['#f1f5f9','#0f172a'];
    return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.7rem;padding:0.3em 0.75em;">${text}</span>`;
}

function _referBtn(s) {
    const title   = (s.title || s.formData?.['f-title'] || 'Untitled').replace(/'/g,"&#39;");
    const agency  = (s.agency || s.formData?.['f-agency'] || '—').replace(/'/g,"&#39;");
    const already = s.referredToPdipb;
    if (already) {
        return `<span class="badge rounded-pill bg-success bg-opacity-10 text-success fw-medium px-3" style="font-size:0.72rem;">
                    <i data-lucide="check" width="12" class="me-1"></i>Referred
                </span>`;
    }
    return `<button class="btn btn-sm btn-primary rounded-pill px-3 admin-refer-btn fw-semibold"
                style="font-size:0.75rem;background:#154A9A;border-color:#154A9A;"
                data-id="${s.id}" data-title="${title}" data-agency="${agency}">
                <i data-lucide="send" width="13" class="me-1"></i>Refer
            </button>`;
}

// Stage-to-filter map
const STAGE_FILTERS = {
    'submission':  s => !['Draft'].includes(_getSubStatus(s)),
    'referred':    s => !!s.referredToPdipb,
    'evaluation':  s => _getSubStatus(s) === 'Review',
    'findings':    s => _getSubStatus(s) === 'Validated',
    'revised':     s => ['For Revision','Resubmitted','Revised'].includes(_getSubStatus(s)),
    'sectoral':    s => _getSubStatus(s) === 'Sectoral Presentation',
    'rdc-pres':    s => _getSubStatus(s) === 'RDC Presentation',
    'approved':    s => ['Approved','RDC Approved'].includes(_getSubStatus(s)),
};

const STAGE_LABELS = {
    'submission':  'All Submissions',
    'referred':    'Referred to PDIPB Staff',
    'evaluation':  'Project Assessment (In Review)',
    'findings':    'Comments & Recommendations (Validated)',
    'revised':     'Revised Submissions',
    'sectoral':    'SecCom Presentation',
    'rdc-pres':    'RDC Presentation',
    'approved':    'RDC Approved',
};

let _currentAdminStage = '';

async function _renderAdminStageTable(stageId, searchTerm = '') {
    const tbody = document.getElementById('admin-pipeline-tbody');
    const countEl = document.getElementById('admin-pipeline-count');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading...</td></tr>`;

    try {
        const allSubs = await localforage.getItem('cpp_submissions') || [];
        const filter  = STAGE_FILTERS[stageId] || (() => true);
        let filtered  = allSubs.filter(filter);

        if (searchTerm.trim()) {
            const q = searchTerm.toLowerCase();
            filtered = filtered.filter(s => {
                const title  = (s.title || s.formData?.['f-title'] || '').toLowerCase();
                const agency = (s.agency || s.formData?.['f-agency'] || '').toLowerCase();
                return title.includes(q) || agency.includes(q);
            });
        }

        if (countEl) countEl.textContent = filtered.length;

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted small">
                No submissions found for this stage.</td></tr>`;
            return;
        }

        tbody.innerHTML = filtered.map(s => {
            const title  = s.title  || s.formData?.['f-title']  || 'Untitled';
            const agency = s.agency || s.formData?.['f-agency'] || '—';
            const status = _getSubStatus(s);
            const date   = s.submittedAt || s.updatedAt || s.createdAt || '';
            const dateStr = date ? new Date(date).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
            return `<tr>
                <td class="ps-4 fw-medium text-dark small">${title}</td>
                <td class="small text-muted">${agency}</td>
                <td>${_statusPill(status)}</td>
                <td class="small text-muted">${dateStr}</td>
                <td class="text-center pe-4">${_referBtn(s)}</td>
            </tr>`;
        }).join('');

        if (window.lucide) window.lucide.createIcons({ target: tbody });

        // Wire Refer buttons
        tbody.querySelectorAll('.admin-refer-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id     = btn.dataset.id;
                const title  = btn.dataset.title;
                const agency = btn.dataset.agency;
                // Populate the existing referPdipbModal
                const titleEl  = document.getElementById('referPdipb-project-title');
                const agencyEl = document.getElementById('referPdipb-agency');
                const hiddenId = document.getElementById('referPdipb-submission-id');
                if (titleEl)  titleEl.textContent  = title;
                if (agencyEl) agencyEl.textContent = agency;
                if (hiddenId) hiddenId.value = id;
                // Populate staff list
                if (typeof window.populateReferStaffList === 'function') {
                    window.populateReferStaffList();
                }
                const modal = document.getElementById('referPdipbModal');
                if (modal && window.bootstrap?.Modal) {
                    new window.bootstrap.Modal(modal).show();
                }
            });
        });

    } catch (err) {
        console.error('[AdminPipeline] Table error:', err);
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Error loading data.</td></tr>`;
    }
}

/**
 * Main toggle — called from onclick="toggleAdminStepDetails(event, 'submission')" etc.
 * Completely separate from the staff's toggleStepDetails.
 */
window.toggleAdminStepDetails = async function(event, stageId) {
    const panel    = document.getElementById('admin-workflow-details-panel');
    const innerEl  = document.getElementById('admin-workflow-details-inner');
    if (!panel || !innerEl) return;

    // Highlight active step
    document.querySelectorAll('#admin-pipeline-steps .process-step').forEach(s => s.classList.remove('active'));
    const stepEl = event?.currentTarget || document.querySelector(`#admin-pipeline-steps .process-step[data-stage="${stageId}"]`);
    if (stepEl) stepEl.classList.add('active');

    // If same stage is already open — close it (toggle behaviour)
    if (_currentAdminStage === stageId && panel.classList.contains('show')) {
        panel.classList.remove('show');
        _currentAdminStage = '';
        return;
    }
    _currentAdminStage = stageId;

    // Render panel
    innerEl.innerHTML = `
        <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <span class="p-1 px-2 rounded bg-primary text-white" style="font-size:0.7rem;">STAGE</span>
                ${STAGE_LABELS[stageId] || stageId}
            </h5>
            <div class="input-group input-group-sm" style="max-width:220px;">
                <span class="input-group-text bg-light border-0"><i data-lucide="search" width="13" class="text-muted"></i></span>
                <input type="text" class="form-control bg-light border-0 rounded-end-pill"
                    id="admin-pipeline-search" placeholder="Search submissions...">
            </div>
        </div>

        <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
            <div class="d-flex align-items-center justify-content-between px-4 pt-3 pb-2 border-bottom" style="background:rgba(21,74,154,0.02);">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2 small">
                    <i data-lucide="list" width="15" class="text-primary"></i>
                    Submissions
                    <span id="admin-pipeline-count" class="badge bg-primary bg-opacity-10 text-primary fw-bold ms-1" style="font-size:0.7rem;">—</span>
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.84rem;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th class="small text-secondary fw-semibold ps-4" style="width:35%;">Project Title</th>
                            <th class="small text-secondary fw-semibold" style="width:25%;">Agency</th>
                            <th class="small text-secondary fw-semibold" style="width:15%;">Status</th>
                            <th class="small text-secondary fw-semibold" style="width:13%;">Date</th>
                            <th class="small text-secondary fw-semibold text-center pe-4" style="width:12%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="admin-pipeline-tbody">
                        <tr><td colspan="5" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>Loading...
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>`;

    panel.classList.add('show');
    if (window.lucide) window.lucide.createIcons({ target: innerEl });

    // Load data
    await _renderAdminStageTable(stageId);

    // Wire search
    const searchInput = document.getElementById('admin-pipeline-search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            _renderAdminStageTable(stageId, e.target.value);
        });
    }
};
