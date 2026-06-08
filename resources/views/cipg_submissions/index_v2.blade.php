@extends('layouts.app_v2')
@section('content')
@php $isManage = $isManage ?? false; @endphp

    <section id="submissions" class="page-content active container-fluid py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-0">{{ $isManage ? 'Manage Projects for RDIP Inclusion' : 'Submit Project' }}</h2>
                <p class="text-muted small mb-0">Comprehensive Investment Programming Guide — FM-PDI-01</p>
            </div>
            @if(!$isManage)
            @can('cipg_submission-create')
                <div>
                    <a href="{{ route('v2.cipg_submissions.create') }}" class="btn text-white px-4 py-2 fw-semibold rounded-pill shadow-sm"
                        style="background-color: #154A9A; border-color: #154A9A;">
                        <i data-lucide="file-plus" class="me-2" width="16"></i> New CPP Submission
                    </a>
                </div>
            @endcan
            @endif
        </div>

        <!-- Submissions Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex flex-column" style="min-height: 300px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
                @include('partials.table-header')

                <div class="table-responsive-lg" style="border-radius: 8px; min-height: 350px;">
                    <table class="table table-hover align-middle" data-sortable="true">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-secondary">Project Title</th>
                                @if($isManage)
                                <th class="small text-secondary">Agency</th>
                                @endif
                                <th class="small text-secondary">Sector</th>
                                @if(!$isManage)
                                <th class="small text-secondary">Sub-Sector</th>
                                @endif
                                <th class="small text-secondary">Stage</th>
                                <th class="small text-secondary">Status of Submission</th>
                                <th class="small text-secondary">Submitted</th>
                                <th class="small text-secondary" data-sort-skip="true">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="submissions-tbody">
                            @forelse($submissions as $submission)
                            @php
                                $status = $submission->status ?? 'Submitted';
                                $statusStyles = [
                                    'Draft'        => ['bg' => '#f1f5f9', 'color' => '#0f172a'],
                                    'Submitted'    => ['bg' => '#ede9fe', 'color' => '#5b21b6'],
                                    'Review'       => ['bg' => '#e0f2fe', 'color' => '#075985'],
                                    'Approved'     => ['bg' => '#dcfce7', 'color' => '#15803d'],
                                    'Validated'    => ['bg' => '#f0fdf4', 'color' => '#166534'],
                                    'Incomplete'   => ['bg' => '#fff1f2', 'color' => '#e11d48'],
                                    'For Revision' => ['bg' => '#fff7ed', 'color' => '#9a3412'],
                                    'Revised'      => ['bg' => '#f5f3ff', 'color' => '#5b21b6'],
                                    'Resubmitted'  => ['bg' => '#e0e7ff', 'color' => '#3730a3'],
                                ];
                                $style = $statusStyles[$status] ?? ['bg' => '#ede9fe', 'color' => '#5b21b6'];
                                $canEdit = in_array($status, ['Draft', 'For Revision', 'Incomplete']);
                            @endphp
                            <tr>
                                <td class="fw-medium small py-3" style="max-width:280px;">{{ $submission->project_title }}</td>
                                @if($isManage)
                                <td class="small text-muted py-3">{{ $submission->user->agency->agency_acronym ?? '—' }}</td>
                                @endif
                                <td class="small text-muted py-3">{{ $submission->sector->sector_name ?? '—' }}</td>
                                @if(!$isManage)
                                <td class="small text-muted py-3">{{ $submission->sub_sector->subsector_name ?? '—' }}</td>
                                @endif
                                <td class="py-3">
                                    <span class="badge rounded-pill fw-medium" style="background:#e8f0fe;color:#0032A6;font-size:0.7rem;padding:0.35em 0.8em;">
                                        {{ $submission->stage ?? 'Submission' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="badge rounded-pill fw-medium"
                                          style="background:{{ $style['bg'] }};color:{{ $style['color'] }};font-size:0.7rem;padding:0.35em 0.8em;">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="small text-muted">{{ $submission->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="text-center py-3">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                            <i data-lucide="more-vertical" width="20"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li><a class="dropdown-item small" href="{{ route('v2.cipg_submissions.show', $submission->id) }}">
                                                <i data-lucide="eye" class="me-2" width="14"></i>View</a></li>
                                            @if($canEdit)
                                            @can('cipg_submission-edit')
                                            <li><a class="dropdown-item small" href="{{ route('v2.cipg_submissions.edit', $submission->id) }}">
                                                <i data-lucide="edit-2" class="me-2" width="14"></i>Edit</a></li>
                                            @endcan
                                            @endif
                                            @if($submission->stage === 'Completeness Test and Validation' && $submission->status === 'Incomplete')
                                            <li><a class="dropdown-item small feedback-sub" href="#" 
                                                data-id="{{ $submission->id }}"
                                                data-title="{{ $submission->project_title }}"
                                                data-feedback="{{ $submission->feedbacks->last()->notes ?? 'No specific instructions provided.' }}"
                                                ><i data-lucide="message-circle" class="me-2" width="14"></i>Feedback</a></li>
                                            @endif
                                            @php
                                                $submittedComments = [];
                                                if ($submission->status === 'For Revision' && $submission->comments_and_recommendations) {
                                                    $submittedComments = $submission->comments_and_recommendations->filter(function ($comment) use ($submission) {
                                                        return $comment->stage === $submission->stage && $comment->status === 'Submitted';
                                                    })->values()->toArray();
                                                }
                                            @endphp
                                            @if(count($submittedComments) > 0)
                                            <li><a class="dropdown-item small db-par-feedback-sub" href="#"
                                                   data-id="{{ $submission->id }}"
                                                   data-title="{{ e($submission->project_title) }}"
                                                   data-comments='@json($submittedComments)'>
                                                   <i data-lucide="message-square" class="me-2" width="14"></i>Comments
                                                </a>
                                            </li>
                                            @endif
                                            @php
                                                $attachments = $submission->getMedia('attachments')->map(function ($attachment) {
                                                    return [
                                                        'name' => $attachment->file_name,
                                                        'url' => $attachment->getUrl(),
                                                        'type' => $attachment->getCustomProperty('type'),
                                                        'label' => $attachment->getCustomProperty('label'),
                                                    ];
                                                })->values();
                                            @endphp
                                            <li><a class="dropdown-item small attachments-sub" href="#"
                                                data-id="{{ $submission->id }}"
                                                data-title="{{ e($submission->project_title) }}"
                                                data-attachments='@json($attachments)'>
                                                <i data-lucide="paperclip" class="me-2" width="14"></i>Attachments</a></li>
                                            @php
                                                $par = $submission->assessment_report;
                                                $parUrl = $par ? $par->getFirstMediaUrl('final_technical_reports') : null;
                                            @endphp
                                            @if($parUrl)
                                            <li>
                                                <a class="dropdown-item small" 
                                                   href="{{ $parUrl }}" 
                                                   target="_blank">
                                                    <i data-lucide="file-down" class="me-2" width="14"></i>
                                                    PAR Document
                                                </a>
                                            </li>
                                            @endif
                                            <!--<li><a class="dropdown-item small history-sub" href="#" data-id="{{ $submission->id }}">
                                                <i data-lucide="clock" class="me-2" width="14"></i>Version History
                                                <span class="badge ms-1" style="background:#154A9A;color:#fff;font-size:0.65rem;border-radius:20px;padding:0.15em 0.5em;"></span>
                                            </a></li>-->
                                            @if(!$isManage)
                                            @can('cipg_submission-delete')
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('v2.cipg_submissions.destroy', $submission->id) }}" method="POST"
                                                      onsubmit="return confirm('Delete this submission? This cannot be undone.')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="dropdown-item small text-danger">
                                                        <i data-lucide="trash-2" class="me-2" width="14"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                            @endcan
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $isManage ? 8 : 7 }}" class="text-center py-4 text-muted">
                                    <i data-lucide="inbox" class="mb-2" width="32"></i>
                                    <p class="mb-0 small">No submissions found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('partials.table-pagination', ['paginator' => $submissions])
            </div>
        </div>
    </section>

    <!-- Attachments Modal -->
    <div class="modal fade" id="attachmentsModal" tabindex="-1" aria-labelledby="attachmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
                <div class="modal-header border-0 pb-0"
                    style="background: linear-gradient(135deg, #14532d 0%, #16a34a 100%); color:#fff; padding:1.5rem 1.75rem;">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="attachmentsModalLabel">
                            <i data-lucide="paperclip" width="18" class="me-2"></i>Submitted Attachments
                        </h5>
                        <p class="mb-0 small opacity-75" id="attachments-modal-project-title" style="font-size:0.8rem;"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height:520px; overflow-y:auto; padding:1.5rem 1.75rem;">
                    <div id="attachments-modal-list">
                        <div class="text-center py-4 text-muted small">
                            <i data-lucide="paperclip" width="32" class="mb-2 opacity-30"></i>
                            <p class="mb-0">Click an Attachments item to view uploaded files.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0" style="padding:1rem 1.75rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Version History Modal -->
    <div class="modal fade" id="versionHistoryModal" tabindex="-1" aria-labelledby="versionHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
                <div class="modal-header border-0 pb-0"
                    style="background: linear-gradient(135deg, #154A9A 0%, #1e6fd9 100%); color:#fff; padding:1.5rem 1.75rem;">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="versionHistoryModalLabel">
                            <i data-lucide="clock" width="18" class="me-2"></i>Submission Version History
                        </h5>
                        <p class="mb-0 small opacity-75" id="version-modal-project-title" style="font-size:0.8rem;"></p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="version-history-list" style="max-height:420px; overflow-y:auto; padding:1.25rem 1.75rem;">
                        <!-- Injected by JS -->
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0" style="padding:1rem 1.75rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Secretariat Comments Modal -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius:18px; overflow:hidden;">
                <div class="modal-header border-0 pb-0"
                    style="background: linear-gradient(135deg, #b45309 0%, #d97706 100%); color:#fff; padding:1.5rem 1.75rem;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
                                style="width:32px;height:32px;">
                                <i data-lucide="message-square" width="16" style="color: white !important;"></i>
                            </div>
                            <h5 class="modal-title fw-bold mb-0">Comments and Recommendations</h5>
                        </div>
                        <p class="mb-0 small opacity-75" id="par-feedback-modal-project-title" style="font-size:0.8rem;">
                        </p>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:1.75rem;">
                    <div class="alert alert-warning border-0 rounded-4 d-flex gap-3 mb-4shadow-sm"
                        style="background:#fffbeb; color:#92400e;">
                        <i data-lucide="info" width="20" class="flex-shrink-0 mt-1" style="color:#92400e !important;"></i>
                        <div class="small">
                            Your submission has been reviewed by the technical staff. Please address the following findings
                            and recommendations before resubmitting.
                        </div>
                    </div>

                    <div id="par-feedback-list-container" class="d-flex flex-column gap-3">
                        <div class="p-3 border rounded-4 bg-light bg-opacity-50 mb-2">
                                <div class="row align-items-start">
                                    <div class="col-md-6 border-end">
                                        <div class="text-uppercase fw-bold text-secondary mb-2" style="font-size:0.65rem; letter-spacing:0.05em;">Secretariat Findings</div>
                                        <div class="text-dark small lh-base"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-uppercase fw-bold text-secondary mb-2" style="font-size:0.65rem; letter-spacing:0.05em;">Recommendations</div>
                                        <div class="text-dark small lh-base"></div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="modal-footer border-0" style="padding:1rem 1.75rem 1.5rem;">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                        data-bs-dismiss="modal">Close</button>
                    @can('cipg_submission-edit')
                    <button type="button"
                        class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark border-0 edit-sub-from-feedback"
                        style="background:#f59e0b;">
                        <i data-lucide="edit-3" width="14" class="me-1"></i> Edit Submission
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>

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
                            <h6 class="fw-bold mb-0 text-dark" style="font-size:1.05rem; line-height:1.4;">Title</h6>
                        </div>
                        
                        <div class="position-relative p-4 rounded-4" style="background: rgba(225, 29, 72, 0.03); border: 1px dashed rgba(225, 29, 72, 0.2);">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i data-lucide="file-text" width="16" style="color: #e11d48 !important;"></i>
                                <span class="text-danger small fw-bold text-uppercase" style="font-size:0.7rem; letter-spacing:0.05em;">Feedback Instruction</span>
                            </div>
                            <div class="text-dark" style="white-space:pre-wrap; font-size:0.92rem; line-height:1.7; font-weight:450;">Feedback</div>
                        </div>
                        
                        <div class="mt-4 p-3 rounded-4 d-flex align-items-start gap-3" style="background: #fef2f2; border: 1px solid #fee2e2;">
                           <i data-lucide="info" width="18" style="color: #e11d48 !important;" class="flex-shrink-0 mt-1"></i>
                           <p class="mb-0 text-danger" style="font-size:0.8rem; line-height:1.5;">Please review the notes above carefully and update your submission accordingly to proceed with the next evaluation stage.</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 px-lg-5 pt-0 d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-semibold small border" data-bs-dismiss="modal">Dismiss</button>
                        @can('cipg_submission-edit')
                        <button type="button" class="btn btn-warning rounded-pill px-4 py-2 fw-bold border-0 edit-sub-from-agency-feedback shadow-sm"
                                style="background:#e11d48; color: #fff; font-size:0.85rem;">
                            <i data-lucide="edit-3" width="14" class="me-1"></i> Edit Submission
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const attachmentsModalEl = document.getElementById('attachmentsModal');
    const attachmentsListEl = document.getElementById('attachments-modal-list');
    const attachmentsTitleEl = document.getElementById('attachments-modal-project-title');

    function normalizeAttachmentType(attachment) {
        const rawType = (attachment?.type || attachment?.label || '').toString().trim();
        if (rawType) return rawType;

        const fileName = (attachment?.name || '').toString();
        const extension = fileName.includes('.') ? fileName.split('.').pop().toLowerCase() : '';
        const map = {
            pdf: 'PDF Document',
            doc: 'Word Document',
            docx: 'Word Document',
            xls: 'Excel Spreadsheet',
            xlsx: 'Excel Spreadsheet',
            csv: 'CSV File',
            jpg: 'Image',
            jpeg: 'Image',
            png: 'Image',
            gif: 'Image',
            zip: 'Compressed Archive',
            rar: 'Compressed Archive',
        };

        return map[extension] || 'Other Attachment';
    }

    function escapeHtml(value) {
        return (value || '')
            .toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function safeAttachmentUrl(value) {
        const url = (value || '').toString().trim();
        if (!url) return '#';
        if (/^(https?:)?\/\//i.test(url) || url.startsWith('/')) {
            return url;
        }
        return '#';
    }

    function renderAttachmentsModal(title, attachments) {
        if (attachmentsTitleEl) {
            attachmentsTitleEl.textContent = title || 'Untitled Project';
        }

        if (!attachmentsListEl) return;

        if (!Array.isArray(attachments) || attachments.length === 0) {
            attachmentsListEl.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <div class="mb-2"><i data-lucide="inbox" width="32"></i></div>
                    <p class="mb-0 small">No attachments for this submission.</p>
                </div>
            `;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        attachmentsListEl.innerHTML = `
            <div class="d-flex flex-column gap-2">
                ${attachments.map((attachment) => {
                    const attachmentLabel = (attachment?.label || '').toString().trim();
                    const rawType = (attachment?.type || '').toString().trim();
                    
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

                    const displayLabel = typeMap[rawType] || attachmentLabel || normalizeAttachmentType(attachment);
                    const attachmentUrl = safeAttachmentUrl(attachment?.url);

                    return `
                        <a href="${attachmentUrl}" target="_blank" rel="noopener noreferrer"
                           class="list-group-item list-group-item-action rounded-3 border d-block mb-2"
                           style="border-color:#e2e8f0 !important; transition: all 0.2s;">
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" 
                                         style="width:40px; height:40px; background:rgba(21, 74, 154, 0.08); color:#154A9A;">
                                        <i data-lucide="file-text" width="18"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1 fw-bold text-dark" style="font-size:0.92rem; line-height:1.2;">${escapeHtml(attachment?.name || 'Unnamed attachment')}</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge rounded-pill" style="background:#f1f5f9; color:#475569; font-size:0.68rem; font-weight:600; border:1px solid #e2e8f0;">
                                                ${escapeHtml(displayLabel)}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <span class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold" style="font-size:0.75rem;">Open</span>
                            </div>
                        </a>
                    `;
                }).join('')}
            </div>
        `;

        if (window.lucide) window.lucide.createIcons();
    }

    // Technical Feedback Handler for Agency
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.feedback-sub');
        if (!btn) return;
        e.preventDefault();

        const title = btn.getAttribute('data-title') || 'Untitled Project';
        const feedback = btn.getAttribute('data-feedback') || 'No feedback notes available.';
        const subId = btn.getAttribute('data-id');
        
        const modalEl = document.getElementById('agency-feedback-modal');
        if (!modalEl) return;

        // Populate Modal
        modalEl.querySelector('h6.text-dark').textContent = title;
        modalEl.querySelector('.text-dark[style*="white-space:pre-wrap"]').textContent = feedback;

        // Setup Edit Button
        const editBtn = modalEl.querySelector('.edit-sub-from-agency-feedback');
        if (editBtn && subId) {
            editBtn.onclick = () => {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                window.location.href = `/v2/cipg_submissions/${subId}/edit`;
            };
        }

        // Show Modal
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        if (window.lucide) window.lucide.createIcons({ target: modalEl });
    });

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.attachments-sub');
        if (!btn) return;
        e.preventDefault();

        const title = btn.getAttribute('data-title') || 'Untitled Project';
        let attachments = [];

        try {
            attachments = JSON.parse(btn.getAttribute('data-attachments') || '[]');
        } catch (error) {
            attachments = [];
        }

        renderAttachmentsModal(title, attachments);

        if (!attachmentsModalEl) return;
        const modal = new bootstrap.Modal(attachmentsModalEl);
        modal.show();
    });

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.db-par-feedback-sub');
        if (!btn) return;
        e.preventDefault();

        const title = btn.getAttribute('data-title') || 'Untitled Project';
        const subId = btn.getAttribute('data-id');
        let comments = [];

        try {
            comments = JSON.parse(btn.getAttribute('data-comments') || '[]');
        } catch (error) {
            comments = [];
        }

        const modalEl = document.getElementById('commentModal');
        if (!modalEl) return;

        const titleEl = document.getElementById('par-feedback-modal-project-title');
        const container = document.getElementById('par-feedback-list-container');

        if (titleEl) titleEl.textContent = title;

        if (container) {
            if (comments.length === 0) {
                container.innerHTML = '<p class="text-muted small">No specific findings listed.</p>';
            } else {
                container.innerHTML = comments.map(c => `
                    <div class="p-3 border rounded-4 bg-light bg-opacity-50 mb-2">
                        <div class="row align-items-start">
                            <div class="col-md-6 border-end">
                                <div class="text-uppercase fw-bold text-secondary mb-2" style="font-size:0.65rem; letter-spacing:0.05em;">Secretariat Findings</div>
                                <div class="text-dark small lh-base">${escapeHtml(c.finding || '—')}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-uppercase fw-bold text-secondary mb-2" style="font-size:0.65rem; letter-spacing:0.05em;">Recommendations</div>
                                <div class="text-dark small lh-base">${escapeHtml(c.recommendation || '—')}</div>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        const editBtn = modalEl.querySelector('.edit-sub-from-feedback');
        if (editBtn) {
            editBtn.onclick = () => {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                window.location.href = `/v2/cipg_submissions/${subId}/edit`;
            };
        }

        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        if (window.lucide) window.lucide.createIcons({ target: modalEl });
    });
});
</script>
@endsection