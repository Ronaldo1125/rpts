/**
 * Global RPTS UI Utilities
 */

const RPTS = {
    toast: {
        instance: null,
        init() {
            const toastEl = document.getElementById('customToast');
            if (toastEl) {
                this.instance = new bootstrap.Toast(toastEl, { delay: 5000 });
            }
        },
        show(message, type = 'success') {
            if (!this.instance) this.init();
            
            const toastMessage = document.getElementById('toastMessage');
            const toastTitle = document.getElementById('toastTitle');
            const toastAccent = document.getElementById('toastAccent');
            const toastIcon = document.getElementById('toastIcon');
            const toastProgress = document.getElementById('toastProgress');

            if (!toastMessage) return;

            const configs = {
                success: { title: 'Created!', color: '#2ecc71', icon: 'check-circle' },
                warning: { title: 'Warning', color: '#f59f00', icon: 'alert-triangle' },
                danger: { title: 'Deleted!', color: '#fa5252', icon: 'trash-2' },
                info: { title: 'Updated!', color: '#154A9A', icon: 'info' }
            };

            const config = configs[type] || configs.success;
            
            toastMessage.textContent = message;
            toastTitle.textContent = config.title;
            toastAccent.style.background = config.color;
            toastProgress.style.background = config.color;
            
            toastIcon.setAttribute('data-lucide', config.icon);
            if (window.lucide) lucide.createIcons();

            this.instance.show();
        }
    },

    validation: {
        async validateField(url, input, field, errorEl, ignoreId = null) {
            const value = input.value.trim();
            if (!value) return;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ field, value, ignore_id: ignoreId })
                });

                const data = await response.json();
                if (data.exists) {
                    errorEl.textContent = data.message;
                    errorEl.classList.remove('d-none');
                    input.classList.add('is-invalid');
                } else {
                    errorEl.classList.add('d-none');
                    input.classList.remove('is-invalid');
                }
            } catch (error) {
                console.error('Validation error:', error);
            }
        }
    },

    delete: {
        itemToDelete: null,
        modal: null,
        callback: null,
        init(callback) {
            const modalEl = document.getElementById('deleteConfirmModal');
            if (modalEl) {
                this.modal = new bootstrap.Modal(modalEl);
                this.callback = callback;
                
                document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
                    if (this.itemToDelete && this.callback) {
                        this.callback(this.itemToDelete);
                    }
                });
            }
        },
        confirm(id, name) {
            this.itemToDelete = id;
            const nameEl = document.getElementById('deleteItemName');
            if (nameEl) nameEl.textContent = name;
            this.modal.show();
        },
        hide() {
            if (this.modal) this.modal.hide();
        }
    }
};

document.addEventListener('DOMContentLoaded', () => {
    RPTS.toast.init();
});
