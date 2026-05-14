<section id="cpp-view" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-start mb-4 no-print">
        <div>
            <h2 class="fw-bold mb-0" id="cpp-view-title" style="font-size:1.3rem;">Project Profile View</h2>
            <p class="text-muted small mb-0">Detailed information for the selected submission.</p>
        </div>
        <div class="d-flex gap-2" id="cpp-view-actions">

            <button class="btn-back-dash" data-page="submissions">
                <i data-lucide="chevron-left" width="16"></i> Back to Submissions
            </button>
        </div>
    </div>

    <!-- The physical form design injected here -->
    <div id="cpp-view-container">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Retrieving submission details...</p>
        </div>
    </div>

    <style>
        @media print {
            body { background: white !important; }
            .no-print, .btn-back-dash, .btn-outline-primary, .sidebar, .topnav { display: none !important; }
            #cpp-view { padding: 0 !important; width: 100% !important; margin: 0 !important; }
            .page-container { box-shadow: none !important; padding: 0 !important; width: 100% !important; margin: 0 !important; }
            @page { margin: 1cm; }
        }
    </style>
</section>
