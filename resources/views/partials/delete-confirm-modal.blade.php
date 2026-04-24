<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-20 text-center p-4">
            <div class="mb-3 mt-2">
                <div class="delete-icon-wrapper mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: #fff5f5; color: #fa5252; border-radius: 50%;">
                    <i data-lucide="alert-triangle" width="32" height="32"></i>
                </div>
                <h5 class="fw-bold text-dark">Are you sure?</h5>
                <p class="text-secondary small">You are about to delete <span id="deleteItemName" class="fw-bold text-dark"></span>. This action cannot be undone.</p>
            </div>
            <div class="d-flex gap-2 mt-2">
                <button type="button" class="btn btn-light w-100 rounded-12 py-2 fw-semibold border-0" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger w-100 rounded-12 py-2 fw-semibold border-0">Delete</button>
            </div>
        </div>
    </div>
</div>
