<!-- Add Indicator Modal -->
<div class="modal fade" id="addIndicatorModal" tabindex="-1" aria-labelledby="addIndicatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
            <div class="modal-accent-primary"></div>
            <div class="modal-header border-0 pt-4 px-4 pb-1">
                <h5 class="modal-title fw-bold" id="addIndicatorModalLabel">Add Indicator</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 text-start">
                <form id="addIndicatorForm">
                    <div class="mb-0">
                        <label for="new_indicator_name" class="form-label small fw-semibold text-secondary mb-1">Indicator Name</label>
                        <input type="text" name="indicator_name" class="form-control rounded-12" id="new_indicator_name" placeholder="Enter Indicator Name" required>
                        <span class="text-danger mt-1 d-block" id="indicator-error" style="font-size: 0.75rem;"></span>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveIndicatorBtn" class="btn btn-primary px-4 py-2 fw-semibold rounded-12 shadow-sm" style="background-color: #154A9A; border-color: #154A9A;">
                    <span class="btn-text">Save Indicator</span>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>
