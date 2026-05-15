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

        let submissions = [];
        try {
            const response = await fetch('/v2/cipg_submissions/fetch');
            if (response.ok) {
                submissions = await response.json();
            }
        } catch (err) {
            console.error('Failed to fetch submissions from server:', err);
        }

        // Also get any strictly local drafts
        let localDrafts = await localforage.getItem('cpp_submissions');
        if (localDrafts && Array.isArray(localDrafts)) {
            // Only keep items that are actually drafts (not yet on server)
            const trueDrafts = localDrafts.filter(s => String(s.id).startsWith('DRAFT-'));
            submissions = [...submissions, ...trueDrafts];
        }

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
                            ${(() => {
                                const attachments = Array.isArray(s.media) ? s.media.map(m => ({
                                    name: m.file_name,
                                    url: m.original_url || m.url,
                                    type: m.custom_properties?.type,
                                    label: m.custom_properties?.label
                                })) : [];
                                return `<li><a class="dropdown-item small attachments-sub" href="#" 
                                    data-id="${s.id}" 
                                    data-title="${(s.title || s.formData?.['f-title'] || 'Untitled').replace(/"/g, '&quot;')}"
                                    data-attachments='${JSON.stringify(attachments).replace(/'/g, "&apos;")}'>
                                    <i data-lucide="paperclip" class="me-2" width="14"></i>Attachments</a></li>`;
                            })()}
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
                            ${(() => {
                                const attachments = Array.isArray(s.media) ? s.media.map(m => ({
                                    name: m.file_name,
                                    url: m.original_url || m.url,
                                    type: m.custom_properties?.type,
                                    label: m.custom_properties?.label
                                })) : [];
                                return `<li><a class="dropdown-item attachments-sub d-flex align-items-center gap-2 py-2" href="#" 
                                    data-id="${s.id}"
                                    data-title="${(s.title || s.formData?.['f-title'] || 'Untitled').replace(/"/g, '&quot;')}"
                                    data-attachments='${JSON.stringify(attachments).replace(/'/g, "&apos;")}'>
                                    <i data-lucide="paperclip" width="15"></i>Attachments</a></li>`;
                            })()}
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

        // Shared Attachment Modal Handler
        const typeMap = {
            'letter': 'Letter Request Document',
            'bor': 'BOR/BOT Resolution Document',
            'sp': 'SP Resolution Document',
            'sb': 'SB Resolution Document',
            'ded': 'Detailed Engineering Design',
            'env': 'Environmental Clearance',
            'hgdg': 'HGDG Document',
            'consult': 'Public Consultation Documentation',
            'geo_photo': 'Geotagged Photo',
            'spatial_cov': 'Spatial Coverage File',
            'sig_prep': 'Signature (Prepared By)',
            'sig_noted': 'Signature (Noted By)',
            'other': 'Other Attachment'
        };

        function normalizeAttachmentType(a) {
            const rawType = (a?.type || a?.label || '').toString().trim();
            if (rawType) return rawType;
            const fileName = (a?.name || '').toString();
            const extension = fileName.includes('.') ? fileName.split('.').pop().toLowerCase() : '';
            const map = {
                pdf: 'PDF Document', doc: 'Word Document', docx: 'Word Document',
                xls: 'Excel Spreadsheet', xlsx: 'Excel Spreadsheet', csv: 'CSV File',
                jpg: 'Image', jpeg: 'Image', png: 'Image', gif: 'Image',
                zip: 'Compressed Archive', rar: 'Compressed Archive',
            };
            return map[extension] || 'Other Attachment';
        }

        function escapeHtml(val) {
            return (val || '').toString().replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

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

    renderTable();
}
