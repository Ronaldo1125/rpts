/**
 * cpp-form.js
 * Loads the 5 CPP form page partials into #cpp-steps-container
 * and runs the multi-step form controller.
 */

const CPP_PAGES = [
    'pages/agency/cpp-form-page1.html',
    'pages/agency/cpp-form-page2.html',
    'pages/agency/cpp-form-page3.html',
    'pages/agency/cpp-form-page4.html',
    'pages/agency/cpp-form-page5.html',
];

let CACHED_PARTS = null;

/* ─────────────────────────────────────────────────────────
   PUBLIC: call this once after all page HTML is injected
───────────────────────────────────────────────────────── */
export async function initCppForm() {
    window.initCppForm = initCppForm;
    const container = document.getElementById('cpp-steps-container');
    if (!container) return; // page not in DOM yet

    // ── View mode: always handle FIRST, before any other logic ──
    // This ensures View works whether the form was previously loaded or not.
    const viewId = sessionStorage.getItem('cpp_view_id');
    if (viewId) {
        sessionStorage.removeItem('cpp_view_id');
        const allSubs = await localforage.getItem('cpp_submissions') || [];
        const sub = allSubs.find(s => s.id === viewId);
        if (sub) {
            // If form pages aren't loaded yet, load them first
            if (!CACHED_PARTS) {
                CACHED_PARTS = await Promise.all(
                    CPP_PAGES.map(p => fetch(p).then(r => {
                        if (!r.ok) throw new Error(`Failed to load ${p}`);
                        return r.text();
                    }))
                );
            }
            // Always re-inject pages for a clean render
            container.innerHTML = CACHED_PARTS.join('\n');
            container.dataset.loaded = '1';
            if (window.lucide) window.lucide.createIcons();
            _initController(); // sets window.showCppReview

            // Hide all form chrome
            const stepper = document.getElementById('cppStepper');
            const nav = document.querySelector('.cpp-nav');
            const counter = document.getElementById('cppStepCounter');
            const floatActions = document.getElementById('cppFloatActions');
            const backBtn = document.querySelector('.btn-back-dash');
            if (stepper) stepper.style.setProperty('display', 'none', 'important');
            if (nav) nav.style.setProperty('display', 'none', 'important');
            if (counter) counter.style.setProperty('display', 'none', 'important');
            if (floatActions) floatActions.style.setProperty('display', 'none', 'important');
            if (backBtn) backBtn.style.setProperty('display', 'none', 'important');

            window.showCppReview(sub);

            // ── Show view-mode floating action bar ──────────────────────────
            const viewActions = document.getElementById('cppViewActions');
            if (viewActions) viewActions.style.removeProperty('display');

            // Wire Attachments button
            const attachBtn = document.getElementById('viewAttachmentsBtn');
            if (attachBtn && !attachBtn._wired) {
                attachBtn._wired = true;
                attachBtn.addEventListener('click', async () => {
                    const listEl  = document.getElementById('attachments-modal-list');
                    const titleEl = document.getElementById('attachments-modal-project-title');
                    if (titleEl) titleEl.textContent = sub.title || sub.formData?.['f-title'] || 'Untitled';
                    if (listEl) {
                        const attachments = Array.isArray(sub.formData?.['_attachments']) ? sub.formData['_attachments'] : [];
                        if (attachments.length === 0) {
                            listEl.innerHTML = '<div class="text-center py-4 text-muted small"><i data-lucide="paperclip" width="32" class="mb-2 opacity-30"></i><p class="mb-0">No attachments found for this submission.</p></div>';
                        } else {
                            listEl.innerHTML = attachments.map(a => `
                                <div class="p-3 border rounded-3 mb-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="small fw-bold d-block">${a.desc || 'File'}</span>
                                        <span class="x-small text-muted">${a.fileName || 'unknown'}</span>
                                    </div>
                                    <a href="${a.data}" download="${a.fileName}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i data-lucide="download" width="13" class="me-1"></i>Download
                                    </a>
                                </div>
                            `).join('');
                        }
                        if (window.lucide) window.lucide.createIcons();
                    }
                    const modalEl = document.getElementById('attachmentsModal');
                    if (modalEl && window.bootstrap?.Modal) new window.bootstrap.Modal(modalEl).show();
                });
            }

            // Wire Version History button
            const histBtn = document.getElementById('viewHistoryBtn');
            if (histBtn && !histBtn._wired) {
                histBtn._wired = true;
                histBtn.addEventListener('click', () => {
                    const histEl  = document.getElementById('version-history-list');
                    const titleEl = document.getElementById('version-modal-project-title');
                    if (titleEl) titleEl.textContent = sub.title || sub.formData?.['f-title'] || 'Untitled';
                    if (histEl) {
                        const versions = sub.versions || [];
                        if (versions.length === 0) {
                            histEl.innerHTML = '<p class="text-muted text-center py-4 small">No version history available for this submission.</p>';
                        } else {
                            histEl.innerHTML = versions.map((v, i) => `
                                <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                                    <div>
                                        <span class="fw-bold small d-block">Version ${i + 1}</span>
                                        <div class="x-small text-muted">${new Date(v.date).toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' })}</div>
                                        ${v.note ? `<div class="small text-secondary mt-1">${v.note}</div>` : ''}
                                    </div>
                                    <span class="badge rounded-pill" style="background:#e8f0fe;color:#154A9A;font-size:0.65rem;">Rev ${i + 1}</span>
                                </div>
                            `).join('');
                        }
                    }
                    const modalEl = document.getElementById('versionHistoryModal');
                    if (modalEl && window.bootstrap?.Modal) new window.bootstrap.Modal(modalEl).show();
                });
            }

            return;
        }
    }

    // Reset visibility of core UI elements that might have been hidden by Review mode
    const stepper = document.getElementById('cppStepper');
    const counter = document.getElementById('cppStepCounter');
    const nav = document.querySelector('.cpp-nav');
    const backBtn = document.querySelector('.btn-back-dash');
    const floatActions = document.getElementById('cppFloatActions');

    if (stepper) stepper.style.removeProperty('display');
    if (counter) stepper && counter.style.removeProperty('display');
    if (nav) nav.style.removeProperty('display');
    if (backBtn) backBtn.style.setProperty('display', 'inline-flex', 'important');
    if (floatActions) floatActions.style.removeProperty('display');

    // Always hide view-mode actions in normal form mode
    const viewActionsBar = document.getElementById('cppViewActions');
    if (viewActionsBar) viewActionsBar.style.setProperty('display', 'none', 'important');

    // If we're already viewing the form (not review), just reset to step 1
    const isReview = !!container.querySelector('#cpp-final-wrapper');
    if (container.dataset.loaded === '1' && !isReview) {
        const editId = sessionStorage.getItem('cpp_edit_id');
        if (editId && window._loadSubmissionData) {
            window._loadSubmissionData(editId);
        }
        _attachBackListener();
        if (window.showStep) window.showStep(1);
        return;
    }

    try {
        // Use cached HTML if available, else fetch once
        if (!CACHED_PARTS) {
            console.log('[cpp-form] Fetching form parts...');
            CACHED_PARTS = await Promise.all(
                CPP_PAGES.map(p => fetch(p).then(r => {
                    if (!r.ok) throw new Error(`Failed to load ${p}`);
                    return r.text();
                }))
            );
        }

        container.innerHTML = CACHED_PARTS.join('\n');
        container.dataset.loaded = '1';

        // Autofill Implementing Agency from logged-in user
        const userData = JSON.parse(localStorage.getItem('currentUser') || '{}');
        const agencyField = document.getElementById('f-agency');
        if (agencyField && userData.agency && !agencyField.value) {
            agencyField.value = userData.agency;
        }

        if (window.lucide) window.lucide.createIcons();
        _initController();

        // Load data if editing
        const editId = sessionStorage.getItem('cpp_edit_id');
        if (editId && window._loadSubmissionData) {
            window._loadSubmissionData(editId);
        }
    } catch (err) {
        container.innerHTML = `<div class="alert alert-danger small">Error loading form pages: ${err.message}</div>`;
        console.error('[cpp-form] load error:', err);
    }
}

/* ─────────────────────────────────────────────────────────
   PRIVATE: multi-step form controller
───────────────────────────────────────────────────────── */
function _initController() {
    let currentStep = 1;
    const totalSteps = 5;

    function _hideTopAlert() {
        const alertEl = document.getElementById('cppAlert');
        if (alertEl) alertEl.classList.remove('show');
    }

    function _setErrDisplay(errId, show) {
        const el = document.getElementById(errId);
        if (!el) return;
        el.style.setProperty('display', show ? 'block' : 'none', 'important');
    }

    function _isBlankValue(el) {
        if (!el) return true;
        if (el.type === 'checkbox') return !el.checked;
        return !(el.value && el.value.trim());
    }

    /* ── Helper: check if canvas is blank ── */
    // NOTE: Only used internally if needed. Canonical version lives in _getFormData.
    function _isCanvasBlank(canvas) {
        if (!canvas) return true;
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        const data = ctx.getImageData(0, 0, canvas.width, canvas.height).data;
        // Check the alpha channel of every pixel
        for (let i = 3; i < data.length; i += 4) { if (data[i] > 0) return false; }
        return true;
    }

    /* ── Validation ── */
    function validateStep(step) {
        let valid = true;
        const alertEl = document.getElementById('cppAlert');
        const alertMsg = document.getElementById('cppAlertMsg');
        if (alertEl) alertEl.classList.remove('show');

        if (step === 1) {
            let reqFields = ['f-agency', 'f-sector', 'f-sub-sector', 'f-title'];
            const locSpecific = document.getElementById('coverage-location-specific')?.checked;
            const interProv = document.getElementById('coverage-inter-province')?.checked;
            if (locSpecific) reqFields.push('f-province', 'f-district', 'f-municipality');
            if (interProv) reqFields.push('f-provinces');

            reqFields.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                const isBlank = !el.value.trim();
                el.classList.toggle('is-invalid', isBlank);
                if (isBlank) valid = false;
            });

            const typeChecked = [...document.querySelectorAll('input[name="project-type"]')].some(c => c.checked);
            const typeErr = document.getElementById('type-error');
            if (typeErr) {
                if (!typeChecked) { typeErr.style.setProperty('display', 'block', 'important'); valid = false; }
                else typeErr.style.setProperty('display', 'none', 'important');
            }
            const covChecked = document.querySelector('input[name="project-coverage"]:checked');
            const covErr = document.getElementById('coverage-error');
            if (covErr) {
                if (!covChecked) { covErr.style.setProperty('display', 'block', 'important'); valid = false; }
                else covErr.style.setProperty('display', 'none', 'important');
            }
            // Validate Current Project Status radio group
            const statusChecked = document.querySelector('input[name="project-status"]:checked');
            const statusErr = document.getElementById('status-error');
            if (statusErr) {
                if (!statusChecked) { statusErr.style.setProperty('display', 'block', 'important'); valid = false; }
                else statusErr.style.setProperty('display', 'none', 'important');
            }
        }

        if (step === 2) {
            const req = ['f-alignment', 'f-background', 'f-goal', 'f-purpose', 'f-outputs', 'f-activities', 'f-linkages', 'f-counterpart-funding', 'f-total-cost', 'f-funding-source', 'f-beneficiaries', 'f-social-benefits', 'f-economic-benefits'];
            req.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                const isBlank = !el.value.trim();
                el.classList.toggle('is-invalid', isBlank);

                if (id === 'f-alignment') {
                    const box = document.getElementById('f-alignment-box');
                    const err = document.getElementById('f-alignment-err');
                    if (box) box.classList.toggle('is-invalid', isBlank);
                    if (err) err.style.setProperty('display', isBlank ? 'block' : 'none', 'important');
                }

                if (isBlank) valid = false;
            });
        }

        if (step === 3) {
            const container = document.getElementById('step-3');
            if (container) {
                // generic data-required fields inside step-3
                container.querySelectorAll('[data-required]').forEach(el => {
                    if (el.disabled || el.offsetParent === null) return;
                    const isBlank = !el.value || !el.value.trim();
                    el.classList.toggle('is-invalid', isBlank);
                    if (isBlank) valid = false;
                });

                // Implementation schedule: require at least one non-empty row
                const implBody = document.getElementById('impl-schedule-body');
                const implErr = document.getElementById('impl-schedule-err');
                if (implBody && implErr) {
                    const hasFilled = [...implBody.querySelectorAll('tr')].some(row => {
                        return [...row.querySelectorAll('input, textarea')].some(i => i.value && i.value.trim());
                    });
                    if (!hasFilled) { implErr.style.setProperty('display', 'block', 'important'); valid = false; }
                    else implErr.style.setProperty('display', 'none', 'important');
                }

                // Environmental clearance: required
                const envDesc = document.getElementById('f-env-clearance-desc');
                if (envDesc) {
                    const isBlank = !envDesc.value || !envDesc.value.trim();
                    envDesc.classList.toggle('is-invalid', isBlank);
                    if (isBlank) valid = false;
                }

                // Consultation status radio group
                const consultChecked = document.querySelector('input[name="consultation-status"]:checked');
                const consultErr = document.getElementById('consult-status-err');
                if (consultErr) {
                    if (!consultChecked) { consultErr.style.setProperty('display', 'block', 'important'); valid = false; }
                    else consultErr.style.setProperty('display', 'none', 'important');
                }
            }
        }

        if (step === 4) {
            const container4 = document.getElementById('step-4');
            if (container4) {
                container4.querySelectorAll('[data-required]').forEach(el => {
                    if (el.disabled || el.offsetParent === null) return;
                    const isBlank = !el.value || !el.value.trim();
                    el.classList.toggle('is-invalid', isBlank);
                    if (isBlank) valid = false;
                });
            }
        }

        if (step === 5) {
            const container = document.getElementById('step-5');
            if (container) {
                // Validate all data-required fields in step 5 (includes geolocation fields)
                container.querySelectorAll('[data-required]').forEach(el => {
                    if (el.disabled || el.offsetParent === null) return;
                    const isBlank = !el.value || !el.value.trim();
                    el.classList.toggle('is-invalid', isBlank);
                    if (isBlank) valid = false;
                });
            }
            ['f-prep-name', 'f-prep-position', 'f-noted-name', 'f-noted-position'].forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.value.trim()) { el.classList.add('is-invalid'); valid = false; }
            });
            const certify = document.getElementById('f-certify');
            if (certify && !certify.checked) {
                const err = document.getElementById('certify-error');
                if (err) err.style.setProperty('display', 'block', 'important');
                valid = false;
            }
        }

        if (!valid && alertEl && alertMsg) {
            alertMsg.textContent = 'Please fill in all required fields marked with * before proceeding.';
            alertEl.classList.add('show');

            // Scroll to the first invalid element in the current step
            const stepEl = document.getElementById(`step-${step}`);
            const searchRoot = stepEl || document.getElementById('cppMainForm') || document;
            // Find any .is-invalid field, or error divs that are now visible
            const firstInvalid = searchRoot.querySelector(
                '.is-invalid, .invalid-feedback[style*="block"], .invalid-feedback.d-block:not([style*="none"])'
            );
            if (firstInvalid) {
                // Scroll to the element itself or its nearest labeled parent
                const target = firstInvalid.closest('.mb-4, .row, .prep-card') || firstInvalid;
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                alertEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        return valid;
    }

    function _attachLiveValidation() {
        const root = document.getElementById('cppFormContainer') || document;

        const attachRequired = (el) => {
            if (!el) return;
            const handler = () => {
                const blank = _isBlankValue(el);
                el.classList.toggle('is-invalid', blank);
                if (!blank) _hideTopAlert();
            };
            el.addEventListener('input', handler);
            el.addEventListener('change', handler);
        };

        // Text/select required fields used across steps
        root.querySelectorAll('[data-required]').forEach(attachRequired);

        // Step 1 required fields (non data-required ones)
        ['f-agency', 'f-sector', 'f-sub-sector', 'f-title', 'f-province', 'f-district', 'f-municipality', 'f-provinces'].forEach(id => {
            const el = document.getElementById(id);
            if (el) attachRequired(el);
        });

        // Step 2 required fields
        ['f-alignment', 'f-background', 'f-goal', 'f-purpose', 'f-outputs', 'f-activities', 'f-linkages', 'f-counterpart-funding', 'f-total-cost', 'f-funding-source', 'f-beneficiaries', 'f-social-benefits', 'f-economic-benefits'].forEach(id => {
            const el = document.getElementById(id);
            if (el) attachRequired(el);
        });

        // Radio groups: hide group errors once a selection exists
        const attachRadioGroup = (name, errId) => {
            const radios = [...document.querySelectorAll(`input[name="${name}"]`)];
            if (!radios.length) return;
            const handler = () => {
                const checked = radios.some(r => r.checked);
                if (checked) {
                    _setErrDisplay(errId, false);
                    _hideTopAlert();
                }
            };
            radios.forEach(r => r.addEventListener('change', handler));
        };
        attachRadioGroup('project-type', 'type-error');
        attachRadioGroup('project-coverage', 'coverage-error');
        attachRadioGroup('project-status', 'status-error');
        // attachRadioGroup('env-clearance-opt', 'env-clearance-err'); // removed
        attachRadioGroup('consultation-status', 'consult-status-err');

        // Implementation schedule: hide error once at least one row has content
        const implBody = document.getElementById('impl-schedule-body');
        if (implBody) {
            const handler = () => {
                const hasFilled = [...implBody.querySelectorAll('tr')].some(row => {
                    return [...row.querySelectorAll('input, textarea')].some(i => i.value && i.value.trim());
                });
                if (hasFilled) {
                    _setErrDisplay('impl-schedule-err', false);
                    _hideTopAlert();
                }
            };
            implBody.addEventListener('input', handler);
            implBody.addEventListener('change', handler);
        }

        // Certify checkbox
        const certify = document.getElementById('f-certify');
        if (certify) {
            certify.addEventListener('change', () => {
                if (certify.checked) {
                    _setErrDisplay('certify-error', false);
                    _hideTopAlert();
                }
            });
        }
    }

    /* ── Stepper UI ── */
    function updateStepper(step) {
        document.querySelectorAll('#cppStepper .cpp-step').forEach((el, i) => {
            el.classList.remove('active', 'completed');
            if (i + 1 < step) el.classList.add('completed');
            if (i + 1 === step) el.classList.add('active');
        });
        const counter = document.getElementById('cppStepCounter');
        if (counter) counter.textContent = `Step ${step} of ${totalSteps}`;
        const prevBtn = document.getElementById('cppPrevBtn');
        if (prevBtn) prevBtn.style.visibility = step === 1 ? 'hidden' : 'visible';
        const nextBtn = document.getElementById('cppNextBtn');
        const submitBtn = document.getElementById('cppSubmitBtn');
        if (nextBtn && submitBtn) {
            if (step === totalSteps) { nextBtn.classList.add('d-none'); submitBtn.classList.remove('d-none'); _populateSummary(); }
            else { nextBtn.classList.remove('d-none'); submitBtn.classList.add('d-none'); }
        }
    }

    /* ── Show Step ── */
    function showStep(step) {
        currentStep = step;
        document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
        const target = document.getElementById('step-' + step);
        if (target) target.classList.add('active');
        updateStepper(step);

        // Update Buttons based on Status
        const editId = sessionStorage.getItem('cpp_edit_id');
        const draftBtn = document.getElementById('cppDraftBtn');
        const submitBtn = document.getElementById('cppSubmitBtn');

        if (submitBtn || draftBtn) {
            localforage.getItem('cpp_submissions').then(subs => {
                const subData = editId && subs ? subs.find(s => s.id === editId) : null;
                const st = subData ? (subData.submissionStatus || subData.status) : '';
                const isFormalReturn = (st === 'Incomplete' || st === 'For Revision');

                // 1. "Save as Draft" Visibility
                if (draftBtn) {
                    draftBtn.style.display = isFormalReturn ? 'none' : 'flex';
                }

                // 2. Submit Button Text
                if (submitBtn) {
                    const isDraft = !subData || (st === 'Draft' || String(subData.id).startsWith('DRAFT-'));
                    if (editId && !isDraft) {
                        if (st === 'Incomplete') {
                            submitBtn.innerHTML = '<i data-lucide="send" class="me-2" width="16"></i>Resubmit Proposal';
                        } else if (st === 'For Revision') {
                            submitBtn.innerHTML = '<i data-lucide="send" class="me-2" width="16"></i>Submit Revised Proposal';
                        } else {
                            submitBtn.innerHTML = '<i data-lucide="send" class="me-2" width="16"></i>Submit Updated Proposal';
                        }
                    } else {
                        submitBtn.innerHTML = '<i data-lucide="send" class="me-2" width="16"></i>Submit CPP for Review';
                    }
                }
                if (window.lucide) window.lucide.createIcons();
            });
        }
    }
    window.showStep = showStep;

    /* ── Summary Panel (Step 4) ── */
    function _populateSummary() {
        const g = id => { const el = document.getElementById(id); return el ? el.value.trim() : ''; };
        const statusEl = document.querySelector('input[name="project-status"]:checked');
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || '—'; };

        set('sum-agency', g('f-agency'));
        set('sum-sector', g('f-sector'));
        set('sum-title', g('f-title'));
        set('sum-province', g('f-province'));
        set('sum-status', statusEl ? statusEl.value : '');
        const cost = g('f-total-cost');
        set('sum-cost', cost ? '₱ ' + parseFloat(cost).toLocaleString() : '');
        set('sum-funding', g('f-funding-source'));
        set('sum-start', g('f-start-date'));
        set('sum-end', g('f-end-date'));
    }

    /* ── Global navigation callbacks ── */
    function cppNext() {
        if (!validateStep(currentStep)) return;
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
    window.cppNext = cppNext;

    function cppPrev() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            const alertEl = document.getElementById('cppAlert');
            if (alertEl) alertEl.classList.remove('show');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
    window.cppPrev = cppPrev;

    function _attachBackListener() {
        const backBtnSub = document.getElementById('cppBackSubmissions');
        if (!backBtnSub) return;
        backBtnSub.onclick = (e) => {
            e.preventDefault();
            const editId = sessionStorage.getItem('cpp_edit_id');
            const title = document.getElementById('f-title')?.value.trim();
            const agency = document.getElementById('f-agency')?.value.trim();
            const isDirty = editId || title || agency;
            const curRole = JSON.parse(localStorage.getItem('currentUser') || '{}').role;
            const isDashboard = window.location.pathname.includes('dashboard.html');
            const hasAdminLink = document.querySelector('[data-page="manage-submissions"]');
            const returnPage = (curRole === 'admin' || isDashboard || hasAdminLink) ? 'manage-submissions' : 'submissions';
            if (isDirty) {
                if (window.showConfirmModal) {
                    window.showConfirmModal({
                        title: 'Discard Changes?',
                        message: 'You have unsaved changes. Are you sure you want to leave this page?',
                        confirmText: 'Discard',
                        confirmClass: 'btn-danger',
                        onConfirm: () => {
                            sessionStorage.removeItem('cpp_edit_id');
                            window.switchPage(returnPage);
                        }
                    });
                } else if (confirm('Discard changes?')) {
                    sessionStorage.removeItem('cpp_edit_id');
                    window.switchPage(returnPage);
                }
            } else {
                window.switchPage(returnPage);
            }
        };
    }
    window._attachBackListener = _attachBackListener;
    _attachBackListener();

    _attachLiveValidation();

    async function _safeSave(key, data) {
        try {
            await localforage.setItem(key, data);
            return true;
        } catch (e) {
            console.error('Storage Error:', e);
            if (window.showSimpleAlert) {
                window.showSimpleAlert('Save failed: Browser storage quota exceeded. Please remove large attachments or photos and try again.', 'danger');
            } else if (window.showToast) {
                window.showToast('Save failed: Browser storage quota exceeded. Please remove large attachments or photos and try again.', 'danger');
            } else {
                alert('Save failed: Browser storage quota exceeded. Please remove large attachments or photos and try again.');
            }
            return false;
        }
    }

    function cppSubmit() {
        // Final validation check for ALL steps
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                currentStep = i;
                showStep(currentStep);
                return;
            }
        }

        // Add confirmation prompt
        const confirmSubmission = () => {
            _executeSubmit();
        };

        if (window.showConfirmModal) {
            window.showConfirmModal({
                title: 'Confirm Submission?',
                message: 'Please review all your project details one last time. Are you sure you want to proceed with this submission?',
                confirmText: 'Submit Now',
                confirmClass: 'btn-primary',
                onConfirm: confirmSubmission
            });
        } else if (confirm('Are you sure you want to submit this project profile? Please check all details before confirming.')) {
            confirmSubmission();
        }
    }

    // Move the actual submission logic into a helper
    async function _executeSubmit() {

        const formData = _getFormData();
        console.log('[cpp] formData at submit →', JSON.stringify(formData, null, 2));
        const editId = sessionStorage.getItem('cpp_edit_id');

        // Auto-stamp the submission date/time into formData and signature date fields
        const nowISO = new Date().toISOString();
        const nowFormatted = new Date().toLocaleDateString('en-PH', {
            year: 'numeric', month: 'long', day: 'numeric'
        });
        formData['f-submission-date'] = nowISO;
        formData['f-prep-date'] = nowFormatted;
        formData['f-noted-date'] = nowFormatted;

        // Update the visible date displays in the form (page 5 fields)
        const prepDateDisplay = document.getElementById('f-prep-date-display');
        if (prepDateDisplay) prepDateDisplay.textContent = nowFormatted;
        const notedDateDisplay = document.getElementById('f-noted-date-display');
        if (notedDateDisplay) notedDateDisplay.textContent = nowFormatted;
        const prepDateHidden = document.getElementById('f-prep-date');
        if (prepDateHidden) prepDateHidden.value = nowFormatted;
        const notedDateHidden = document.getElementById('f-noted-date');
        if (notedDateHidden) notedDateHidden.value = nowFormatted;

        const newVersion = {
            versionNumber: 1,
            date: nowISO,
            timestamp: 'Just now',
            formData: { ...formData }
        };

        const existing = await localforage.getItem('cpp_submissions') || [];
        const newId = editId || 'CPP-' + Date.now().toString().slice(-6);

        let nextStatus = 'Submitted';
        let preservedStage = '';
        let preservedCppStage = '';
        /** For Revision at RDC stage → resubmit keeps stage RDC with status Revised */
        let forceRdcStageAfterRevision = false;
        if (editId) {
            const idx = existing.findIndex(s => s.id === editId);
            if (idx !== -1) {
                const prev = existing[idx];
                const prevStatus = prev.submissionStatus || prev.status;

                // Differentiate resubmission status
                if (prevStatus === 'For Revision' || prevStatus === 'Revised') {
                    nextStatus = 'Revised';
                } else if (prevStatus === 'Incomplete' || prevStatus === 'Resubmitted') {
                    nextStatus = 'Resubmitted';
                }

                preservedStage = prev.stage || prev.cppStage || '';
                preservedCppStage = prev.cppStage || prev.stage || '';

                const rdcStage = ['stage', 'cppStage', 'projectStage'].some(
                    k => String(prev[k] || '').trim().toLowerCase() === 'rdc'
                );
                forceRdcStageAfterRevision = prevStatus === 'For Revision' && rdcStage;
            }
        }

        const submission = {
            id: newId,
            title: formData['f-title'] || '',
            agency: formData['f-agency'] || '',
            sector: formData['f-sector'] || '',
            projectStatus: formData['project-status'] || '',
            status: nextStatus,
            submissionStatus: nextStatus,
            date: nowISO,
            timestamp: 'Just now',
            formData: formData,
            versions: []
        };

        if (preservedStage) submission.stage = preservedStage;
        if (preservedCppStage) submission.cppStage = preservedCppStage;
        if (nextStatus === 'Revised') {
            submission.cppStatus = 'Revised';
        } else if (nextStatus === 'Resubmitted') {
            submission.cppStatus = 'Resubmitted';
        }
        if (forceRdcStageAfterRevision) {
            submission.stage = 'RDC';
            submission.cppStage = 'RDC';
            submission.projectStage = 'RDC';
        }

        if (editId) {
            const idx = existing.findIndex(s => s.id === editId);
            if (idx !== -1) {
                // Preserve existing versions, append new version
                const prev = existing[idx];
                const versions = prev.versions || [];
                newVersion.versionNumber = versions.length + 1;
                submission.versions = [...versions, newVersion];
                existing[idx] = submission;
            } else {
                newVersion.versionNumber = 1;
                submission.versions = [newVersion];
                existing.unshift(submission);
            }
        } else {
            newVersion.versionNumber = 1;
            submission.versions = [newVersion];
            existing.unshift(submission);
        }

        if (!(await _safeSave('cpp_submissions', existing))) return;

        sessionStorage.removeItem('cpp_edit_id');

        _showFinalReview(submission);
    }
    window.cppSubmit = cppSubmit;

    async function cppDraft() {
        const formData = _getFormData();
        const editId = sessionStorage.getItem('cpp_edit_id');

        const draft = {
            id: editId || 'DRAFT-' + Date.now().toString().slice(-6),
            title: formData['f-title'] || '(Untitled Draft)',
            agency: formData['f-agency'] || '—',
            sector: formData['f-sector'] || '—',
            projectStatus: formData['project-status'] || '',
            status: 'Draft',
            date: new Date().toISOString(),
            timestamp: 'Just now (Draft)',
            formData: formData
        };

        const existing = await localforage.getItem('cpp_submissions') || [];
        if (editId) {
            const idx = existing.findIndex(s => s.id === editId);
            if (idx !== -1) existing[idx] = draft;
            else existing.unshift(draft);
        } else {
            existing.unshift(draft);
        }

        if (!(await _safeSave('cpp_submissions', existing))) return;
        sessionStorage.setItem('cpp_edit_id', draft.id);

        if (window.showSimpleAlert) window.showSimpleAlert('Draft saved successfully.', 'success');
        else if (window.showToast) window.showToast('Draft saved successfully!', 'success');

        const returnPage = (JSON.parse(localStorage.getItem('currentUser') || '{}').role === 'admin') ? 'manage-submissions' : 'submissions';
        if (window.switchPage) {
            setTimeout(() => window.switchPage(returnPage), (window.showSimpleAlert || window.showToast) ? 650 : 0);
        }
    }
    window.cppDraft = cppDraft;

    function _saveWithStatus(statusLabel, successMessage) {
        const proceedSave = () => {
            _executeSaveWithStatus(statusLabel, successMessage);
        };

        const actionText = statusLabel === 'Review' ? 'submit this for Review' : (statusLabel === 'Approved' ? 'approve this project' : 'save this project');

        if (window.showConfirmModal) {
            window.showConfirmModal({
                title: 'Confirm Action?',
                message: `Are you sure you want to ${actionText}? Please verify all details before proceeding.`,
                confirmText: 'Proceed',
                confirmClass: statusLabel === 'Approved' ? 'btn-success' : 'btn-primary',
                onConfirm: proceedSave
            });
        } else if (confirm(`Are you sure you want to ${actionText}?`)) {
            proceedSave();
        }
    }

    async function _executeSaveWithStatus(statusLabel, successMessage) {
        const formData = _getFormData();
        const editId = sessionStorage.getItem('cpp_edit_id');

        const submission = {
            id: editId || 'CPP-' + Date.now().toString().slice(-6),
            title: formData['f-title'] || '',
            agency: formData['f-agency'] || '',
            sector: formData['f-sector'] || '',
            projectStatus: formData['project-status'] || '',
            status: statusLabel,
            date: new Date().toISOString(),
            timestamp: 'Just now',
            formData: formData
        };

        const existing = await localforage.getItem('cpp_submissions') || [];
        if (editId) {
            const idx = existing.findIndex(s => s.id === editId);
            if (idx !== -1) existing[idx] = submission;
            else existing.unshift(submission);
        } else {
            existing.unshift(submission);
        }

        if (!(await _safeSave('cpp_submissions', existing))) return;
        sessionStorage.removeItem('cpp_edit_id');

        if (window.showSimpleAlert) window.showSimpleAlert(successMessage, 'success');
        else if (window.showToast) window.showToast(successMessage, 'success');

        const returnPage = (JSON.parse(localStorage.getItem('currentUser') || '{}').role === 'admin') ? 'manage-submissions' : 'submissions';
        if (window.switchPage) {
            setTimeout(() => window.switchPage(returnPage), (window.showSimpleAlert || window.showToast) ? 650 : 0);
        }
    }

    function cppSaveAsReview() {
        _saveWithStatus('Review', 'Saved as Review successfully.');
    }
    window.cppSaveAsReview = cppSaveAsReview;

    function cppSaveAsApproved() {
        _saveWithStatus('Approved', 'Saved as Approved successfully.');
    }
    window.cppSaveAsApproved = cppSaveAsApproved;

    function _getFormData() {
        const data = {};

        // ── Strategy 1: scan the form container ──
        const formEl = document.getElementById('cppMainForm');
        const stepsEl = document.getElementById('cpp-steps-container');
        // Use whichever root we can find; also scan the full step container as fallback
        const roots = [formEl, stepsEl].filter(Boolean);
        const seen = new Set();

        roots.forEach(root => {
            root.querySelectorAll('input, select, textarea').forEach(el => {
                if (!el.id || seen.has(el.id)) return;
                seen.add(el.id);
                if (el.type === 'checkbox') {
                    data[el.id] = el.checked;
                } else if (el.type === 'radio') {
                    if (el.checked) data[el.id] = el.value;
                } else {
                    data[el.id] = el.value.trim();
                }
            });
        });

        // ── Strategy 2: also catch any id^="f-" / id^="lf-" fields anywhere in DOM ──
        // This covers fields that may be rendered outside #cppMainForm (e.g., in a header panel)
        document.querySelectorAll('[id^="f-"], [id^="lf-"]').forEach(el => {
            if (!el.id || seen.has(el.id)) return;
            seen.add(el.id);
            if (el.type === 'checkbox') {
                data[el.id] = el.checked;
            } else if (el.type === 'radio') {
                if (el.checked) data[el.id] = el.value;
            } else if (el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
                data[el.id] = el.value.trim();
            }
        });

        // ── Named radio groups → store as data[name] = selected value ──
        const radioGroups = [
            'project-status',
            'project-coverage',
            'project-type',       // also handled as array below
            'consultation-status',
            'env-clearance',
            'hgdg',
        ];
        radioGroups.forEach(name => {
            const checked = document.querySelector(`input[name="${name}"]:checked`);
            if (checked) data[name] = checked.value;
        });

        // ── project-type: also store as an array for the checkboxes ──
        data['project-type'] = [...document.querySelectorAll('input[name="project-type"]:checked')].map(c => c.value);

        // ── Readiness checkboxes (explicit IDs without f- prefix) ──
        ['prep-site', 'prep-row', 'prep-ded'].forEach(id => {
            data[id] = document.getElementById(id)?.checked || false;
        });

        // ── Environmental clearance upload ──
        const envPanel = document.getElementById('env-clearance-upload-panel');
        if (envPanel && envPanel.dataset.dataUri) {
            data['env-clearance-data'] = envPanel.dataset.dataUri;
            data['env-clearance-name'] = envPanel.dataset.fileName || '';
            data['env-clearance-mime'] = envPanel.dataset.mimeType || '';
        }

        // ── f-provinces hidden input (inter-province tag picker) ──
        const provInput = document.getElementById('f-provinces');
        if (provInput) data['f-provinces'] = provInput.value.trim();

        // ── project-coverage: ensure it's stored from the checked radio ──
        const cov = document.querySelector('input[name="project-coverage"]:checked');
        if (cov) data['project-coverage'] = cov.value;

        // ── Signatures: drawn canvas or uploaded image ──
        ['prep', 'noted'].forEach(prefix => {
            const canvas = document.getElementById(`sig-canvas-${prefix}`);
            if (canvas && !_isCanvasBlank(canvas)) {
                data[`sig-${prefix}-data`] = canvas.toDataURL();
            }
            // Only use uploaded image if it's an actual data URI (not a placeholder src)
            const previewImg = document.getElementById(`sig-${prefix}-upload-preview`)?.querySelector('img');
            if (previewImg && previewImg.src && previewImg.src.startsWith('data:')) {
                data[`sig-${prefix}-data`] = previewImg.src;
            }
        });

        // ── Geotagged photo: save data URI if present ──
        const geoPhotoPreview = document.getElementById('geo-photo-preview');
        if (geoPhotoPreview) {
            // Prefer dataset (set by _handleGeoFile for all file types including PDF)
            if (geoPhotoPreview.dataset.dataUri) {
                data['geo-photo-data'] = geoPhotoPreview.dataset.dataUri;
                data['geo-photo-name'] = geoPhotoPreview.dataset.fileName || '';
                data['geo-photo-mime'] = geoPhotoPreview.dataset.mimeType || '';
            } else {
                // Fallback: scan for an img tag (legacy)
                const geoImg = geoPhotoPreview.querySelector('img');
                if (geoImg && geoImg.src.startsWith('data:')) {
                    data['geo-photo-data'] = geoImg.src;
                    data['geo-photo-mime'] = 'image/png';
                }
                const nameEl = geoPhotoPreview.querySelector('.text-success');
                if (nameEl) data['geo-photo-name'] = nameEl.textContent.replace('✓', '').replace('✓', '').trim();
            }
        }

        // ── Other attachments: scan each row using dataset (set by _handleAttachFile) ──
        const attRows = document.querySelectorAll('#other-attachments-list .attachment-row');
        const attachments = [];
        attRows.forEach(row => {
            const descEl = row.querySelector('input[type="text"]');
            const previewEl = row.querySelector('[id$="-preview"]');
            const desc = descEl?.value?.trim() || '';
            const dataUri = previewEl?.dataset?.dataUri || null;
            const fileName = previewEl?.dataset?.fileName || '';
            const mimeType = previewEl?.dataset?.mimeType || '';
            if (desc || dataUri || fileName) {
                attachments.push({ desc, dataUri, fileName, mimeType });
            }
        });

        // ── Upload-zone panels (endorsements, DED, env clearance, consult, HGDG, etc.) ──
        // _createUploadZone stores file data in panel.dataset after FileReader completes.
        document.querySelectorAll('[data-attach-file-name], [data-attach-data-uri]').forEach(panel => {
            const dataUri  = panel.dataset.attachDataUri  || null;
            const fileName = panel.dataset.attachFileName || '';
            const mimeType = panel.dataset.attachMimeType || '';
            const desc     = panel.dataset.attachLabel    || panel.id || 'Uploaded Document';
            if (dataUri || fileName) {
                // Avoid duplicates with the other-attachments list by checking desc+fileName
                const alreadyAdded = attachments.some(a => a.fileName === fileName && a.desc === desc);
                if (!alreadyAdded) attachments.push({ desc, dataUri, fileName, mimeType });
            }
        });

        if (attachments.length > 0) data['_attachments'] = attachments;

        // ── Implementation Schedule ──
        const implSchedule = [];
        document.querySelectorAll('#impl-schedule-body tr').forEach(row => {
            const yearEl = row.querySelector('td:nth-child(1) input');
            const targetEl = row.querySelector('td:nth-child(2) textarea');
            const indicatorEl = row.querySelector('td:nth-child(3) input');
            const amountEl = row.querySelector('td:nth-child(4) input');
            if (yearEl && targetEl && indicatorEl && amountEl) {
                implSchedule.push({
                    year: yearEl.value.trim(),
                    target: targetEl.value.trim(),
                    indicator: indicatorEl.value.trim(),
                    amount: amountEl.value.trim()
                });
            }
        });
        if (implSchedule.length > 0) data['_impl_schedule'] = implSchedule;

        return data;
    }


    window._loadSubmissionData = async function (id) {
        try {
            const submissions = await localforage.getItem('cpp_submissions') || [];
            const submission = submissions.find(s => s.id === id);
            if (!submission || !submission.formData) return;

            // Wait a tiny bit for the DOM to be fully painted after innerHTML injection
            await new Promise(r => setTimeout(r, 100));

            const d = submission.formData;
            Object.keys(d).forEach(key => {
                const el = document.getElementById(key);
                if (!el) return;
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = !!d[key];
                    el.dispatchEvent(new Event('change'));
                } else {
                    el.value = d[key];
                    el.dispatchEvent(new Event('input'));
                }
            });

            if (Array.isArray(d['project-type'])) {
                document.querySelectorAll('input[name="project-type"]').forEach(el => {
                    el.checked = d['project-type'].includes(el.value);
                });
            }

            // ── Restore project-coverage radio first (shows/hides location panels) ──
            if (d['project-coverage']) {
                const covEl = document.querySelector(`input[name="project-coverage"][value="${d['project-coverage']}"]`);
                if (covEl) { covEl.checked = true; covEl.dispatchEvent(new Event('change')); }
            }

            // ── Restore project-status radio ──
            if (d['project-status']) {
                const stEl = document.querySelector(`input[name="project-status"][value="${d['project-status']}"]`);
                if (stEl) { stEl.checked = true; stEl.dispatchEvent(new Event('change')); }
            }

            // ── Restore Location-Specific cascaded selects ──
            // Must run AFTER the coverage change event populates the panel visibility.
            if (d['f-province'] && d['project-coverage'] === 'Location-Specific') {
                await new Promise(r => setTimeout(r, 60));
                const provinceEl = document.getElementById('f-province');
                if (provinceEl) {
                    provinceEl.value = d['f-province'];
                    provinceEl.dispatchEvent(new Event('change')); // populates districts
                    await new Promise(r => setTimeout(r, 40));
                    const districtEl = document.getElementById('f-district');
                    if (districtEl && d['f-district']) {
                        districtEl.value = d['f-district'];
                        districtEl.dispatchEvent(new Event('change')); // populates municipalities
                        await new Promise(r => setTimeout(r, 40));
                        const municipalEl = document.getElementById('f-municipality');
                        if (municipalEl && d['f-municipality']) {
                            municipalEl.value = d['f-municipality'];
                        }
                    }
                }
            }

            // ── Restore inter-province provinces (without opening the dropdown) ──
            const savedProvinces = d['f-provinces'];
            if (savedProvinces && d['project-coverage'] === 'Inter-Province') {
                const tagHidden = document.getElementById('f-provinces');
                if (tagHidden) tagHidden.value = savedProvinces;
                // Use the exposed sync function so the tag INPUT's focus event is not triggered
                if (window.syncProvincesTags) {
                    window.syncProvincesTags();
                } else {
                    // Fallback: rebuild tags manually
                    const tagBox = document.getElementById('f-provinces-box');
                    if (tagBox) {
                        tagBox.querySelectorAll('.prov-tag').forEach(t => t.remove());
                        const addInput = tagBox.querySelector('input');
                        savedProvinces.split(',').map(p => p.trim()).filter(Boolean).forEach(prov => {
                            const tag = document.createElement('span');
                            tag.className = 'prov-tag d-inline-flex align-items-stretch border rounded';
                            tag.style.background = '#e2e8f0';
                            tag.style.fontSize = '0.86rem';
                            tag.innerHTML = `<span class="prov-remove border-end px-2 text-muted d-flex align-items-center" style="cursor:pointer;" data-prov="${prov}">&times;</span><span class="px-2 py-1" style="color:#1e293b;">${prov}</span>`;
                            if (addInput) tagBox.insertBefore(tag, addInput);
                            else tagBox.appendChild(tag);
                        });
                    }
                }
                // Tick checkboxes in dropdown without opening it
                const provList = savedProvinces.split(',').map(p => p.trim()).filter(Boolean);
                document.querySelectorAll('#provinces-dropdown .dropdown-item input[type="checkbox"]').forEach(chk => {
                    chk.checked = provList.includes(chk.closest('[data-prov]')?.getAttribute('data-prov') || '');
                });
            }

            // ── Restore SDG alignment tags (with retry if widget not ready) ──
            const savedSDGs = d['f-alignment'];
            if (savedSDGs) {
                const sdgHidden = document.getElementById('f-alignment');
                if (sdgHidden) sdgHidden.value = savedSDGs;
                const _trySyncSDG = (attempts = 0) => {
                    if (window.syncSDGTags) { window.syncSDGTags(); }
                    else if (attempts < 10) { setTimeout(() => _trySyncSDG(attempts + 1), 80); }
                };
                _trySyncSDG();
            }

            // ── Restore RDP 2023-2028 alignment tags (with retry if widget not ready) ──
            const savedRDP = d['f-rdp-alignment'];
            if (savedRDP) {
                const rdpHidden = document.getElementById('f-rdp-alignment');
                if (rdpHidden) rdpHidden.value = savedRDP;
                const _trySyncRDP = (attempts = 0) => {
                    if (window.syncRDPTags) { window.syncRDPTags(); }
                    else if (attempts < 10) { setTimeout(() => _trySyncRDP(attempts + 1), 80); }
                };
                _trySyncRDP();
            }

            // ── Restore consultation-status radio + date tags ──
            if (d['consultation-status']) {
                const radioEl = document.querySelector(`input[name="consultation-status"][value="${d['consultation-status']}"]`);
                if (radioEl) { radioEl.checked = true; radioEl.dispatchEvent(new Event('change')); }
            }
            const savedConsultDates = d['f-consult-done-dates'];
            if (savedConsultDates) {
                const box = document.getElementById('consult-dates-box');
                const placeholder = document.getElementById('consult-dates-placeholder');
                const hidden = document.getElementById('f-consult-done-dates');
                if (box && hidden) {
                    hidden.value = savedConsultDates;
                    box.querySelectorAll('.consult-date-tag').forEach(t => t.remove());
                    const addSpan = box.querySelector('.consult-date-add');
                    const dates = savedConsultDates.split(',').map(s => s.trim()).filter(Boolean);
                    dates.forEach(dateStr => {
                        const tag = document.createElement('span');
                        tag.className = 'consult-date-tag d-inline-flex align-items-center gap-1';
                        tag.style.cssText = 'background:#e0eaff; color:#154A9A; border:1px solid #b8d0ff; border-radius:6px; padding:2px 8px; font-size:0.78rem; font-weight:600; white-space:nowrap;';
                        tag.innerHTML = `${dateStr} <span class="consult-tag-remove" style="cursor:pointer; font-size:1rem; line-height:1; margin-left:2px; color:#7c9fdb;">&times;</span>`;
                        if (addSpan) box.insertBefore(tag, addSpan);
                        else box.appendChild(tag);
                    });
                    if (placeholder) placeholder.textContent = dates.length > 0 ? 'Add more' : 'Pick date(s)';
                    box.style.borderColor = dates.length > 0 ? '#154A9A' : '#dee2e6';
                }
            }

            // ── Restore geotagged photo preview ──
            const geoData = d['geo-photo-data'];
            const geoName = d['geo-photo-name'];
            const geoMime = d['geo-photo-mime'] || '';
            const geoPreview = document.getElementById('geo-photo-preview');
            if (geoPreview && (geoData || geoName)) {
                geoPreview.dataset.dataUri  = geoData  || '';
                geoPreview.dataset.fileName = geoName  || '';
                geoPreview.dataset.mimeType = geoMime;
                if (geoData) {
                    if (geoMime.startsWith('image/') || geoData.startsWith('data:image/')) {
                        geoPreview.innerHTML = `<div class="position-relative d-inline-block mt-2"><img src="${geoData}" style="max-height:200px;max-width:100%;border-radius:8px;border:1px solid #e2e8f0;" alt="Geotagged Photo"><div class="text-success small fw-bold mt-1"><i data-lucide="check-circle" width="13"></i> ${geoName || 'photo'}</div></div>`;
                    } else if (geoMime === 'application/pdf' || geoData.startsWith('data:application/pdf')) {
                        geoPreview.innerHTML = `<div class="d-flex align-items-center gap-2 mt-2 p-2 rounded" style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);"><i data-lucide="file-text" width="16" style="color:#dc2626;"></i><span class="small fw-semibold" style="color:#dc2626;">✓ ${geoName || 'document.pdf'}</span></div>`;
                    } else {
                        geoPreview.innerHTML = `<div class="text-success small fw-bold mt-2 p-2 rounded" style="background:rgba(22,163,74,0.07);border:1px solid rgba(22,163,74,0.2);">✓ ${geoName || 'file'}</div>`;
                    }
                    if (window.lucide) window.lucide.createIcons();
                } else if (geoName) {
                    geoPreview.innerHTML = `<div class="text-success small fw-bold mt-2">✓ ${geoName}</div>`;
                }
            }

            // ── Restore signatures onto canvas ──
            ['prep', 'noted'].forEach(prefix => {
                const sigData = d[`sig-${prefix}-data`];
                if (!sigData || !sigData.startsWith('data:')) return;
                const canvas = document.getElementById(`sig-canvas-${prefix}`);
                const hint   = document.getElementById(`sig-${prefix}-hint`);
                if (canvas) {
                    const img = new Image();
                    img.onload = () => {
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        if (hint) hint.textContent = '✓ Signature recorded';
                    };
                    img.src = sigData;
                }
            });

            // ── Restore Other Attachments rows ──
            const savedAtts = Array.isArray(d['_attachments']) ? d['_attachments'] : [];
            const attList = document.getElementById('other-attachments-list');
            if (attList && savedAtts.length > 0) {
                attList.innerHTML = '';
                savedAtts.forEach((att, i) => {
                    if (!att.dataUri && !att.fileName && !att.desc) return;
                    const uidBase = `att-restore-${i}`;
                    const row = document.createElement('div');
                    row.className = 'attachment-row d-flex align-items-start gap-2 mb-3';
                    row.dataset.index = i;
                    const borderColor = att.fileName ? '#22c55e' : '#cbd5e1';
                    const bgColor = att.fileName ? 'rgba(22,163,74,0.05)' : '#f8fafc';
                    row.innerHTML = `
                        <div style="flex:1;">
                            <input type="text" class="form-control form-control-sm mb-2" id="${uidBase}-desc" placeholder="Document title / description" style="border-radius:8px;font-size:0.83rem;" value="${(att.desc||'').replace(/"/g,'&quot;')}">
                            <div class="att-drop-zone d-flex align-items-center gap-2 p-2 rounded" id="${uidBase}-zone" tabindex="0" style="border:1.5px dashed ${borderColor};cursor:pointer;background:${bgColor};border-radius:8px;">
                                <i data-lucide="upload-cloud" width="18" style="color:#94a3b8;flex-shrink:0;"></i>
                                <span class="small text-secondary" id="${uidBase}-label">${att.fileName ? 'File attached' : 'Click or drag to attach file'}</span>
                            </div>
                            <input type="file" id="${uidBase}-file" style="display:none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.shp,.zip">
                            <div id="${uidBase}-preview" class="mt-1"></div>
                        </div>
                        <button type="button" class="att-remove-btn btn btn-sm btn-outline-danger" style="border-radius:8px;padding:0.3rem 0.6rem;font-size:0.75rem;flex-shrink:0;margin-top:2px;" title="Remove">
                            <i data-lucide="x" width="14"></i>
                        </button>`;
                    attList.appendChild(row);
                    const previewEl = row.querySelector(`#${uidBase}-preview`);
                    if (previewEl && att.dataUri) {
                        previewEl.dataset.dataUri = att.dataUri;
                        previewEl.dataset.fileName = att.fileName || '';
                        previewEl.dataset.mimeType = att.mimeType || '';
                        const mime = att.mimeType || '';
                        if (mime.startsWith('image/')) {
                            previewEl.innerHTML = `<div class="d-flex align-items-center gap-2 mt-1"><img src="${att.dataUri}" style="height:44px;width:44px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;"><span class="small text-success fw-semibold">✓ ${att.fileName}</span></div>`;
                        } else if (mime === 'application/pdf') {
                            previewEl.innerHTML = `<div class="d-flex align-items-center gap-2 mt-1 p-2 rounded" style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);"><i data-lucide="file-text" width="16" style="color:#dc2626;"></i><span class="small fw-semibold" style="color:#dc2626;">✓ ${att.fileName}</span></div>`;
                        } else if (att.fileName) {
                            previewEl.innerHTML = `<div class="d-flex align-items-center gap-2 mt-1 p-2 rounded" style="background:rgba(22,163,74,0.07);border:1px solid rgba(22,163,74,0.2);"><i data-lucide="file" width="16" style="color:#22c55e;"></i><span class="small text-success fw-semibold">✓ ${att.fileName}</span></div>`;
                        }
                    } else if (previewEl && att.fileName) {
                        previewEl.dataset.fileName = att.fileName;
                        previewEl.innerHTML = `<div class="small text-success fw-bold mt-1">✓ ${att.fileName}</div>`;
                    }
                    row.querySelector('.att-remove-btn').addEventListener('click', () => row.remove());
                });
                if (window.lucide) window.lucide.createIcons();
            }

            // ── Restore Implementation Schedule rows ──
            const savedSchedule = Array.isArray(d['_impl_schedule']) ? d['_impl_schedule'] : [];
            const implBody = document.getElementById('impl-schedule-body');
            if (implBody && savedSchedule.length > 0) {
                implBody.innerHTML = '';
                savedSchedule.forEach((rowData, i) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><input type="text" class="form-control form-control-sm" placeholder="Year" data-required value="${(rowData.year||'').replace(/"/g,'&quot;')}"><div class="invalid-feedback">Year is required.</div></td>
                        <td><textarea class="form-control form-control-sm" rows="2" placeholder="Physical Target" data-required>${rowData.target||''}</textarea><div class="invalid-feedback">Physical target is required.</div></td>
                        <td><input type="text" class="form-control form-control-sm" placeholder="Indicator" data-required value="${(rowData.indicator||'').replace(/"/g,'&quot;')}"><div class="invalid-feedback">Indicator is required.</div></td>
                        <td><input type="number" class="form-control form-control-sm" placeholder="0.00" min="0" step="0.01" data-required value="${rowData.amount||''}" onkeydown="if(['e','E','+','-'].includes(event.key)) event.preventDefault();"><div class="invalid-feedback">Amount is required.</div></td>
                        <td class="text-center align-middle">${i > 0 ? '<button type="button" class="btn btn-sm text-danger p-0 border-0 impl-del-row" title="Remove row" style="font-size:1rem;line-height:1;">&times;</button>' : ''}</td>`;
                    implBody.appendChild(tr);
                });
            }
            // ── Restore ALL upload-zone panels (SP, SB, Letter, BOR, DED, Env, Consult, HGDG, etc.) ──
            // Wait one tick so checkbox change events have had time to call _createUploadZone
            await new Promise(r => setTimeout(r, 120));

            // Build label → panelId map from every input that has data-upload-label + data-upload-panel
            const uploadLabelMap = {};
            document.querySelectorAll('[data-upload-label][data-upload-panel]').forEach(el => {
                const lbl = el.dataset.uploadLabel;
                const pid = el.dataset.uploadPanel;
                if (lbl && pid) uploadLabelMap[lbl] = pid;
            });

            const allAtts = Array.isArray(d['_attachments']) ? d['_attachments'] : [];
            for (const att of allAtts) {
                if (!att.desc || (!att.dataUri && !att.fileName)) continue;
                const panelId = uploadLabelMap[att.desc];
                if (!panelId) continue;   // not an upload-zone attachment
                const panel = document.getElementById(panelId);
                if (!panel) continue;

                // ── Restore dataset so _getFormData() re-captures it on next save ──
                if (att.dataUri)  panel.dataset.attachDataUri  = att.dataUri;
                if (att.fileName) panel.dataset.attachFileName = att.fileName;
                if (att.mimeType) panel.dataset.attachMimeType = att.mimeType;
                panel.dataset.attachLabel = att.desc;

                // ── Special: DED panel (bespoke HTML structure, no .upload-accordion) ──
                if (panelId === 'ded-upload-panel') {
                    panel.style.display = 'block';
                    const dedDz = document.getElementById('ded-dropzone');
                    const dedName = document.getElementById('ded-file-name');
                    if (dedDz) { dedDz.style.borderColor = '#15803d'; dedDz.style.background = 'rgba(22,163,74,0.04)'; }
                    if (dedName) {
                        dedName.textContent = '✓ ' + att.fileName;
                        dedName.style.display = 'block';
                        dedName.style.color = '#15803d';
                    }
                    continue;
                }

                // ── Standard _createUploadZone panel ──
                // Make the panel itself visible (it may be hidden if parent toggle is off)
                panel.style.display = 'block';

                const acc       = panel.querySelector('.upload-accordion');
                if (!acc) continue;

                const labelEl   = acc.querySelector('.upload-toggle-label');
                const changeBtn = acc.querySelector('.upload-change-btn');
                const arrowEl   = acc.querySelector('.upload-arrow');
                const bodyEl    = acc.querySelector('.upload-body');
                const dz        = acc.querySelector('.drop-zone');
                const listEl    = acc.querySelector('[id$="-list"]');
                const toggleEl  = acc.querySelector('.upload-toggle');

                // Update label → filename in green
                if (labelEl) { labelEl.textContent = att.fileName || att.desc; labelEl.style.color = '#15803d'; }
                // Show Change button
                if (changeBtn) changeBtn.style.display = 'inline-block';
                // Arrow in green
                if (arrowEl) { arrowEl.setAttribute('data-lucide', 'chevron-down'); arrowEl.style.color = '#15803d'; }
                // Update toggle title
                if (toggleEl) toggleEl.title = "Click to collapse  ·  Use 'Change' to replace the file";
                // Hide dropzone; show filename row
                if (dz) dz.style.display = 'none';
                if (listEl) {
                    listEl.innerHTML = `<span style="color:#15803d;">&#10003; ${att.fileName || att.desc}</span>`;
                    listEl.style.display = 'block';
                }
                // Keep body closed (collapsed) by default after restore
                if (bodyEl) bodyEl.style.display = 'none';
                if (arrowEl) arrowEl.style.transform = 'rotate(0deg)';
                if (toggleEl) toggleEl.style.background = '#f8fafc';
            }
            if (window.lucide) window.lucide.createIcons();

        } catch (e) {
            console.error('[cpp-form] Error loading submission data:', e);
        }
    };

    function _showFinalReview(data = null) {
        const container = document.getElementById('cpp-steps-container');
        if (!container) return;

        // Hide stepper with !important — its CSS uses display:flex !important
        const stepper = document.getElementById('cppStepper');
        if (stepper) stepper.style.setProperty('display', 'none', 'important');

        // Hide step counter and nav bar
        const stepCounter = document.getElementById('cppStepCounter');
        if (stepCounter) stepCounter.style.setProperty('display', 'none', 'important');
        const nav = document.querySelector('.cpp-nav');
        if (nav) nav.style.setProperty('display', 'none', 'important');

        // Hide the back button
        const backBtn = document.querySelector('.btn-back-dash');
        if (backBtn) backBtn.style.setProperty('display', 'none', 'important');

        // Hide the entire floating actions panel (Draft / Review / Approved buttons)
        const floatActions = document.getElementById('cppFloatActions');
        if (floatActions) floatActions.style.setProperty('display', 'none', 'important');

        // Helper to get val from raw event data OR DOM
        const g = id => {
            if (data && data.formData) {
                const val = data.formData[id];
                // If the stored value is genuinely empty, try reading the live DOM as fallback
                if (val === undefined || val === null || val === '') {
                    const liveEl = document.getElementById(id);
                    const liveVal = liveEl?.value?.trim();
                    return liveVal || '\u2014';
                }
                return String(val);
            }
            const el = document.getElementById(id);
            return (el?.value.trim() || '\u2014');
        };
        // Helper specifically for booleans (checkbox state)
        const gBool = id => {
            if (data && data.formData) return data.formData[id] === true;
            return document.getElementById(id)?.checked || false;
        };
        // Helper for arrays (project-type)
        const gArr = id => {
            if (data && data.formData) {
                const val = data.formData[id];
                return Array.isArray(val) ? val.join(', ') : (val || '—');
            }
            return [...document.querySelectorAll(`input[name="${id}"]:checked`)].map(c => c.value).join(', ') || '—';
        };
        // Helper for sig image (returns empty string if absent, not '—')
        const gSig = id => {
            if (data && data.formData) return data.formData[id] || '';
            return '';
        };

        const renderPage = (num, content) => `
            <div class="page-container mb-4" style="background:#fff; padding:1.25in; box-shadow:0 0 10px rgba(0,0,0,0.1); min-height:10.5in; position:relative;">
                 ${num === 1 ? `
                 <div class="page-header d-flex justify-content-between border-bottom pb-2 mb-3">
                      <div class="d-flex align-items-center gap-2">
                           <img src="assets/images/rdc.png" style="width:40px;height:40px;" onerror="this.src='../assets/images/rdc.png'">
                           <img src="assets/images/rnp.png" style="width:40px;height:40px;" onerror="this.src='../assets/images/rnp.png'">
                           <div style="font-size:0.6rem; font-weight:700; line-height:1.1;">
                                REPUBLIC OF THE PHILIPPINES<br>
                                <span style="color:#108543;">REGIONAL DEVELOPMENT COUNCIL</span><br>
                                <span style="color:#154A9A;">BICOL REGION</span>
                           </div>
                      </div>
                      <div class="text-end" style="font-size:0.5rem; color:#64748b;">
                           <div>FM-PDI-01 | CPP Form | Rev 00</div>
                           <div class="fw-bold text-dark">Page ${num} of 5</div>
                      </div>
                 </div>` : `
                 <div class="d-flex justify-content-end border-bottom pb-1 mb-3">
                      <div class="text-end" style="font-size:0.5rem; color:#64748b;">
                           <div>FM-PDI-01 | CPP Form | Rev 00</div>
                           <div class="fw-bold text-dark">Page ${num} of 5</div>
                      </div>
                 </div>`}
                 ${content}
                 <div style="position:absolute; bottom:0.5in; right:1.25in; font-size:0.5rem; color:#94a3b8;">RPTS Generated</div>
            </div>
        `;

        const p1 = renderPage(1, `
            <div style="background:#154A9A; color:#fff; text-align:center; padding:8px; font-weight:bold; font-size:1rem; margin-bottom:20px;">COMPREHENSIVE PROJECT PROFILE</div>
            <div class="border border-dark p-2 mb-4">
                 <div class="row g-2">
                      <div class="col-12"><strong>Agency:</strong> <div class="border-bottom d-inline-block" style="width:85%;">${g('f-agency')}</div></div>
                      <div class="col-6"><strong>Sector:</strong> <div class="border-bottom d-inline-block" style="width:70%;">${g('f-sector')}</div></div>
                      <div class="col-6"><strong>Sub-Sector:</strong> <div class="border-bottom d-inline-block" style="width:65%;">${g('f-sub-sector')}</div></div>
                 </div>
            </div>
            <div class="fw-bold mb-1">I. PROJECT INFORMATION</div>
            <div class="ps-3">
                 <div class="mb-3">1. Project Title: <div class="border p-2 fw-bold" style="background:#f8fafc;">${g('f-title')}</div></div>
                 <div class="row mb-3">
                      <div class="col-6">2. Project Type: <div class="border p-2 mt-1" style="font-size:0.75rem;">${gArr('project-type')}</div></div>
                      <div class="col-6">3. Components: <div class="border p-2 mt-1" style="min-height:80px; font-size:0.75rem;">${g('f-components')}</div></div>
                 </div>
                 <div class="mb-3">4. Location:
                      <div class="mt-1 px-2 small">
                           ${(function () {
                const cov = g('project-coverage');
                if (cov === 'Regionwide') {
                    return '<span class="badge text-bg-secondary">Regionwide</span>';
                } else if (cov === 'Inter-Province') {
                    const provs = g('f-provinces');
                    const tags = (provs && provs !== '\u2014')
                        ? provs.split(',').map(p => `<span style="background:#e0eaff;color:#154A9A;border:1px solid #b8d0ff;border-radius:6px;padding:1px 7px;font-size:0.75rem;font-weight:600;margin-right:3px;display:inline-block;">${p.trim()}</span>`).join('')
                        : '\u2014';
                    return `<span style="font-size:0.78rem;">Inter-Province: ${tags}</span>`;
                } else {
                    return `<div class="row">` +
                        `<div class="col-3 border-end">Prov: <strong>${g('f-province')}</strong></div>` +
                        `<div class="col-3 border-end">Dist: <strong>${g('f-district')}</strong></div>` +
                        `<div class="col-3 border-end">Mun: <strong>${g('f-municipality')}</strong></div>` +
                        `<div class="col-3">Brgy: <strong>${g('f-barangay')}</strong></div>` +
                        `</div>`;
                }
            })()}
                      </div>
                 </div>
            </div>
            <div class="fw-bold mt-4 mb-1">II. PROJECT STATUS</div>
            <div class="ps-3 border p-2">
                 <div class="row">
                      <div class="col-6"><strong>Current Stage:</strong><br>${g('project-status')}</div>
                      <div class="col-6 border-start ps-3"><strong>Preparatory Works:</strong><br>
                           <div class="small">Site is readily available: ${gBool('prep-site') ? '✓' : '×'}<br>No issue on right-of-way acquisition: ${gBool('prep-row') ? '✓' : '×'}<br>Detailed Engineering Design was prepared: ${gBool('prep-ded') ? '✓' : '×'}</div>
                      </div>
                 </div>
            </div>

             <div class="fw-bold mt-4 mb-1">III. ENDORSEMENTS</div>
             <div class="ps-3">
                  <div class="row g-2 mb-1" style="font-size:0.78rem;">
                       <div class="col-6">SP Resolution No.:<div class="border p-1 mt-1">${g('f-sp-res')}</div></div>
                       <div class="col-6">SP Date of Issuance:<div class="border p-1 mt-1">${g('f-sp-date')}</div></div>
                       <div class="col-6">SB Resolution No.:<div class="border p-1 mt-1">${g('f-sb-res')}</div></div>
                       <div class="col-6">SB Date of Issuance:<div class="border p-1 mt-1">${g('f-sb-date')}</div></div>
                       <div class="col-6">Letter Request (Ref/Desc):<div class="border p-1 mt-1">${g('f-letter-req')}</div></div>
                       <div class="col-6">Date of Transmittal:<div class="border p-1 mt-1">${g('f-letter-date')}</div></div>
                       <div class="col-6">BOR/BOT Resolution No.:<div class="border p-1 mt-1">${g('f-bor-res')}</div></div>
                       <div class="col-6">BOR/BOT Date of Issuance:<div class="border p-1 mt-1">${g('f-bor-date')}</div></div>
                  </div>
             </div>
        `);

        const p2 = renderPage(2, `
            <div class="fw-bold mb-1">IV. PROJECT JUSTIFICATION</div>
            <div class="ps-3">
                 <div class="mb-2">Alignment to SDGs:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-alignment')}</div></div>
                 <div class="mb-3">Alignment to RDP 2023&ndash;2028:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-rdp-alignment')}</div></div>
                 <div class="mb-3">1. Background / Demand for the Project:<div class="border p-3 mt-1" style="min-height:130px; font-size:0.8rem; text-align:justify;">${g('f-background')}</div></div>
                 <div class="mb-3">2. Goal:<div class="border p-2 mt-1" style="min-height:50px;">${g('f-goal')}</div></div>
                 <div class="mb-3">3. Purpose:<div class="border p-2 mt-1" style="min-height:50px;">${g('f-purpose')}</div></div>
                 <div class="mb-3">4. Project Outputs:<div class="border p-2 mt-1" style="min-height:50px; font-size:0.78rem; white-space:pre-line;">${g('f-outputs')}</div></div>
                 <div class="mb-3">5. Project Activities:<div class="border p-2 mt-1" style="min-height:40px; font-size:0.78rem;">${g('f-activities')}</div></div>
                 <div class="mb-3">6. Project Linkages:<div class="border p-2 mt-1" style="min-height:35px; font-size:0.78rem;">${g('f-linkages')}</div></div>
            </div>
        `);

        const p3 = renderPage(3, `
             <div class="fw-bold mb-1">V. PROJECT FINANCING</div>
             <div class="ps-3">
                  <div class="mb-2">1. Funding Requirement (PHP):<div class="border p-2 mt-1 fw-bold">&#8369;${g('f-total-cost')}</div></div>
                  <div class="mb-2">2. Project Financing / Source of Fund:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-funding-source')}</div></div>
                  <div class="mb-2">3. Counterpart Funding:<div class="border p-1 mt-1" style="font-size:0.78rem;">${g('f-counterpart-funding') || 'N/A'}</div></div>
             </div>
             <div class="fw-bold mt-3 mb-1">VI. PROJECT BENEFITS AND COSTS</div>
             <div class="ps-3">
                  <div class="mb-2">1. Beneficiaries:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-beneficiaries')}</div></div>
                  <div class="mb-2">2. Social Benefits:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-social-benefits')}</div></div>
                  <div class="mb-2">3. Economic Benefits:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-economic-benefits')}</div></div>
                  <div class="mb-2">4. Social Costs:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-social-costs')}</div></div>
                  <div class="mb-2">5. Economic Costs:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-economic-costs')}</div></div>
             </div>
             <div class="fw-bold mt-3 mb-1">VII. PROJECT IMPLEMENTATION</div>
             <div class="ps-3">
                  <div class="mb-2">1. Agencies Involved:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-agencies-involved')}</div></div>
                   <div class="mb-2">2. Implementation Schedule:
                        <div class="mt-1" style="overflow-x:auto;">
                             <table class="table table-bordered table-sm" style="font-size:0.7rem;">
                                  <thead style="background:#f0f5ff;"><tr><th>Year</th><th>Physical Target</th><th>Indicator</th><th style="text-align:right;">Amount (₱)</th></tr></thead>
                                  <tbody>
                                  ${(()=>{
                                      const rows = (data&&data.formData&&Array.isArray(data.formData['_impl_schedule']))?data.formData['_impl_schedule']:[];
                                      if(!rows.length) return '<tr><td colspan="4" class="text-center text-muted">—</td></tr>';
                                      return rows.map(r=>`<tr><td>${r.year||'—'}</td><td>${r.target||'—'}</td><td>${r.indicator||'—'}</td><td style="text-align:right;">₱${parseFloat(r.amount||0).toLocaleString()}</td></tr>`).join('');
                                  })()}
                                  </tbody>
                             </table>
                        </div>
                   </div>
                  <div class="mb-2">3. Implementation Arrangement:<div class="border p-2 mt-1" style="font-size:0.78rem;white-space:pre-line;">${g('f-impl-arrangement')}</div></div>
                  <div class="mb-2">4. Environmental Clearance:
                       <div class="border p-2 mt-1 small">
                            <div class="mb-2" style="white-space:pre-line;">${g('f-env-clearance-desc')}</div>
                            ${(data && data.formData && data.formData['env-clearance-data']) ? `
                                <div class="d-flex align-items-center gap-2 mt-1 p-1 px-2 border rounded bg-light" style="font-size:0.7rem;">
                                    <i data-lucide="paperclip" width="12"></i>
                                    <span class="text-primary fw-bold">${data.formData['env-clearance-name'] || 'Clearance Document'}</span>
                                </div>
                            ` : ''}
                       </div>
                  </div>
                  <div class="mb-2">5. Social Acceptability:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-social-accept')}</div></div>
                  <div class="mb-1 small">Conducted public consultation?
                       <strong>${g('consultation-status') || '\u2014'}</strong>
                       ${g('consultation-status') === 'No' ? ' &mdash; Planned date: ' + (g('f-consult-planned-date') || '\u2014') : ''}
                       ${g('consultation-status') === 'Yes' ? ' &mdash; Date/s: ' + (g('f-consult-done-dates') || '\u2014') : ''}
                  </div>
                  <div class="mb-2">Highlights of Public Consultation:<div class="border p-1 mt-1 small">${g('f-consult-highlights')}</div></div>
                  <div class="mb-2">5.1 HGDG:<div class="border p-2 mt-1" style="font-size:0.78rem;">${g('f-hgdg')}</div></div>
             </div>
         `);

        const p4 = renderPage(4, `
             <div class="fw-bold mb-1">VIII. PROJECT LOGICAL FRAMEWORK</div>
             <div class="ps-1" style="overflow-x:auto;">
                  <table class="table table-sm table-bordered border-dark" style="font-size:0.65rem;min-width:600px;">
                       <thead class="table-light text-center fw-bold">
                            <tr>
                                 <th style="width:12%">Hierarchy</th>
                                 <th>Narrative Summary</th>
                                 <th>Objectively Verifiable Indicators</th>
                                 <th>Means of Verification</th>
                                 <th>Assumptions / Risks</th>
                            </tr>
                       </thead>
                       <tbody>
                            <tr>
                                 <td class="fw-bold text-center align-middle" style="background:#f8fafc;">Goal</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-goal-narrative')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-goal-indicators')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-goal-verification')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-goal-assumptions')}</td>
                            </tr>
                            <tr>
                                 <td class="fw-bold text-center align-middle" style="background:#f8fafc;">Purpose</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-purpose-narrative')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-purpose-indicators')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-purpose-verification')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-purpose-assumptions')}</td>
                            </tr>
                            <tr>
                                 <td class="fw-bold text-center align-middle" style="background:#f8fafc;">Outputs</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-outputs-narrative')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-outputs-indicators')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-outputs-verification')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-outputs-assumptions')}</td>
                            </tr>
                            <tr>
                                 <td class="fw-bold text-center align-middle" style="background:#f8fafc;">Inputs / Activities</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-inputs-narrative')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-inputs-indicators')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-inputs-verification')}</td>
                                 <td class="p-1" style="white-space:pre-line;">${g('lf-inputs-assumptions')}</td>
                            </tr>
                       </tbody>
                  </table>
             </div>
         `);

        const p5 = renderPage(5, `
            <div class="fw-bold mb-1 mt-2">IX. GEOTAGGED PHOTO / LOCATION MAP</div>
            <div class="ps-3 mb-4">
                 <div class="border p-2 mt-1 d-flex flex-column align-items-center justify-content-center" style="min-height: 150px; background: #f8fafc;">
                      ${(data && data.formData && data.formData['geo-photo-data']) ?
                `<img src="${data.formData['geo-photo-data']}" style="max-height:180px; max-width:100%; object-fit:contain;">` :
                '<span class="small text-muted fst-italic">No photo provided</span>'}
                      ${(data && data.formData && data.formData['geo-photo-name']) ?
                `<div class="small mt-2 fw-semibold text-secondary">${data.formData['geo-photo-name']}</div>` : ''}
                 </div>
            </div>

            <div class="fw-bold mb-1">X. GEOLOCATION COORDINATES</div>
            <div class="ps-3 mb-5">
                 <div class="border mt-1">
                      <div class="row g-0 border-bottom text-center small fw-bold" style="background:#f0f5ff;">
                           <div class="col-6 py-1 border-end">Beginning / Point 1</div>
                           <div class="col-6 py-1">End / Point 2</div>
                      </div>
                      <div class="row g-0 text-center small">
                           <div class="col-6 py-2 border-end">
                                Lat: <span class="fw-medium">${g('f-geo-start-lat')}</span><br>
                                Lng: <span class="fw-medium">${g('f-geo-start-lng')}</span>
                           </div>
                           <div class="col-6 py-2">
                                Lat: <span class="fw-medium">${g('f-geo-end-lat')}</span><br>
                                Lng: <span class="fw-medium">${g('f-geo-end-lng')}</span>
                           </div>
                      </div>
                 </div>
            </div>

            <div class="row g-5 mt-4">
                 <div class="col-6 text-center">
                      <div class="fw-bold small mb-2 text-start">Prepared By:</div>
                      <div style="height:80px; display:flex; align-items:center; justify-content:center; margin-bottom:-15px;">
                           ${gSig('sig-prep-data') ? `<img src="${gSig('sig-prep-data')}" style="max-height:80px; max-width:200px;">` : '<div style="height:40px;"></div>'}
                      </div>
                      <div class="border-bottom fw-bold" style="font-size:1.1rem;">${g('f-prep-name')}</div>
                      <div class="small text-muted">${g('f-prep-position')}</div>
                      <div class="small mt-1" style="font-size:0.72rem; color:#334155;">${g('f-prep-date')}</div>
                 </div>
                 <div class="col-6 text-center">
                      <div class="fw-bold small mb-2 text-start">Noted By:</div>
                      <div style="height:80px; display:flex; align-items:center; justify-content:center; margin-bottom:-15px;">
                           ${gSig('sig-noted-data') ? `<img src="${gSig('sig-noted-data')}" style="max-height:80px; max-width:200px;">` : '<div style="height:40px;"></div>'}
                      </div>
                      <div class="border-bottom fw-bold" style="font-size:1.1rem;">${g('f-noted-name')}</div>
                      <div class="small text-muted">${g('f-noted-position')}</div>
                      <div class="small mt-1" style="font-size:0.72rem; color:#334155;">${g('f-noted-date')}</div>
                 </div>
            </div>

            <div class="mt-5 pt-3 border-top">
                 <div class="fw-bold small mb-2">Certification:</div>
                 <p style="font-size:0.75rem; color:#475569; line-height:1.6; text-align:justify;">I certify that all information provided in this Comprehensive Project Profile is true and accurate to the best of my knowledge, and that this submission is made on behalf of the implementing agency.</p>
                 <div class="d-flex align-items-center gap-2 text-success fw-bold small">
                      <i data-lucide="shield-check" width="16"></i> Verified Submission ✓
                 </div>
            </div>
        `);

        container.innerHTML = `
            <div id="cpp-final-wrapper" style="max-width:950px; margin:0 auto; padding-bottom:5rem;">
                <div class="alert alert-primary d-flex align-items-center justify-content-between mb-4 no-print shadow-sm" style="border-radius:12px;">
                    <div><i data-lucide="${data ? 'eye' : 'check-circle'}" class="me-2"></i><strong>${data ? 'Archive View' : 'Submission Success!'}</strong></div>
                    <div class="d-flex gap-2">
                        ${!data ? `<button class="btn btn-navy btn-sm" onclick="sessionStorage.removeItem('cpp_edit_id'); const c=document.getElementById('cpp-steps-container'); if(c){c.innerHTML=''; delete c.dataset.loaded;} window.initCppForm();"><i data-lucide="plus" class="me-1"></i>New</button>` : ''}
                        <button class="btn btn-success btn-sm px-4" onclick="window.print()" title="Use the browser's native 'Save as PDF' via Print for 100% accurate formatting"><i data-lucide="printer" class="me-1"></i>Print / Save PDF</button>
                    </div>
                </div>
                <div id="printable-cpp">${p1}${p2}${p3}${p4}${p5}</div>

                ${''}
            </div>
            <style>
                @media print {
                    @page { margin: 0; size: letter portrait; }
                    /* Hide all dashboard chrome */
                    #sidebarMenu, #topnav-container, .no-print, .btn-back-dash,
                    header, footer, .navbar, #cppDraftBtn,
                    .cpp-stepper, .cpp-nav, .cpp-alert, .cpp-badge,
                    #cppBackSubmissions, #cpp-form > .d-flex.justify-content-between,
                    #cpp-form > h2, #cpp-form > .card > .card-body > .cpp-nav,
                    .card-body > .cpp-nav { display: none !important; }
                    /* Reset all layout to full width */
                    body { background: #fff !important; padding: 0 !important; margin: 0 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                    .main, #cpp-form, .container-fluid { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
                    /* Only print the CPP pages, hide surrounding card shell */
                    .card { border: none !important; box-shadow: none !important; }
                    .card-body { padding: 0 !important; }
                    /* Each page: full letter page, break before except first */
                    .page-container { 
                        margin: 0 auto !important; 
                        box-shadow: none !important; 
                        page-break-before: always; 
                        break-before: page; 
                        padding: 0.5in 0.6in !important; 
                        width: 100% !important; 
                        min-height: 9.5in !important; 
                        height: auto !important; 
                        overflow: visible !important; 
                        border: none !important; 
                        box-sizing: border-box !important; 
                        position: relative; 
                        background: #fff !important; 
                    }
                    .page-container:first-child { page-break-before: avoid; break-before: avoid; }
                    /* Remove after-breaks to prevent blank pages */
                    .page-container { page-break-after: auto !important; break-after: auto !important; }
                    #printable-cpp { margin: 0 !important; width: 100% !important; background: #fff !important; }
                    #cpp-final-wrapper { max-width: 100% !important; padding: 0 !important; }
                    .table { border-collapse: collapse !important; }
                    .table td, .table th { background-color: transparent !important; }
                }
            </style>
        `;
        if (window.lucide) window.lucide.createIcons();
        window.scrollTo({ top: 0, behavior: 'smooth' });

        const viewActions = document.getElementById('cppViewActions');

        // ── Wire PDIPB floating action buttons (Integrated into Archive View Actions) ─
        const pdipbSid = sessionStorage.getItem('cpp_pdipb_view_sid');
        if (pdipbSid && viewActions) {
            sessionStorage.removeItem('cpp_pdipb_view_sid');
            viewActions.style.setProperty('display', 'flex', 'important');
            viewActions.querySelectorAll('.pdipb-integrated-btn').forEach(b => b.remove());

            const agencyDraftBar = document.getElementById('cppFloatActions');
            if (agencyDraftBar) agencyDraftBar.style.setProperty('display', 'none', 'important');

            const st = data?.submissionStatus || data?.status || '';
            const isApproved = st === 'Approved';
            
            const btnComplete = document.createElement('button');
            btnComplete.type = 'button';
            btnComplete.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
            btnComplete.disabled = isApproved;
            btnComplete.style.cssText = `background:#16a34a;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(22,163,74,0.3);transition:all 0.2s;`;
            btnComplete.innerHTML = `<i data-lucide="check-circle" width="16"></i>Mark as Complete`;

            const btnFeedback = document.createElement('button');
            btnFeedback.type = 'button';
            btnFeedback.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
            btnFeedback.disabled = isApproved;
            btnFeedback.style.cssText = `background:#ef4444;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(239,68,68,0.3);transition:all 0.2s;`;
            btnFeedback.innerHTML = `<i data-lucide="message-circle" width="16"></i>Send Feedback`;

            viewActions.prepend(btnFeedback);
            viewActions.prepend(btnComplete);
            
            if (window.lucide) window.lucide.createIcons();

            localforage.getItem('cpp_submissions').then(allSubs => {
                const sub = (allSubs || []).find(s => s.id === pdipbSid);
                if (!sub) return;
                const title = sub.title || sub.formData?.['f-title'] || 'this submission';
                const agency = sub.agency || sub.formData?.['f-agency'] || '—';

                btnComplete.addEventListener('click', async (e) => {
                    e.preventDefault();
                    _showPdipbCteModal(title, agency, pdipbSid, async (formData) => {
                        const subsLatest = await localforage.getItem('cpp_submissions') || [];
                        const sUpdate = subsLatest.find(s => s.id === pdipbSid);
                        if (!sUpdate) return;
                        sUpdate.submissionStatus = 'Validated';
                        sUpdate.reviewedAt = new Date().toISOString();
                        await localforage.setItem('cpp_submissions', subsLatest);

                        const cteRecords = await localforage.getItem('test_and_evaluation') || [];
                        const cteId = `CTE-${Date.now()}`;
                        cteRecords.unshift({
                            id: cteId,
                            submissionId: pdipbSid,
                            projectTitle: title,
                            implementingAgency: agency,
                            authorizedOfficial: formData.authorizedOfficial,
                            dateCreated: new Date().toISOString(),
                            ...formData
                        });
                        await localforage.setItem('test_and_evaluation', cteRecords);

                        const referrals = await localforage.getItem('project_referrals') || [];
                        const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
                        referrals.unshift({
                            id: `REF-${Date.now()}`,
                            submissionId: pdipbSid,
                            projectTitle: title,
                            agency: agency,
                            referrerName: currentUser.name || currentUser.email || 'PDIPB Staff',
                            referrerRole: 'PDIPB Staff',
                            referredToDivision: 'PDIPBD',
                            referralDate: new Date().toISOString(),
                            referralNotes: 'Validated by PDIPB staff — Completeness Test (Form CTE-01) finalized.',
                            status: 'Validated',
                            cteId: cteId
                        });
                        await localforage.setItem('project_referrals', referrals);

                        if (window.showSimpleAlert) window.showSimpleAlert(`"${title}" successfully validated.`, 'success');
                        if (window.switchPage) window.switchPage('test-and-evaluation');
                    });
                });

                btnFeedback.addEventListener('click', (e) => {
                    e.preventDefault();
                    _showPdipbFeedbackModal(title, async (msg) => {
                        const subsLatest = await localforage.getItem('cpp_submissions') || [];
                        const sUpdate = subsLatest.find(s => s.id === pdipbSid);
                        if (!sUpdate) return;
                        sUpdate.submissionStatus = 'Incomplete';
                        sUpdate.pmedFeedback = msg; 
                        sUpdate.reviewedAt = new Date().toISOString();
                        await localforage.setItem('cpp_submissions', subsLatest);
                        if (window.showSimpleAlert) window.showSimpleAlert(`Feedback sent for "${title}".`, 'warning');
                        if (window.switchPage) window.switchPage('test-and-evaluation');
                    });
                });
            });
        }

        // ── Wire PDIPB Revision floating action button ───────────────────────
        const revisionSid = sessionStorage.getItem('cpp_pdipb_revision_view_sid');
        if (revisionSid && viewActions) {
            sessionStorage.removeItem('cpp_pdipb_revision_view_sid');
            viewActions.style.setProperty('display', 'flex', 'important');
            viewActions.querySelectorAll('.pdipb-integrated-btn').forEach(b => b.remove());

            const agencyDraftBar = document.getElementById('cppFloatActions');
            if (agencyDraftBar) agencyDraftBar.style.setProperty('display', 'none', 'important');

            const _normRev = x => String(x || '').trim().toLowerCase();
            const rdcStages = [data?.stage, data?.cppStage, data?.projectStage].map(_normRev);
            const isRdcStage = rdcStages.some(s => s === 'rdc');
            const revStatuses = [data?.submissionStatus, data?.status, data?.projectStatus, data?.cppStatus].map(_normRev);
            const isRevisionWorkflow = revStatuses.some(s => ['revised', 'resubmitted', 'for revision'].includes(s));
            /** Revised submissions at RDC: approve at RDC, not Sectoral Presentation */
            const isRdcRevisionReview = isRdcStage && isRevisionWorkflow;

            const btnComment = document.createElement('button');
            btnComment.type = 'button';
            btnComment.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
            btnComment.style.cssText = `background:#64748b;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(100,116,139,0.3);transition:all 0.2s;`;
            btnComment.innerHTML = `<i data-lucide="message-circle" width="16"></i>Comments`;

            btnComment.addEventListener('click', async (e) => {
                e.preventDefault();
                _showPdipbFindingsModal(revisionSid);
            });

            if (isRdcRevisionReview) {
                const btnApprove = document.createElement('button');
                btnApprove.type = 'button';
                btnApprove.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
                btnApprove.style.cssText = `background:#16a34a;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(22,163,74,0.3);transition:all 0.2s;`;
                btnApprove.innerHTML = `<i data-lucide="check-circle" width="16"></i>RDC Approved`;

                viewActions.prepend(btnComment);
                viewActions.prepend(btnApprove);
                if (window.lucide) window.lucide.createIcons();

                btnApprove.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const title = data?.title || data?.formData?.['f-title'] || 'this project';
                    if (!confirm(`Mark "${title}" as RDC Approved?`)) return;

                    const subsLatest = await localforage.getItem('cpp_submissions') || [];
                    const sUpdate = subsLatest.find(s => s.id === revisionSid);
                    if (sUpdate) {
                        sUpdate.status = 'RDC Approved';
                        sUpdate.projectStatus = 'RDC Approved';
                        sUpdate.submissionStatus = 'RDC Approved';
                        sUpdate.cppStatus = 'RDC Approved';
                        sUpdate.stage = 'RDC';
                        sUpdate.cppStage = 'RDC';
                        sUpdate.projectStage = 'RDC';
                        sUpdate.referredToPdipb = false;
                        sUpdate.assignedStaffId = '';
                        sUpdate.assignedStaffName = '';
                        sUpdate.assignedStaffEmail = '';
                        sUpdate.referralNotes = '';
                        sUpdate.referralDate = '';
                        sUpdate.referralStage = '';
                        sUpdate.updatedAt = new Date().toISOString();
                        await localforage.setItem('cpp_submissions', subsLatest);

                        const referrals = await localforage.getItem('project_referrals') || [];
                        const ref = referrals.find(r => r.submissionId === revisionSid);
                        if (ref) {
                            ref.status = 'RDC Approved';
                            ref.stage = 'RDC';
                            ref.referredToPdipbd = false;
                            ref.updatedAt = new Date().toISOString();
                            await localforage.setItem('project_referrals', referrals);
                        }

                        if (window.showSimpleAlert) window.showSimpleAlert(`"${title}" marked as RDC Approved.`, 'success');
                        if (window.switchPage) window.switchPage('staff-dashboard');
                    }
                });
            } else {
                const hasSectoralCommitteeStage = ['Sectoral Committee'].includes(data?.stage) || ['Sectoral Committee'].includes(data?.cppStage) || ['Sectoral Presentation'].includes(data?.projectStatus) || ['Sectoral Presentation'].includes(data?.submissionStatus) || ['Sectoral Presentation'].includes(data?.status);

                const btnAction = document.createElement('button');
                btnAction.type = 'button';
                btnAction.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
                btnAction.style.cssText = `background:#3b82f6;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(59,130,246,0.3);transition:all 0.2s;`;
                btnAction.innerHTML = hasSectoralCommitteeStage
                    ? `<i data-lucide="arrow-right-circle" width="16"></i>RDC Presentation`
                    : `<i data-lucide="presentation" width="16"></i>Sectoral Presentation`;

                viewActions.prepend(btnAction);
                viewActions.prepend(btnComment);
                if (window.lucide) window.lucide.createIcons();

                btnAction.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const title = data?.title || data?.formData?.['f-title'] || 'this project';

                    if (hasSectoralCommitteeStage) {
                        if (!confirm(`Move "${title}" to RDC Presentation?`)) return;
                        const subsLatest = await localforage.getItem('cpp_submissions') || [];
                        const sUpdate = subsLatest.find(s => s.id === revisionSid);
                        if (sUpdate) {
                            sUpdate.status = 'RDC Presentation';
                            sUpdate.projectStatus = 'RDC Presentation';
                            sUpdate.submissionStatus = 'RDC Presentation';
                            sUpdate.stage = 'RDC';
                            sUpdate.cppStatus = 'RDC Presentation';
                            sUpdate.cppStage = 'RDC';
                            sUpdate.referredToPdipb = false;
                            sUpdate.assignedStaffId = '';
                            sUpdate.assignedStaffName = '';
                            sUpdate.assignedStaffEmail = '';
                            sUpdate.referralNotes = '';
                            sUpdate.referralDate = '';
                            sUpdate.referralStage = '';
                            sUpdate.updatedAt = new Date().toISOString();
                            await localforage.setItem('cpp_submissions', subsLatest);

                            const referrals = await localforage.getItem('project_referrals') || [];
                            const ref = referrals.find(r => r.submissionId === revisionSid);
                            if (ref) {
                                ref.status = 'RDC Presentation';
                                ref.stage = 'RDC';
                                ref.updatedAt = new Date().toISOString();
                                await localforage.setItem('project_referrals', referrals);
                            }

                            if (window.showSimpleAlert) window.showSimpleAlert(`"${title}" moved to RDC Presentation.`, 'success');
                            if (window.switchPage) window.switchPage('staff-dashboard');
                        }
                    } else {
                        if (!confirm(`Move "${title}" to Sectoral Presentation stage?`)) return;
                        const subsLatest = await localforage.getItem('cpp_submissions') || [];
                        const sUpdate = subsLatest.find(s => s.id === revisionSid);
                        if (sUpdate) {
                            sUpdate.status = 'Sectoral Presentation';
                            sUpdate.projectStatus = 'Sectoral Presentation';
                            sUpdate.submissionStatus = 'Sectoral Presentation';
                            sUpdate.stage = 'Sectoral Committee';
                            sUpdate.cppStatus = 'Sectoral Presentation';
                            sUpdate.cppStage = 'Sectoral Committee';
                            sUpdate.referredToPdipb = false; // Successfully processed
                            sUpdate.updatedAt = new Date().toISOString();
                            await localforage.setItem('cpp_submissions', subsLatest);

                            const referrals = await localforage.getItem('project_referrals') || [];
                            const ref = referrals.find(r => r.submissionId === revisionSid);
                            if (ref) {
                                ref.status = 'Sectoral Presentation';
                                ref.stage = 'Sectoral Committee';
                                ref.updatedAt = new Date().toISOString();
                                await localforage.setItem('project_referrals', referrals);
                            }

                            if (window.showSimpleAlert) window.showSimpleAlert(`"${title}" successfully moved to Sectoral Presentation.`, 'success');
                            if (window.switchPage) window.switchPage('staff-dashboard');
                        }
                    }
                });
            }
        }

        const sectoralReferralSid = sessionStorage.getItem('cpp_pdipb_sectoral_referral_view_sid');
        if (sectoralReferralSid && viewActions) {
            sessionStorage.removeItem('cpp_pdipb_sectoral_referral_view_sid');
            viewActions.style.setProperty('display', 'flex', 'important');
            viewActions.querySelectorAll('.pdipb-integrated-btn').forEach(b => b.remove());

            const agencyDraftBar = document.getElementById('cppFloatActions');
            if (agencyDraftBar) agencyDraftBar.style.setProperty('display', 'none', 'important');

            const btnRdc = document.createElement('button');
            btnRdc.type = 'button';
            btnRdc.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
            btnRdc.style.cssText = `background:#2563eb;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(37,99,235,0.3);transition:all 0.2s;`;
            btnRdc.innerHTML = `<i data-lucide="arrow-right-circle" width="16"></i>RDC Presentation`;

            const btnComment = document.createElement('button');
            btnComment.type = 'button';
            btnComment.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
            btnComment.style.cssText = `background:#64748b;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(100,116,139,0.3);transition:all 0.2s;`;
            btnComment.innerHTML = `<i data-lucide="message-circle" width="16"></i>Comments`;

            viewActions.prepend(btnComment);
            viewActions.prepend(btnRdc);
            if (window.lucide) window.lucide.createIcons();

            btnComment.addEventListener('click', async (e) => {
                e.preventDefault();
                _showPdipbFindingsModal(sectoralReferralSid);
            });

            btnRdc.addEventListener('click', async (e) => {
                e.preventDefault();
                const title = data?.title || data?.formData?.['f-title'] || 'this project';
                if (!confirm(`Move "${title}" to RDC Presentation?`)) return;

                const subsLatest = await localforage.getItem('cpp_submissions') || [];
                const sUpdate = subsLatest.find(s => s.id === sectoralReferralSid);
                if (sUpdate) {
                    sUpdate.status = 'RDC Presentation';
                    sUpdate.projectStatus = 'RDC Presentation';
                    sUpdate.submissionStatus = 'RDC Presentation';
                    sUpdate.stage = 'RDC';
                    sUpdate.cppStatus = 'RDC Presentation';
                    sUpdate.cppStage = 'RDC';
                    sUpdate.referredToPdipb = false;
                    sUpdate.assignedStaffId = '';
                    sUpdate.assignedStaffName = '';
                    sUpdate.assignedStaffEmail = '';
                    sUpdate.referralNotes = '';
                    sUpdate.referralDate = '';
                    sUpdate.referralStage = '';
                    sUpdate.updatedAt = new Date().toISOString();
                    await localforage.setItem('cpp_submissions', subsLatest);

                    const referrals = await localforage.getItem('project_referrals') || [];
                    const ref = referrals.find(r => r.submissionId === sectoralReferralSid);
                    if (ref) {
                        ref.status = 'RDC Presentation';
                        ref.stage = 'RDC';
                        ref.updatedAt = new Date().toISOString();
                        await localforage.setItem('project_referrals', referrals);
                    }

                    if (window.showSimpleAlert) window.showSimpleAlert(`"${title}" moved to RDC Presentation.`, 'success');
                    if (window.switchPage) window.switchPage('staff-dashboard');
                }
            });
        }

        const rdcReferralSid = sessionStorage.getItem('cpp_pdipb_rdc_referral_view_sid');
        if (rdcReferralSid && viewActions) {
            sessionStorage.removeItem('cpp_pdipb_rdc_referral_view_sid');
            viewActions.style.setProperty('display', 'flex', 'important');
            viewActions.querySelectorAll('.pdipb-integrated-btn').forEach(b => b.remove());

            const agencyDraftBar = document.getElementById('cppFloatActions');
            if (agencyDraftBar) agencyDraftBar.style.setProperty('display', 'none', 'important');

            const btnApprove = document.createElement('button');
            btnApprove.type = 'button';
            btnApprove.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
            btnApprove.style.cssText = `background:#16a34a;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(22,163,74,0.3);transition:all 0.2s;`;
            btnApprove.innerHTML = `<i data-lucide="check-circle" width="16"></i>RDC Approved`;

            const btnComment = document.createElement('button');
            btnComment.type = 'button';
            btnComment.className = 'btn fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 pdipb-integrated-btn';
            btnComment.style.cssText = `background:#64748b;color:#fff;border:none;font-size:0.82rem;padding:0.9rem 1rem;border-radius:14px;box-shadow:0 6px 20px rgba(100,116,139,0.3);transition:all 0.2s;`;
            btnComment.innerHTML = `<i data-lucide="message-circle" width="16"></i>Comments`;

            viewActions.prepend(btnComment);
            viewActions.prepend(btnApprove);
            if (window.lucide) window.lucide.createIcons();

            btnComment.addEventListener('click', async (e) => {
                e.preventDefault();
                _showPdipbFindingsModal(rdcReferralSid);
            });

            btnApprove.addEventListener('click', async (e) => {
                e.preventDefault();
                const title = data?.title || data?.formData?.['f-title'] || 'this project';
                if (!confirm(`Mark "${title}" as RDC Approved?`)) return;

                const subsLatest = await localforage.getItem('cpp_submissions') || [];
                const sUpdate = subsLatest.find(s => s.id === rdcReferralSid);
                if (sUpdate) {
                    sUpdate.status = 'RDC Approved';
                    sUpdate.projectStatus = 'RDC Approved';
                    sUpdate.submissionStatus = 'RDC Approved';
                    sUpdate.cppStatus = 'RDC Approved';
                    sUpdate.stage = 'RDC';
                    sUpdate.cppStage = 'RDC';
                    sUpdate.projectStage = 'RDC';
                    sUpdate.referredToPdipb = false;
                    sUpdate.assignedStaffId = '';
                    sUpdate.assignedStaffName = '';
                    sUpdate.assignedStaffEmail = '';
                    sUpdate.referralNotes = '';
                    sUpdate.referralDate = '';
                    sUpdate.referralStage = '';
                    sUpdate.updatedAt = new Date().toISOString();
                    await localforage.setItem('cpp_submissions', subsLatest);

                    const referrals = await localforage.getItem('project_referrals') || [];
                    const ref = referrals.find(r => r.submissionId === rdcReferralSid);
                    if (ref) {
                        ref.status = 'RDC Approved';
                        ref.stage = 'RDC';
                        ref.referredToPdipbd = false;
                        ref.updatedAt = new Date().toISOString();
                        await localforage.setItem('project_referrals', referrals);
                    }

                    if (window.showSimpleAlert) window.showSimpleAlert(`"${title}" marked as RDC Approved.`, 'success');
                    if (window.switchPage) window.switchPage('staff-dashboard');
                }
            });
        }
    }

    // ── PDIPB Findings Modal Logic (Integrated into CPP View) ────────────────
    async function _showPdipbFindingsModal(sid) {
        const container = document.getElementById('parFindingsContainer');
        if (!container) return;

        const allAssessments = await localforage.getItem('project_assessments') || [];
        let par = allAssessments.find(p => p.connectedCteId === sid);

        // If no PAR exists, create a dummy one to store findings
        if (!par) {
            const allSubs = await localforage.getItem('cpp_submissions') || [];
            const sub = allSubs.find(s => s.id === sid);
            par = {
                id: `PAR-${Date.now()}`,
                connectedCteId: sid,
                projectTitle: sub?.title || sub?.formData?.['f-title'] || 'Untitled',
                proponent: sub?.agency || sub?.formData?.['f-agency'] || '—',
                findings: [],
                status: 'Draft',
                datePrepared: new Date().toISOString()
            };
            allAssessments.push(par);
            await localforage.setItem('project_assessments', allAssessments);
        }

        container.innerHTML = '';
        const allCR = await localforage.getItem('comments_recommendations') || [];
        const linkedCR = allCR.find(cr => cr.connectedParId === par.id || cr.connectedCteId === sid);
        
        if (linkedCR?.formData?.projects?.[0]?.findingsList?.length > 0) {
            linkedCR.formData.projects[0].findingsList.forEach(f => _addPARFindingRowInternal(f.findings, f.recommendations));
        } else {
            _addPARFindingRowInternal();
        }

        const modal = new bootstrap.Modal(document.getElementById('parCommentsModal'));
        modal.show();

        // Wire modal actions
        const btnAdd = document.getElementById('btnAddFindingRow');
        const btnSave = document.getElementById('btnSaveCommentsOnly');
        const btnSubmit = document.getElementById('btnSubmitCommentsAgency');
        if (btnAdd) btnAdd.onclick = () => _addPARFindingRowInternal();
        if (btnSave) btnSave.onclick = async () => {
            const saved = await _savePARCommentsInternal(sid, par.id, false);
            if (saved) modal.hide();
        };
        if (btnSubmit) btnSubmit.onclick = async () => {
            const saved = await _savePARCommentsInternal(sid, par.id, true);
            if (saved) {
                modal.hide();
                if (window.switchPage) {
                    setTimeout(() => window.switchPage('staff-dashboard'), (window.showSimpleAlert || window.showToast) ? 650 : 0);
                }
            }
        };
    }

    function _addPARFindingRowInternal(findings = '', recomms = '') {
        const container = document.getElementById('parFindingsContainer');
        if (!container) return;

        const rowHtml = `
            <div class="row g-3 mb-3 par-findings-row border p-3 rounded bg-white shadow-sm position-relative mx-0" style="border-left: 4px solid #154A9A !important;">
                <button type="button" class="btn btn-link text-danger p-0 position-absolute btn-remove-par-finding" style="top: 0px; right: 8px; z-index: 5; text-decoration: none;">
                    <i data-lucide="x-circle" width="18"></i>
                </button>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.65rem;">Secretariat Findings</label>
                    <textarea class="form-control par-finding-item border-0 bg-light" rows="3" placeholder="Detail the technical observations...">${findings}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.65rem;">Secretariat Recommendations</label>
                    <textarea class="form-control par-recomm-item border-0 bg-light" rows="3" placeholder="Specify actions required...">${recomms}</textarea>
                </div>
            </div>
        `;
        const div = document.createElement('div');
        div.innerHTML = rowHtml.trim();
        const rowEl = div.firstChild;

        rowEl.querySelector('.btn-remove-par-finding').onclick = () => {
            if (container.querySelectorAll('.par-findings-row').length > 1) rowEl.remove();
        };

        container.appendChild(rowEl);
        if (window.lucide) window.lucide.createIcons({ target: rowEl });
    }

    async function _savePARCommentsInternal(sid, parId, isSubmitted = false) {
        const rows = document.querySelectorAll('.par-findings-row');
        const findingsList = Array.from(rows).map(r => ({
            findings: r.querySelector('.par-finding-item').value.trim(),
            recommendations: r.querySelector('.par-recomm-item').value.trim()
        })).filter(f => f.findings || f.recommendations);

        if (findingsList.length === 0) {
            if (window.showSimpleAlert) window.showSimpleAlert('Please provide at least one technical observation.', 'warning');
            return false;
        }

        const allCR = await localforage.getItem('comments_recommendations') || [];
        let cr = allCR.find(x => x.connectedParId === parId || x.connectedCteId === sid);
        
        const allSubs = await localforage.getItem('cpp_submissions') || [];
        const sub = allSubs.find(s => s.id === sid);

        if (!cr) {
            cr = {
                id: `CR-${Date.now()}`,
                connectedParId: parId,
                connectedCteId: sid,
                status: isSubmitted ? 'Submitted' : 'Draft',
                datePrepared: new Date().toISOString()
            };
            allCR.unshift(cr);
        }

        cr.formData = {
            projects: [{
                projectTitle: sub?.title || 'Untitled',
                agency: sub?.agency || '—',
                findingsList: findingsList
            }]
        };
        cr.lastSaved = new Date().toISOString();
        if (isSubmitted) {
            cr.status = 'Submitted';
        } else if (!cr.status) {
            cr.status = 'Draft';
        }
        await localforage.setItem('comments_recommendations', allCR);

        if (isSubmitted && sub) {
            const norm = x => String(x || '').trim().toLowerCase();
            const statuses = [sub.submissionStatus, sub.status, sub.projectStatus, sub.cppStatus].map(norm);
            // RDC Presentation referrals: keep stage as RDC when sending back for revision
            const isRdcPresentation = statuses.includes('rdc presentation');

            sub.status = 'For Revision';
            sub.projectStatus = 'For Revision';
            sub.submissionStatus = 'For Revision';
            sub.cppStatus = 'For Revision';
            if (isRdcPresentation) {
                sub.stage = 'RDC';
                sub.cppStage = 'RDC';
                sub.projectStage = 'RDC';
            } else {
                sub.stage = 'Sectoral Committee';
                sub.cppStage = 'Sectoral Committee';
            }
            sub.updatedAt = new Date().toISOString();
            await localforage.setItem('cpp_submissions', allSubs);

            const referrals = await localforage.getItem('project_referrals') || [];
            const ref = referrals.find(r => r.submissionId === sid || (sub.cteId && r.cteId === sub.cteId));
            if (ref) {
                ref.status = 'For Revision';
                ref.stage = isRdcPresentation ? 'RDC' : 'Sectoral Committee';
                ref.updatedAt = new Date().toISOString();
                await localforage.setItem('project_referrals', referrals);
            }
        }

        if (window.showSimpleAlert) {
            window.showSimpleAlert(
                isSubmitted ? 'Technical observations submitted successfully.' : 'Technical observations saved successfully.',
                'success'
            );
        }
        return true;
    }

    // ── PDIPB CTE Form Modal (Completeness Test) ──────────────────────────────
    window._showPdipbCteModal = _showPdipbCteModal;
    function _showPdipbCteModal(title, agency, sid, onComplete) {
        const existing = document.getElementById('pdipb-cte-checklist-modal');
        if (existing) {
            try { bootstrap.Modal.getInstance(existing)?.dispose(); } catch (_) {}
            existing.remove();
        }

        document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="pdipb-cte-checklist-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden" style="border-radius:18px;">
                    <div style="height:6px; background:linear-gradient(90deg, #154A9A, #1e6fd9);"></div>
                    <div class="modal-header border-0 pt-4 px-4 px-md-5 pb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1" style="font-size:0.7rem;">FORM-CTE-01</span>
                                <span class="text-muted small">Checklist for RDIP Inclusion</span>
                            </div>
                            <h4 class="modal-title fw-bold mb-0">Completeness Test and Validation</h4>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 px-md-5 pb-4 bg-light">
                        <form id="pdipb-cte-form" novalidate>
                            
                            <div class="card border-0 shadow-sm rounded-16 mb-4">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-primary mb-3 text-uppercase" style="font-size:0.8rem;letter-spacing:0.05em;">I. Project Details & Transmittal</h6>
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <label class="form-label small fw-semibold text-secondary">Program/Project Title</label>
                                            <input type="text" class="form-control rounded-12 bg-light border-0" value="${title}" readonly>
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label small fw-semibold text-secondary">Implementing Agency</label>
                                            <input type="text" class="form-control rounded-12 bg-light border-0" value="${agency}" readonly>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <label class="form-label small fw-semibold text-secondary">Authorized Official (Transmittal Signatory) <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control rounded-12" name="authorizedOfficial" placeholder="Name of Head of Agency/Official" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm rounded-16 mb-4">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold text-primary mb-4 text-uppercase" style="font-size:0.8rem;letter-spacing:0.05em;">II. Contact Persons and Details</h6>
                                    
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 rounded-16 h-100 p-4">
                                                <h6 class="fw-bold text-dark mb-3" style="font-size:0.9rem;">Director Level</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm rounded-8 border-0" name="dir_name" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Designation <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm rounded-8 border-0" name="dir_designation" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Office <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm rounded-8 border-0" name="dir_office" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Tel. No. <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm rounded-8 border-0" name="dir_tel" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Email Address <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control form-control-sm rounded-8 border-0" name="dir_email" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0 rounded-16 h-100 p-4">
                                                <h6 class="fw-bold text-dark mb-3" style="font-size:0.9rem;">Focal Technical Staff</h6>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm rounded-8 border-0" name="focal_name" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Designation <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm rounded-8 border-0" name="focal_designation" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Office <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm rounded-8 border-0" name="focal_office" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Tel. No. <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm rounded-8 border-0" name="focal_tel" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label small fw-semibold text-secondary mb-1">Email Address <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control form-control-sm rounded-8 border-0" name="focal_email" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start gap-2 px-2 text-muted small">
                                <i data-lucide="info" width="16" class="mt-1 text-primary flex-shrink-0"></i>
                                <p class="mb-0">Completing this form will mark the CPP submission as <strong>Approved</strong> and forward it to the <strong>Referral to Division</strong> queue automatically.</p>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer border-0 pt-3 pb-4 px-4 px-md-5 d-flex justify-content-end gap-2 bg-white">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-medium text-muted" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success text-white rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2" id="pdipb-cte-save-btn">
                            <i data-lucide="check-circle" width="16"></i>Complete & Save Validation
                        </button>
                    </div>
                </div>
            </div>
        </div>`);

        if (window.lucide) window.lucide.createIcons();
        const modalEl = document.getElementById('pdipb-cte-checklist-modal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        document.getElementById('pdipb-cte-save-btn').addEventListener('click', () => {
            const form = document.getElementById('pdipb-cte-form');
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            // Extract form data
            const fd = new FormData(form);
            const data = {};
            fd.forEach((val, key) => data[key] = val);

            const btn = document.getElementById('pdipb-cte-save-btn');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            btn.disabled = true;

            setTimeout(() => {
                modal.hide();
                onComplete(data);
            }, 500);
        });

        modalEl.addEventListener('hidden.bs.modal', () => {
            modalEl.remove();
        });
    }

    // ── PDIPB Feedback Modal (used from _showFinalReview floating buttons) ─────
    function _showPdipbFeedbackModal(title, onSend) {
        const existing = document.getElementById('pdipb-cpp-feedback-modal');
        if (existing) {
            try { bootstrap.Modal.getInstance(existing)?.dispose(); } catch (_) {}
            existing.remove();
        }
        document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="pdipb-cpp-feedback-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
                    <div class="modal-header border-0" style="background:#1e40af;color:#fff;padding:1.25rem 1.5rem;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex align-items-center justify-content-center rounded-2"
                                style="width:32px;height:32px;background:rgba(255,255,255,0.15);">
                                <i data-lucide="message-circle" width="15" class="text-white"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold mb-0" style="font-size:0.95rem;">Send Feedback</h6>
                                <p class="mb-0 text-white opacity-75" style="font-size:0.72rem;">Submission will be returned to agency for revision</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="px-4 py-3" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                            <div class="text-muted fw-bold text-uppercase mb-1" style="font-size:0.65rem;letter-spacing:.06em;">Submission</div>
                            <div class="fw-bold text-dark lh-sm" style="font-size:0.875rem;">${title}</div>
                        </div>
                        <div class="px-4 pt-3 pb-4">
                            <label class="small fw-semibold text-dark mb-2 d-block">
                                Feedback / Notes for Revision <span class="text-danger">*</span>
                            </label>
                            <textarea id="pdipb-cpp-feedback-text" rows="5"
                                class="form-control border-0 rounded-3"
                                style="background:#f1f5f9;resize:none;font-size:0.85rem;"
                                placeholder="Describe what needs to be revised, corrected, or clarified..."></textarea>
                            <div id="pdipb-cpp-feedback-err" class="text-danger small mt-1" style="display:none;">
                                Please enter feedback before sending.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top d-flex gap-2 justify-content-end"
                        style="padding:0.875rem 1.5rem;background:#f8fafc;">
                        <button type="button" class="btn btn-sm rounded-pill px-4 fw-medium"
                            style="background:#e2e8f0;color:#475569;border:none;" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm rounded-pill px-4 fw-semibold text-white"
                            id="pdipb-cpp-feedback-send" style="background:#1e40af;border:none;">
                            <i data-lucide="send" width="13" class="me-1"></i>Send Feedback
                        </button>
                    </div>
                </div>
            </div>
        </div>`);
        if (window.lucide) window.lucide.createIcons();
        const modalEl = document.getElementById('pdipb-cpp-feedback-modal');
        const modal   = new bootstrap.Modal(modalEl);
        modal.show();
        document.getElementById('pdipb-cpp-feedback-send').addEventListener('click', () => {
            const msg = document.getElementById('pdipb-cpp-feedback-text').value.trim();
            const err = document.getElementById('pdipb-cpp-feedback-err');
            if (!msg) { if (err) err.style.display = ''; return; }
            modal.hide();
            modalEl.addEventListener('hidden.bs.modal', () => {
                modalEl.remove();
                onSend(msg);
            }, { once: true });
        });
    }

    // Bake all live input/select/textarea values into DOM attributes so screenshot
    // libraries (html2canvas) and the browser print engine read the actual values,
    // not the stale HTML "value" attribute or nothing at all.
    function _bakeValuesIntoDOM(root) {
        root.querySelectorAll('input, textarea, select').forEach(el => {
            if (el.type === 'checkbox' || el.type === 'radio') {
                if (el.checked) el.setAttribute('checked', '');
                else el.removeAttribute('checked');
            } else {
                el.setAttribute('value', el.value);
                // Textareas have no value attribute — use textContent
                if (el.tagName === 'TEXTAREA') el.textContent = el.value;
            }
        });
    }

    // Wait for every <img> in `root` to finish loading (fixes async race condition)
    function _waitForImages(root) {
        const imgs = [...root.querySelectorAll('img')];
        return Promise.all(imgs.map(img =>
            img.complete
                ? Promise.resolve()
                : new Promise(resolve => {
                    img.onload = resolve;
                    img.onerror = resolve; // don't block on broken images
                })
        ));
    }

    window.saveCppAsPdf = async (btn) => {
        const wrap = document.getElementById('printable-cpp');
        if (!wrap) { alert('Nothing to export yet.'); return; }

        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span style="font-size:0.8rem;">Generating PDF...</span>';

        try {
            // Wait for all images (signatures, geo photo) to be fully loaded
            await _waitForImages(wrap);

            // Bake live values into DOM attributes so the renderer sees them
            _bakeValuesIntoDOM(wrap);

            const opt = {
                margin: 0, 
                filename: `CPP_Submission_${new Date().toISOString().slice(0, 10)}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2, 
                    useCORS: true, 
                    allowTaint: false, 
                    logging: false,
                    letterRendering: true
                },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
                pagebreak: { mode: 'css', before: '.page-container' }
            };

            await html2pdf().from(wrap).set(opt).save();
        } catch (e) {
            console.error('[cpp] PDF generation failed:', e);
            alert('Could not generate PDF. Try the Print button instead (Ctrl+P).');
        } finally {
            btn.disabled = false;
            btn.innerHTML = original;
            if (window.lucide) window.lucide.createIcons();
        }
    };

    // Browser Print path — bake values before the print dialog opens
    window.addEventListener('beforeprint', () => {
        const wrap = document.getElementById('printable-cpp');
        if (wrap) _bakeValuesIntoDOM(wrap);
    });


    /* ── Shared Upload Zone Utility ── */
    function _createUploadZone(panelId, label) {
        const panel = document.getElementById(panelId);
        if (!panel) return;
        const uniqueId = 'up-' + Math.random().toString(36).substr(2, 5);

        panel.innerHTML = `
            <div class="upload-accordion" style="
                border:1px solid #e2e8f0; border-radius:10px;
                margin-top:0.5rem; overflow:hidden;
            ">
                <!-- Toggle Header -->
                <div class="upload-toggle" style="
                    display:flex; align-items:center; gap:8px;
                    padding:8px 12px; cursor:pointer;
                    background:#f8fafc; user-select:none;
                    transition:background 0.15s;
                " title="Click to expand / collapse">
                    <!-- Change pill — hidden until a file is attached -->
                    <button type="button" class="upload-change-btn" title="Replace attached document"
                        style="display:none; flex-shrink:0; font-size:0.65rem; font-weight:700;
                               color:#154A9A; background:#e0eaff; border:1px solid #b8d0ff;
                               border-radius:20px; padding:2px 8px; cursor:pointer;
                               transition:background 0.15s; white-space:nowrap;"
                        onmouseover="this.style.background='#c7d9ff'"
                        onmouseout="this.style.background='#e0eaff'">
                        &#8631; Change
                    </button>
                    <div style="
                        width:26px; height:26px; flex-shrink:0;
                        background:rgba(21,74,154,0.1); border-radius:6px;
                        display:flex; align-items:center; justify-content:center;
                    ">
                        <i data-lucide="paperclip" width="13" style="color:#154A9A;"></i>
                    </div>
                    <span class="upload-toggle-label" style="
                        font-size:0.74rem; font-weight:600; color:#154A9A; flex:1;
                        white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
                    ">${label}</span>
                    <i class="upload-arrow" data-lucide="chevron-down" width="15"
                       style="color:#94a3b8; transition:transform 0.22s ease; flex-shrink:0;"></i>
                </div>

                <!-- Collapsible Dropzone Body -->
                <div class="upload-body" style="display:none; padding:8px; border-top:1px solid #e2e8f0; background:#fff;">
                    <div class="drop-zone" style="
                        border:2px dashed #154A9A; border-radius:8px;
                        background:rgba(21,74,154,0.03); cursor:pointer;
                        padding:14px; text-align:center; transition:background 0.2s;
                    ">
                        <div style="
                            width:30px; height:30px; margin:0 auto 6px;
                            background:rgba(21,74,154,0.1); border-radius:50%;
                            display:flex; align-items:center; justify-content:center;
                        ">
                            <i data-lucide="upload-cloud" width="14" style="color:#154A9A;"></i>
                        </div>
                        <p class="mb-0" style="font-size:0.72rem; font-weight:600; color:#154A9A;">
                            Click to browse or drag &amp; drop
                        </p>
                        <input type="file" id="${uniqueId}" hidden>
                    </div>
                    <div id="${uniqueId}-list" class="mt-1 small fw-bold p-2 rounded"
                         style="display:none; color:#15803d; background:rgba(22,163,74,0.05); border:1px solid rgba(22,163,74,0.2);">
                    </div>
                </div>
            </div>
        `;

        const toggleEl = panel.querySelector('.upload-toggle');
        const arrowEl = panel.querySelector('.upload-arrow');
        const labelEl = panel.querySelector('.upload-toggle-label');
        const bodyEl = panel.querySelector('.upload-body');
        const changeBtn = panel.querySelector('.upload-change-btn');
        const dz = panel.querySelector('.drop-zone');
        const inp = panel.querySelector(`#${uniqueId}`);
        const list = panel.querySelector(`#${uniqueId}-list`);

        // ── Accordion toggle ── (always collapsible, even after file attached)
        toggleEl.addEventListener('click', (e) => {
            // Don't trigger if the Change button was clicked
            if (changeBtn && (e.target === changeBtn || changeBtn.contains(e.target))) return;
            if (bodyEl.style.display === 'none') {
                bodyEl.style.display = 'block';
                arrowEl.style.transform = 'rotate(180deg)';
                toggleEl.style.background = '#eef3ff';
            } else {
                bodyEl.style.display = 'none';
                arrowEl.style.transform = 'rotate(0deg)';
                toggleEl.style.background = '#f8fafc';
            }
        });

        // Hover feedback
        toggleEl.addEventListener('mouseenter', () => {
            toggleEl.style.background = bodyEl.style.display === 'none' ? '#f0f5ff' : '#eef3ff';
        });
        toggleEl.addEventListener('mouseleave', () => {
            toggleEl.style.background = bodyEl.style.display === 'none' ? '#f8fafc' : '#eef3ff';
        });

        // ── Change button: re-open panel + re-show dropzone + trigger picker ──
        if (changeBtn) {
            changeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                // Re-show the dropzone so user can drag or click
                if (dz) dz.style.display = 'block';
                bodyEl.style.display = 'block';
                arrowEl.style.transform = 'rotate(180deg)';
                toggleEl.style.background = '#eef3ff';
                inp.click();
            });
        }

        // ── File picker ──
        if (dz && inp) {
            dz.addEventListener('click', () => inp.click());
            dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.style.background = 'rgba(21,74,154,0.07)'; });
            dz.addEventListener('dragleave', () => { dz.style.background = 'rgba(21,74,154,0.03)'; });
            dz.addEventListener('drop', (e) => {
                e.preventDefault();
                dz.style.background = 'rgba(21,74,154,0.03)';
                if (e.dataTransfer.files.length) _handleFile(e.dataTransfer.files[0]);
            });
            inp.addEventListener('change', () => {
                if (inp.files.length) _handleFile(inp.files[0]);
            });
        }

        function _handleFile(file) {
            // Hide the dropzone — Change button handles replacement from now on
            if (dz) dz.style.display = 'none';
            // Show the filename confirmation row
            list.innerHTML = `<span style="color:#15803d;">&#10003; ${file.name}</span>`;
            list.style.display = 'block';
            // Update header label to show filename in green
            labelEl.textContent = file.name;
            labelEl.style.color = '#15803d';
            // Show the Change pill button
            if (changeBtn) changeBtn.style.display = 'inline-block';
            // Keep arrow as chevron (panel stays collapsible)
            arrowEl.setAttribute('data-lucide', 'chevron-down');
            arrowEl.style.color = '#15803d';
            toggleEl.title = 'Click to collapse  ·  Use \'Change\' to replace the file';
            if (window.lucide) window.lucide.createIcons();
        }

        if (window.lucide) window.lucide.createIcons();
    }

    /* ── Helpers ── */
    function _initHelpers() {
        // Sector / Sub-Sector Dependency
        const sectorSelect = document.getElementById('f-sector');
        const subSectorSelect = document.getElementById('f-sub-sector');
        if (sectorSelect && subSectorSelect) {
            // Keep a master copy of the optgroups
            if (!subSectorSelect._masterGroups) {
                subSectorSelect._masterGroups = [...subSectorSelect.querySelectorAll('optgroup')];
                subSectorSelect._placeholder = subSectorSelect.options[0];
            }
            sectorSelect.addEventListener('change', () => {
                const sector = sectorSelect.value;
                subSectorSelect.innerHTML = '';
                subSectorSelect.appendChild(subSectorSelect._placeholder);
                if (sector) {
                    subSectorSelect.disabled = false;
                    const match = subSectorSelect._masterGroups.find(g => g.dataset.sector === sector);
                    if (match) {
                        // Append options directly without the group label
                        [...match.querySelectorAll('option')].forEach(opt => {
                            subSectorSelect.appendChild(opt.cloneNode(true));
                        });
                    }
                } else {
                    subSectorSelect.disabled = true;
                    subSectorSelect.value = '';
                }
            });
            // Trigger if already has value (e.g. edit mode)
            if (sectorSelect.value) {
                const val = subSectorSelect.value;
                sectorSelect.dispatchEvent(new Event('change'));
                subSectorSelect.value = val;
            }
        }

        ['f-start-date', 'f-end-date'].forEach(id => {
            const el = document.getElementById(id); if (el) el.addEventListener('change', () => {
                const s = document.getElementById('f-start-date')?.value;
                const e = document.getElementById('f-end-date')?.value;
                const dur = document.getElementById('f-duration');
                if (s && e && dur) { const m = Math.round((new Date(e) - new Date(s)) / (1000 * 60 * 60 * 24 * 30.4)); dur.value = m > 0 ? m : ''; }
            });
        });

        // --- PAGE 1: Endorsements ---

        ['f-sp-res', 'f-sb-res', 'f-letter-req', 'f-bor-res'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                const showAndInit = () => {
                    const panelId = el.dataset.uploadPanel;
                    const panel = document.getElementById(panelId);
                    if (panel) {
                        panel.style.display = 'block';
                        if (panel.children.length === 0) {
                            _createUploadZone(panelId, el.dataset.uploadLabel);
                        }
                    }
                };
                const hideIfEmpty = () => {
                    if (!el.value.trim()) {
                        const panel = document.getElementById(el.dataset.uploadPanel);
                        if (panel) panel.style.display = 'none';
                    }
                };
                el.addEventListener('focus', showAndInit);
                el.addEventListener('click', showAndInit);
                el.addEventListener('input', () => { showAndInit(); hideIfEmpty(); });
                if (el.value.trim()) showAndInit();
            }
        });

        // --- PAGE 1: DED Upload ---
        const ded = document.getElementById('prep-ded');
        if (ded) ded.addEventListener('change', () => {
            const panel = document.getElementById('ded-upload-panel');
            if (panel) panel.style.display = ded.checked ? 'block' : 'none';
        });
        // Connect the existing DED dropzone in HTML
        const dedDz = document.getElementById('ded-dropzone');
        const dedInp = document.getElementById('ded-file-input');
        const dedName = document.getElementById('ded-file-name');
        if (dedDz && dedInp) {
            dedDz.onclick = () => dedInp.click();
            dedInp.onchange = () => {
                if (dedInp.files.length) {
                    dedName.textContent = '✓ ' + dedInp.files[0].name;
                    dedName.style.display = 'block';
                }
            }
        }

        // --- PAGE 3: Environmental Clearance ---
        const envPanelId = 'env-clearance-upload-panel';
        if (document.getElementById(envPanelId)) {
            _createUploadZone(envPanelId, 'Environmental Clearance Document');
        }

        // --- PAGE 3: Social Acceptability – Public Consultation Upload ---
        document.querySelectorAll('input[name="consultation-status"]').forEach(r => {
            r.addEventListener('change', () => {
                const panel = document.getElementById('consult-yes-panel');
                if (!panel) return;
                const isYes = r.checked && r.id === 'consult-yes';
                panel.style.display = isYes ? 'block' : 'none';
                if (isYes && panel.children.length === 0) {
                    _createUploadZone('consult-yes-panel', 'Public Consultation Documentation');
                }
            });
        });

        // --- PAGE 3: HGDG Upload ---
        const hgdg = document.getElementById('f-hgdg');
        if (hgdg) {
            const showHGDG = () => {
                const panel = document.getElementById('p3-hgdg-panel');
                if (panel) {
                    panel.style.display = 'block';
                    if (panel.children.length === 0) {
                        _createUploadZone('p3-hgdg-panel', 'HGDG Document');
                    }
                }
            };
            hgdg.addEventListener('focus', showHGDG);
            hgdg.addEventListener('click', showHGDG);
            hgdg.addEventListener('input', showHGDG);
        }

        // --- PAGE 3: Consultation Multi-Date Picker ---
        (function _initConsultDatePicker() {
            const box = document.getElementById('consult-dates-box');
            const picker = document.getElementById('consult-date-picker');
            const hidden = document.getElementById('f-consult-done-dates');
            const placeholder = document.getElementById('consult-dates-placeholder');
            if (!box || !picker || !hidden) return;

            let selectedDates = []; // array of 'YYYY-MM-DD' strings

            function _fmt(dateStr) {
                // Format 'YYYY-MM-DD' → 'MMM D, YYYY'
                const [y, m, d] = dateStr.split('-').map(Number);
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return `${months[m - 1]} ${d}, ${y}`;
            }

            function _renderTags() {
                // Remove old tags
                box.querySelectorAll('.consult-date-tag').forEach(t => t.remove());

                const addBtn = box.querySelector('.consult-date-add');

                selectedDates.forEach(dateStr => {
                    const tag = document.createElement('span');
                    tag.className = 'consult-date-tag d-inline-flex align-items-center gap-1';
                    tag.style.cssText = 'background:#e0eaff; color:#154A9A; border:1px solid #b8d0ff; border-radius:6px; padding:2px 8px; font-size:0.78rem; font-weight:600; white-space:nowrap;';
                    tag.innerHTML = `${_fmt(dateStr)} <span class="consult-tag-remove" data-date="${dateStr}" style="cursor:pointer; font-size:1rem; line-height:1; margin-left:2px; color:#7c9fdb;">&times;</span>`;
                    box.insertBefore(tag, addBtn);
                });

                hidden.value = selectedDates.map(_fmt).join(', ');
                if (placeholder) {
                    placeholder.textContent = selectedDates.length > 0 ? 'Add more' : 'Pick date(s)';
                }

                // Update box border to indicate filled state
                box.style.borderColor = selectedDates.length > 0 ? '#154A9A' : '#dee2e6';
            }

            // Open the native date picker when box is clicked
            box.addEventListener('click', (e) => {
                // Clicked on a remove button
                if (e.target.classList.contains('consult-tag-remove')) {
                    const date = e.target.dataset.date;
                    selectedDates = selectedDates.filter(d => d !== date);
                    _renderTags();
                    return;
                }
                // Open picker
                picker.showPicker ? picker.showPicker() : picker.click();
            });

            picker.addEventListener('change', () => {
                const val = picker.value; // 'YYYY-MM-DD'
                if (!val) return;
                if (!selectedDates.includes(val)) {
                    selectedDates.push(val);
                    selectedDates.sort(); // chronological
                    _renderTags();
                }
                picker.value = ''; // reset so same date can't be re-triggered but can be re-added if removed
            });

            // Hover style
            box.addEventListener('mouseover', () => { box.style.borderColor = '#154A9A'; });
            box.addEventListener('mouseout', () => { box.style.borderColor = selectedDates.length > 0 ? '#154A9A' : '#dee2e6'; });
        })();



        // --- PAGE 3: Implementation Schedule - input restrictions and row controls ---
        (function _initImplScheduleFeatures() {
            const table = document.getElementById('impl-schedule-table');
            const body = document.getElementById('impl-schedule-body');
            const addBtn = document.getElementById('impl-add-row');
            if (!table || !body || !addBtn) return;

            // function to create a fresh schedule row (with delete button)
            function _createRow() {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><input type="text" class="form-control form-control-sm" placeholder="Year" data-required><div class="invalid-feedback">Year is required.</div></td>
                    <td><textarea class="form-control form-control-sm" rows="2" placeholder="Physical Target" data-required></textarea><div class="invalid-feedback">Physical target is required.</div></td>
                    <td><input type="text" class="form-control form-control-sm" placeholder="Indicator" data-required><div class="invalid-feedback">Indicator is required.</div></td>
                    <td><input type="number" class="form-control form-control-sm" placeholder="0.00" min="0" step="0.01" data-required onkeydown="if(['e', 'E', '+', '-'].includes(event.key)) event.preventDefault();"><div class="invalid-feedback">Amount is required.</div></td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-sm text-danger p-0 border-0 impl-del-row" title="Remove row" style="font-size:1rem;line-height:1;">&times;</button>
                    </td>`;
                return tr;
            }

            // ensure first row has no delete button (in case clones or editing)
            function _refreshFirstRow() {
                const first = body.querySelector('tr');
                if (first) {
                    const lastCell = first.querySelector('td:last-child');
                    if (lastCell) lastCell.innerHTML = ''; // clear any button
                }
            }

            // initial cleanup
            _refreshFirstRow();

            // add row handler
            addBtn.addEventListener('click', () => {
                const newRow = _createRow();
                body.appendChild(newRow);
            });

            // delegate removal and also manage first row state
            body.addEventListener('click', (ev) => {
                if (ev.target.closest('.impl-del-row')) {
                    const row = ev.target.closest('tr');
                    if (row) row.remove();
                    // if only one row remains, clear its delete button
                    if (body.querySelectorAll('tr').length === 1) {
                        _refreshFirstRow();
                    }
                }
            });

            // --- element-specific restrictions (existing code) ---
            // iterate over all numeric inputs in table for 'e' blocking
            const selector = '#impl-schedule-table input[type="number"]';
            const inputs = document.querySelectorAll(selector);
            inputs.forEach(inp => {
                inp.addEventListener('keydown', (ev) => {
                    if (ev.key === 'e' || ev.key === 'E') ev.preventDefault();
                });
                inp.addEventListener('paste', (ev) => {
                    const paste = (ev.clipboardData || window.clipboardData).getData('text');
                    if (/[eE]/.test(paste)) {
                        ev.preventDefault();
                        const sanitized = paste.replace(/[eE]/g, '');
                        const start = inp.selectionStart || 0;
                        const end = inp.selectionEnd || 0;
                        const val = inp.value || '';
                        inp.value = val.slice(0, start) + sanitized + val.slice(end);
                        const pos = start + sanitized.length;
                        inp.setSelectionRange(pos, pos);
                        inp.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
                inp.addEventListener('input', () => {
                    if (/[eE]/.test(inp.value)) {
                        inp.value = inp.value.replace(/[eE]/g, '');
                    }
                });
            });

            // delegate year/number restrictions (also covers future rows)
            table.addEventListener('keydown', (ev) => {
                const t = ev.target;
                if (!(t instanceof HTMLInputElement)) return;

                // Year column: allow only digits and control keys
                if (t.closest('td') && Array.from(t.closest('tr').children).indexOf(t.closest('td')) === 0) {
                    const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Tab', 'Enter', 'Home', 'End'];
                    if (allowed.includes(ev.key)) return;
                    if (!/^[0-9]$/.test(ev.key)) ev.preventDefault();
                    return;
                }

                if (t.type === 'number') {
                    if (ev.key === 'e' || ev.key === 'E' || ev.key === '+' || ev.key === '-') ev.preventDefault();
                }
            });

            table.addEventListener('paste', (ev) => {
                const t = ev.target;
                if (!(t instanceof HTMLInputElement)) return;
                const paste = (ev.clipboardData || window.clipboardData).getData('text') || '';

                if (t.closest('td') && Array.from(t.closest('tr').children).indexOf(t.closest('td')) === 0) {
                    if (/[^0-9]/.test(paste)) {
                        ev.preventDefault();
                        const sanitized = paste.replace(/[^0-9]/g, '');
                        const start = t.selectionStart || 0;
                        const end = t.selectionEnd || 0;
                        const val = t.value || '';
                        t.value = val.slice(0, start) + sanitized + val.slice(end);
                        const pos = start + sanitized.length;
                        t.setSelectionRange(pos, pos);
                        t.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    return;
                }

                if (t.type === 'number') {
                    if (/[eE]/.test(paste)) {
                        ev.preventDefault();
                        const sanitized = paste.replace(/[eE]/g, '');
                        const start = t.selectionStart || 0;
                        const end = t.selectionEnd || 0;
                        const val = t.value || '';
                        t.value = val.slice(0, start) + sanitized + val.slice(end);
                        const pos = start + sanitized.length;
                        t.setSelectionRange(pos, pos);
                        t.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            });

            table.addEventListener('input', (ev) => {
                const t = ev.target;
                if (!(t instanceof HTMLInputElement)) return;
                if (t.closest('td') && Array.from(t.closest('tr').children).indexOf(t.closest('td')) === 0) {
                    if (/[^0-9]/.test(t.value)) {
                        t.value = t.value.replace(/[^0-9]/g, '');
                    }
                } else if (t.type === 'number') {
                    if (/[eE]/.test(t.value)) t.value = t.value.replace(/[eE]/g, '');
                }
            });
        })();

        function _createAttachmentRow(list, index) {
            const row = document.createElement('div');
            row.className = 'attachment-row d-flex align-items-start gap-2 mb-3';
            row.dataset.index = index;
            const uid = 'att-' + index + '-' + Math.random().toString(36).substr(2, 4);
            row.innerHTML = `
                <div style="flex:1;">
                    <input type="text" class="form-control form-control-sm mb-2"
                        id="${uid}-desc" placeholder="Document title / description (e.g. Survey Map, Agency Letter)"
                        style="border-radius:8px; font-size:0.83rem;">
                    <div class="att-drop-zone d-flex align-items-center gap-2 p-2 rounded"
                        id="${uid}-zone" tabindex="0"
                        style="border:1.5px dashed #cbd5e1; cursor:pointer; background:#f8fafc; border-radius:8px; transition:border-color 0.2s;">
                        <i data-lucide="upload-cloud" width="18" style="color:#94a3b8; flex-shrink:0;"></i>
                        <span class="small text-secondary" id="${uid}-label">Click or drag to attach file</span>
                    </div>
                    <input type="file" id="${uid}-file" style="display:none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                    <div id="${uid}-preview" class="mt-1"></div>
                </div>
                <button type="button" class="att-remove-btn btn btn-sm btn-outline-danger"
                    style="border-radius:8px; padding:0.3rem 0.6rem; font-size:0.75rem; flex-shrink:0; margin-top:2px;"
                    title="Remove this attachment">
                    <i data-lucide="x" width="14"></i>
                </button>
            `;
            list.appendChild(row);

            if (window.lucide) window.lucide.createIcons();

            // Wire up file pick
            const zone = row.querySelector(`#${uid}-zone`);
            const fileInput = row.querySelector(`#${uid}-file`);
            const labelEl = row.querySelector(`#${uid}-label`);
            const preview = row.querySelector(`#${uid}-preview`);

            zone.addEventListener('click', () => fileInput.click());
            zone.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') fileInput.click(); });
            zone.addEventListener('dragover', e => { e.preventDefault(); zone.style.borderColor = '#154A9A'; zone.style.background = '#f0f5ff'; });
            zone.addEventListener('dragleave', () => { zone.style.borderColor = '#cbd5e1'; zone.style.background = '#f8fafc'; });
            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.style.borderColor = '#cbd5e1'; zone.style.background = '#f8fafc';
                if (e.dataTransfer.files.length) _handleAttachFile(e.dataTransfer.files[0], zone, labelEl, preview);
            });
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length) _handleAttachFile(fileInput.files[0], zone, labelEl, preview);
            });

            // Remove row
            row.querySelector('.att-remove-btn').addEventListener('click', () => {
                row.remove();
                // Keep at least one row
                if (list.querySelectorAll('.attachment-row').length === 0) {
                    _createAttachmentRow(list, Date.now());
                }
            });
        }

        function _handleAttachFile(file, zone, labelEl, preview) {
            zone.style.borderColor = '#22c55e';
            zone.style.background = 'rgba(22,163,74,0.05)';
            const reader = new FileReader();
            reader.onload = ev => {
                const dataUri = ev.target.result;
                // Store the data URI on the preview element for later capture
                preview.dataset.dataUri = dataUri;
                preview.dataset.fileName = file.name;
                preview.dataset.mimeType = file.type;

                if (file.type.startsWith('image/')) {
                    preview.innerHTML = `
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <img src="${dataUri}" style="height:44px; width:44px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0;">
                            <span class="small text-success fw-semibold">✓ ${file.name}</span>
                        </div>`;
                } else if (file.type === 'application/pdf') {
                    preview.innerHTML = `
                        <div class="d-flex align-items-center gap-2 mt-1 p-2 rounded" style="background:rgba(220,38,38,0.06); border:1px solid rgba(220,38,38,0.2);">
                            <i data-lucide="file-text" width="16" style="color:#dc2626;"></i>
                            <span class="small fw-semibold" style="color:#dc2626;">✓ ${file.name}</span>
                            <span class="badge ms-1" style="background:#fef2f2;color:#dc2626;font-size:0.65rem;border-radius:20px;padding:0.15em 0.5em;">PDF</span>
                        </div>`;
                } else {
                    preview.innerHTML = `
                        <div class="d-flex align-items-center gap-2 mt-1 p-2 rounded" style="background:rgba(22,163,74,0.07); border:1px solid rgba(22,163,74,0.2);">
                            <i data-lucide="file" width="16" style="color:#22c55e;"></i>
                            <span class="small text-success fw-semibold">✓ ${file.name}</span>
                        </div>`;
                }
                if (window.lucide) window.lucide.createIcons();
            };
            reader.readAsDataURL(file);
            if (labelEl) labelEl.textContent = 'File attached';
        }

        const attList = document.getElementById('other-attachments-list');
        const addAttBtn = document.getElementById('add-attachment-btn');
        if (attList) {
            _createAttachmentRow(attList, 1); // start with one row
            if (addAttBtn) {
                addAttBtn.addEventListener('click', () => {
                    _createAttachmentRow(attList, Date.now());
                });
                addAttBtn.addEventListener('mouseover', () => { addAttBtn.style.background = '#f0f5ff'; });
                addAttBtn.addEventListener('mouseout', () => { addAttBtn.style.background = 'transparent'; });
            }
        }


        // --- PAGE 5: Geotagged Photo Dropzone ---
        const geoZone = document.getElementById('geo-photo-zone');
        const geoInput = document.getElementById('geo-photo-input');
        const geoPreview = document.getElementById('geo-photo-preview');
        if (geoZone && geoInput) {
            geoZone.addEventListener('click', () => geoInput.click());
            geoZone.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') geoInput.click(); });
            geoZone.addEventListener('dragover', e => { e.preventDefault(); geoZone.classList.add('dragover'); });
            geoZone.addEventListener('dragleave', () => geoZone.classList.remove('dragover'));
            geoZone.addEventListener('drop', e => {
                e.preventDefault();
                geoZone.classList.remove('dragover');
                if (e.dataTransfer.files.length) _handleGeoFile(e.dataTransfer.files[0]);
            });
            geoInput.addEventListener('change', () => {
                if (geoInput.files.length) _handleGeoFile(geoInput.files[0]);
            });
            function _handleGeoFile(file) {
                const reader = new FileReader();
                reader.onload = ev => {
                    const dataUri = ev.target.result;
                    // Store on preview element for capture at submit time
                    if (geoPreview) {
                        geoPreview.dataset.dataUri = dataUri;
                        geoPreview.dataset.fileName = file.name;
                        geoPreview.dataset.mimeType = file.type;

                        if (file.type.startsWith('image/')) {
                            geoPreview.innerHTML = `
                                <div class="position-relative d-inline-block mt-2">
                                    <img src="${dataUri}" style="max-height:200px; max-width:100%; border-radius:8px; border:1px solid #e2e8f0;" alt="Geotagged Photo">
                                    <div class="text-success small fw-bold mt-1"><i data-lucide="check-circle" width="13"></i> ${file.name}</div>
                                </div>`;
                        } else if (file.type === 'application/pdf') {
                            geoPreview.innerHTML = `
                                <div class="d-flex align-items-center gap-2 mt-2 p-2 rounded" style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);">
                                    <i data-lucide="file-text" width="16" style="color:#dc2626;"></i>
                                    <span class="small fw-semibold" style="color:#dc2626;">✓ ${file.name}</span>
                                    <span class="badge ms-1" style="background:#fef2f2;color:#dc2626;font-size:0.65rem;border-radius:20px;padding:0.15em 0.5em;">PDF</span>
                                </div>`;
                        } else {
                            geoPreview.innerHTML = `<div class="text-success small fw-bold mt-2 p-2 rounded" style="background:rgba(22,163,74,0.07);border:1px solid rgba(22,163,74,0.2);">✓ ${file.name}</div>`;
                        }
                        if (window.lucide) window.lucide.createIcons();
                    }
                    geoZone.querySelector('p')?.classList.add('text-success');
                };
                reader.readAsDataURL(file);
            }
        }

        // --- PAGE 5: Geolocation fields - block letter 'e' ---
        (function _preventEInGeoFields() {
            const geoFieldIds = ['f-geo-start-lat', 'f-geo-start-lng', 'f-geo-end-lat', 'f-geo-end-lng'];
            geoFieldIds.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;

                // Prevent typing 'e' or 'E'
                el.addEventListener('keydown', (ev) => {
                    if (ev.key === 'e' || ev.key === 'E') ev.preventDefault();
                });

                // Prevent pasting values that contain 'e' or 'E'
                el.addEventListener('paste', (ev) => {
                    const paste = (ev.clipboardData || window.clipboardData).getData('text');
                    if (/[eE]/.test(paste)) {
                        ev.preventDefault();
                        const sanitized = paste.replace(/[eE]/g, '');
                        const start = el.selectionStart || 0;
                        const end = el.selectionEnd || 0;
                        const val = el.value || '';
                        el.value = val.slice(0, start) + sanitized + val.slice(end);
                        const pos = start + sanitized.length;
                        el.setSelectionRange(pos, pos);
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });

                // Sanitize any 'e' characters that might still appear (safety)
                el.addEventListener('input', () => {
                    if (/[eE]/.test(el.value)) {
                        el.value = el.value.replace(/[eE]/g, '');
                    }
                });
            });
        })();

        // --- PAGE 5: Signature Canvas (Draw, Upload, Mode Switch, Clear) ---
        function _initSignature(prefix) {
            const drawRadio = document.getElementById(`sig-${prefix}-draw`);
            const uploadRadio = document.getElementById(`sig-${prefix}-upload`);
            const drawPanel = document.getElementById(`sig-${prefix}-draw-panel`);
            const uploadPanel = document.getElementById(`sig-${prefix}-upload-panel`);
            const canvas = document.getElementById(`sig-canvas-${prefix}`);
            const hint = document.getElementById(`sig-${prefix}-hint`);
            const uploadZone = document.getElementById(`sig-${prefix}-upload-zone`);
            const uploadInput = document.getElementById(`sig-${prefix}-upload-input`);
            const preview = document.getElementById(`sig-${prefix}-upload-preview`);

            if (!canvas) return;

            // Mode switching : Draw ↔ Upload
            [drawRadio, uploadRadio].forEach(radio => {
                if (!radio) return;
                radio.addEventListener('change', () => {
                    const isDraw = drawRadio?.checked;
                    if (drawPanel) drawPanel.style.display = isDraw ? 'block' : 'none';
                    if (uploadPanel) uploadPanel.style.display = isDraw ? 'none' : 'block';
                });
            });

            // Canvas drawing
            const ctx = canvas.getContext('2d');
            ctx.strokeStyle = '#154A9A';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            let drawing = false;

            function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const scaleX = canvas.width / rect.width;
                const scaleY = canvas.height / rect.height;
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return { x: (clientX - rect.left) * scaleX, y: (clientY - rect.top) * scaleY };
            }

            canvas.addEventListener('mousedown', e => { drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
            canvas.addEventListener('mousemove', e => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
            canvas.addEventListener('mouseup', () => { drawing = false; if (hint) hint.textContent = '✓ Signature recorded'; });
            canvas.addEventListener('mouseleave', () => { drawing = false; });
            canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }, { passive: false });
            canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }, { passive: false });
            canvas.addEventListener('touchend', () => { drawing = false; if (hint) hint.textContent = '✓ Signature recorded'; });

            // Upload signature image
            if (uploadZone && uploadInput) {
                uploadZone.addEventListener('click', () => uploadInput.click());
                uploadInput.addEventListener('change', () => {
                    if (!uploadInput.files.length) return;
                    const file = uploadInput.files[0];
                    const reader = new FileReader();
                    reader.onload = ev => {
                        if (preview) {
                            preview.innerHTML = `<img src="${ev.target.result}" style="max-height:100px; border-radius:6px; border:1px solid #e2e8f0; margin-top:6px;">
                                <div class="small text-success fw-bold mt-1">✓ ${file.name}</div>`;
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        _initSignature('prep');
        _initSignature('noted');

        // Clear button for canvases
        document.querySelectorAll('.sig-clear-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const cid = btn.dataset.target;
                const hid = btn.dataset.hint;
                const c = document.getElementById(cid);
                if (c) c.getContext('2d').clearRect(0, 0, c.width, c.height);
                const h = document.getElementById(hid);
                if (h) h.textContent = 'Please sign above.';
            });
        });
    }
    _initHelpers();

    /* ── Bicol Region location data ── */
    const LOCATION_DATA = {
        'Albay': {
            '1st District': ['Legazpi City', 'Ligao City', 'Libon', 'Oas', 'Polangui', 'St. Michaels (Pitogo)'],
            '2nd District': ['Tabaco City', 'Bacacay', 'Malilipot', 'Malinao', 'Santo Domingo', 'Tiwi'],
            '3rd District': ['Camalig', 'Daraga', 'Guinobatan', 'Jovellar', 'Manito', 'Rapu-Rapu'],
        },
        'Camarines Norte': {
            '1st District': ['Daet', 'Basud', 'Capalonga', 'Jose Panganiban', 'Labo', 'Mercedes', 'San Vicente', 'Sta. Elena', 'Talisay', 'Vinzons'],
            '2nd District': ['Paracale', 'San Lorenzo Ruiz', 'Garchitorena', 'Mambulao (Jose Panganiban)'],
        },
        'Camarines Sur': {
            '1st District': ['Naga City', 'Bombom', 'Calabanga', 'Camaligan', 'Canaman', 'Gainza', 'Magarao', 'Milaor', 'Minalabac', 'Pamplona', 'Pasacao', 'San Fernando'],
            '2nd District': ['Partido Area: Goa', 'Lagonoy', 'Presentacion', 'Sangay', 'San Jose', 'Tigaon', 'Tinambac'],
            '3rd District': ['Iriga City', 'Baao', 'Balatan', 'Bula', 'Buhi', 'Bato', 'Nabua', 'Pili'],
            '4th District': ['Caramoan', 'Del Gallego', 'Lupi', 'Ragay', 'Sipocot', 'Cabusao'],
        },
        'Catanduanes': {
            '1st District': ['Virac', 'Bagamanoc', 'Baras', 'Bato', 'Caramoran', 'Gigmoto'],
            '2nd District': ['Pandan', 'Panganiban', 'San Andres', 'San Miguel', 'Viga'],
        },
        'Masbate': {
            '1st District': ['Masbate City', 'Aroroy', 'Baleno', 'Balud', 'Batuan', 'Cataingan', 'Cawayan'],
            '2nd District': ['Claveria', 'Dimasalang', 'Esperanza', 'Mandaon', 'Milagros', 'Mobo', 'Monreal'],
            '3rd District': ['Palanas', 'Pio V. Corpuz', 'Placer', 'San Fernando', 'San Jacinto', 'San Pascual', 'Uson'],
        },
        'Sorsogon': {
            '1st District': ['Sorsogon City', 'Barcelona', 'Bulusan', 'Casiguran', 'Castilla', 'Donsol', 'Gubat'],
            '2nd District': ['Irosin', 'Juban', 'Magallanes', 'Matnog', 'Pilar', 'Prieto Diaz', 'Santa Magdalena'],
        },
    };

    /* ── Show/Hide Location Fields ── */
    function _initLocationFields() {
        const rads = document.querySelectorAll('input[name="project-coverage"]');
        const locFields = document.getElementById('location-specific-fields');
        const interProvFields = document.getElementById('inter-province-fields');
        const provinceEl = document.getElementById('f-province');
        const districtEl = document.getElementById('f-district');
        const municipalEl = document.getElementById('f-municipality');

        if (!rads.length) return;

        function _populateSelect(el, items, placeholder) {
            el.innerHTML = `<option value="">${placeholder}</option>`;
            items.forEach(val => {
                const opt = document.createElement('option');
                opt.value = val; opt.textContent = val;
                el.appendChild(opt);
            });
        }

        function _resetSelect(el, placeholder) {
            el.innerHTML = `<option value="">${placeholder}</option>`;
            el.disabled = true;
            el.value = '';
        }

        // When coverage radio changes
        rads.forEach(r => r.addEventListener('change', () => {
            const isLocSpec = r.value === 'Location-Specific';
            const isInterProv = r.value === 'Inter-Province';
            if (locFields) locFields.style.display = isLocSpec ? 'block' : 'none';
            if (interProvFields) interProvFields.style.display = isInterProv ? 'block' : 'none';

            // Enable only Province; reset District & Municipality
            if (provinceEl) { provinceEl.disabled = !isLocSpec; provinceEl.value = ''; }
            _resetSelect(districtEl, '-- Select District --');
            _resetSelect(municipalEl, '-- Select City/Municipality --');

            // Enable/disable & reset the Provinces tag widget
            const tagInput = document.getElementById('provinces-tag-input');
            const tagHidden = document.getElementById('f-provinces');
            const tagBox = document.getElementById('f-provinces-box');
            if (tagInput) { tagInput.disabled = !isInterProv; }
            if (tagHidden) { tagHidden.disabled = !isInterProv; if (!isInterProv) tagHidden.value = ''; }
            if (tagBox) {
                tagBox.style.opacity = isInterProv ? '1' : '0.6';
                tagBox.style.pointerEvents = isInterProv ? '' : 'none';
                tagBox.style.cursor = isInterProv ? 'text' : 'not-allowed';
            }
            // Reset tag selections when switching away
            if (!isInterProv) {
                const existingTags = tagBox ? tagBox.querySelectorAll('.prov-tag') : [];
                existingTags.forEach(t => t.remove());
                if (tagInput) { tagInput.placeholder = 'Select provinces...'; tagInput.value = ''; }
                // Sync checkboxes
                document.querySelectorAll('#provinces-dropdown .dropdown-item input[type="checkbox"]')
                    .forEach(chk => chk.checked = false);
            }
        }));

        // Province → populate & enable District
        if (provinceEl) {
            provinceEl.addEventListener('change', () => {
                _resetSelect(districtEl, '-- Select District --');
                _resetSelect(municipalEl, '-- Select City/Municipality --');
                const districts = LOCATION_DATA[provinceEl.value];
                if (districts && provinceEl.value) {
                    _populateSelect(districtEl, Object.keys(districts), '-- Select District --');
                    districtEl.disabled = false;
                }
            });
        }

        // District → populate & enable City/Municipality
        if (districtEl) {
            districtEl.addEventListener('change', () => {
                _resetSelect(municipalEl, '-- Select City/Municipality --');
                const province = provinceEl ? provinceEl.value : '';
                const districts = LOCATION_DATA[province];
                const cities = districts ? districts[districtEl.value] : null;
                if (cities && districtEl.value) {
                    _populateSelect(municipalEl, cities, '-- Select City/Municipality --');
                    municipalEl.disabled = false;
                }
            });
        }
    }
    _initLocationFields();

    /* ── Provinces Tag Input ── */
    function _initProvincesTagInput() {
        const box = document.getElementById('f-provinces-box');
        const input = document.getElementById('provinces-tag-input');
        const dropdown = document.getElementById('provinces-dropdown');
        const hiddenInput = document.getElementById('f-provinces');
        if (!box || !input || !dropdown || !hiddenInput) return;

        let selectedProvinces = hiddenInput.value ? hiddenInput.value.split(', ').map(s => s.trim()).filter(v => v) : [];

        function renderTags() {
            // Remove existing tags
            const tags = box.querySelectorAll('.prov-tag');
            tags.forEach(t => t.remove());

            // Add new tags before the input
            selectedProvinces.forEach(prov => {
                const tag = document.createElement('span');
                tag.className = 'prov-tag d-inline-flex align-items-stretch border rounded';
                tag.style.background = '#e2e8f0';
                tag.style.borderColor = '#cbd5e1 !important';
                tag.style.fontSize = '0.86rem';
                tag.innerHTML = `
                    <span class="prov-remove border-end px-2 text-muted d-flex align-items-center justify-content-center" 
                          style="cursor:pointer; border-right-color: #cbd5e1 !important;" data-prov="${prov}">&times;</span>
                    <span class="px-2 py-1" style="color:#1e293b;">${prov}</span>
                `;
                box.insertBefore(tag, input);
            });

            hiddenInput.value = selectedProvinces.join(', ');
            input.placeholder = selectedProvinces.length > 0 ? '' : 'Select provinces...';

            // Clear validation error dynamically if valid
            if (hiddenInput.classList.contains('is-invalid') && selectedProvinces.length > 0) {
                hiddenInput.classList.remove('is-invalid');
                box.classList.remove('is-invalid');
                const err = document.getElementById('f-provinces-err');
                if (err) err.style.setProperty('display', 'none', 'important');
            }
        }
        renderTags(); // Ensure initial tags are drawn if any

        // Expose sync function for external data loading
        window.syncProvincesTags = () => {
            selectedProvinces = hiddenInput.value ? hiddenInput.value.split(', ').map(s => s.trim()).filter(v => v) : [];
            renderTags();
        };

        box.addEventListener('click', (e) => {
            if (e.target.classList.contains('prov-remove')) {
                const prov = e.target.getAttribute('data-prov');
                selectedProvinces = selectedProvinces.filter(p => p !== prov);
                renderTags();
                filterDropdown();
                input.focus();
                return;
            }
            input.focus();
        });

        const showDropdown = () => dropdown.classList.add('show');
        const hideDropdown = () => setTimeout(() => dropdown.classList.remove('show'), 200);

        input.addEventListener('focus', () => { filterDropdown(); showDropdown(); });
        input.addEventListener('blur', hideDropdown);
        // No 'input' listener — typing disabled (readonly), dropdown is not searchable

        function filterDropdown() {
            const val = input.value.toLowerCase().trim();
            const items = dropdown.querySelectorAll('.dropdown-item');
            let hasVisible = false;
            items.forEach(item => {
                const prov = item.getAttribute('data-prov');
                const match = prov.toLowerCase().includes(val);

                const chk = item.querySelector('input[type="checkbox"]');
                if (chk) chk.checked = selectedProvinces.includes(prov);

                if (match) {
                    item.parentElement.style.display = '';
                    hasVisible = true;
                } else {
                    item.parentElement.style.display = 'none';
                }
            });
            if (!hasVisible) dropdown.classList.remove('show');
        }

        dropdown.addEventListener('mousedown', (e) => {
            e.preventDefault(); // Prevent input blur
            let item = e.target;
            if (item.closest('.dropdown-item')) {
                item = item.closest('.dropdown-item');
                const prov = item.getAttribute('data-prov');
                if (selectedProvinces.includes(prov)) {
                    selectedProvinces = selectedProvinces.filter(p => p !== prov);
                } else {
                    selectedProvinces.push(prov);
                }
                input.value = '';
                renderTags();
                filterDropdown();
                input.focus();
            }
        });

        dropdown.addEventListener('click', (e) => {
            if (e.target.closest('a')) e.preventDefault();
        });

        input.addEventListener('keydown', (e) => {
            // Block all typing; only allow Backspace to remove the last tag
            if (e.key === 'Backspace' && selectedProvinces.length > 0) {
                selectedProvinces.pop();
                renderTags();
                filterDropdown();
            } else if (e.key !== 'Tab' && e.key !== 'Escape') {
                e.preventDefault();
            }
        });
    }
    _initProvincesTagInput();

    /* ── SDG Tag Input ── */
    function _initSDGTagInput() {
        const box = document.getElementById('f-alignment-box');
        const input = document.getElementById('sdg-tag-input');
        const dropdown = document.getElementById('sdg-dropdown');
        const hiddenInput = document.getElementById('f-alignment');
        if (!box || !input || !dropdown || !hiddenInput) return;

        let selectedSDGs = hiddenInput.value ? hiddenInput.value.split(', ').map(s => s.trim()).filter(v => v) : [];

        function renderTags() {
            // Remove existing tags
            const tags = box.querySelectorAll('.sdg-tag');
            tags.forEach(t => t.remove());

            // Add new tags before the input
            selectedSDGs.forEach(sdg => {
                const tag = document.createElement('span');
                tag.className = 'sdg-tag d-inline-flex align-items-stretch border rounded';
                tag.style.background = '#e2e8f0';
                tag.style.borderColor = '#cbd5e1 !important';
                tag.style.fontSize = '0.86rem';
                tag.innerHTML = `
                    <span class="sdg-remove border-end px-2 text-muted d-flex align-items-center justify-content-center" 
                          style="cursor:pointer; border-right-color: #cbd5e1 !important;" data-val="${sdg}">&times;</span>
                    <span class="px-2 py-1" style="color:#1e293b;">${sdg}</span>
                `;
                box.insertBefore(tag, input);
            });

            hiddenInput.value = selectedSDGs.join(', ');
            input.placeholder = selectedSDGs.length > 0 ? '' : 'Select SDGs...';

            // Clear validation error dynamically if valid
            if (hiddenInput.classList.contains('is-invalid') && selectedSDGs.length > 0) {
                hiddenInput.classList.remove('is-invalid');
                box.classList.remove('is-invalid');
                const err = document.getElementById('f-alignment-err');
                if (err) err.style.setProperty('display', 'none', 'important');
            }
        }
        renderTags(); // Ensure initial tags are drawn if any

        // Expose sync function for external data loading
        window.syncSDGTags = () => {
            selectedSDGs = hiddenInput.value ? hiddenInput.value.split(', ').map(s => s.trim()).filter(v => v) : [];
            renderTags();
        };

        box.addEventListener('click', (e) => {
            if (e.target.classList.contains('sdg-remove')) {
                const val = e.target.getAttribute('data-val');
                selectedSDGs = selectedSDGs.filter(p => p !== val);
                renderTags();
                filterDropdown();
                input.focus();
                return;
            }
            input.focus();
        });

        const showDropdown = () => dropdown.classList.add('show');
        const hideDropdown = () => setTimeout(() => dropdown.classList.remove('show'), 200);

        input.addEventListener('focus', () => { filterDropdown(); showDropdown(); });
        input.addEventListener('blur', hideDropdown);
        // No 'input' listener — typing disabled (readonly), dropdown is not searchable

        function filterDropdown() {
            const val = input.value.toLowerCase().trim();
            const items = dropdown.querySelectorAll('.dropdown-item');
            let hasVisible = false;
            items.forEach(item => {
                const sdg = item.getAttribute('data-val');
                const match = sdg.toLowerCase().includes(val);

                const chk = item.querySelector('input[type="checkbox"]');
                if (chk) chk.checked = selectedSDGs.includes(sdg);

                if (match) {
                    item.parentElement.style.display = '';
                    hasVisible = true;
                } else {
                    item.parentElement.style.display = 'none';
                }
            });
            if (!hasVisible) dropdown.classList.remove('show');
        }

        dropdown.addEventListener('mousedown', (e) => {
            e.preventDefault(); // Prevent input blur
            let item = e.target;
            if (item.closest('.dropdown-item')) {
                item = item.closest('.dropdown-item');
                const sdg = item.getAttribute('data-val');
                if (selectedSDGs.includes(sdg)) {
                    selectedSDGs = selectedSDGs.filter(p => p !== sdg);
                } else {
                    selectedSDGs.push(sdg);
                }
                input.value = '';
                renderTags();
                filterDropdown();
                input.focus();
            }
        });

        dropdown.addEventListener('click', (e) => {
            if (e.target.closest('a')) e.preventDefault();
        });

        input.addEventListener('keydown', (e) => {
            // Block all typing; only allow Backspace to remove the last tag
            if (e.key === 'Backspace' && selectedSDGs.length > 0) {
                selectedSDGs.pop();
                renderTags();
                filterDropdown();
            } else if (e.key !== 'Tab' && e.key !== 'Escape') {
                e.preventDefault();
            }
        });
    }
    _initSDGTagInput();

    function _initRDPTagInput() {
        const box         = document.getElementById('f-rdp-alignment-box');
        const input       = document.getElementById('rdp-tag-input');
        const dropdown    = document.getElementById('rdp-dropdown');
        const hiddenInput = document.getElementById('f-rdp-alignment');
        if (!box || !input || !dropdown || !hiddenInput) return;

        let selectedRDP = [];

        function renderTags() {
            box.querySelectorAll('.rdp-tag').forEach(t => t.remove());
            selectedRDP.forEach(ch => {
                const tag = document.createElement('span');
                tag.className = 'rdp-tag d-inline-flex align-items-stretch border rounded';
                tag.style.background = '#e2e8f0';
                tag.style.borderColor = '#cbd5e1 !important';
                tag.style.fontSize = '0.86rem';
                tag.innerHTML = `
                    <span class="rdp-remove border-end px-2 text-muted d-flex align-items-center justify-content-center"
                          style="cursor:pointer; border-right-color: #cbd5e1 !important;" data-val="${ch}">&times;</span>
                    <span class="px-2 py-1" style="color:#1e293b;" title="${ch}">${ch}</span>
                `;
                box.insertBefore(tag, input);
            });
            hiddenInput.value = selectedRDP.join(', ');
            input.placeholder = selectedRDP.length > 0 ? '' : 'Select RDP chapters...';
            if (hiddenInput.classList.contains('is-invalid') && selectedRDP.length > 0) {
                hiddenInput.classList.remove('is-invalid');
                box.classList.remove('is-invalid');
                const err = document.getElementById('f-rdp-alignment-err');
                if (err) err.style.setProperty('display', 'none', 'important');
            }
        }
        renderTags();

        window.syncRDPTags = () => {
            selectedRDP = hiddenInput.value ? hiddenInput.value.split(', ').map(s => s.trim()).filter(v => v) : [];
            renderTags();
        };

        box.addEventListener('click', (e) => {
            if (e.target.classList.contains('rdp-remove')) {
                const val = e.target.getAttribute('data-val');
                selectedRDP = selectedRDP.filter(p => p !== val);
                renderTags();
                filterDropdown();
                input.focus();
                return;
            }
            input.focus();
        });

        const showDropdown = () => dropdown.classList.add('show');
        const hideDropdown = () => setTimeout(() => dropdown.classList.remove('show'), 200);

        input.addEventListener('focus', () => { filterDropdown(); showDropdown(); });
        input.addEventListener('blur', hideDropdown);
        // No 'input' listener — typing disabled (readonly), dropdown is not searchable

        function filterDropdown() {
            const items = dropdown.querySelectorAll('.dropdown-item');
            items.forEach(item => {
                const chk = item.querySelector('input[type="checkbox"]');
                if (chk) chk.checked = selectedRDP.includes(item.getAttribute('data-val'));
                item.parentElement.style.display = '';
            });
        }

        dropdown.addEventListener('mousedown', (e) => {
            e.preventDefault();
            let item = e.target;
            if (item.closest('.dropdown-item')) {
                item = item.closest('.dropdown-item');
                const val = item.getAttribute('data-val');
                if (selectedRDP.includes(val)) {
                    selectedRDP = selectedRDP.filter(p => p !== val);
                } else {
                    selectedRDP.push(val);
                }
                renderTags();
                filterDropdown();
                input.focus();
            }
        });

        dropdown.addEventListener('click', (e) => {
            if (e.target.closest('a')) e.preventDefault();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && selectedRDP.length > 0) {
                selectedRDP.pop();
                renderTags();
                filterDropdown();
            } else if (e.key !== 'Tab' && e.key !== 'Escape') {
                e.preventDefault();
            }
        });
    }
    _initRDPTagInput();

    // ── Load Submission Data ──
    async function _loadSubmissionData(id) {
        const subs = await localforage.getItem('cpp_submissions') || [];
        const s = subs.find(x => x.id === id);
        if (!s) return;

        const fd = s.formData || {};
        const form = document.getElementById('cppMainForm');
        if (!form) return;

        // 1. Simple Inputs/Textareas
        for (const [key, val] of Object.entries(fd)) {
            const el = document.getElementById(key);
            if (el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT')) {
                if (el.type !== 'radio' && el.type !== 'checkbox') {
                    el.value = val;
                }
            }
        }

        // 2. Radios (name-based)
        if (fd['project-type']) {
            const rad = document.querySelector(`input[name="project-type"][value="${fd['project-type']}"]`);
            if (rad) rad.checked = true;
        }
        if (fd['project-coverage']) {
            const rad = document.querySelector(`input[name="project-coverage"][value="${fd['project-coverage']}"]`);
            if (rad) rad.checked = true;
        }
        if (fd['project-status']) {
            const rad = document.querySelector(`input[name="project-status"][value="${fd['project-status']}"]`);
            if (rad) rad.checked = true;
        }
        if (fd['consultation-status']) {
            const rad = document.querySelector(`input[name="consultation-status"][value="${fd['consultation-status']}"]`);
            if (rad) rad.checked = true;
        }

        // 3. Checkboxes
        if (Array.isArray(fd['f-targets'])) {
            fd['f-targets'].forEach(v => {
                const cb = document.querySelector(`input[name="f-targets"][value="${v}"]`);
                if (cb) cb.checked = true;
            });
        }

        // 4. Repeating Table: Implementation Schedule
        if (Array.isArray(fd['impl_schedule'])) {
            const tbody = document.getElementById('impl-schedule-body');
            if (tbody) {
                tbody.innerHTML = '';
                fd['impl_schedule'].forEach(row => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td><input type="text" class="form-control form-control-sm border-0 bg-light" name="impl_period[]" value="${row.period || ''}" placeholder="e.g. Q1 2024"></td>
                        <td><input type="number" class="form-control form-control-sm border-0 bg-light" name="impl_amount[]" value="${row.amount || ''}" placeholder="0.00"></td>
                        <td><textarea class="form-control form-control-sm border-0 bg-light" name="impl_remarks[]" rows="1" placeholder="Optional notes...">${row.remarks || ''}</textarea></td>
                        <td class="text-center"><button type="button" class="btn btn-sm text-danger p-1 remove-impl-row"><i data-lucide="trash-2" width="14"></i></button></td>
                    `;
                    tbody.appendChild(tr);
                });
                if (window.lucide) window.lucide.createIcons();
            }
        }

        // 5. Tag Inputs (SDG / RDP)
        if (fd['f-sdg-alignment'] && window.syncSDGTags) window.syncSDGTags();
        if (fd['f-rdp-alignment'] && window.syncRDPTags) window.syncRDPTags();

        console.log(`[cpp-form] Successfully loaded data for: ${id}`);
    }
    window._loadSubmissionData = _loadSubmissionData;

    /* ── Init ── */
    showStep(1);

    // Expose _showFinalReview globally so initCppForm's view-mode path can call it
    // on ANY page load — even before _showFinalReview has ever been invoked.
    window.showCppReview = _showFinalReview;
}

/**
 * Global status update utility for admins
 */
window.updateCppStatus = async (id, newStatus) => {
    try {
        const all = await localforage.getItem('cpp_submissions') || [];
        const idx = all.findIndex(s => s.id === id);
        if (idx !== -1) {
            all[idx].status = newStatus;
            all[idx].projectStatus = newStatus;
            all[idx].submissionStatus = newStatus;
            if (newStatus === 'Sectoral Presentation') {
                all[idx].stage = 'Sectoral Committee';
                all[idx].cppStage = 'Sectoral Committee';
            }
            
            await localforage.setItem('cpp_submissions', all);
            
            if (window.showSimpleAlert) {
                window.showSimpleAlert(`Submission status updated to ${newStatus} successfully.`, 'success');
            } else {
                alert(`Status updated to ${newStatus}`);
            }

            // Optionally refresh the view or go back
            // For now, just re-render the success alert state or refresh table if we can find it
        }
    } catch (err) {
        console.error('Failed to update status:', err);
    }
};

