/**
 * Toast Notification Utility
 */
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toast-container') || (() => {
        const c = document.createElement('div');
        c.id = 'toast-container';
        c.style.cssText = 'position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:10px;';
        document.body.appendChild(c);
        return c;
    })();

    const toast = document.createElement('div');
    const color = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#f59e0b');
    const icon = type === 'success' ? 'check-circle' : (type === 'error' ? 'alert-circle' : 'info');
    
    toast.style.cssText = `
        background: #fff; color: #334155; padding: 12px 20px; border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        display: flex; align-items: center; gap: 12px; min-width: 300px;
        transform: translateX(120%); transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-left: 4px solid ${color};
    `;
    
    toast.innerHTML = `
        <i data-lucide="${icon}" width="20" style="color:${color}"></i>
        <div style="flex:1; font-size:0.9rem; font-weight:500;">${message}</div>
        <i data-lucide="x" width="16" style="color:#94a3b8; cursor:pointer;" onclick="this.parentElement.remove()"></i>
    `;
    
    container.appendChild(toast);
    if (window.lucide) window.lucide.createIcons({ node: toast });
    
    // Animate in
    setTimeout(() => { toast.style.transform = 'translateX(0)'; }, 10);
    
    // Auto remove
    setTimeout(() => {
        toast.style.transform = 'translateX(120%)';
        setTimeout(() => toast.remove(), 400);
    }, 4000);
};

// Override alert with toast
window.alert = (msg) => window.showToast(msg, 'info');

const getDraftKey = () => {
    const editId = window.EDIT_ID || sessionStorage.getItem('cpp_edit_id');
    return editId ? `cpp_submission_draft_${editId}` : 'cpp_submission_draft_new';
};

// Save form state to local storage
window.saveDraftLocally = function() {
    // Prevent saving if we are currently loading data or in the middle of a server save
    if (window.isInitializing || window.isSaving) return;
    const data = gatherAllData();
    if (!data) return;

    data._timestamp = Date.now();
    localStorage.setItem(getDraftKey(), JSON.stringify(data));
};

// Load form state from local storage
window.loadDraftLocally = function() {
    const saved = localStorage.getItem(getDraftKey());
    if (!saved) return;
    
    try {
        const draftData = JSON.parse(saved);
        const form = document.getElementById('cppMainForm');
        if (!form) return;
        
        Object.keys(draftData).forEach(key => {
            const val = draftData[key];
            const input = form.querySelector(`[name="${key}"], #${key}`);
            
            if (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    const options = form.querySelectorAll(`[name="${key}"], #${key}`);
                    options.forEach(opt => {
                        if (Array.isArray(val)) {
                            opt.checked = val.includes(opt.value);
                        } else {
                            opt.checked = (opt.value === val || val === 'on');
                        }
                        // Dispatch change to trigger UI dependencies
                        opt.dispatchEvent(new Event('change'));
                    });
                } else {
                    input.value = val;
                    // Trigger change for all to be safe
                    input.dispatchEvent(new Event('change'));
                }
            }
        });

        // Restore Implementation Schedule
        if (draftData.impl_schedule && draftData.impl_schedule.length) {
            const body = document.getElementById('impl-schedule-body');
            if (body) {
                // Keep the first row if it exists, otherwise clear everything
                const rows = body.querySelectorAll('tr');
                const firstRow = rows[0];
                body.innerHTML = '';
                if (firstRow) body.appendChild(firstRow);

                draftData.impl_schedule.forEach((data, index) => {
                    let targetRow;
                    if (index === 0 && firstRow) {
                        targetRow = firstRow;
                    } else {
                        // We need the _createRow function which might be local to a closure
                        // But we can manually recreate it here or trigger the add button
                        const addBtn = document.getElementById('impl-add-row');
                        if (addBtn) {
                            addBtn.click();
                            const newRows = body.querySelectorAll('tr');
                            targetRow = newRows[newRows.length - 1];
                        }
                    }

                    if (targetRow) {
                        const inputs = targetRow.querySelectorAll('input, textarea');
                        if (inputs.length >= 4) {
                            inputs[0].value = data.year || '';
                            inputs[1].value = data.physical_target || '';
                            inputs[2].value = data.indicator || '';
                            inputs[3].value = data.amount || '';
                        }
                    }
                });
            }
        }
        // Restore Attachments
        if (draftData.attachments && draftData.attachments.length) {
            const attPanelIdMap = {
                'sp':          'sp-upload-panel',
                'sb':          'sb-upload-panel',
                'letter':      'letter-upload-panel',
                'bor':         'bor-upload-panel',
                'ded':         'ded-upload-panel',
                'env':         'env-clearance-upload-panel',
                'consult':     'consult-yes-panel',
                'hgdg':        'hgdg-upload-panel',
            };

            draftData.attachments.forEach(att => {
                const panelId = attPanelIdMap[att.type] || `${att.type}-upload-panel`;
                const panel = document.getElementById(panelId);
                const label = att.label || 'Document';

                if (panel && att.data) {
                    panel.style.display = 'block';
                    panel.dataset.attachDataUri = att.data;
                    panel.dataset.attachFileName = att.name || 'document.pdf';
                    panel.dataset.attachMimeType = att.mime || 'application/pdf';
                    panel.dataset.attachLabel = label;

                    if (window._createUploadZone) {
                        window._createUploadZone(panel, label);
                    }
                    
                    // Update UI manually for _createUploadZone to show "✓ filename"
                    const labelEl = panel.querySelector('.upload-toggle-label');
                    const changeBtn = panel.querySelector('.upload-change-btn');
                    const dz = panel.querySelector('.drop-zone');
                    const listEl = panel.querySelector('[id$="-list"]');
                    const body = panel.querySelector('.upload-body');

                    if (labelEl) { labelEl.textContent = '✓ ' + (att.name || 'Attached'); labelEl.style.color = '#15803d'; }
                    if (changeBtn) changeBtn.style.display = 'inline-block';
                    if (dz) dz.style.display = 'none';
                    if (listEl) {
                        listEl.innerHTML = `<span style="color:#15803d;">&#10003; ${att.name || 'Attached'}</span>`;
                        listEl.style.display = 'block';
                    }
                    if (body) body.style.display = 'none';
                } 
                // Handle Geotagged Photo
                else if (att.type === 'geo_photo' && att.data) {
                    const geoPreview = document.getElementById('geo-photo-preview');
                    const geoZone = document.getElementById('geo-photo-zone');
                    if (geoPreview) {
                        geoPreview.dataset.dataUri = att.data;
                        geoPreview.dataset.fileName = att.name;
                        geoPreview.dataset.mimeType = att.mime;
                        
                        const isImage = att.mime?.startsWith('image/');
                        if (isImage) {
                            geoPreview.innerHTML = `
                                <div class="position-relative d-inline-block mt-2">
                                    <img src="${att.data}" style="max-height:200px; max-width:100%; border-radius:8px; border:1px solid #e2e8f0;" alt="Geotagged Photo">
                                    <div class="text-success small fw-bold mt-1"><i data-lucide="check-circle" width="13"></i> ${att.name}</div>
                                </div>`;
                        } else {
                            geoPreview.innerHTML = `<div class="text-success small fw-bold mt-2 p-2 rounded" style="background:rgba(22,163,74,0.07);border:1px solid rgba(22,163,74,0.2);">✓ ${att.name}</div>`;
                        }
                        if (geoZone) geoZone.querySelector('p')?.classList.add('text-success');
                        if (window.lucide) window.lucide.createIcons({ node: geoPreview });
                    }
                }
                // Handle Other Attachments
                else if (att.type === 'other' && att.data) {
                    const attList = document.getElementById('other-attachments-list');
                    if (attList) {
                        // Create a new row (using the internal helper if reachable, but since it's an IIFE we might need to trigger the btn)
                        const addBtn = document.getElementById('add-attachment-btn');
                        if (addBtn) {
                            addBtn.click();
                            setTimeout(() => {
                                const rows = attList.querySelectorAll('.attachment-row');
                                const lastRow = rows[rows.length - 1];
                                if (lastRow) {
                                    const descInput = lastRow.querySelector('input[type="text"]');
                                    const preview = lastRow.querySelector('div[id$="-preview"]');
                                    const labelEl = lastRow.querySelector('span[id$="-label"]');
                                    const zone = lastRow.querySelector('div[id$="-zone"]');
                                    if (descInput) descInput.value = att.label || '';
                                    if (preview) {
                                        preview.dataset.dataUri = att.data;
                                        preview.dataset.fileName = att.name;
                                        preview.dataset.mimeType = att.mime;
                                        preview.innerHTML = `<div class="small text-success fw-bold">✓ ${att.name}</div>`;
                                    }
                                    if (labelEl) labelEl.textContent = 'File attached';
                                    if (zone) zone.style.borderColor = '#22c55e';
                                }
                            }, 100);
                        }
                    }
                }
                // Handle Signatures — defer 350ms so _initSignature listeners are attached first
                else if (att.type.startsWith('sig_') && att.data) {
                    const prefix = att.type.replace('sig_', '');
                    const preview = document.getElementById(`sig-${prefix}-upload-preview`);
                    if (preview) {
                        preview.dataset.dataUri = att.data;
                        preview.dataset.fileName = att.name;
                        preview.dataset.mimeType = att.mime;
                        const img = preview.querySelector('img');
                        if (img) img.src = att.data;
                        preview.style.display = 'block';
                        
                        // Switch radio to upload mode
                        const radio = document.getElementById(`sig-${prefix}-upload`);
                        if (radio) {
                            radio.checked = true;
                            radio.dispatchEvent(new Event('change'));
                        }
                    }
                }
            });
        }
    } catch (e) {
        console.error("Error parsing draft data", e);
    }
};

// Data Fetching Logic
function initDataFetchers() {
    const sectorSelect = document.getElementById('f-sector');
    const subSectorSelect = document.getElementById('f-sub-sector');
    if (sectorSelect && subSectorSelect) {
        sectorSelect.addEventListener('change', function() {
            const sectorId = this.value;
            subSectorSelect.innerHTML = '<option value="">-- Select Sub-Sector --</option>';
            if (!sectorId) { subSectorSelect.disabled = true; return; }
            subSectorSelect.disabled = true;
            fetch(`/projects/getSubSectors?sector_id=${sectorId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.subsector_name || item.name;
                        subSectorSelect.appendChild(opt);
                    });
                    subSectorSelect.disabled = false;
                    const saved = localStorage.getItem(getDraftKey());
                    if (saved) {
                        try {
                            const draft = JSON.parse(saved);
                            if (draft['f-sub-sector']) subSectorSelect.value = draft['f-sub-sector'];
                        } catch (e) {}
                    }
                });
        });
    }

    const provinceSelect = document.getElementById('f-province');
    const districtSelect = document.getElementById('f-district');
    const municipalitySelect = document.getElementById('f-municipality');
    const barangaySelect = document.getElementById('f-barangay');
    
    if (provinceSelect && districtSelect) {
        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
            if (municipalitySelect) municipalitySelect.innerHTML = '<option value="">-- Select City/Municipality --</option>';
            if (barangaySelect) barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            if (!provinceId) { districtSelect.disabled = true; return; }
            fetch(`/location/getDistricts?province_id=${provinceId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id; opt.textContent = item.district_name || item.name;
                        districtSelect.appendChild(opt);
                    });
                    districtSelect.disabled = false;
                    const saved = JSON.parse(localStorage.getItem(getDraftKey()) || '{}');
                    if (saved['f-district']) { districtSelect.value = saved['f-district']; setTimeout(() => districtSelect.dispatchEvent(new Event('change')), 50); }
                });
        });
    }

    if (districtSelect && municipalitySelect) {
        districtSelect.addEventListener('change', function() {
            const districtId = this.value;
            municipalitySelect.innerHTML = '<option value="">-- Select City/Municipality --</option>';
            if (!districtId) { municipalitySelect.disabled = true; return; }
            fetch(`/location/getMunicipalities?province_id=${provinceSelect.value}&district_id=${districtId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id; opt.textContent = item.municipality_name || item.name;
                        municipalitySelect.appendChild(opt);
                    });
                    municipalitySelect.disabled = false;
                    const saved = JSON.parse(localStorage.getItem(getDraftKey()) || '{}');
                    if (saved['f-municipality']) { municipalitySelect.value = saved['f-municipality']; setTimeout(() => municipalitySelect.dispatchEvent(new Event('change')), 50); }
                });
        });
    }

    if (municipalitySelect && barangaySelect) {
        municipalitySelect.addEventListener('change', function() {
            const municipalityId = this.value;
            barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
            if (!municipalityId) { barangaySelect.disabled = true; return; }
            fetch(`/location/getBarangays?municipality_id=${municipalityId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id; opt.textContent = item.barangay_name || item.name;
                        barangaySelect.appendChild(opt);
                    });
                    barangaySelect.disabled = false;
                    const saved = JSON.parse(localStorage.getItem(getDraftKey()) || '{}');
                    if (saved['f-barangay']) barangaySelect.value = saved['f-barangay'];
                });
        });
    }
}

// ---------------------------------------------------------
// BACKEND INTEGRATION: Data Gathering & AJAX Submission
// ---------------------------------------------------------

/**
 * Scrapes the entire form and all custom components (tables, uploads, canvases)
 * returns a plain object ready for JSON stringification
 */
function gatherAllData() {
    const form = document.getElementById('cppMainForm');
    if (!form) return {};

    // 1. Basic Fields via FormData
    const fd = new FormData(form);
    const data = {};
    fd.forEach((value, key) => {
        if (data[key]) {
            if (!Array.isArray(data[key])) data[key] = [data[key]];
            data[key].push(value);
        } else {
            data[key] = value;
        }
    });

    // 2. Project Preparation Status (JSON)
    data.prep_status = {
        site: document.getElementById('prep-site')?.checked || false,
        row: document.getElementById('prep-row')?.checked || false,
        ded: document.getElementById('prep-ded')?.checked || false
    };

    // Auto-set signature dates if empty
    const today = new Date().toISOString().split('T')[0];
    if (!data['f-prepared-date']) data['f-prepared-date'] = today;
    if (!data['f-noted-date']) data['f-noted-date'] = today;

    // Explicitly grab these to ensure they are captured
    data['f-consult-highlights'] = document.getElementById('f-consult-highlights')?.value || '';
    data['f-hgdg'] = document.getElementById('f-hgdg')?.value || '';

    // 3. Project Types (Array)
    data['project-type'] = [];
    document.querySelectorAll('input[name="project-type"]:checked').forEach(cb => {
        data['project-type'].push(cb.value);
    });

    // 4. Implementation Schedule (Array of Objects)
    data.impl_schedule = [];
    document.querySelectorAll('#impl-schedule-body tr').forEach((tr, index) => {
        const inputs = tr.querySelectorAll('input, textarea');
        if (inputs.length >= 4) {
            data.impl_schedule.push({
                year: inputs[0].value,
                physical_target: inputs[1].value,
                indicator: inputs[2].value,
                amount: inputs[3].value,
                sort_order: index
            });
        }
    });

    // 5. Consultation Dates (Array)
    const cDates = document.getElementById('f-consult-done-dates')?.value;
    data.consultation_dates = cDates ? cDates.split(',') : [];

    // 6. Attachments (DataURIs)
    // We scrape all elements that might hold uploaded files in datasets
    data.attachments = [];
    
    // Named upload panels
    // Panel IDs must exactly match the HTML id attributes in the blade partials
    const panels = [
        'sp-upload-panel', 'sb-upload-panel', 'letter-upload-panel',
        'bor-upload-panel', 'ded-upload-panel', 'env-clearance-upload-panel',
        'consult-yes-panel', 'hgdg-upload-panel', 'spatial-cov-upload-panel'
    ];
    // Map panel id → attachment type key stored in media custom_properties
    const panelTypeMap = {
        'sp-upload-panel':              'sp',
        'sb-upload-panel':              'sb',
        'letter-upload-panel':          'letter',
        'bor-upload-panel':             'bor',
        'ded-upload-panel':             'ded',
        'env-clearance-upload-panel':   'env',
        'consult-yes-panel':            'consult',
        'hgdg-upload-panel':            'hgdg',
        'spatial-cov-upload-panel':     'spatial-cov',
    };
    panels.forEach(id => {
        const p = document.getElementById(id);
        if (!p) return;
        const type = panelTypeMap[id] || id.replace('-upload-panel', '').replace('-panel', '');

        if (p.dataset.attachDataUri) {
            // User uploaded a new replacement file
            data.attachments.push({
                type:  type,
                label: p.dataset.attachLabel || '',
                name:  p.dataset.attachFileName,
                mime:  p.dataset.attachMimeType,
                data:  p.dataset.attachDataUri
            });
        } else if (p.dataset.attachExistingUrl) {
            // No replacement chosen — tell the server to keep the existing file
            data.attachments.push({
                type:         type,
                label:        p.dataset.attachLabel || '',
                name:         p.dataset.attachExistingName || p.dataset.attachFileName || '',
                existing_url: p.dataset.attachExistingUrl
            });
        }
    });

    // Geotagged Photo
    const gp = document.getElementById('geo-photo-preview');
    if (gp) {
        if (gp.dataset.dataUri) {
            // New upload
            data.attachments.push({ type: 'geo_photo', name: gp.dataset.fileName, mime: gp.dataset.mimeType, data: gp.dataset.dataUri });
        } else if (gp.dataset.existingUrl) {
            // Keep existing
            data.attachments.push({ type: 'geo_photo', name: gp.dataset.fileName, existing_url: gp.dataset.existingUrl });
        }
    }

    // Other Attachments List
    document.querySelectorAll('#other-attachments-list .attachment-row').forEach(row => {
        const desc = row.querySelector('input[type="text"]')?.value;
        const preview = row.querySelector('div[id$="-preview"]');
        if (preview) {
            if (preview.dataset.dataUri) {
                // New upload
                data.attachments.push({
                    type: 'other',
                    label: desc || 'Other Attachment',
                    name: preview.dataset.fileName,
                    mime: preview.dataset.mimeType,
                    data: preview.dataset.dataUri
                });
            } else if (preview.dataset.existingUrl) {
                // Keep existing
                data.attachments.push({
                    type: 'other',
                    label: desc || 'Other Attachment',
                    name: preview.dataset.fileName,
                    existing_url: preview.dataset.existingUrl
                });
            }
        }
    });

    // 7. Signatures (Canvas or Upload)
    ['prep', 'noted'].forEach(prefix => {
        const isUpload = document.getElementById(`sig-${prefix}-upload`)?.checked;
        if (isUpload) {
            const preview = document.getElementById(`sig-${prefix}-upload-preview`);
            const panel = document.getElementById(`sig-${prefix}-upload-panel`);
            
            if (preview && preview.dataset.dataUri) {
                // New upload
                data.attachments.push({ type: `sig_${prefix}`, name: preview.dataset.fileName, mime: preview.dataset.mimeType, data: preview.dataset.dataUri });
            } else if (panel && panel.dataset.attachExistingUrl) {
                // Keep existing upload
                data.attachments.push({ type: `sig_${prefix}`, name: panel.dataset.attachFileName || 'signature.png', existing_url: panel.dataset.attachExistingUrl });
            }
        } else {
            // It's a canvas drawing
            const canvas = document.getElementById(`sig-canvas-${prefix}`);
            if (canvas && canvas.dataset.dirty === 'true') {
                const dataUri = canvas.toDataURL('image/png');
                data.attachments.push({ type: `sig_${prefix}`, name: `signature_${prefix}.png`, mime: 'image/png', data: dataUri });
            }
        }
    });

    return data;
}

/**
 * Submit the form via AJAX to the Laravel Backend
 */
window.cppSubmit = async function(event) {
    if (event) event.preventDefault();
    const currentKey = getDraftKey();
    
    // Confirmation
    if (!confirm("Are you sure you want to submit this Project Profile?")) return;

    const btn = document.getElementById('cppSubmitBtn');
    const originalText = btn.innerHTML;
    window.isSaving = true;
    
    try {
        // 1. Show Loading
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Submitting...`;

        // 2. Final Validation Check for ALL steps
        let firstInvalidStep = 0;
        for (let s = 1; s <= window.totalSteps; s++) {
            if (window.validateStep && !window.validateStep(s)) {
                firstInvalidStep = s;
                break;
            }
        }

        if (firstInvalidStep > 0) {
            window.showToast?.("Please complete all required fields and uploads in all steps before submitting.", "warning");
            window.goToStep?.(firstInvalidStep);
            btn.disabled = false;
            btn.innerHTML = originalText;
            window.isSaving = false;
            return;
        }

        // 3. Gather All Data
        const payload = gatherAllData();
        console.log("Submitting Payload:", payload);

        // 3. Send AJAX Request
        const response = await fetch('/v2/cipg_submissions/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok) {
            // 4. Success Handling
            showToast("Success! Your CPP submission has been received.", "success");
            localStorage.removeItem(currentKey);
            localStorage.removeItem(getDraftKey());
            setTimeout(() => {
                window.location.href = result.redirect || '/v2/cipg_submissions';
            }, 1000);
        } else {
            // 5. Validation/Server Error Handling
            console.error("Submission Error:", result);
            
            // Clear previous errors
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            if (response.status === 422 && result.errors) {
                // Map errors to fields
                let firstErrEl = null;
                Object.keys(result.errors).forEach(key => {
                    const input = document.querySelector(`[name="${key}"], #${key}`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.parentElement.querySelector('.invalid-feedback');
                        if (feedback) feedback.innerText = result.errors[key][0];
                        if (!firstErrEl) firstErrEl = input;
                    }
                });
                
                if (firstErrEl) {
                    const step = firstErrEl.closest('.form-step')?.id.replace('step-', '');
                    if (step) window.goToStep(parseInt(step));
                    firstErrEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                showToast("Please correct the errors highlighted in red.", "error");
            } else {
                showToast(result.message || "An error occurred during submission.", "error");
            }
        }
    } catch (error) {
        console.error("Fetch Error:", error);
        alert("A network error occurred. Please check your connection and try again.");
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
        window.isSaving = false;
    }
};



window.cppDraft = async function(event) {
    if (event) event.preventDefault();
    const currentKey = getDraftKey();
    
    // Requirement: At least a title to save to DB
    const title = document.getElementById('f-title')?.value;
    if (!title || title.trim() === "") {
        showToast("Please enter at least a Project Title before saving as draft to the server.", "warning");
        return;
    }

    const btn = document.getElementById('cppDraftBtn');
    const originalText = btn.innerHTML;
    window.isSaving = true;

    try {
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Saving...`;

        // 1. Gather all data as if submitting
        const payload = gatherAllData();
        payload.is_draft = true;

        // 2. Send to Server
        const response = await fetch('/v2/cipg_submissions/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok) {
            showToast("Draft saved successfully to the server.", "success");
            
            // 3. Update the form with the new ID if it was just created
            if (result.id) {
                const idInput = document.querySelector('input[name="submission_id"]');
                if (idInput) idInput.value = result.id;
                window.EDIT_ID = result.id;
            }
            
            // 4. Clear local storage because it is now safely in the database
            localStorage.removeItem(currentKey);
            localStorage.removeItem(getDraftKey());

            // 5. Redirect back to submissions list
            setTimeout(() => {
                window.location.href = '/v2/cipg_submissions';
            }, 1000);
        } else {
            showToast(result.message || "Failed to save draft to server.", "error");
        }
    } catch (error) {
        console.error("Draft Save Error:", error);
        showToast("A network error occurred while saving your draft.", "error");
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
        window.isSaving = false;
    }
};

document.addEventListener('DOMContentLoaded', async function() {
    window.isInitializing = true;
    initDataFetchers();

    const editId = window.EDIT_ID || sessionStorage.getItem('cpp_edit_id');
    if (editId) sessionStorage.removeItem('cpp_view_id'); // Ensure we are not in read-only mode
    const viewId = sessionStorage.getItem('cpp_view_id');
    const targetId = editId || viewId;

    if (targetId && targetId !== "" && !targetId.startsWith('DRAFT-')) {
        // Load from Server
        try {
            const res = await fetch(`/v2/cipg_submissions/${targetId}/details`);
            if (res.ok) {
                const data = await res.json();
                
                // 1. Populate standard fields
                const form = document.getElementById('cppMainForm');
                Object.keys(data).forEach(key => {
                    const val = data[key];
                    const input = form.querySelector(`[name="${key}"], #${key}`);
                    if (input) {
                        if (input.type === 'checkbox' || input.type === 'radio') {
                            const options = form.querySelectorAll(`[name="${key}"], #${key}`);
                            options.forEach(opt => {
                                if (Array.isArray(val)) opt.checked = val.includes(opt.value);
                                else opt.checked = (opt.value === val || val === true || val === 'on');
                            });
                        } else {
                            input.value = val || '';
                            // Trigger events for dependent UI logic (like showing upload panels)
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            input.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                });

                // After setting radio values, fire change events so UI panels react.
                // Without this, coverage-dependent panels (Inter-Province, Location-Specific)
                // stay hidden because .checked = true does not trigger change listeners.
                ['project-coverage', 'project-status', 'project-type'].forEach(name => {
                    const checked = form.querySelector(`input[name="${name}"]:checked`);
                    if (checked) checked.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // 2. Implementation Schedule
                if (data.impl_schedule) {
                    const body = document.getElementById('impl-schedule-body');
                    if (body) {
                        body.innerHTML = '';
                        data.impl_schedule.forEach(row => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="text" class="form-control form-control-sm" value="${row.year || ''}"></td>
                                <td><textarea class="form-control form-control-sm" rows="2">${row.physical_target || ''}</textarea></td>
                                <td><input type="text" class="form-control form-control-sm" value="${row.indicator || ''}"></td>
                                <td><input type="number" class="form-control form-control-sm" value="${row.amount || ''}"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i data-lucide="trash-2" width="14"></i></button></td>
                            `;
                            body.appendChild(tr);
                        });
                        if (window.lucide) window.lucide.createIcons({ node: body });
                    }
                }

                // 3. Logframe & Endorsements
                if (data.logframe) {
                    Object.keys(data.logframe).forEach(k => {
                        const el = document.getElementById(k);
                        if (el) el.value = data.logframe[k] || '';
                    });
                }
                if (data.endorsement) {
                    Object.keys(data.endorsement).forEach(k => {
                        const el = document.getElementById(k);
                        if (el) {
                            el.value = data.endorsement[k] || '';
                            // Trigger input/change so UI reactive logic (like upload panels) triggers
                            el.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                }

                // 4. Consultation Dates
                if (data.consult_dates) {
                    const list = document.getElementById('consult-dates-list');
                    if (list) {
                        list.innerHTML = '';
                        data.consult_dates.forEach(date => {
                            const badge = document.createElement('div');
                            badge.className = 'consult-date-badge';
                            badge.innerHTML = `<span>${date}</span><i data-lucide="x" width="12" class="ms-1 remove-date"></i>`;
                            list.appendChild(badge);
                        });
                        const hiddenInput = document.getElementById('f-consult-done-dates');
                        if (hiddenInput) hiddenInput.value = data.consult_dates.join(',');
                        if (window.lucide) window.lucide.createIcons({ node: list });
                    }
                }

                // 5. Inter-Province Tags
                // Delay until after handleCoverage() has shown #inter-province-fields,
                // otherwise the dropdown checkboxes may not be in the visible DOM.
                if (data['f-provinces']) {
                    const hiddenInput = document.getElementById('f-provinces');
                    if (hiddenInput) {
                        hiddenInput.value = data['f-provinces'];
                        // Use setTimeout so this runs after the coverage panel is displayed
                        setTimeout(() => {
                            if (window.refreshProvinceTags) window.refreshProvinceTags();
                        }, 150);
                    }
                }

                // 5.1 SDG & RDP Alignment Tags
                if (data['f-alignment']) {
                    const hiddenInput = document.getElementById('f-alignment');
                    if (hiddenInput) {
                        hiddenInput.value = data['f-alignment'];
                        if (window.refreshSdgTags) window.refreshSdgTags();
                    }
                }
                if (data['f-rdp-alignment']) {
                    const hiddenInput = document.getElementById('f-rdp-alignment');
                    if (hiddenInput) {
                        hiddenInput.value = data['f-rdp-alignment'];
                        if (window.refreshRdpTags) window.refreshRdpTags();
                    }
                }

                // 6. Attachments — build full interactive upload zone so user can replace files
                if (data.attachments) {
                    // Map attachment type → human-readable label (mirrors gatherAllData panel list)
                    const attLabelMap = {
                        'sp':          'SP Resolution Document',
                        'sb':          'SB Resolution Document',
                        'letter':      'Letter Request Document',
                        'bor':         'BOR/BOT Resolution Document',
                        'ded':         'Detailed Engineering Design',
                        'env':         'Environmental Clearance Document',
                        'consult-yes': 'Public Consultation Documentation',
                        'hgdg':        'HGDG Document',
                        'spatial-cov': 'Spatial Coverage File',
                        'geo_photo':   'Geotagged Photo',
                        'sig_prep':    'Signature (Prepared By)',
                        'sig_noted':   'Signature (Noted By)',
                    };
                    // Map type → actual panel HTML id for restoration
                    // NOTE: sig_ types and geo_photo are handled separately below
                    const attPanelIdMap = {
                        'sp':          'sp-upload-panel',
                        'sb':          'sb-upload-panel',
                        'letter':      'letter-upload-panel',
                        'bor':         'bor-upload-panel',
                        'ded':         'ded-upload-panel',
                        'env':         'env-clearance-upload-panel',
                        'consult':     'consult-yes-panel',
                        'hgdg':        'hgdg-upload-panel',
                        'spatial-cov': 'spatial-cov-upload-panel',
                    };

                    data.attachments.forEach(att => {
                        // Resolve panel id from explicit map first, then fall back to sig_ normalization
                        const panelId = attPanelIdMap[att.type] || `${att.type}-upload-panel`;
                        const panel = document.getElementById(panelId);
                        const label = attLabelMap[att.type] || (att.label || 'Document');

                        if (panel) {
                            panel.style.display = 'block';
                            panel.dataset.attachExistingUrl = att.url || '';
                            panel.dataset.attachExistingName = att.name || '';
                            panel.dataset.attachFileName = att.name || '';
                            panel.dataset.attachType = att.type || '';
                            panel.dataset.attachLabel = label;

                            if (window._createUploadZone) {
                                window._createUploadZone(panel, label);
                            }

                            panel.dataset.attachExistingUrl  = att.url  || '';
                            panel.dataset.attachExistingName = att.name || '';
                            panel.dataset.attachFileName     = att.name || '';
                            panel.dataset.attachType         = att.type || '';
                            panel.dataset.attachLabel        = label;

                            const labelEl    = panel.querySelector('.upload-toggle-label');
                            const changeBtn  = panel.querySelector('.upload-change-btn');
                            const dz         = panel.querySelector('.drop-zone');
                            const listEl     = panel.querySelector('[id$="-list"]');
                            const body       = panel.querySelector('.upload-body');
                            const arrow      = panel.querySelector('.upload-arrow');

                            if (labelEl) { labelEl.textContent = '✓ ' + att.name; labelEl.style.color = '#15803d'; }
                            if (changeBtn) changeBtn.style.display = 'inline-block';

                            if (dz)      dz.style.display = 'none';
                            if (listEl) {
                                listEl.innerHTML = `
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <span style="color:#15803d;">&#10003; ${att.name}</span>
                                        ${att.url ? `<a href="${att.url}" target="_blank" rel="noopener"
                                            class="btn btn-sm btn-outline-secondary"
                                            style="font-size:0.7rem;padding:0.15rem 0.6rem;border-radius:6px;">
                                            <i data-lucide="external-link" width="12" class="me-1"></i>View
                                        </a>` : ''}
                                    </div>`;
                                listEl.style.display = 'block';
                            }
                            if (body)  body.style.display  = 'none';
                            if (arrow) arrow.style.transform = 'rotate(0deg)';

                            if (window.lucide) window.lucide.createIcons({ node: panel });
                        } 
                        // Handle Signatures (sig_prep, sig_noted) — must be before geo_photo
                        else if (att.type.startsWith('sig_')) {
                            const _prefix = att.type.replace('sig_', '');
                            const _url    = att.url;
                            const _name   = att.name;

                            setTimeout(() => {
                                const drawPanel   = document.getElementById(`sig-${_prefix}-draw-panel`);
                                const uploadPanel = document.getElementById(`sig-${_prefix}-upload-panel`);
                                const drawRadio   = document.getElementById(`sig-${_prefix}-draw`);
                                const hint        = document.getElementById(`sig-${_prefix}-hint`);

                                // Show draw panel, hide upload panel
                                if (drawPanel)   drawPanel.style.display  = 'block';
                                if (uploadPanel) uploadPanel.style.display = 'none';
                                if (drawRadio)   drawRadio.checked = true;

                                if (drawPanel && _url) {
                                    drawPanel.querySelector('.sig-restored-preview')?.remove();

                                    const wrapper = document.createElement('div');
                                    wrapper.className = 'sig-restored-preview mb-2';
                                    wrapper.innerHTML = `
                                        <img src="${_url}"
                                            style="max-height:130px;width:100%;object-fit:contain;border-radius:6px;border:1px solid #e2e8f0;background:#fff;display:block;">
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="small text-success fw-semibold">&#10003; Existing signature: ${_name}</span>
                                            <a href="${_url}" target="_blank" class="small text-primary ms-auto">View</a>
                                        </div>`;
                                    drawPanel.insertBefore(wrapper, drawPanel.firstChild);
                                }

                                if (hint) hint.textContent = '✓ Signature on file';

                                // Store URL so gatherAllData picks it up as existing
                                const upPanel = document.getElementById(`sig-${_prefix}-upload-panel`);
                                if (upPanel) {
                                    upPanel.dataset.attachExistingUrl = _url  || '';
                                    upPanel.dataset.attachFileName    = _name || '';
                                }
                            }, 350);
                        }
                        // Handle Geotagged Photo specifically
                        else if (att.type === 'geo_photo') {
                            const geoPreview = document.getElementById('geo-photo-preview');
                            const geoZone = document.getElementById('geo-photo-zone');
                            if (geoPreview && geoZone) {
                                geoPreview.dataset.existingUrl = att.url;
                                geoPreview.dataset.fileName = att.name;
                                
                                // Show preview based on type (simulating _handleGeoFile logic but with URL)
                                const isImage = att.name.toLowerCase().match(/\.(jpg|jpeg|png|gif)$/);
                                const isPdf = att.name.toLowerCase().endsWith('.pdf');
                                
                                if (isImage) {
                                    geoPreview.innerHTML = `
                                        <div class="position-relative d-inline-block mt-2">
                                            <img src="${att.url}" style="max-height:200px; max-width:100%; border-radius:8px; border:1px solid #e2e8f0;" alt="Geotagged Photo">
                                            <div class="text-success small fw-bold mt-1">
                                                <i data-lucide="check-circle" width="13"></i> Existing: ${att.name} 
                                                <a href="${att.url}" target="_blank" class="ms-2 text-primary fw-normal">View Full</a>
                                            </div>
                                        </div>`;
                                } else if (isPdf) {
                                    geoPreview.innerHTML = `
                                        <div class="d-flex align-items-center gap-2 mt-2 p-2 rounded" style="background:rgba(220,38,38,0.06);border:1px solid rgba(220,38,38,0.2);">
                                            <i data-lucide="file-text" width="16" style="color:#dc2626;"></i>
                                            <span class="small fw-semibold" style="color:#dc2626;">✓ Existing PDF: ${att.name}</span>
                                            <a href="${att.url}" target="_blank" class="btn btn-xs btn-link text-danger p-0 ms-auto">View</a>
                                        </div>`;
                                } else {
                                    geoPreview.innerHTML = `<div class="text-success small fw-bold mt-2 p-2 rounded" style="background:rgba(22,163,74,0.07);border:1px solid rgba(22,163,74,0.2);">✓ Existing: ${att.name} <a href="${att.url}" target="_blank" class="ms-2">View</a></div>`;
                                }
                                
                                geoZone.querySelector('p')?.classList.add('text-success');
                                if (window.lucide) window.lucide.createIcons({ node: geoPreview });
                    
                    // Trigger draft save
                    if (window.saveDraftLocally) window.saveDraftLocally();
                            }
                        }
                        // Handle Other Attachments
                        else if (att.type === 'other') {
                            const attList = document.getElementById('other-attachments-list');
                            if (attList) {
                                // If this is the first server attachment, clear the default empty row
                                if (!attList.dataset.restored) {
                                    attList.innerHTML = '';
                                    attList.dataset.restored = 'true';
                                }
                                
                                // Create a new row
                                const index = Date.now() + Math.floor(Math.random() * 1000);
                                _createAttachmentRow(attList, index);
                                
                                // Find the last added row and populate it
                                const rows = attList.querySelectorAll('.attachment-row');
                                const lastRow = rows[rows.length - 1];
                                if (lastRow) {
                                    const descInput = lastRow.querySelector('input[type="text"]');
                                    const preview = lastRow.querySelector('div[id$="-preview"]');
                                    const labelEl = lastRow.querySelector('span[id$="-label"]');
                                    const zone = lastRow.querySelector('div[id$="-zone"]');
                                    
                                    if (descInput) descInput.value = att.label || '';
                                    if (preview) {
                                        preview.dataset.existingUrl = att.url;
                                        preview.dataset.fileName = att.name;
                                        preview.innerHTML = `<div class="small text-success fw-bold">✓ Existing: ${att.name} <a href="${att.url}" target="_blank" class="ms-1">View</a></div>`;
                                    }
                                    if (labelEl) labelEl.textContent = 'Existing file attached';
                                    if (zone) zone.style.borderColor = '#15803d';
                                }
                            }
                        }
                    });
                }

                // 4. Handle Location/Sector dependencies
                if (data['f-sector']) {
                    const sectorEl = document.getElementById('f-sector');
                    sectorEl.value = data['f-sector'];
                    // We need to wait for sub-sectors to load
                    sectorEl.dispatchEvent(new Event('change'));
                    setTimeout(() => {
                        const subSectorEl = document.getElementById('f-sub-sector');
                        if (subSectorEl) subSectorEl.value = data['f-sub-sector'] || '';
                    }, 500);
                }

                if (data['f-province']) {
                    const provEl = document.getElementById('f-province');
                    provEl.value = data['f-province'];
                    provEl.dispatchEvent(new Event('change'));
                    setTimeout(() => {
                        const distEl = document.getElementById('f-district');
                        if (distEl) {
                            distEl.value = data['f-district'] || '';
                            distEl.dispatchEvent(new Event('change'));
                            setTimeout(() => {
                                const munEl = document.getElementById('f-municipality');
                                if (munEl) {
                                    munEl.value = data['f-municipality'] || '';
                                    munEl.dispatchEvent(new Event('change'));
                                    setTimeout(() => {
                                        const brgyEl = document.getElementById('f-barangay');
                                        if (brgyEl) brgyEl.value = data['f-barangay'] || '';
                                    }, 500);
                                }
                            }, 500);
                        }
                    }, 500);
                }

                // RECOVERY LOGIC: Check if there's a local draft that is newer/unsaved
                const localSaved = localStorage.getItem(getDraftKey());
                if (localSaved) {
                    try {
                        const localData = JSON.parse(localSaved);
                        // If we have local data, it means there were unsaved changes before a crash/refresh
                        if (confirm("We found unsaved changes for this project from a previous session. Would you like to restore them?")) {
                            window.loadDraftLocally();
                        } else {
                            // If user declines, clear it so they aren't asked again
                            localStorage.removeItem(getDraftKey());
                        }
                    } catch(e) {}
                }

                // 5. Read-only Mode
                if (viewId) {
                    form.querySelectorAll('input, select, textarea, button').forEach(el => {
                        if (!el.classList.contains('btn-cpp-prev') && !el.classList.contains('btn-cpp-next')) {
                            el.disabled = true;
                            if (el.tagName === 'BUTTON') el.style.display = 'none';
                        }
                    });
                    const submitBtn = document.getElementById('cppSubmitBtn');
                    if (submitBtn) submitBtn.style.display = 'none';
                    const draftBtn = document.getElementById('cppDraftBtn');
                    if (draftBtn) draftBtn.style.display = 'none';
                }

            }
        } catch (e) {
            console.error("Failed to load submission from server:", e);
        }
    } else {
        // Fallback to local draft
        window.loadDraftLocally();
    }

    // ── Initialize file upload zones ──
    // Environmental Clearance
    const envPanelId = 'env-clearance-upload-panel';
    if (document.getElementById(envPanelId)) {
        _createUploadZone(envPanelId, 'Environmental Clearance Document');
    }

    // Detailed Engineering Design (DED)
    const ded = document.getElementById('prep-ded');
    if (ded) {
        ded.addEventListener('change', () => {
            const panel = document.getElementById('ded-upload-panel');
            if (!panel) return;
            const isChecked = ded.checked;
            panel.style.display = isChecked ? 'block' : 'none';
            if (isChecked && panel.children.length === 0) {
                _createUploadZone('ded-upload-panel', 'Detailed Engineering Design Document');
            }
        });
        // Initial check
        if (ded.checked) {
            const panel = document.getElementById('ded-upload-panel');
            if (panel) {
                panel.style.display = 'block';
                if (panel.children.length === 0) {
                    _createUploadZone('ded-upload-panel', 'Detailed Engineering Design Document');
                }
            }
        }
    }

    // Public Consultation Upload & Input Toggling
    const initConsultationToggling = () => {
        const radios = document.querySelectorAll('input[name="consultation-status"]');
        const handleToggle = () => {
            const r = document.querySelector('input[name="consultation-status"]:checked');
            if (!r) return;

            const panel = document.getElementById('consult-yes-panel');
            const plannedDateInput = document.getElementById('f-consult-planned-date');
            const datesBox = document.getElementById('consult-dates-box');
            const datePicker = document.getElementById('consult-date-picker');
            const hiddenDates = document.getElementById('f-consult-done-dates');

            const isYes = r.value === 'Yes';
            const isNo = r.value === 'No';

            if (panel) panel.style.display = isYes ? 'block' : 'none';
            if (plannedDateInput) plannedDateInput.disabled = !isNo;

            if (datesBox && datePicker) {
                if (isYes) {
                    datesBox.style.background = '#fff';
                    datesBox.style.cursor = 'pointer';
                    datesBox.style.opacity = '1';
                    datesBox.style.pointerEvents = 'auto';
                    datePicker.disabled = false;
                    const addBtn = datesBox.querySelector('.consult-date-add');
                    if (addBtn) addBtn.style.cursor = 'pointer';
                } else {
                    datesBox.style.background = '#f8fafc';
                    datesBox.style.cursor = 'not-allowed';
                    datesBox.style.opacity = '0.6';
                    datesBox.style.pointerEvents = 'none';
                    datePicker.disabled = true;
                    const addBtn = datesBox.querySelector('.consult-date-add');
                    if (addBtn) addBtn.style.cursor = 'default';
                }
            }

            if (isYes && panel && panel.children.length === 0) {
                panel.style.marginLeft = '0';
                panel.style.marginTop = '0.5rem';
                _createUploadZone('consult-yes-panel', 'Public Consultation Documentation');
            }
        };

        radios.forEach(r => r.addEventListener('change', handleToggle));
        // Also call it once to handle initial state after refresh/load
        setTimeout(handleToggle, 100); 
    };
    initConsultationToggling();

    // --- PAGE 3: HGDG Upload ---
    const hgdg = document.getElementById('f-hgdg');
    if (hgdg) {
        const showHGDG = () => {
            const panel = document.getElementById('hgdg-upload-panel');
            if (panel) {
                panel.style.display = 'block';
                if (panel.children.length === 0) {
                    _createUploadZone('hgdg-upload-panel', 'HGDG Document');
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

        let selectedDates = [];

        function _fmt(dateStr) {
            const [y, m, d] = dateStr.split('-').map(Number);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return `${months[m - 1]} ${d}, ${y}`;
        }

        function _renderTags() {
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
            if (placeholder) placeholder.textContent = selectedDates.length > 0 ? 'Add more' : 'Pick date(s)';
            box.style.borderColor = selectedDates.length > 0 ? '#154A9A' : '#dee2e6';
        }

        box.addEventListener('click', (e) => {
            if (e.target.classList.contains('consult-tag-remove')) {
                const date = e.target.dataset.date;
                selectedDates = selectedDates.filter(d => d !== date);
                _renderTags();
                return;
            }
            picker.showPicker ? picker.showPicker() : picker.click();
        });

        picker.addEventListener('change', () => {
            const val = picker.value;
            if (!val) return;
            if (!selectedDates.includes(val)) {
                selectedDates.push(val);
                selectedDates.sort();
                _renderTags();
            }
            picker.value = '';
        });

        box.addEventListener('mouseover', () => { box.style.borderColor = '#154A9A'; });
        box.addEventListener('mouseout', () => { box.style.borderColor = selectedDates.length > 0 ? '#154A9A' : '#dee2e6'; });
    })();

    // --- PAGE 3: Implementation Schedule Features ---
    (function _initImplScheduleFeatures() {
        const table = document.getElementById('impl-schedule-table');
        const body = document.getElementById('impl-schedule-body');
        const addBtn = document.getElementById('impl-add-row');
        if (!table || !body || !addBtn) return;

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

        function _refreshFirstRow() {
            const first = body.querySelector('tr');
            if (first) {
                const lastCell = first.querySelector('td:last-child');
                if (lastCell) lastCell.innerHTML = '';
            }
        }

        _refreshFirstRow();

        addBtn.addEventListener('click', () => {
            const newRow = _createRow();
            body.appendChild(newRow);
        });

        body.addEventListener('click', (ev) => {
            if (ev.target.closest('.impl-del-row')) {
                const row = ev.target.closest('tr');
                if (row) row.remove();
                if (body.querySelectorAll('tr').length === 1) _refreshFirstRow();
                if (window.saveDraftLocally) window.saveDraftLocally();
            }
        });

        // Trigger draft save on any input within the table
        body.addEventListener('input', () => {
            if (window.saveDraftLocally) window.saveDraftLocally();
        });

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
                if (/[eE]/.test(inp.value)) inp.value = inp.value.replace(/[eE]/g, '');
            });
        });

        table.addEventListener('keydown', (ev) => {
            const t = ev.target;
            if (!(t instanceof HTMLInputElement)) return;
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
                if (/[^0-9]/.test(t.value)) t.value = t.value.replace(/[^0-9]/g, '');
            } else if (t.type === 'number') {
                if (/[eE]/.test(t.value)) t.value = t.value.replace(/[eE]/g, '');
            }
        });
    })();

    // --- PAGE 3/5: Other Attachments ---
    function _createAttachmentRow(list, index) {
        const row = document.createElement('div');
        row.className = 'attachment-row d-flex align-items-start gap-2 mb-3';
        row.dataset.index = index;
        const uid = 'att-' + index + '-' + Math.random().toString(36).substr(2, 4);
        row.innerHTML = `
            <div style="flex:1;">
                <input type="text" class="form-control form-control-sm mb-2"
                    id="${uid}-desc" placeholder="Document title / description"
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

        row.querySelector('.att-remove-btn').addEventListener('click', () => {
            row.remove();
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
            
            // Trigger draft save to capture the new file data
            if (window.saveDraftLocally) window.saveDraftLocally();
        };
        reader.readAsDataURL(file);
        if (labelEl) labelEl.textContent = 'File attached';
    }

    const attList = document.getElementById('other-attachments-list');
    const addAttBtn = document.getElementById('add-attachment-btn');
    if (attList) {
        _createAttachmentRow(attList, 1);
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
                    if (window.saveDraftLocally) window.saveDraftLocally();
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
            el.addEventListener('keydown', (ev) => {
                if (ev.key === 'e' || ev.key === 'E') ev.preventDefault();
            });
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
            el.addEventListener('input', () => {
                if (/[eE]/.test(el.value)) el.value = el.value.replace(/[eE]/g, '');
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
        // Only mark canvas as clean if it's truly blank (has no restored content)
        const _checkBlank = () => {
            const _ctx = canvas.getContext('2d');
            const _data = _ctx.getImageData(0, 0, canvas.width, canvas.height).data;
            return !_data.some(v => v !== 0);
        };
        if (_checkBlank()) canvas.dataset.dirty = 'false';

        [drawRadio, uploadRadio].forEach(radio => {
            if (!radio) return;
            radio.addEventListener('change', () => {
                const isDraw = drawRadio?.checked;
                if (drawPanel) drawPanel.style.display = isDraw ? 'block' : 'none';
                if (uploadPanel) uploadPanel.style.display = isDraw ? 'none' : 'block';
            });
        });

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

        canvas.addEventListener('mousedown', e => { 
            drawing = true; 
            canvas.dataset.dirty = 'true';
            const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); 
        });
        canvas.addEventListener('mousemove', e => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('mouseup', () => { 
            drawing = false; 
            if (hint) hint.textContent = '✓ Signature recorded'; 
            if (window.saveDraftLocally) window.saveDraftLocally();
        });
        canvas.addEventListener('mouseleave', () => { drawing = false; });
        canvas.addEventListener('touchstart', e => { 
            e.preventDefault(); 
            drawing = true; 
            canvas.dataset.dirty = 'true';
            const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); 
        }, { passive: false });
        canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }, { passive: false });
        canvas.addEventListener('touchend', e => { 
            e.preventDefault(); 
            drawing = false; 
            if (hint) hint.textContent = '✓ Signature recorded'; 
            if (window.saveDraftLocally) window.saveDraftLocally();
        });

        if (uploadZone && uploadInput) {
            uploadZone.addEventListener('click', () => uploadInput.click());
            uploadInput.addEventListener('change', () => {
                if (!uploadInput.files.length) return;
                const file = uploadInput.files[0];
                const reader = new FileReader();
                reader.onload = ev => {
                    const dataUri = ev.target.result;
                    if (preview) {
                        preview.dataset.dataUri = dataUri;
                        preview.dataset.fileName = file.name;
                        preview.dataset.mimeType = file.type;
                        preview.innerHTML = `<img src="${dataUri}" style="max-height:100px; border-radius:6px; border:1px solid #e2e8f0; margin-top:6px;">
                            <div class="small text-success fw-bold mt-1">✓ ${file.name}</div>`;
                    }
                };
                reader.readAsDataURL(file);
            });
        }
    }

    _initSignature('prep');
    _initSignature('noted');

    document.querySelectorAll('.sig-clear-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const cid = btn.dataset.target;
            const hid = btn.dataset.hint;
            const c = document.getElementById(cid);
            if (c) {
                c.getContext('2d').clearRect(0, 0, c.width, c.height);
                c.dataset.dirty = 'false';
            }
            const h = document.getElementById(hid);
            if (h) h.textContent = 'Please sign above.';
        });
    });
    
    // Unset initialization flag after a short delay to allow all change events to settle
    setTimeout(() => { window.isInitializing = false; }, 1000);
});
