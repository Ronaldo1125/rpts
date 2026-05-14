export function initAdminActions() {
    // store original modal titles and save button text
    document.querySelectorAll('.modal').forEach(modal => {
        const title = modal.querySelector('.modal-title');
        const saveBtn = modal.querySelector('.modal-footer .btn-primary');
        if (title && !title.dataset.orig) title.dataset.orig = title.textContent.trim();
        if (saveBtn && !saveBtn.dataset.orig) saveBtn.dataset.orig = saveBtn.textContent.trim();
    });

    document.addEventListener('click', (e) => {
        const edit = e.target.closest('.dropdown-item[data-action="edit"]');
        if (!edit) return;
        e.preventDefault();

        const row = edit.closest('tr');
        const section = row ? row.closest('section') : document.querySelector('section');
        const pageId = section ? section.id : null;

        const modalMap = {
            'manage-submissions': '#cipgSubmitModal',
            users: '#addUserModal',
            roles: '#addRoleModal',
            permissions: '#addPermissionModal',
            projects: '#addProjectModal',
            agency: '#addAgencyModal',
            indicator: '#addIndicatorModal',
            'funding-category': '#addFundingCategoryModal',
            'endorse-year': '#addEndorseYearModal',
            'rdp-chapter': '#addRdpChapterModal'
        };

        let modalEl = null;
        if (pageId && modalMap[pageId]) {
            modalEl = document.querySelector(modalMap[pageId]);
        }
        if (!modalEl) {
            // fallback: first modal inside the same section, or any modal on page
            modalEl = section ? section.querySelector('.modal') : null;
            if (!modalEl) modalEl = document.querySelector('.modal');
        }
        if (!modalEl) return;

        const title = modalEl.querySelector('.modal-title');
        const saveBtn = modalEl.querySelector('.modal-footer .btn-primary');
        if (title && title.dataset.orig) {
            const cleaned = title.dataset.orig.replace(/\b(submit(?:ting)?|add|create)\b[:\s-]*/ig, '').trim();
            title.textContent = `Edit ${cleaned || title.dataset.orig}`;
        }
        if (saveBtn && saveBtn.dataset.orig) saveBtn.textContent = 'Update';

        // store editing reference (optional) so consumer can read it
        if (row && row.dataset && row.dataset.id) modalEl.dataset.editingId = row.dataset.id;

        // show modal using Bootstrap's modal API (if available)
        try {
            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.show();
        } catch (err) {
            // fallback: add show class
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            modalEl.removeAttribute('aria-hidden');
        }
    });

    // Global Delete Handler for Admin Tables
    document.addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('[data-action="delete"]');
        if (!deleteBtn) return;
        e.preventDefault();

        const row = deleteBtn.closest('tr');
        if (!row) return;

        // Try to find a name/title cell
        const nameCell = row.querySelector('td:first-child');
        const itemName = nameCell ? nameCell.textContent.trim() : 'this item';

        const section = row.closest('section');
        const pageTitle = section ? section.querySelector('h2')?.textContent.trim() || 'Record' : 'Record';

        // Improved plural-to-singular logic
        let typeLabel = pageTitle.replace(/Manage\s*/i, '');
        if (typeLabel.toLowerCase().endsWith('ies')) {
            typeLabel = typeLabel.replace(/ies$/i, 'y');
        } else {
            typeLabel = typeLabel.replace(/s$/i, '');
        }

        if (window.showConfirmModal) {
            window.showConfirmModal({
                title: `Delete ${typeLabel}`,
                message: `Are you sure you want to delete ${typeLabel.toLowerCase()} "<strong>${itemName}</strong>"? This action cannot be undone.`,
                confirmText: `Delete ${typeLabel}`,
                confirmClass: 'btn-danger',
                onConfirm: () => {
                    row.remove();
                    if (window.showSimpleAlert) {
                        window.showSimpleAlert(`${typeLabel} deleted successfully.`, 'success');
                    }
                }
            });
        }
    });

    // Global Form Submit Handler for Admin Modals
    document.addEventListener('submit', (e) => {
        const form = e.target;
        const modalEl = form.closest('.modal');
        if (!modalEl) return;

        e.preventDefault();

        const title = modalEl.querySelector('.modal-title')?.textContent.trim() || 'Record';
        const isEdit = title.toLowerCase().includes('edit');
        const actionLabel = isEdit ? 'updated' : 'created';

        // Specific logic for Indicator addition
        if (form.id === 'addIndicatorForm' && !isEdit) {
            const val = document.getElementById('indicatorName')?.value.trim();
            if (val) {
                // 1. Update Indicator Table (if on indicator page)
                const tbody = document.querySelector('#indicator table tbody');
                if (tbody) {
                    if (tbody.querySelector('.text-center.py-4')) tbody.innerHTML = '';
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td style="color: inherit !important;">${val}</td>
                        <td><span class="badge rounded-pill fw-medium small px-3 py-2" style="background-color: #e8f0fe; color: #0032A6;">Just Now</span></td>
                        <td>
                            <div class="dropdown position-static">
                                <button class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i data-lucide="more-vertical" width="20"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li><a class="dropdown-item" href="#" data-action="edit"><i data-lucide="edit-2" class="me-2" width="16"></i>Edit</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" data-action="delete"><i data-lucide="trash-2" class="me-2" width="16"></i>Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    `;
                    tbody.prepend(newRow);
                }

                // 2. Update Project Workspace Dropdown (if active)
                const workspaceSelect = document.getElementById('f-indicator');
                if (workspaceSelect) {
                    const opt = document.createElement('option');
                    opt.value = val; opt.textContent = val; opt.selected = true;
                    workspaceSelect.add(opt);
                }

                // 3. Update Component Project Dropdown (if active)
                const compSelect = document.getElementById('comp-indicator');
                if (compSelect) {
                    const opt = document.createElement('option');
                    opt.value = val; opt.textContent = val; opt.selected = true;
                    compSelect.add(opt);
                }

                if (window.lucide) window.lucide.createIcons();
            }
        }

        // Hide modal
        try {
            const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            bsModal.hide();
        } catch (err) {
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
        }

        // Show global success alert
        if (window.showSimpleAlert) {
            window.showSimpleAlert(`${title} ${actionLabel} successfully.`, 'success');
        }

        form.reset();
    });

    // restore original texts when modal is hidden
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', () => {
            const title = modal.querySelector('.modal-title');
            const saveBtn = modal.querySelector('.modal-footer .btn-primary');
            if (title && title.dataset.orig) title.textContent = title.dataset.orig;
            if (saveBtn && saveBtn.dataset.orig) saveBtn.textContent = saveBtn.dataset.orig;
            delete modal.dataset.editingId;
        });
    });

    // Global Password Toggle Handler
    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('.toggle-password');
        if (!toggleBtn) return;

        const input = toggleBtn.parentElement.querySelector('input');
        if (!input) return;

        const icon = toggleBtn.querySelector('i, svg');

        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            if (icon) icon.setAttribute('data-lucide', 'eye');
        }

        if (window.lucide) {
            window.lucide.createIcons();
        }
    });

    // Global File Input Filename & Preview Handler
    document.addEventListener('change', (e) => {
        const fileInput = e.target.closest('#profilePictureInput');
        if (!fileInput) return;

        const textField = fileInput.parentElement.querySelector('input[type="text"]');
        const preview = fileInput.closest('.modal-body')?.querySelector('img');

        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            if (textField) textField.value = file.name;

            if (preview) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    preview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    });
    
    // Global User Role Toggle (Show/Hide Division and Agency fields)
    document.addEventListener('change', (e) => {
        const roleSelect = e.target.closest('#userRole');
        if (!roleSelect) return;

        const staffFields = document.getElementById('staffFields');
        const agencyContainer = document.getElementById('agencyFieldContainer');
        const agencySelect = document.getElementById('userAgency');

        // Toggle Staff Fields
        if (staffFields) {
            if (roleSelect.value === 'staff' || roleSelect.value === 'division-head') {
                staffFields.classList.remove('d-none');
                staffFields.style.display = 'block';
            } else {
                staffFields.classList.add('d-none');
                staffFields.style.display = 'none';
                const divSelect = document.getElementById('userDivision');
                if (divSelect) divSelect.value = '';
            }
        }

        // Toggle Agency Field
        if (agencyContainer && agencySelect) {
            if (roleSelect.value === 'agency') {
                agencyContainer.classList.remove('d-none');
                agencyContainer.style.display = 'block';
                agencySelect.required = true;
                agencySelect.value = ''; // Reset for them to choose
            } else {
                agencyContainer.classList.add('d-none');
                agencyContainer.style.display = 'none';
                agencySelect.required = false;
                // Auto-select NEDA 5 for internal roles
                if (roleSelect.value === 'admin' || roleSelect.value === 'staff' || roleSelect.value === 'division-head') {
                    agencySelect.value = 'NEDA 5';
                }
            }
        }
    });

    // Global Division Toggle (Enable Division Role fields)
    document.addEventListener('change', (e) => {
        const divSelect = e.target.closest('#userDivision');
        if (!divSelect) return;

        const roleSelect = document.getElementById('userDivisionRole');
        if (roleSelect) {
            roleSelect.disabled = !divSelect.value;
        }
    });
}
