/**
 * CPP Form UI & Workflow Logic
 * Handles stepper, UI components, and dynamic field toggles.
 */

window.currentStep = 1;
window.totalSteps = 5;

// Update UI to reflect current step
window.updateStepperAndURL = function() {
    document.querySelectorAll('.form-step').forEach(el => {
        el.classList.remove('active');
        el.style.display = 'none';
    });
    
    const currentStepEl = document.getElementById('step-' + window.currentStep);
    if (currentStepEl) {
        currentStepEl.classList.add('active');
        currentStepEl.style.display = 'block';
    }

    document.querySelectorAll('.cpp-step').forEach(el => {
        el.classList.remove('active');
        if (parseInt(el.getAttribute('data-step')) === window.currentStep) {
            el.classList.add('active');
        }
    });

    const prevBtn = document.getElementById('cppPrevBtn');
    const nextBtn = document.getElementById('cppNextBtn');
    const submitBtn = document.getElementById('cppSubmitBtn');

    if (prevBtn) prevBtn.style.visibility = window.currentStep === 1 ? 'hidden' : 'visible';
    
    if (window.currentStep === window.totalSteps) {
        if (nextBtn) nextBtn.classList.add('d-none');
        if (submitBtn) submitBtn.classList.remove('d-none');
    } else {
        if (nextBtn) nextBtn.classList.remove('d-none');
        if (submitBtn) submitBtn.classList.add('d-none');
    }

    const counter = document.getElementById('cppStepCounter');
    if (counter) counter.innerText = `Step ${window.currentStep} of ${window.totalSteps}`;

    const url = new URL(window.location);
    url.searchParams.set('step', window.currentStep);
    window.history.pushState({step: window.currentStep}, '', url);
};

/**
 * REUSABLE VALIDATION CORE
 */

// 1. Central Validation Logic for a single field or group
window.validateField = function(el) {
    if (!el) return true;
    
    // Ignore disabled or hidden fields (but allow hidden inputs that are required)
    if (el.disabled || (el.offsetParent === null && el.type !== 'hidden')) return true;

    let isValid = true;
    const type = el.type;
    const name = el.name;

    // Handle Checkbox/Radio Groups
    if (type === 'checkbox' || type === 'radio') {
        const group = document.querySelectorAll(`input[name="${name}"]`);
        const checked = [...group].some(i => i.checked);
        
        // Find specific error containers based on name
        const errMap = {
            'project-type': 'type-error',
            'project-coverage': 'coverage-error',
            'project-status': 'status-error',
            'consultation-status': 'consult-status-err'
        };
        
        const errId = errMap[name];
        if (errId) {
            const errEl = document.getElementById(errId);
            if (errEl) {
                if (!checked) {
                    errEl.style.setProperty('display', 'block', 'important');
                    if (errEl.classList.contains('invalid-feedback')) errEl.classList.add('d-block');
                    isValid = false;
                } else {
                    errEl.style.setProperty('display', 'none', 'important');
                    if (errEl.classList.contains('invalid-feedback')) errEl.classList.remove('d-block');
                }
            }
        }
        return isValid;
    }

    // Handle Custom Components (Alignment Boxes)
    if (el.id === 'f-alignment' || el.id === 'f-rdp-alignment') {
        const isBlank = !el.value || !el.value.trim();
        const box = document.getElementById(el.id + '-box');
        const err = document.getElementById(el.id + '-err');
        
        if (box) {
            box.classList.toggle('border-danger', isBlank);
            box.classList.toggle('is-invalid', isBlank); // Keep both for safety
        }
        if (err) err.style.setProperty('display', isBlank ? 'block' : 'none', 'important');
        
        return !isBlank;
    }

    // Handle Standard Inputs
    if (el.hasAttribute('data-required') || el.required) {
        const isBlank = !el.value || !el.value.trim();
        el.classList.toggle('is-invalid', isBlank);
        isValid = !isBlank;
    }

    return isValid;
};

// 2. Attach Listeners for Real-Time Feedback
window.initRealTimeValidation = function() {
    const form = document.getElementById('cppMainForm');
    if (!form) return;

    // Listen for blur, input, and change
    const events = ['blur', 'input', 'change'];
    
    form.querySelectorAll('input, textarea, select').forEach(el => {
        events.forEach(eventType => {
            el.addEventListener(eventType, () => {
                // Only validate on interaction
                window.validateField(el);
                
                // Also trigger draft saving
                if (eventType !== 'blur' && window.saveDraftLocally) {
                    window.saveDraftLocally();
                }
            });
        });
    });
};

// 3. Updated validateStep to use the core
window.validateStep = function(step) {
    const panel = document.getElementById('step-' + step);
    if (!panel) return true;
    
    let stepValid = true;

    // Validate all inputs in the current step
    const inputs = panel.querySelectorAll('input, textarea, select');
    inputs.forEach(el => {
        // Skip inputs that are inside the implementation schedule table (handled separately below)
        if (el.closest('#impl-schedule-body')) return;
        
        if (!window.validateField(el)) {
            stepValid = false;
        }
    });

    // --- STEP 1: Conditional File Upload Requirements ---
    if (step === 1) {
        const checkConditionalUpload = (triggerElId, panelId, errorMsg) => {
            const trigger = document.getElementById(triggerElId);
            const panel = document.getElementById(panelId);
            if (trigger && panel) {
                const isActive = (trigger.type === 'checkbox') ? trigger.checked : (trigger.value && trigger.value.trim() !== '');
                const hasFile = panel.dataset.attachDataUri || panel.dataset.attachExistingUrl;
                
                if (isActive && !hasFile) {
                    window.showToast?.(errorMsg, "warning");
                    panel.style.border = '1px solid #ef4444';
                    panel.style.borderRadius = '10px';
                    stepValid = false;
                } else {
                    panel.style.border = 'none';
                }
            }
        };

        // 1. DED
        checkConditionalUpload('prep-ded', 'ded-upload-panel', "Please upload the Detailed Engineering Design (DED) document.");
        
        // 2. Endorsements
        checkConditionalUpload('f-sp-res', 'sp-upload-panel', "Please upload the SP Resolution document.");
        checkConditionalUpload('f-sb-res', 'sb-upload-panel', "Please upload the SB Resolution document.");
        checkConditionalUpload('f-letter-req', 'letter-upload-panel', "Please upload the Letter Request document.");
        checkConditionalUpload('f-bor-res', 'bor-upload-panel', "Please upload the BOR/BOT Resolution document.");
    }

    // --- STEP 3: More Conditional Requirements ---
    if (step === 3) {
        const checkConditionalUpload = (triggerElId, panelId, errorMsg) => {
            const trigger = document.getElementById(triggerElId);
            const panel = document.getElementById(panelId);
            if (trigger && panel) {
                const isActive = (trigger.type === 'checkbox' || trigger.type === 'radio') ? trigger.checked : (trigger.value && trigger.value.trim() !== '');
                const hasFile = panel.dataset.attachDataUri || panel.dataset.attachExistingUrl;
                
                if (isActive && !hasFile) {
                    window.showToast?.(errorMsg, "warning");
                    panel.style.border = '1px solid #ef4444';
                    panel.style.borderRadius = '10px';
                    stepValid = false;
                } else {
                    panel.style.border = 'none';
                }
            }
        };

        // 1. Environmental Clearance
        checkConditionalUpload('f-env-clearance-desc', 'env-clearance-upload-panel', "Please upload the Environmental Clearance document.");
        
        // 2. Public Consultation
        const consultYes = document.getElementById('consult-yes');
        const consultNo = document.getElementById('consult-no');
        const plannedDate = document.getElementById('f-consult-planned-date');
        const doneDates = document.getElementById('f-consult-done-dates');
        const datesBox = document.getElementById('consult-dates-box');

        if (consultYes && consultYes.checked) {
            // Validate File
            const panel = document.getElementById('consult-yes-panel');
            const hasFile = panel?.dataset.attachDataUri || panel?.dataset.attachExistingUrl;
            if (!hasFile) {
                window.showToast?.("Please upload the Public Consultation Documentation.", "warning");
                if (panel) { panel.style.border = '1px solid #ef4444'; panel.style.borderRadius = '10px'; }
                stepValid = false;
            } else if (panel) {
                panel.style.border = 'none';
            }

            // Validate Dates
            if (doneDates && (!doneDates.value || doneDates.value.trim() === '')) {
                window.showToast?.("Please pick the date(s) when the consultation was conducted.", "warning");
                if (datesBox) datesBox.style.setProperty('border-color', '#ef4444', 'important');
                stepValid = false;
            } else if (datesBox) {
                datesBox.style.setProperty('border-color', '#dee2e6', 'important');
            }
        } else if (consultNo && consultNo.checked) {
            // Validate Planned Date
            if (plannedDate && (!plannedDate.value || plannedDate.value.trim() === '')) {
                window.showToast?.("Please provide the planned date for the public consultation.", "warning");
                plannedDate.classList.add('is-invalid');
                stepValid = false;
            } else if (plannedDate) {
                plannedDate.classList.remove('is-invalid');
            }
        }

        // 3. HGDG
        checkConditionalUpload('f-hgdg', 'hgdg-upload-panel', "Please upload the HGDG Checklist document.");

        // Implementation schedule: require at least one non-empty row
        const implBody = document.getElementById('impl-schedule-body');
        const implErr = document.getElementById('impl-schedule-err');
        if (implBody && implErr) {
            const hasFilled = [...implBody.querySelectorAll('tr')].some(row => {
                return [...row.querySelectorAll('input, textarea')].some(i => i.value && i.value.trim());
            });
            if (!hasFilled) {
                implErr.innerText = "Please fill in at least one year of implementation schedule.";
                implErr.style.setProperty('display', 'block', 'important');
                stepValid = false;
            } else {
                implErr.style.setProperty('display', 'none', 'important');
            }
        }
        // Consultation status radio group
        const consultChecked = document.querySelector('input[name="consultation-status"]:checked');
        const consultErr = document.getElementById('consult-status-err');
        if (consultErr) {
            if (!consultChecked) { 
                consultErr.innerText = "Please indicate if public consultation was conducted.";
                consultErr.style.setProperty('display', 'block', 'important'); 
                stepValid = false; 
            } else {
                consultErr.style.setProperty('display', 'none', 'important');
            }
        }
    }

    // --- STEP 5: Mandatory Geotagged Photo & Geolocation ---
    if (step === 5) {
        // 1. Geotagged Photo
        const geoPreview = document.getElementById('geo-photo-preview');
        const geoZone = document.getElementById('geo-photo-zone');
        if (geoPreview) {
            const hasPhoto = geoPreview.dataset.dataUri || geoPreview.dataset.existingUrl;
            if (!hasPhoto) {
                window.showToast?.("Please upload a geotagged photo or location map.", "warning");
                if (geoZone) geoZone.style.setProperty('border-color', '#ef4444', 'important');
                stepValid = false;
            } else {
                if (geoZone) geoZone.style.setProperty('border-color', '#cbd5e1', 'important');
            }
        }

        // 2. Geolocation Coordinates (Manual check to ensure feedback is shown)
        const geoInputs = panel.querySelectorAll('input[name^="f-geo-"]');
        geoInputs.forEach(inp => {
            if (inp.hasAttribute('data-required')) {
                const isBlank = !inp.value || !inp.value.trim();
                inp.classList.toggle('is-invalid', isBlank);
                const feedback = inp.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.style.display = isBlank ? 'block' : 'none';
                }
                if (isBlank) stepValid = false;
            }
        });

        // 3. Other Attachments
        const attRows = document.querySelectorAll('#other-attachments-list .attachment-row');
        attRows.forEach(row => {
            const descEl = row.querySelector('input[type="text"]');
            const previewEl = row.querySelector('[id$="-preview"]');
            const zoneEl = row.querySelector('[id$="-zone"]');
            if (descEl && previewEl && zoneEl) {
                const hasDesc = descEl.value && descEl.value.trim() !== '';
                const hasFile = previewEl.dataset.dataUri || previewEl.dataset.existingUrl;
                if (hasDesc && !hasFile) {
                    window.showToast?.("Please upload the file for '" + descEl.value + "'", "warning");
                    zoneEl.style.setProperty('border-color', '#ef4444', 'important');
                    stepValid = false;
                } else {
                    zoneEl.style.setProperty('border-color', '#cbd5e1', 'important');
                }
            }
        });
    }

    return stepValid;
};

// Navigation handlers
window.cppNext = function(event) {
    if (event) event.preventDefault();
    
    if (!window.validateStep(window.currentStep)) {
        if (window.showToast) {
            window.showToast("Please fill in all required fields before proceeding.", "warning");
        } else {
            alert("Please fill in all required fields before proceeding.");
        }
        return;
    }

    if (window.currentStep < window.totalSteps) {
        if (window.saveDraftLocally) window.saveDraftLocally();
        window.currentStep++;
        window.updateStepperAndURL();
        window.scrollTo(0, 0);
    }
};

window.cppPrev = function(event) {
    if (event) event.preventDefault();
    if (window.currentStep > 1) {
        if (window.saveDraftLocally) window.saveDraftLocally();
        window.currentStep--;
        window.updateStepperAndURL();
        window.scrollTo(0, 0);
    }
};

window.goToStep = function(step) {
    if (step >= 1 && step <= window.totalSteps) {
        if (window.saveDraftLocally) window.saveDraftLocally();
        window.currentStep = step;
        window.updateStepperAndURL();
    }
};

window.addEventListener('popstate', function(event) {
    if (event.state && event.state.step) {
        window.currentStep = event.state.step;
        window.updateStepperAndURL();
    }
});

// UI Components
window._createUploadZone = function(panelEl, label) {
    if (typeof panelEl === 'string') panelEl = document.getElementById(panelEl);
    if (!panelEl || panelEl.querySelector('.upload-accordion')) return;
    const uid = 'uz-' + Math.random().toString(36).slice(2, 8);
    panelEl.innerHTML = `
        <div class="upload-accordion mt-2" style="border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden; background:#fff;">
            <div class="upload-toggle d-flex align-items-center justify-content-between px-3 py-2" 
                 style="cursor:pointer; background:#f8fafc; border-bottom:1px solid #e2e8f0; user-select:none;">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="paperclip" width="14" style="color:#64748b;"></i>
                    <span class="upload-toggle-label small fw-semibold" style="color:#334155;">${label}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary upload-change-btn" style="display:none; font-size:0.7rem; padding:0.15rem 0.55rem;">Change</button>
                    <i data-lucide="chevron-down" class="upload-arrow" width="16" style="color:#94a3b8; transition:transform 0.2s;"></i>
                </div>
            </div>
            <div class="upload-body" style="display:none; padding:0.9rem 1rem;">
                <div class="drop-zone d-flex align-items-center gap-3 p-3 rounded-3" id="${uid}-dz" tabindex="0" style="border:1.5px dashed #cbd5e1; cursor:pointer; background:#f8fafc;">
                    <div style="width:34px;height:34px;border-radius:8px;background:rgba(21,74,154,0.08);display:flex;align-items:center;justify-content:center;">
                        <i data-lucide="upload-cloud" width="16" style="color:#154A9A;"></i>
                    </div>
                    <div style="flex:1;">
                        <p class="mb-0 small fw-semibold" style="color:#334155;">Click to browse or drag &amp; drop</p>
                        <p class="mb-0" style="font-size:0.71rem;color:#94a3b8;">PDF, DOC, DOCX, PNG, JPG · max 20 MB</p>
                    </div>
                </div>
                <div id="${uid}-list" class="mt-2 small" style="display:none;"></div>
                <input type="file" id="${uid}-file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" style="display:none;">
            </div>
        </div>`;

    if (window.lucide) window.lucide.createIcons({ node: panelEl });
    const toggle = panelEl.querySelector('.upload-toggle');
    const arrow = panelEl.querySelector('.upload-arrow');
    const body = panelEl.querySelector('.upload-body');
    const dz = panelEl.querySelector(`#${uid}-dz`);
    const listEl = panelEl.querySelector(`#${uid}-list`);
    const fileInput = panelEl.querySelector(`#${uid}-file`);
    const labelEl = panelEl.querySelector('.upload-toggle-label');
    const changeBtn = panelEl.querySelector('.upload-change-btn');
    let isOpen = false;

    const open = () => { isOpen = true; body.style.display = 'block'; arrow.style.transform = 'rotate(180deg)'; };
    const close = () => { isOpen = false; body.style.display = 'none'; arrow.style.transform = 'rotate(0deg)'; };
    toggle.addEventListener('click', () => isOpen ? close() : open());

    const handle = (file) => {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            panelEl.dataset.attachDataUri = e.target.result;
            panelEl.dataset.attachFileName = file.name;
            panelEl.dataset.attachMimeType = file.type;
            panelEl.dataset.attachLabel = label;
            panelEl.dataset.attachType = label.toLowerCase().includes('bor') ? 'bor' : (label.toLowerCase().includes('sp') ? 'sp' : (label.toLowerCase().includes('sb') ? 'sb' : (label.toLowerCase().includes('letter') ? 'letter' : 'other')));
            if (labelEl) { labelEl.textContent = '✓ ' + file.name; labelEl.style.color = '#15803d'; }
            if (changeBtn) changeBtn.style.display = 'inline-block';
            dz.style.display = 'none';
            listEl.innerHTML = `<span style="color:#15803d;">&#10003; ${file.name}</span>`;
            listEl.style.display = 'block';
            close();
            if (window.saveDraftLocally) window.saveDraftLocally();
        };
        reader.readAsDataURL(file);
    };

    dz.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', (e) => { if (e.target.files[0]) handle(e.target.files[0]); });
    changeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        delete panelEl.dataset.attachDataUri;
        labelEl.textContent = label; labelEl.style.color = '#334155';
        changeBtn.style.display = 'none'; dz.style.display = 'flex'; listEl.style.display = 'none';
        open();
    });
    open();
};

window.initTagDropdown = function(boxId, dropdownId, hiddenId, inputIdStr) {
    const box = document.getElementById(boxId);
    const dropdown = document.getElementById(dropdownId);
    const hidden = document.getElementById(hiddenId);
    const inputEl = document.getElementById(inputIdStr);
    if (!box || !dropdown || !hidden) return;

    function updateTags() {
        const checkedItems = Array.from(dropdown.querySelectorAll('input[type="checkbox"]:checked'));
        box.querySelectorAll('.badge').forEach(b => b.remove());
        const selectedVals = [];
        checkedItems.forEach(cb => {
            const link = cb.closest('a');
            const val = link.getAttribute('data-val');
            selectedVals.push(val);
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary d-flex align-items-center gap-1';
            badge.innerHTML = `${val} <i data-lucide="x" width="12" style="cursor:pointer;" class="remove-tag"></i>`;
            badge.querySelector('.remove-tag').addEventListener('click', (e) => {
                e.stopPropagation();
                cb.checked = false;
                updateTags();
                if (window.saveDraftLocally) window.saveDraftLocally();
            });
            box.insertBefore(badge, inputEl);
        });
        if (window.lucide) window.lucide.createIcons();
        hidden.value = selectedVals.join('||');
        inputEl.placeholder = checkedItems.length > 0 ? '' : 'Select options...';

        // Trigger real-time validation for this component
        if (window.validateField) {
            window.validateField(hidden);
        }
    }

    const refresh = () => {
        if (hidden.value) {
            hidden.value.split('||').forEach(v => {
                const trimmed = v.trim();
                const link = dropdown.querySelector(`a[data-val="${trimmed}"], a[data-prov="${trimmed}"]`);
                if (link) {
                    const cb = link.querySelector('input');
                    if (cb) cb.checked = true;
                }
            });
        }
        updateTags();
    };

    // Expose refresh function globally if needed
    if (boxId === 'f-provinces-box') window.refreshProvinceTags = refresh;
    if (boxId === 'f-alignment-box') window.refreshSdgTags = refresh;
    if (boxId === 'f-rdp-alignment-box') window.refreshRdpTags = refresh;

    box.addEventListener('click', (e) => { e.stopPropagation(); dropdown.classList.toggle('show'); });
    document.addEventListener('click', () => dropdown.classList.remove('show'));
    dropdown.addEventListener('click', (e) => {
        e.stopPropagation();
        const item = e.target.closest('a.dropdown-item');
        if (!item) return;
        e.preventDefault();
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox) checkbox.checked = !checkbox.checked;
        updateTags();
        if (window.saveDraftLocally) window.saveDraftLocally();
    });
    setTimeout(refresh, 100);
};

window.initRadioToggle = function(radioName, panelId, uploadLabel) {
    const radios = document.querySelectorAll(`input[name="${radioName}"]`);
    const panel = document.getElementById(panelId);
    if (!radios.length || !panel) return;
    function toggle() {
        const checked = document.querySelector(`input[name="${radioName}"]:checked`);
        if (checked && checked.value === 'Yes') {
            panel.style.display = 'block';
            if (uploadLabel) {
                window._createUploadZone(panel, uploadLabel);
            }
        } else {
            panel.style.display = 'none';
        }
    }
    radios.forEach(r => r.addEventListener('change', toggle));
    toggle();
};

window._createAttachmentRow = function(list, index) {
    const row = document.createElement('div');
    row.className = 'attachment-row d-flex align-items-start gap-2 mb-3';
    row.dataset.index = index;
    const uid = 'att-' + index + '-' + Math.random().toString(36).slice(2, 6);
    row.innerHTML = `
        <div style="flex:1;">
            <input type="text" class="form-control form-control-sm mb-2" id="${uid}-desc" placeholder="Document title / description" style="border-radius:8px; font-size:0.83rem;">
            <div class="att-drop-zone d-flex align-items-center gap-2 p-2 rounded" id="${uid}-zone" tabindex="0" style="border:1.5px dashed #cbd5e1; cursor:pointer; background:#f8fafc;">
                <i data-lucide="upload-cloud" width="18" style="color:#94a3b8;"></i>
                <span class="small text-secondary" id="${uid}-label">Click or drag to attach file</span>
            </div>
            <input type="file" id="${uid}-file" style="display:none;" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.shp,.zip">
            <div id="${uid}-preview" class="mt-1"></div>
        </div>
        <button type="button" class="att-remove-btn btn btn-sm btn-outline-danger" style="border-radius:8px;"><i data-lucide="x" width="14"></i></button>`;
    list.appendChild(row);
    if (window.lucide) window.lucide.createIcons();

    const zone = row.querySelector(`#${uid}-zone`);
    const fileInput = row.querySelector(`#${uid}-file`);
    const labelEl = row.querySelector(`#${uid}-label`);
    const preview = row.querySelector(`#${uid}-preview`);

    zone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0]; if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            preview.dataset.dataUri = ev.target.result; preview.dataset.fileName = file.name; preview.dataset.mimeType = file.type;
            preview.innerHTML = `<div class="small text-success fw-bold">✓ ${file.name}</div>`;
            if (labelEl) labelEl.textContent = 'File attached';
            if (window.saveDraftLocally) window.saveDraftLocally();
        };
        reader.readAsDataURL(file);
    });
    row.querySelector('.att-remove-btn').addEventListener('click', () => { row.remove(); if (window.saveDraftLocally) window.saveDraftLocally(); });
};

window._initSignature = function(prefix) {
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
    canvas.dataset.dirty = 'false';
    [drawRadio, uploadRadio].forEach(r => r?.addEventListener('change', () => {
        const isDraw = drawRadio?.checked;
        if (drawPanel) drawPanel.style.display = isDraw ? 'block' : 'none';
        if (uploadPanel) uploadPanel.style.display = isDraw ? 'none' : 'block';
    }));

    const ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#154A9A'; ctx.lineWidth = 2; ctx.lineCap = 'round';
    let drawing = false;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return { x: (clientX - rect.left) * (canvas.width / rect.width), y: (clientY - rect.top) * (canvas.height / rect.height) };
    }

    canvas.addEventListener('mousedown', e => { 
        drawing = true; 
        canvas.dataset.dirty = 'true';
        const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); 
    });
    canvas.addEventListener('mousemove', e => { if (drawing) { const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); } });
    canvas.addEventListener('mouseup', () => { drawing = false; if (hint) hint.textContent = '✓ Signature recorded'; if (window.saveDraftLocally) window.saveDraftLocally(); });
    canvas.addEventListener('touchstart', e => { 
        e.preventDefault(); 
        drawing = true; 
        canvas.dataset.dirty = 'true';
        const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); 
    }, { passive: false });
    canvas.addEventListener('touchmove', e => { e.preventDefault(); if (drawing) { const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); } }, { passive: false });
    canvas.addEventListener('touchend', () => { drawing = false; if (hint) hint.textContent = '✓ Signature recorded'; if (window.saveDraftLocally) window.saveDraftLocally(); });

    if (uploadZone && uploadInput) {
        uploadZone.addEventListener('click', () => uploadInput.click());
        uploadInput.addEventListener('change', () => {
            const file = uploadInput.files[0]; if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => {
                if (preview) {
                    preview.innerHTML = `<img src="${ev.target.result}" style="max-height:80px;"><div class="small text-success">✓ ${file.name}</div>`;
                    preview.dataset.dataUri = ev.target.result; preview.dataset.fileName = file.name; preview.dataset.mimeType = file.type;
                }
                if (window.saveDraftLocally) window.saveDraftLocally();
            };
            reader.readAsDataURL(file);
        });
    }
};

// Main UI Initialization
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    let step = parseInt(urlParams.get('step'));
    window.currentStep = (step >= 1 && step <= window.totalSteps) ? step : 1;
    
    document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
    
    if (window.loadDraftLocally) window.loadDraftLocally();
    
    const form = document.getElementById('cppMainForm');
    if (form) {
        const clearError = (e) => {
            const target = e.target;
            
            // Clear standard invalid class
            if (target.classList.contains('is-invalid')) {
                target.classList.remove('is-invalid');
            }
            
            // Handle Project Type (Checkboxes)
            if (target.name === 'project-type') {
                const checked = document.querySelectorAll('input[name="project-type"]:checked').length;
                const err = document.getElementById('type-error');
                if (err) err.style.display = checked > 0 ? 'none' : 'block';
            }

            // Handle Project Coverage (Radios)
            if (target.name === 'project-coverage') {
                const checked = document.querySelector('input[name="project-coverage"]:checked');
                const err = document.getElementById('coverage-error');
                if (err) err.style.display = checked ? 'none' : 'block';
            }

            // Handle Project Status (Radios)
            if (target.name === 'project-status') {
                const checked = document.querySelector('input[name="project-status"]:checked');
                const err = document.getElementById('status-error');
                if (err) {
                    if (checked) {
                        err.style.display = 'none';
                        err.classList.remove('d-block');
                    } else {
                        err.style.display = 'block';
                        err.classList.add('d-block');
                    }
                }
            }

            // Handle Implementation Schedule (Table)
            if (target.closest('#impl-schedule-body')) {
                const implBody = document.getElementById('impl-schedule-body');
                const implErr = document.getElementById('impl-schedule-err');
                if (implBody && implErr) {
                    const hasCompleteRow = [...implBody.querySelectorAll('tr')].some(row => {
                        const inputs = row.querySelectorAll('input, textarea');
                        return [...inputs].every(i => i.value && i.value.trim());
                    });
                    if (hasCompleteRow) {
                        implErr.style.setProperty('display', 'none', 'important');
                    }
                }
            }
        };

        form.addEventListener('input', (e) => { 
            if (window.saveDraftLocally) window.saveDraftLocally(); 
            clearError(e);
        });

        form.addEventListener('change', (e) => { 
            if (window.saveDraftLocally) window.saveDraftLocally(); 
            clearError(e);
        });
    }
    
    if (window.initRealTimeValidation) window.initRealTimeValidation();
    window.updateStepperAndURL();

    // Coverage Toggles
    const handleCoverage = () => {
        const val = document.querySelector('input[name="project-coverage"]:checked')?.value;
        const ls = document.getElementById('location-specific-fields');
        const ip = document.getElementById('inter-province-fields');
        const fp = document.getElementById('f-province');
        const pti = document.getElementById('provinces-tag-input');
        const fpv = document.getElementById('f-provinces');

        if (ls) ls.style.display = val === 'Location-Specific' ? 'block' : 'none';
        if (ip) ip.style.display = val === 'Inter-Province' ? 'block' : 'none';
        
        if (fp) fp.disabled = val !== 'Location-Specific';
        if (pti) pti.disabled = val !== 'Inter-Province';
        if (fpv) fpv.disabled = val !== 'Inter-Province';
    };
    document.querySelectorAll('input[name="project-coverage"]').forEach(r => r.addEventListener('change', handleCoverage));
    handleCoverage();

    // Endorsements
    // Generic Data-Upload-Panel Handler (for textareas and single inputs)
    document.querySelectorAll('[data-upload-panel]').forEach(input => {
        const panelId = input.getAttribute('data-upload-panel');
        const panel = document.getElementById(panelId);
        if (!panel) return;

        const label = input.getAttribute('data-upload-label') || 'Document';
        
        const toggle = () => {
            let shouldShow = false;
            if (input.type === 'checkbox' || input.type === 'radio') {
                shouldShow = input.checked && input.value === 'Yes';
            } else {
                shouldShow = input.value.trim() !== '';
            }

            if (shouldShow) {
                panel.style.display = 'block';
                window._createUploadZone(panel, label);
            } else {
                // If it's a radio, only hide if NO radio in the group is checked as 'Yes'
                if (input.type === 'radio') {
                    const checked = document.querySelector(`input[name="${input.name}"]:checked`);
                    if (!checked || checked.value !== 'Yes') {
                        panel.style.display = 'none';
                    }
                } else {
                    panel.style.display = 'none';
                }
            }
        };

        // Listen for input (textareas) and change (radios/checkboxes)
        input.addEventListener('input', toggle);
        input.addEventListener('change', toggle);
        
        // Initial state
        toggle();
    });

    // DED
    const dedCb = document.getElementById('prep-ded');
    const dedPl = document.getElementById('ded-upload-panel');
    if (dedCb && dedPl) {
        const toggle = () => {
            dedPl.style.display = dedCb.checked ? 'block' : 'none';
            const dz = document.getElementById('ded-dropzone');
            const fi = document.getElementById('ded-file-input');
            if (dedCb.checked && dz && fi && !dz._wired) {
                dz._wired = true;
                dz.addEventListener('click', () => fi.click());
                fi.addEventListener('change', () => {
                    const file = fi.files[0]; if (!file) return;
                    const r = new FileReader(); r.onload = e => {
                        dedPl.dataset.attachDataUri = e.target.result;
                        dedPl.dataset.attachFileName = file.name;
                        dedPl.dataset.attachLabel = 'Detailed Engineering Design';
                        dedPl.dataset.attachType = 'ded';
                        document.getElementById('ded-file-name').textContent = '✓ ' + file.name;
                        if (window.saveDraftLocally) window.saveDraftLocally();
                    }; r.readAsDataURL(file);
                });
            }
        };
        dedCb.addEventListener('change', toggle); toggle();
    }

    // Dropdowns
    window.initTagDropdown('f-alignment-box', 'sdg-dropdown', 'f-alignment', 'sdg-tag-input');
    window.initTagDropdown('f-rdp-alignment-box', 'rdp-dropdown', 'f-rdp-alignment', 'rdp-tag-input');
    window.initTagDropdown('f-provinces-box', 'provinces-dropdown', 'f-provinces', 'provinces-tag-input');

    // Radio Toggles
    window.initRadioToggle('consultation-status', 'consult-yes-panel', 'Consultation Document');
    window.initRadioToggle('fs-status', 'fs-yes-panel', 'Pre-FS/FS Document');
    window.initRadioToggle('relocation-status', 'reloc-yes-panel', 'Relocation Action Plan');
    window.initRadioToggle('row-status', 'row-yes-panel', 'ROW Document');
    window.initRadioToggle('icc-status', 'icc-yes-panel', 'NEDA Board / ICC Approval');

    // Consultation Dates
    const dp = document.getElementById('consult-date-picker');
    const db = document.getElementById('consult-dates-box');
    const hd = document.getElementById('f-consult-done-dates');
    if (dp && db && hd) {
        const update = (nd) => {
            let cur = hd.value ? hd.value.split(',') : [];
            if (nd && !cur.includes(nd)) cur.push(nd);
            db.querySelectorAll('.badge').forEach(b => b.remove());
            cur.forEach(d => {
                const b = document.createElement('span'); b.className = 'badge bg-primary d-flex align-items-center gap-1';
                b.innerHTML = `${d} <i data-lucide="x" width="12" class="remove-date"></i>`;
                b.querySelector('.remove-date').addEventListener('click', () => { cur = cur.filter(x => x !== d); hd.value = cur.join(','); update(); });
                db.insertBefore(b, dp);
            });
            hd.value = cur.join(',');
            if (window.lucide) window.lucide.createIcons();
            if (window.saveDraftLocally) window.saveDraftLocally();
        };
        window.refreshConsultationDates = update;
        dp.addEventListener('change', function() { if (this.value) { update(this.value); this.value = ''; } });
        setTimeout(() => { if (hd.value) update(); }, 100);
    }

    // Geotagged Photo — handled in cpp-form.js (full-featured version with image/PDF preview)

    // Other Attachments — handled in cpp-form.js

    // Certification Toggle for Submit Button
    const certifyCb = document.getElementById('f-certify');
    const submitBtn = document.getElementById('cppSubmitBtn');
    if (certifyCb && submitBtn) {
        const toggleSubmit = () => {
            submitBtn.disabled = !certifyCb.checked;
            // Optional: style change
            submitBtn.style.opacity = certifyCb.checked ? '1' : '0.6';
            submitBtn.style.cursor = certifyCb.checked ? 'pointer' : 'not-allowed';
        };
        certifyCb.addEventListener('change', toggleSubmit);
        toggleSubmit(); // Initial state
    }

    if (window.lucide) window.lucide.createIcons();
});
