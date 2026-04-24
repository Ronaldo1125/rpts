<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-4" style="z-index: 9999;">
    <div id="customToast" class="toast border-0 shadow-lg rounded-20 overflow-hidden glass-toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body p-0">
            <div class="d-flex align-items-stretch">
                <div id="toastAccent" class="d-flex align-items-center justify-content-center px-3" style="background: #154A9A;">
                    <i id="toastIcon" data-lucide="check-circle" class="text-white" width="24" height="24"></i>
                </div>
                <div class="p-3 flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 id="toastTitle" class="fw-bold mb-0 text-dark">Action Successful</h6>
                        <button type="button" class="btn-close shadow-none small" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <p id="toastMessage" class="text-secondary mb-0 small fw-medium"></p>
                </div>
            </div>
            <div id="toastProgress" class="toast-progress-bar"></div>
        </div>
    </div>
</div>

<style>
    .glass-toast {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        min-width: 320px;
        animation: toastSlideIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    
    .toast-progress-bar {
        height: 3px;
        background: #154A9A;
        width: 100%;
        animation: toastProgress 5s linear forwards;
    }

    .rounded-20 { border-radius: 20px !important; }

    @keyframes toastSlideIn {
        from { transform: translateX(100%) translateY(20px); opacity: 0; }
        to { transform: translateX(0) translateY(0); opacity: 1; }
    }

    @keyframes toastProgress {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>
