import { showSimpleAlert, showConfirmModal } from '../common/ui-utils.js';

const STORAGE_KEY_OLD = 'commentsRecommendations';
const STORAGE_KEY_NEW = 'comments_recommendations';

function _normalizeComment(raw) {
    if (!raw || !raw.id) return null;

    const batchTitle = raw.batchTitle || raw.formData?.batchTitle || '';
    const createdAt = raw.createdAt || raw.datePrepared || '';
    const agencyResponse = raw.agencyResponse || '';
    const responseUpdatedAt = raw.responseUpdatedAt || raw.responseUpdatedAt || '';

    const rawProjects = raw.projects || raw.formData?.projects || [];
    const projects = Array.isArray(rawProjects) ? rawProjects.map(p => {
        const projectName = p.projectName || p.projectTitle || '';
        const findingsArray = [];

        if (Array.isArray(p.findings)) {
            p.findings.forEach(f => {
                if (!f) return;
                findingsArray.push({
                    finding: f.finding || f.findings || '',
                    recommendation: f.recommendation || f.recommendations || '',
                    severity: f.severity || 'Medium',
                    response: f.response || ''
                });
            });
        } else if (Array.isArray(p.findingsList)) {
            p.findingsList.forEach(f => {
                if (!f) return;
                findingsArray.push({
                    finding: f.findings || '',
                    recommendation: f.recommendations || '',
                    severity: f.severity || 'Medium',
                    response: f.response || ''
                });
            });
        }

        return { projectName, findings: findingsArray };
    }) : [];

    return {
        id: raw.id,
        batchTitle,
        createdAt,
        agencyResponse,
        responseUpdatedAt,
        projects
    };
}

async function _getAllComments() {
    const oldComments = await localforage.getItem(STORAGE_KEY_OLD) || [];
    const newComments = await localforage.getItem(STORAGE_KEY_NEW) || [];

    // Merge and avoid duplicates (new format overrides old when IDs collide)
    const map = new Map();
    oldComments.forEach(item => { if (item && item.id) map.set(item.id, item); });
    newComments.forEach(item => { if (item && item.id) map.set(item.id, item); });

    return Array.from(map.values()).map(_normalizeComment).filter(Boolean);
}

export async function initAgencyCommentsLoader() {
    await renderTable();
    _attachEventHandlers();
}

async function renderTable() {
    const tbody = document.getElementById('agencyCommentsTableBody');
    if (!tbody) return;

    try {
        const comments = await _getAllComments();
        const agencyName = (sessionStorage.getItem('agencyName') || '').trim();
        const useAgencyFilter = agencyName.length > 0;
        
        // Filter comments that are relevant to this agency (if agency is set)
        const agencyComments = comments.filter(comment => {
            if (!useAgencyFilter) return true;
            return comment.projects && comment.projects.some(project => 
                project.projectName && project.projectName.toLowerCase().includes(agencyName.toLowerCase())
            );
        });

        const countEl = document.getElementById('agencyCommentCount');

        if (agencyComments.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i data-lucide="inbox" width="48" class="mb-2"></i>
                        <div>No comments and recommendations found</div>
                        <small class="text-muted">Check back later for RDC feedback on your submissions</small>
                    </td>
                </tr>
            `;
            if (countEl) countEl.textContent = 'Showing 0 to 0 of 0 entries';
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        tbody.innerHTML = agencyComments.map(comment => {
            const totalFindings = comment.projects.reduce((sum, project) => 
                sum + (project.findings ? project.findings.length : 0), 0
            );
            
            const hasResponse = comment.agencyResponse && comment.agencyResponse.trim() !== '';
            const statusBadge = hasResponse 
                ? '<span class="status-badge status-responded">Responded</span>'
                : '<span class="status-badge status-pending">Pending Response</span>';
            
            const dateReceived = new Date(comment.createdAt || Date.now()).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

            return `
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold text-dark mb-0">${comment.batchTitle || 'Untitled Document'}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border fw-normal">${comment.projects ? comment.projects.length : 0} projects</span>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-medium text-secondary">${totalFindings} Findings</div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="small text-dark fw-medium">${dateReceived}</div>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-primary action-btn view-comments-btn px-3" data-comment-id="${comment.id}">
                            <i data-lucide="eye" width="14" class="me-1"></i> View Feedback
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        if (countEl) countEl.textContent = `Showing 1 to ${agencyComments.length} of ${agencyComments.length} entries`;
        if (window.lucide) window.lucide.createIcons();
    } catch (error) {
        console.error('Error loading agency comments:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger py-4">
                    <i data-lucide="alert-circle" width="48" class="mb-2"></i>
                    <div>Error loading comments</div>
                    <small class="text-muted">Please try refreshing the page</small>
                </td>
            </tr>
        `;
        if (window.lucide) window.lucide.createIcons();
    }
}

function _attachEventHandlers() {
    // View comments buttons
    document.addEventListener('click', (e) => {
        if (e.target.closest('.view-comments-btn')) {
            const btn = e.target.closest('.view-comments-btn');
            const commentId = btn.getAttribute('data-comment-id');
            _showCommentsModal(commentId);
        }
    });

    // Save agency response button
    const btnSaveResponse = document.getElementById('btnSaveAgencyResponse');
    if (btnSaveResponse) {
        btnSaveResponse.onclick = async () => {
            await _saveAgencyResponse();
        };
    }

    // Search and filter handlers
    const searchInput = document.getElementById('searchTitle');
    const statusFilter = document.getElementById('filterStatus');
    const dateFilter = document.getElementById('filterDate');
    const searchBtn = document.getElementById('agencyCommentsSearchBtn');

    if (searchInput) searchInput.addEventListener('input', _applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', _applyFilters);
    if (dateFilter) dateFilter.addEventListener('change', _applyFilters);
    if (searchBtn) searchBtn.addEventListener('click', _applyFilters);
}

async function _showCommentsModal(commentId) {
    try {
        const comments = await _getAllComments();
        const comment = comments.find(c => c.id === commentId);
        
        if (!comment) {
            showSimpleAlert('Comment not found', 'error');
            return;
        }

        // Populate modal header information
        document.getElementById('modalDocTitle').textContent = comment.batchTitle || 'Untitled Document';
        document.getElementById('modalDateReceived').textContent = new Date(comment.createdAt || Date.now()).toLocaleDateString();
        
        const hasResponse = comment.agencyResponse && comment.agencyResponse.trim() !== '';
        const statusElement = document.getElementById('modalStatus');
        statusElement.innerHTML = hasResponse 
            ? '<span class="badge status-responded">Responded</span>'
            : '<span class="badge status-pending">Pending Response</span>';

        const lastUpdated = document.getElementById('responseLastUpdated');
        if (comment.responseUpdatedAt) {
            lastUpdated.textContent = new Date(comment.responseUpdatedAt).toLocaleString();
        } else {
            lastUpdated.textContent = 'Not yet responded';
        }

        // Populate projects and findings
        const container = document.getElementById('projectsFindingsContainer');
        container.innerHTML = comment.projects.map((project, index) => {
            const collapseId = `projectCollapse${index}`;
            return `
            <div class="card border mb-3 shadow-none project-response-group" data-project-index="${index}">
                <div class="card-header bg-light py-2 px-3 d-flex align-items-center justify-content-between" 
                    style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">
                        <i data-lucide="layers" width="14" class="me-2 text-primary"></i>
                        PROJECT ${index + 1}: ${project.projectName || 'Unnamed Project'}
                    </h6>
                    <i data-lucide="chevron-down" width="16" class="text-secondary"></i>
                </div>
                <div id="${collapseId}" class="collapse show">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle mb-0" style="font-size: 0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 py-2 text-secondary" style="width: 30%; font-weight: 600;">Findings</th>
                                        <th class="py-2 text-secondary" style="width: 30%; font-weight: 600;">Recommendations</th>
                                        <th class="py-2 text-secondary" style="width: 40%; font-weight: 600;">Agency Response <span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${project.findings && project.findings.length > 0 ? project.findings.map((finding, fIndex) => `
                                        <tr class="finding-response-row" data-finding-index="${fIndex}">
                                            <td class="ps-3 py-3 text-dark align-top border-end">
                                                <div class="fw-bold small mb-1">Finding ${fIndex + 1}:</div>
                                                <div class="small text-muted mb-2">${finding.finding || 'N/A'}</div>
                                            </td>
                                            <td class="py-3 px-3 text-secondary align-top border-end">
                                                <div class="fw-bold small mb-1">Recommendation:</div>
                                                <div class="small p-2 border-start border-2 border-info bg-light-subtle rounded">${finding.recommendation || 'N/A'}</div>
                                            </td>
                                            <td class="pe-3 py-3 align-top">
                                                <textarea class="form-control form-control-sm finding-response-input" rows="4" 
                                                    placeholder="Enter specific response to this finding...">${finding.response || ''}</textarea>
                                            </td>
                                        </tr>
                                    `).join('') : `
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted small">No findings for this project</td>
                                        </tr>
                                    `}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `; }).join('');

        // Store comment ID for saving response
        document.getElementById('btnSaveAgencyResponse').setAttribute('data-comment-id', commentId);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('viewCommentsModal'));
        modal.show();

        if (window.lucide) window.lucide.createIcons();
    } catch (error) {
        console.error('Error showing comments modal:', error);
        showSimpleAlert('Error loading comments', 'error');
    }
}

async function _saveAgencyResponse() {
    try {
        const btnSave = document.getElementById('btnSaveAgencyResponse');
        const commentId = btnSave.getAttribute('data-comment-id');

        // Collect individual responses
        const projectGroups = document.querySelectorAll('.project-response-group');
        const responsesByProject = Array.from(projectGroups).map(group => {
            const rowElements = group.querySelectorAll('.finding-response-row');
            return Array.from(rowElements).map(row => row.querySelector('.finding-response-input').value.trim());
        });

        // Validation: at least some response text is expected
        const flatResponses = responsesByProject.flat();
        if (flatResponses.every(r => !r)) {
            showSimpleAlert('Please enter responses to the findings.', 'warning');
            return;
        }

        const now = new Date().toISOString();
        const responder = sessionStorage.getItem('agencyName') || 'Agency User';

        const [oldComments, newComments] = await Promise.all([
            localforage.getItem(STORAGE_KEY_OLD) || [],
            localforage.getItem(STORAGE_KEY_NEW) || []
        ]);

        let found = false;
        const processComment = (c) => {
            if (c.id !== commentId) return c;
            found = true;

            // Updated project findings with responses
            const updatedProjects = (c.projects || c.formData?.projects || []).map((p, pIdx) => {
                const projectResponses = responsesByProject[pIdx] || [];
                const rawFindings = p.findings || p.findingsList || [];
                const updatedFindings = rawFindings.map((f, fIdx) => ({
                    ...f,
                    response: projectResponses[fIdx] || ''
                }));
                
                // Keep structural compatibility
                if (p.findings) p.findings = updatedFindings;
                if (p.findingsList) p.findingsList = updatedFindings;
                return p;
            });

            const updated = {
                ...c,
                responseUpdatedAt: now,
                respondedBy: responder
            };
            if (updated.formData) updated.formData.projects = updatedProjects;
            else updated.projects = updatedProjects;

            return updated;
        };

        const updatedOld = oldComments.map(processComment);
        const updatedNew = newComments.map(processComment);

        if (!found) {
            // If the comment record is missing, create a minimal record to persist the response.
            const minimal = {
                id: commentId,
                agencyResponse: responseText,
                responseUpdatedAt: now,
                respondedBy: responder,
                createdAt: now,
                batchTitle: 'Untitled Document',
                projects: []
            };
            updatedNew.unshift(minimal);
        }

        await Promise.all([
            localforage.setItem(STORAGE_KEY_OLD, updatedOld),
            localforage.setItem(STORAGE_KEY_NEW, updatedNew)
        ]);

        // Update UI
        document.getElementById('responseLastUpdated').textContent = new Date().toLocaleString();
        document.getElementById('modalStatus').innerHTML = '<span class="badge status-responded">Responded</span>';

        showSimpleAlert('Response saved successfully', 'success');
        
        // Refresh the table
        await renderTable();

        // Close modal after a short delay
        setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('viewCommentsModal')).hide();
        }, 1500);

    } catch (error) {
        console.error('Error saving agency response:', error);
        showSimpleAlert('Error saving response', 'error');
    }
}

async function _applyFilters() {
    const searchTerm = document.getElementById('searchTitle')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    const dateFilter = document.getElementById('filterDate')?.value || '';

    try {
        const comments = await _getAllComments();
        const agencyName = (sessionStorage.getItem('agencyName') || '').trim();
        const useAgencyFilter = agencyName.length > 0;
        
        let filteredComments = comments.filter(comment => {
            if (!useAgencyFilter) return true;
            return comment.projects && comment.projects.some(project => 
                project.projectName && project.projectName.toLowerCase().includes(agencyName.toLowerCase())
            );
        });

        // Apply title filter
        if (searchTerm) {
            filteredComments = filteredComments.filter(comment =>
                comment.batchTitle && comment.batchTitle.toLowerCase().includes(searchTerm)
            );
        }

        // Apply status filter
        if (statusFilter) {
            filteredComments = filteredComments.filter(comment => {
                const hasResponse = comment.agencyResponse && comment.agencyResponse.trim() !== '';
                return statusFilter === 'responded' ? hasResponse : !hasResponse;
            });
        }

        // Apply date filter
        if (dateFilter) {
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            
            filteredComments = filteredComments.filter(comment => {
                const commentDate = new Date(comment.createdAt || Date.now());
                
                switch(dateFilter) {
                    case 'today':
                        return commentDate >= today;
                    case 'week':
                        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                        return commentDate >= weekAgo;
                    case 'month':
                        const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                        return commentDate >= monthAgo;
                    default:
                        return true;
                }
            });
        }

        // Update table with filtered results
        const tbody = document.getElementById('agencyCommentsTableBody');
        const countEl = document.getElementById('agencyCommentCount');

        if (filteredComments.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i data-lucide="search" width="48" class="mb-2"></i>
                        <div>No comments match your filters</div>
                        <small class="text-muted">Try adjusting your search criteria</small>
                    </td>
                </tr>
            `;
            if (countEl) countEl.textContent = 'Showing 0 to 0 of 0 entries';
        } else {
            // Re-render with filtered data (reuse existing render logic)
            tbody.innerHTML = filteredComments.map(comment => {
                const totalFindings = comment.projects.reduce((sum, project) => 
                    sum + (project.findings ? project.findings.length : 0), 0
                );
                
                const hasResponse = comment.agencyResponse && comment.agencyResponse.trim() !== '';
                const statusBadge = hasResponse 
                    ? '<span class="status-badge status-responded">Responded</span>'
                    : '<span class="status-badge status-pending">Pending Response</span>';
                
                const dateReceived = new Date(comment.createdAt || Date.now()).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                return `
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark mb-0">${comment.batchTitle || 'Untitled Document'}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-secondary border fw-normal">${comment.projects ? comment.projects.length : 0} projects</span>
                            </div>
                        </td>
                        <td>
                            <div class="small fw-medium text-secondary">${totalFindings} Findings</div>
                        </td>
                        <td>${statusBadge}</td>
                        <td>
                            <div class="small text-dark fw-medium">${dateReceived}</div>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary action-btn view-comments-btn px-3" data-comment-id="${comment.id}">
                                <i data-lucide="eye" width="14" class="me-1"></i> View Feedback
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
            if (countEl) countEl.textContent = `Showing 1 to ${filteredComments.length} of ${filteredComments.length} entries`;
        }

        if (window.lucide) window.lucide.createIcons();
    } catch (error) {
        console.error('Error applying filters:', error);
    }
}
