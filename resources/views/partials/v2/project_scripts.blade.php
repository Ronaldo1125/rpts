<script>
    document.addEventListener('DOMContentLoaded', () => {
        const isReadOnly = "{{ $isReadOnly ? '1' : '0' }}" === "1";
        const allDropdowns = [];

        // --- GENERIC HELPERS ---

        // Generic Tagging System Helper
        const initTagging = (config) => {
            const box = document.getElementById(config.boxId);
            if (!box) return;
            const dropdownEl = document.getElementById(config.dropdownId);
            const dropdown = new bootstrap.Dropdown(box);
            const tagsContainer = document.getElementById(config.tagsContainerId);
            const hiddenInputsContainer = document.getElementById(config.hiddenInputsId);
            const noSelectionText = box.querySelector('.no-selection');

            const updateTags = () => {
                tagsContainer.innerHTML = '';
                hiddenInputsContainer.innerHTML = '';
                const selectedItems = dropdownEl.querySelectorAll('input:checked');
                
                if (selectedItems.length > 0) {
                    noSelectionText.classList.add('d-none');
                } else {
                    noSelectionText.classList.remove('d-none');
                }

                selectedItems.forEach(input => {
                    const link = input.closest('a');
                    const id = link.dataset.id;
                    const name = link.dataset.name;

                    const tag = document.createElement('span');
                    tag.className = 'badge bg-primary d-flex align-items-center gap-1 fw-medium smaller py-1 px-2 rounded-pill';
                    tag.title = name;
                    tag.innerHTML = `<span class="text-truncate" style="max-width: 450px;">${name}</span><i data-lucide="x" width="12" height="12" class="cursor-pointer remove-tag" data-id="${id}"></i>`;
                    tagsContainer.appendChild(tag);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = config.inputName;
                    hiddenInput.value = id;
                    hiddenInputsContainer.appendChild(hiddenInput);
                });
                lucide.createIcons();
            };

            if (isReadOnly) {
                box.classList.add('bg-light');
                return;
            }

            allDropdowns.push(dropdown);

            box.addEventListener('click', (e) => {
                if (!e.target.classList.contains('remove-tag')) {
                    allDropdowns.forEach(d => { if(d !== dropdown) d.hide(); });
                    dropdown.toggle();
                }
            });

            dropdownEl.addEventListener('click', (e) => {
                const item = e.target.closest('.dropdown-item');
                if (item) {
                    e.preventDefault();
                    e.stopPropagation();
                    const checkbox = item.querySelector('input');
                    checkbox.checked = !checkbox.checked;
                    updateTags();
                }
            });

            tagsContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-tag')) {
                    e.stopPropagation();
                    const id = e.target.dataset.id;
                    const checkbox = dropdownEl.querySelector(`a[data-id="${id}"] input`);
                    if (checkbox) {
                        checkbox.checked = false;
                        updateTags();
                    }
                }
            });
        };

        // Generic Single Custom Select Helper
        const initSingleCustomSelect = (config) => {
            const displayBox = document.getElementById(config.displayBoxId);
            if (!displayBox) return;
            const displayText = document.getElementById(config.displayTextId);
            const dropdownEl = document.getElementById(config.dropdownId);
            const hiddenInput = document.getElementById(config.hiddenInputId);
            const dropdown = new bootstrap.Dropdown(displayBox);

            if (isReadOnly) {
                displayBox.classList.add('bg-light');
                displayBox.style.cursor = 'default';
                return;
            }

            allDropdowns.push(dropdown);

            displayBox.addEventListener('click', (e) => {
                if (displayBox.classList.contains('pe-none')) return;
                allDropdowns.forEach(d => { if(d !== dropdown) d.hide(); });
                dropdown.toggle();
            });

            dropdownEl.addEventListener('click', (e) => {
                const item = e.target.closest('.dropdown-item');
                if (item) {
                    e.preventDefault();
                    e.stopPropagation();
                    const id = item.dataset.id;
                    const name = item.dataset.name;
                    displayText.textContent = name;
                    hiddenInput.value = id;
                    
                    // Update radio checked state
                    dropdownEl.querySelectorAll('input[type="radio"]').forEach(radio => {
                        radio.checked = (radio.closest('a').dataset.id == id);
                    });

                    dropdown.hide();
                    
                    // Trigger change event
                    hiddenInput.dispatchEvent(new Event('change'));
                    if (config.onChange) config.onChange(id, name);
                }
            });
        };

        // --- INITIALIZATION ---

        // Global click handler to hide dropdowns
        window.addEventListener('click', (e) => {
            if (!e.target.closest('.position-relative')) {
                allDropdowns.forEach(d => d.hide());
            }
        });

        // Initialize Tagging
        initTagging({
            boxId: 'f-indicators-box',
            dropdownId: 'indicators-dropdown',
            tagsContainerId: 'selected-indicators-tags',
            hiddenInputsId: 'indicators-hidden-inputs',
            inputName: 'indicators[]'
        });

        initTagging({
            boxId: 'f-chapters-box',
            dropdownId: 'chapters-dropdown',
            tagsContainerId: 'selected-chapters-tags',
            hiddenInputsId: 'chapters-hidden-inputs',
            inputName: 'rdp_chapters[]'
        });

        initTagging({
            boxId: 'f-provinces-box',
            dropdownId: 'provinces-dropdown',
            tagsContainerId: 'selected-provinces-tags',
            hiddenInputsId: 'provinces-hidden-inputs',
            inputName: 'provinces[]'
        });

        // Initialize Single Selects
        initSingleCustomSelect({
            displayBoxId: 'agency-display-box',
            displayTextId: 'agency-display-text',
            dropdownId: 'agency-dropdown',
            hiddenInputId: 'agency_id'
        });

        initSingleCustomSelect({
            displayBoxId: 'status-display-box',
            displayTextId: 'status-display-text',
            dropdownId: 'status-dropdown',
            hiddenInputId: 'status'
        });

        initSingleCustomSelect({
            displayBoxId: 'funding_category-display-box',
            displayTextId: 'funding_category-display-text',
            dropdownId: 'funding_category-dropdown',
            hiddenInputId: 'funding_category'
        });

        initSingleCustomSelect({
            displayBoxId: 'fund_source-display-box',
            displayTextId: 'fund_source-display-text',
            dropdownId: 'fund_source-dropdown',
            hiddenInputId: 'fund_source'
        });

        initSingleCustomSelect({
            displayBoxId: 'endorse_year-display-box',
            displayTextId: 'endorse_year-display-text',
            dropdownId: 'endorse_year-dropdown',
            hiddenInputId: 'endorse_year_id'
        });

        // --- DYNAMIC LOADING LOGIC ---

        const loadSubSectors = async (selectedId = null) => {
            const id = document.getElementById('sector_id').value;
            const subDropdown = document.getElementById('subsector-dropdown');
            const subDisplayText = document.getElementById('subsector-display-text');
            const subHiddenInput = document.getElementById('sub_sector_id');

            if (!id) {
                subDropdown.innerHTML = '<li><a class="dropdown-item smaller text-muted">Select sector first</a></li>';
                subDisplayText.textContent = '-- Select --';
                subHiddenInput.value = '';
                return;
            }

            subDropdown.innerHTML = '<li><a class="dropdown-item smaller text-muted">Loading...</a></li>';
            try {
                const response = await fetch(`/v2/projects/get-sub-sectors?sector_id=${id}`);
                const data = await response.json();
                subDropdown.innerHTML = '';
                let foundName = '-- Select --';

                data.forEach(val => {
                    const isSelected = selectedId && val.id == selectedId;
                    if (isSelected) foundName = val.subsector_name;

                    const li = document.createElement('li');
                    li.innerHTML = `
                        <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="${val.id}" data-name="${val.subsector_name}">
                            <div class="d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0 pe-none" type="radio" name="_sub_sector_radio" ${isSelected ? 'checked' : ''}>
                                <span class="smaller" title="${val.subsector_name}">${val.subsector_name}</span>
                            </div>
                        </a>
                    `;
                    subDropdown.appendChild(li);
                });
                subDisplayText.textContent = foundName;
                if (selectedId) subHiddenInput.value = selectedId;
            } catch (err) {}
        };

        initSingleCustomSelect({
            displayBoxId: 'sector-display-box',
            displayTextId: 'sector-display-text',
            dropdownId: 'sector-dropdown',
            hiddenInputId: 'sector_id',
            onChange: (id) => loadSubSectors()
        });

        initSingleCustomSelect({
            displayBoxId: 'subsector-display-box',
            displayTextId: 'subsector-display-text',
            dropdownId: 'subsector-dropdown',
            hiddenInputId: 'sub_sector_id'
        });

        // Initialize Sub-Sectors if exists
        const initialSector = document.getElementById('sector_id').value;
        const initialSubSector = document.getElementById('sub_sector_id').value;
        if (initialSector && initialSubSector) {
            loadSubSectors(initialSubSector);
        }

        // --- LOCATION LOGIC ---

        const setControlDisabled = (boxId, isDisabled) => {
            const box = document.getElementById(boxId);
            if (!box) return;
            if (isDisabled) {
                box.classList.add('pe-none', 'bg-light', 'opacity-50');
                box.style.cursor = 'default';
            } else {
                box.classList.remove('pe-none', 'bg-light', 'opacity-50');
                box.style.cursor = 'pointer';
            }
        };

        const loadDistricts = async (selectedId = null) => {
            const id = document.getElementById('province_id').value;
            const distDropdown = document.getElementById('district-dropdown');
            const distDisplayText = document.getElementById('district-display-text');
            const distHiddenInput = document.getElementById('district_id');
            const munHiddenInput = document.getElementById('municipality_id');
            const munDisplayText = document.getElementById('municipality-display-text');

            if (!selectedId) {
                distHiddenInput.value = '';
                distDisplayText.textContent = '-- Select --';
                munHiddenInput.value = '';
                munDisplayText.textContent = '-- Select --';
                setControlDisabled('district-display-box', true);
                setControlDisabled('municipality-display-box', true);
            }

            if (!id) {
                distDropdown.innerHTML = '<li><a class="dropdown-item smaller text-muted">Select province first</a></li>';
                return;
            }

            distDropdown.innerHTML = '<li><a class="dropdown-item smaller text-muted">Loading...</a></li>';
            try {
                const response = await fetch(`/location/getDistricts?province_id=${id}`);
                const data = await response.json();
                distDropdown.innerHTML = '';
                setControlDisabled('district-display-box', false);
                let foundName = '-- Select --';

                data.forEach(val => {
                    const isSelected = selectedId && val.id == selectedId;
                    if (isSelected) foundName = val.district_name;
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="${val.id}" data-name="${val.district_name}">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-check-input mt-0 pe-none" type="radio" name="_dist_radio" ${isSelected ? 'checked' : ''}>
                            <span class="smaller" title="${val.district_name}">${val.district_name}</span>
                        </div>
                    </a>`;
                    distDropdown.appendChild(li);
                });
                distDisplayText.textContent = foundName;
                if (selectedId) {
                    distHiddenInput.value = selectedId;
                    const initialMunId = document.getElementById('municipality_id').value;
                    if(initialMunId) loadMunicipalities(initialMunId);
                }
            } catch (err) {}
        };

        const loadMunicipalities = async (selectedId = null) => {
            const pid = document.getElementById('province_id').value;
            const did = document.getElementById('district_id').value;
            const munDropdown = document.getElementById('municipality-dropdown');
            const munDisplayText = document.getElementById('municipality-display-text');
            const munHiddenInput = document.getElementById('municipality_id');

            if (!selectedId) {
                munHiddenInput.value = '';
                munDisplayText.textContent = '-- Select --';
            }

            if (!did || !pid) {
                munDropdown.innerHTML = '<li><a class="dropdown-item smaller text-muted">Select district first</a></li>';
                setControlDisabled('municipality-display-box', true);
                return;
            }

            munDropdown.innerHTML = '<li><a class="dropdown-item smaller text-muted">Loading...</a></li>';
            try {
                const response = await fetch(`/location/getMunicipalities?province_id=${pid}&district_id=${did}`);
                const data = await response.json();
                munDropdown.innerHTML = '';
                setControlDisabled('municipality-display-box', false);
                let foundName = '-- Select --';

                data.forEach(val => {
                    const isSelected = selectedId && val.id == selectedId;
                    if (isSelected) foundName = val.municipality_name;
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="${val.id}" data-name="${val.municipality_name}">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-check-input mt-0 pe-none" type="radio" name="_mun_radio" ${isSelected ? 'checked' : ''}>
                            <span class="smaller" title="${val.municipality_name}">${val.municipality_name}</span>
                        </div>
                    </a>`;
                    munDropdown.appendChild(li);
                });
                munDisplayText.textContent = foundName;
                if (selectedId) munHiddenInput.value = selectedId;
            } catch (err) {}
        };

        initSingleCustomSelect({
            displayBoxId: 'province-display-box',
            displayTextId: 'province-display-text',
            dropdownId: 'province-dropdown',
            hiddenInputId: 'province_id',
            onChange: () => loadDistricts()
        });

        initSingleCustomSelect({
            displayBoxId: 'district-display-box',
            displayTextId: 'district-display-text',
            dropdownId: 'district-dropdown',
            hiddenInputId: 'district_id',
            onChange: () => loadMunicipalities()
        });

        initSingleCustomSelect({
            displayBoxId: 'municipality-display-box',
            displayTextId: 'municipality-display-text',
            dropdownId: 'municipality-dropdown',
            hiddenInputId: 'municipality_id'
        });

        // Initialize Locations if exists
        const initialProv = document.getElementById('province_id').value;
        const initialDist = document.getElementById('district_id').value;
        if (initialProv) {
            loadDistricts(initialDist);
        }

        // Location Type Toggling
        const locationRadios = document.querySelectorAll('input[name="location"]');
        const locSpecificFields = document.getElementById('locationSpecificFields');
        const interProvFields = document.getElementById('inter-province-fields');

        locationRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                const val = document.querySelector('input[name="location"]:checked').value;
                locSpecificFields.classList.toggle('d-none', val !== 'locationspecific');
                interProvFields.classList.toggle('d-none', val !== 'inter-province');
            });
        });

        // --- ATTACHMENTS ---

        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('fileInput');
        const previews = document.getElementById('file-previews');

        if (dropzone && !isReadOnly) {
            dropzone.addEventListener('click', () => fileInput.click());
            dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('bg-primary-subtle'); });
            dropzone.addEventListener('dragleave', () => dropzone.classList.remove('bg-primary-subtle'));
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('bg-primary-subtle');
                handleFiles(e.dataTransfer.files);
            });
            fileInput.addEventListener('change', () => handleFiles(fileInput.files));
        }

        async function handleFiles(files) {
            for (const file of files) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

                const item = document.createElement('div');
                item.className = 'd-flex align-items-center justify-content-between p-2 border rounded bg-white small';
                item.innerHTML = `<div class="d-flex align-items-center gap-2"><i data-lucide="loader-2" width="14" class="spin"></i><span>${file.name}</span></div>`;
                previews.appendChild(item);
                lucide.createIcons();

                try {
                    const response = await fetch('{{ route("v2.projects.storeMedia") }}', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    item.innerHTML = `
                        <div class="d-flex align-items-center gap-2"><i data-lucide="file-check" width="14" class="text-success"></i><span>${file.name}</span></div>
                        <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="this.closest('.d-flex').remove()"><i data-lucide="x" width="14"></i></button>
                        <input type="hidden" name="document[]" value="${data.name}">
                    `;
                    lucide.createIcons();
                } catch (err) { item.innerHTML = '<span class="text-danger">Upload failed</span>'; }
            }
        }

        document.querySelectorAll('.delete-media').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Are you sure you want to remove this attachment?')) btn.closest('.d-flex').remove();
            });
        });
    });

    // --- INDICATOR MODAL ---
    let addIndicatorModal;
    function openIndicatorModal() {
        if (!addIndicatorModal) addIndicatorModal = new bootstrap.Modal(document.getElementById('addIndicatorModal'));
        document.getElementById('addIndicatorForm').reset();
        addIndicatorModal.show();
    }

    const saveIndicatorBtn = document.getElementById('saveIndicatorBtn');
    if (saveIndicatorBtn) {
        saveIndicatorBtn.addEventListener('click', async () => {
            const nameInput = document.getElementById('new_indicator_name');
            const name = nameInput.value.trim();
            if (!name) return;

            try {
                const response = await fetch('{{ route("v2.indicators.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ indicator_name: name })
                });
                const data = await response.json();
                if (data.success) {
                    const dropdown = document.getElementById('indicators-dropdown');
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="${data.data.id}" data-name="${data.data.indicator_name}">
                         <div class="d-flex align-items-center gap-2"><input class="form-check-input mt-0 pe-none" type="checkbox"><span class="smaller" title="${data.data.indicator_name}">${data.data.indicator_name}</span></div>
                    </a>`;
                    dropdown.insertBefore(li, dropdown.firstChild);
                    li.querySelector('a').click();
                    addIndicatorModal.hide();
                }
            } catch (err) {}
        });
    }
</script>
