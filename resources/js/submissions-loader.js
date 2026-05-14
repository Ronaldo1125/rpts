export function initSubmissionsLoader() {
    window.initSubmissionsLoader = initSubmissionsLoader;
    const getSubmissionsTbody = () => document.getElementById('submissions-tbody');
    const getManageTbody = () => document.getElementById('cipgTableBody');
    const getCountSpan = () => document.getElementById('submissions-count');
    const getManageCountSpan = () => document.getElementById('cipg-list-count');

    function _isWorkflowStatus(val) {
        return ['Draft','Submitted','Review','Approved','Validated','For Revision','Resubmitted','Revised','Incomplete','Rejected'].includes(val);
    }

    function _getSubmissionStatus(s) {
        if (s?.submissionStatus?.trim()) return s.submissionStatus;
        if (typeof s?.status === 'string' && _isWorkflowStatus(s.status)) return s.status;
        if (typeof s?.status === 'string' && s.status.trim()) return s.status;
        if (s?.projectStatus?.trim()) return s.projectStatus;
        if (s?.cppStatus?.trim()) return s.cppStatus;
        if (String(s?.id || '').startsWith('DRAFT-') || String(s?.id || '').startsWith('CPP-Draft-')) return 'Draft';
        return 'Submitted';
    }

    function _getProjectStatus(s) {
        if (s?.projectStatus?.trim()) return s.projectStatus;
        const fromForm = s?.formData?.['project-status'];
        if (typeof fromForm === 'string' && fromForm.trim()) return fromForm;
        if (typeof s?.status === 'string' && s.status.trim() && !_isWorkflowStatus(s.status)) return s.status;
        return '—';
    }

    function _getProjectStage(s, par) {
        if (s.stage) {
            const stageLower = s.stage.toLowerCase();
            if (stageLower.includes('preparation')) return `<span class="text-secondary fw-semibold" style="font-size:0.7rem;">${s.stage}</span>`;
            if (stageLower.includes('completeness')) return `<span class="text-info fw-semibold" style="font-size:0.7rem;">${s.stage}</span>`;
            if (stageLower.includes('appraisal')) return `<span class="text-indigo fw-semibold" style="font-size:0.7rem;">${s.stage}</span>`;
            if (stageLower.includes('sectoral')) return `<span class="text-primary fw-semibold" style="font-size:0.7rem;">${s.stage}</span>`;
            if (stageLower.includes('finalization')) return `<span class="text-success fw-semibold" style="font-size:0.7rem;">${s.stage}</span>`;
            return `<span class="text-dark fw-semibold" style="font-size:0.7rem;">${s.stage}</span>`;
        }

        const subStatus = _getSubmissionStatus(s);
        const parStatus = par?.status || '';

        if (subStatus === 'Draft') return '<span class="text-secondary fw-semibold" style="font-size:0.7rem;">Preparation</span>';
        
        if (['Submitted', 'Incomplete', 'Validated'].includes(subStatus)) {
            return '<span class="text-info fw-semibold" style="font-size:0.7rem;">Completeness Test and Validation</span>';
        }
        
        if (s.projectStatus === 'Sectoral Presentation' || parStatus === 'Sectoral Presentation') {
            return '<span class="text-primary fw-semibold" style="font-size:0.7rem;">Sectoral Committee</span>';
        }

        if (parStatus === 'Final' || subStatus === 'Final' || s.projectStatus === 'Technical Report Finalized') {
            return '<span class="text-success fw-semibold" style="font-size:0.7rem;">Finalization</span>';
        }

        if (['Review', 'Approved', 'For Revision', 'Resubmitted', 'Revised', 'Referred to PDIPBD'].includes(subStatus) || parStatus === 'Reviewed') {
            return '<span class="text-indigo fw-semibold" style="font-size:0.7rem;">Project Appraisal</span>';
        }

        return '<span class="text-muted" style="font-size:0.7rem;">&mdash;</span>';
    }

    let currentSectorFilter = 'all';

    window.filterSubmissionsTableBySector = function(sector) {
        currentSectorFilter = sector;
        renderTable();
    };

    async function renderTable() {
        const submissionsTbody = getSubmissionsTbody();
        const manageTbody = getManageTbody();
        const countSpan = getCountSpan();
        const manageCountSpan = getManageCountSpan();

        let submissions = await localforage.getItem('cpp_submissions');
        if (!submissions) submissions = [];

        // Fetch assessments to check for Final PARs
        const allPars = await localforage.getItem('project_assessments') || [];

        function _statusPill(text, bg, color) {
            return `<span class="badge rounded-pill fw-medium" style="background:${bg};color:${color};font-size:0.7rem;padding:0.35em 0.8em;">${text}</span>`;
        }

        function _renderWorkflowStatus(s) {
            const st = _getSubmissionStatus(s);
            if (st === 'Draft') return _statusPill('Draft', '#f1f5f9', '#0f172a');
            if (st === 'Review') return _statusPill('Review', '#e0f2fe', '#075985');
            if (st === 'Approved') return _statusPill('Approved', '#dcfce7', '#15803d');
            if (st === 'Validated') return _statusPill('Validated', '#f0fdf4', '#166534');
            if (st === 'Incomplete') return _statusPill('Incomplete', '#fff1f2', '#e11d48');
            if (st === 'Revised') return _statusPill('Revised', '#f5f3ff', '#5b21b6');
            if (st === 'For Revision') return _statusPill('For Revision', '#fff7ed', '#9a3412');
            if (st === 'Resubmitted') return _statusPill('Resubmitted', '#e0e7ff', '#3730a3');
            if (st === 'Sectoral Presentation') return _statusPill('Sectoral Presentation', '#eff6ff', '#1d4ed8');
            if (st === 'RDC Presentation') return _statusPill('RDC Presentation', '#eff6ff', '#1d4ed8');
            if (st === 'RDC Approved') return _statusPill('RDC Approved', '#dcfce7', '#15803d');
            if (st === 'Technical Report Finalized') return _statusPill('Technical Report Finalized', '#ecfdf5', '#047857');
            return _statusPill('Submitted', '#ede9fe', '#5b21b6');
        }

        const renderRow = (s, showAgency = true) => {
            const subStatus = _getSubmissionStatus(s);
            const canEdit = subStatus === 'Draft' || subStatus === 'For Revision' || subStatus === 'Incomplete';
            const hasVersions = s.versions && s.versions.length > 1;
            return `
            <tr>
                <td class="fw-medium small py-3" style="max-width:280px;">${s.title || s.formData?.['f-title'] || 'Untitled'}</td>
                ${showAgency ? `<td class="small text-muted py-3">${window.getAgencyAbbreviation ? window.getAgencyAbbreviation(s.agency || s.formData?.['f-agency']) : (s.agency || s.formData?.['f-agency'] || '\u2014')}</td>` : ''}
                <td class="py-3"><span class="badge rounded-pill fw-medium" 
                          style="background:#e8f0fe;color:#0032A6;font-size:0.7rem;padding:0.35em 0.8em;">${s.sector || s.formData?.['f-sector'] || '&mdash;'}</span>
                </td>
                <td class="py-3">${(() => {
                    const par = allPars.find(p => (p.connectedCteId === s.id || (s.cteId && p.connectedCteId === s.cteId)));
                    return _getProjectStage(s, par);
                })()}</td>
                <td class="py-3">${_renderWorkflowStatus(s)}</td>
                <td class="py-3"><span class="small text-muted">${_timeAgo(s.date)}</span></td>
                <td class="text-center py-3">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                            <i data-lucide="more-vertical" width="20"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item small view-sub" href="#" data-id="${s.id}"><i data-lucide="eye" class="me-2" width="14"></i>View</a></li>
                            ${s.pmedFeedback ? `<li><a class="dropdown-item small feedback-sub" href="#" data-id="${s.id}"><i data-lucide="message-circle" class="me-2" width="14"></i>View Feedback</a></li>` : ''}
                            ${subStatus === 'For Revision' ? `<li><a class="dropdown-item small par-feedback-sub" href="#" data-id="${s.id}"><i data-lucide="message-square" class="me-2" width="14"></i>Comments</a></li>` : ''}
                            <li><a class="dropdown-item small attachments-sub" href="#" data-id="${s.id}"><i data-lucide="paperclip" class="me-2" width="14"></i>Attachments</a></li>
                            ${(() => {
                                // 1. Try matching by Submission ID or CTE ID
                                let par = allPars.find(p => (p.connectedCteId === s.id || (s.cteId && p.connectedCteId === s.cteId)) && p.status === 'Final');
                                
                                // 2. Fallback: Match by Project Title (if status is Final and and implemented by same agency)
                                if (!par) {
                                    const sTitle = (s.title || s.formData?.['f-title'] || '').toLowerCase().trim();
                                    const sAgency = (s.agency || s.formData?.['f-agency'] || '').toLowerCase().trim();
                                    par = allPars.find(p => {
                                        const pTitle = (p.projectTitle || '').toLowerCase().trim();
                                        const pAgency = (p.proponent || '').toLowerCase().trim();
                                        return p.status === 'Final' && pTitle === sTitle && pAgency === sAgency;
                                    });
                                }

                                if (par && par.finalReportFile) {
                                    return `<li><a class="dropdown-item small download-par-sub" href="#" data-id="${par.id}"><i data-lucide="file-down" class="me-2" width="14"></i>Download PAR</a></li>`;
                                }
                                return '';
                            })()}
                            ${canEdit ? `<li><a class="dropdown-item small edit-sub" href="#" data-id="${s.id}"><i data-lucide="edit-2" class="me-2" width="14"></i>Edit</a></li>` : ''}
                            <li><a class="dropdown-item small history-sub" href="#" data-id="${s.id}">
                                <i data-lucide="clock" class="me-2" width="14"></i>Version History
                                ${hasVersions ? `<span class="badge ms-1" style="background:#154A9A;color:#fff;font-size:0.65rem;border-radius:20px;padding:0.15em 0.5em;">${s.versions.length}</span>` : ''}
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item small text-danger delete-sub" href="#" data-id="${s.id}"><i data-lucide="trash-2" class="me-2" width="14"></i>Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            `;
        };

        const renderAdminRow = (s) => {
            const hasVersions = s.versions && s.versions.length > 1;
            const title = s.title || s.formData?.['f-title'] || 'Untitled';
            const agency = s.agency || s.formData?.['f-agency'] || '—';
            const abbr = window.getAgencyAbbreviation ? window.getAgencyAbbreviation(agency) : agency;
            const sub = s.formData?.['f-sub-sector'] || '—';

            return `
            <tr>
                <td class="fw-medium small py-3 ps-4" style="max-width:300px;">${title}</td>
                <td class="small text-muted py-3">${abbr}</td>
                <td class="small text-muted py-3">${sub}</td>
                <td class="text-center pe-4 py-3">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                            <i data-lucide="more-vertical" width="20"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size:0.8rem;">
                            <li><a class="dropdown-item view-sub d-flex align-items-center gap-2 py-2" href="#" data-id="${s.id}">
                                <i data-lucide="eye" width="15"></i>View Form</a>
                            </li>
                            <li><a class="dropdown-item attachments-sub d-flex align-items-center gap-2 py-2" href="#" data-id="${s.id}">
                                <i data-lucide="paperclip" width="15"></i>Attachments</a>
                            </li>
                            <li><a class="dropdown-item history-sub d-flex align-items-center gap-2 py-2" href="#" data-id="${s.id}">
                                <i data-lucide="clock" width="15"></i>Version History 
                                ${hasVersions ? `<span class="badge rounded-pill ms-1" style="background:#154A9A;color:#fff;font-size:0.6rem;">${s.versions.length}</span>` : ''}</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            `;
        };

        if (submissionsTbody) {
            // Attach Feedback listener (delegated)
            submissionsTbody.addEventListener('click', async e => {
                const btn = e.target.closest('.feedback-sub');
                if (!btn) return;
                e.preventDefault();
                const id = btn.dataset.id;
                const subs = await localforage.getItem('cpp_submissions') || [];
                const s = subs.find(x => x.id === id);
                if (s && s.pmedFeedback) {
                    _showFeedbackModal(s.title || 'Submission', s.pmedFeedback);
                }
            });

            if (submissions.length === 0) {
                submissionsTbody.innerHTML = `<tr><td colspan="7" class="text-center py-5"><p class="text-muted mb-0">No submissions found</p></td></tr>`;
            } else {
                submissionsTbody.innerHTML = submissions.map(s => renderRow(s, false)).join('');
                if (countSpan) countSpan.textContent = `Showing 1 to ${submissions.length} of ${submissions.length} entries`;
            }
        }

        if (manageTbody) {
            let submittedOnly = submissions.filter(s => _getSubmissionStatus(s) !== 'Draft');
            console.log(`[SubmissionsLoader] Total: ${submissions.length}, Sub: ${submittedOnly.length}, Filter: ${currentSectorFilter}`);
            
            if (currentSectorFilter !== 'all') {
                submittedOnly = submittedOnly.filter(s => {
                    const sec = (s.sector || s.formData?.['f-sector'] || '').toLowerCase();
                    if (currentSectorFilter === 'social' && sec.includes('social')) return true;
                    if (currentSectorFilter === 'economic' && sec.includes('economic')) return true;
                    if (currentSectorFilter === 'infra' && (sec.includes('infra') || sec.includes('physical'))) return true;
                    if (currentSectorFilter === 'insti' && (sec.includes('insti') || sec.includes('devt') || sec.includes('admin'))) return true;
                    return false;
                });
            }
            console.log(`[SubmissionsLoader] After Filter: ${submittedOnly.length}`);

            if (submittedOnly.length === 0) {
                manageTbody.innerHTML = `<tr><td colspan="4" class="text-center py-5"><p class="text-muted mb-0">No submissions found</p></td></tr>`;
                if (manageCountSpan) manageCountSpan.textContent = '0';
            } else {
                manageTbody.innerHTML = submittedOnly.map(s => renderAdminRow(s)).join('');
                if (manageCountSpan) manageCountSpan.textContent = submittedOnly.length;
            }
        }

        if (window.lucide) window.lucide.createIcons();
        
        // Re-initialize Bootstrap dropdowns to prevent overflow clip
        if (window.bootstrap?.Dropdown) {
            document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                new window.bootstrap.Dropdown(el, {
                    popperConfig: { strategy: 'fixed' }
                });
            });
        }

        _attachEventListeners();
    }

    function _attachEventListeners() {
        document.querySelectorAll('.view-sub').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                sessionStorage.setItem('cpp_view_id', id);
                if (window.switchPage) window.switchPage('cpp-form');
            });
        });

        document.querySelectorAll('.edit-sub').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                sessionStorage.removeItem('cpp_view_id');
                sessionStorage.setItem('cpp_edit_id', id);
                if (window.switchPage) window.switchPage('cpp-form');
            });
        });

        document.querySelectorAll('.attachments-sub').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                const all = await localforage.getItem('cpp_submissions') || [];
                const sub = all.find(s => s.id === id);
                if (!sub) return;

                const listEl = document.getElementById('attachments-modal-list');
                const titleEl = document.getElementById('attachments-modal-project-title');
                if (titleEl) titleEl.textContent = sub.title || sub.formData?.['f-title'] || 'Untitled';
                if (listEl) {
                    listEl.innerHTML = '<p class="small text-muted py-3">Looking for attachments...</p>';
                    // Simplified logic for brevity:
                    const attachments = Array.isArray(sub.formData?.['_attachments']) ? sub.formData['_attachments'] : [];
                    if (attachments.length === 0) {
                        listEl.innerHTML = '<div class="text-center py-4 text-muted small">No attachments found</div>';
                    } else {
                        listEl.innerHTML = attachments.map(a => `
                            <div class="p-3 border rounded-3 mb-2 d-flex justify-content-between align-items-center">
                                <div><span class="small fw-bold d-block">${a.desc || 'File'}</span><span class="x-small text-muted">${a.fileName || 'unknown'}</span></div>
                                <a href="${a.data}" download="${a.fileName}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Download</a>
                            </div>
                        `).join('');
                    }
                }
                const modalEl = document.getElementById('attachmentsModal');
                if (modalEl && window.bootstrap?.Modal) {
                    new window.bootstrap.Modal(modalEl).show();
                }
            });
        });

        document.querySelectorAll('.par-feedback-sub').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const subId = btn.dataset.id;
                
                // 1. Get the submission for context
                const allSubs = await localforage.getItem('cpp_submissions') || [];
                const sub = allSubs.find(s => s.id === subId);
                const subTitle = (sub?.title || sub?.formData?.['f-title'] || '').toLowerCase().trim();

                // 2. Find the referral — by submissionId first, then by project title
                const referrals = await localforage.getItem('project_referrals') || [];
                let ref = referrals.find(r => r.submissionId === subId);
                if (!ref && subTitle) {
                    ref = referrals.find(r => (r.projectTitle || '').toLowerCase().trim() === subTitle);
                }

                // 3. Find submitted comments — by cteId from referral, or directly by connectedCteId === subId
                const allCR = await localforage.getItem('comments_recommendations') || [];
                let cr = null;
                if (ref?.cteId) {
                    cr = allCR.find(x => x.connectedCteId === ref.cteId && x.status === 'Submitted');
                }
                if (!cr) {
                    cr = allCR.find(x => (x.connectedCteId === subId || x.connectedParId) && x.status === 'Submitted');
                }
                
                if (!cr || !cr.formData?.projects) {
                    if (window.showSimpleAlert) window.showSimpleAlert('Detailed feedback records are pending or unavailable.', 'info');
                    return;
                }

                // 4. Populate modal
                const parFeedbackModal = document.getElementById('parFeedbackModal');
                const container = document.getElementById('par-feedback-list-container');
                const titleEl = document.getElementById('par-feedback-modal-project-title');
                const projects = cr.formData.projects;
                
                if (titleEl) titleEl.textContent = projects[0]?.projectTitle || sub?.title || 'Project Submission';
                
                if (container) {
                    container.innerHTML = projects.map(p => {
                        if (!p.findingsList || p.findingsList.length === 0) return '<p class="text-muted small">No specific findings listed.</p>';
                        return p.findingsList.map((f, idx) => `
                            <div class="p-3 border rounded-4 bg-light bg-opacity-50 mb-2">
                                <div class="row align-items-start">
                                    <div class="col-md-6 border-end">
                                        <div class="text-uppercase fw-bold text-secondary mb-2" style="font-size:0.65rem; letter-spacing:0.05em;">Secretariat Findings</div>
                                        <div class="text-dark small lh-base">${f.findings || '—'}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-uppercase fw-bold text-secondary mb-2" style="font-size:0.65rem; letter-spacing:0.05em;">Recommendations</div>
                                        <div class="text-dark small lh-base">${f.recommendations || '—'}</div>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }).join('');
                }

                const editBtn = document.querySelector('.edit-sub-from-feedback');
                if (editBtn) {
                    editBtn.onclick = () => {
                        sessionStorage.removeItem('cpp_view_id');
                        sessionStorage.setItem('cpp_edit_id', subId);
                        bootstrap.Modal.getInstance(parFeedbackModal)?.hide();
                        if (window.switchPage) window.switchPage('cpp-form');
                    };
                }

                if (parFeedbackModal && window.bootstrap?.Modal) {
                    new window.bootstrap.Modal(parFeedbackModal).show();
                    if (window.lucide) window.lucide.createIcons({ target: parFeedbackModal });
                }
            });
        });

        document.querySelectorAll('.download-par-sub').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const parId = btn.dataset.id;
                const allPars = await localforage.getItem('project_assessments') || [];
                const par = allPars.find(p => p.id === parId);
                
                if (par && par.finalReportFile) {
                    const file = par.finalReportFile;
                    if (window.showSimpleAlert) {
                        window.showSimpleAlert(`Starting download: ${file.name}`, 'info');
                    }
                    console.log('[DownloadPAR] File:', file);
                    // Actual file data would be in file.data (Base64)
                    if (file.data) {
                        const link = document.createElement('a');
                        link.href = file.data;
                        link.download = file.name;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                } else {
                    if (window.showSimpleAlert) window.showSimpleAlert('Final PAR document not found.', 'error');
                }
            });
        });

        document.querySelectorAll('.history-sub').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                const all = await localforage.getItem('cpp_submissions') || [];
                const sub = all.find(s => s.id === id);
                if (!sub) return;
                const histEl = document.getElementById('version-history-list');
                const titleEl = document.getElementById('version-modal-project-title');
                if (titleEl) titleEl.textContent = sub.title || sub.formData?.['f-title'] || 'Untitled';
                if (histEl) {
                    const versions = sub.versions || [];
                    if (versions.length === 0) histEl.innerHTML = '<p class="text-muted text-center py-3">No history available</p>';
                    else {
                        histEl.innerHTML = versions.map((v, i) => `
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div><span class="fw-bold small">Version ${i+1}</span><div class="x-small text-muted">${new Date(v.date).toLocaleString()}</div></div>
                                <button class="btn btn-sm btn-link">View</button>
                            </div>
                        `).join('');
                    }
                }
                const modalEl = document.getElementById('versionHistoryModal');
                if (modalEl && window.bootstrap?.Modal) new window.bootstrap.Modal(modalEl).show();
            });
        });

        document.querySelectorAll('.delete-sub').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const id = btn.dataset.id;
                if (!confirm('Are you sure you want to delete this submission? This action cannot be undone.')) return;

                const all = await localforage.getItem('cpp_submissions') || [];
                const filtered = all.filter(s => s.id !== id);
                await localforage.setItem('cpp_submissions', filtered);

                if (window.showSimpleAlert) window.showSimpleAlert('Submission deleted successfully.', 'success');
                initSubmissionsLoader(); // Refresh
            });
        });
    }

    function _showFeedbackModal(title, feedback) {
        const existing = document.getElementById('agency-feedback-modal');
        if (existing) {
            const old = bootstrap.Modal.getInstance(existing);
            if (old) old.dispose();
            existing.remove();
        }

        const html = `
        <div class="modal fade" id="agency-feedback-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
                    <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #e11d48 0%, #fb7185 100%); color:#fff; padding:1.75rem 2rem;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                                <i data-lucide="message-circle" width="20" style="color: #e11d48 !important;"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold mb-0" style="font-size:1.1rem; letter-spacing:-0.01em;">Technical Feedback</h5>
                                <p class="mb-0 small opacity-75" style="font-size:0.75rem;">Staff observations and instructions</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 pt-5 px-lg-5">
                        <div class="mb-5">
                            <label class="text-muted small fw-bold text-uppercase mb-2 d-block" style="font-size:0.65rem; letter-spacing:0.08em;">Project Title</label>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size:1.05rem; line-height:1.4;">${title}</h6>
                        </div>
                        
                        <div class="position-relative p-4 rounded-4" style="background: rgba(225, 29, 72, 0.03); border: 1px dashed rgba(225, 29, 72, 0.2);">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i data-lucide="file-text" width="16" style="color: #e11d48 !important;"></i>
                                <span class="text-danger small fw-bold text-uppercase" style="font-size:0.7rem; letter-spacing:0.05em;">Feedback Instruction</span>
                            </div>
                            <div class="text-dark" style="white-space:pre-wrap; font-size:0.92rem; line-height:1.7; font-weight:450;">${feedback}</div>
                        </div>
                        
                        <div class="mt-4 p-3 rounded-4 d-flex align-items-start gap-3" style="background: #fef2f2; border: 1px solid #fee2e2;">
                           <i data-lucide="info" width="18" style="color: #e11d48 !important;" class="flex-shrink-0 mt-1"></i>
                           <p class="mb-0 text-danger" style="font-size:0.8rem; line-height:1.5;">Please review the notes above carefully and update your submission accordingly to proceed with the next evaluation stage.</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 px-lg-5 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-5 py-2 fw-semibold small border" data-bs-dismiss="modal">Dismiss</button>
                    </div>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', html);
        if (window.lucide) window.lucide.createIcons();
        const modalEl = document.getElementById('agency-feedback-modal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    function _timeAgo(dateString) {
        if (!dateString) return '—';
        const date = new Date(dateString);
        const seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return 'Just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes}m ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours}h ago`;
        return date.toLocaleDateString();
    }

    const searchInput = document.getElementById('cipgSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#cipgTableBody tr');
            rows.forEach(r => r.style.display = r.textContent.toLowerCase().includes(term) ? '' : 'none');
        });
    }

    // New CPP Submission button — agency side
    const handleNewCpp = () => {
        sessionStorage.removeItem('cpp_edit_id');
        sessionStorage.removeItem('cpp_view_id');
        const cppContainer = document.getElementById('cpp-steps-container');
        if (cppContainer) {
            cppContainer.innerHTML = '';
            delete cppContainer.dataset.loaded;
        }
        if (window.switchPage) window.switchPage('cpp-form');
    };

    const newCppBtn = document.getElementById('newCppBtn');
    if (newCppBtn) {
        newCppBtn.addEventListener('click', handleNewCpp);
    }

    renderTable();
}
