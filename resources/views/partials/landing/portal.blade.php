<div class="entry-modal">
    <div class="portal-logos" style="display: flex; justify-content: center; gap: 2rem; align-items: center; margin-bottom: 2.5rem;">
        <img src="assets/images/depdev.png" alt="DepDev Logo" style="height: 100px; width: auto; filter: brightness(0) invert(1);">
        <img src="assets/images/bago.png" alt="Bago Logo" style="height: 100px; width: auto;">
        <img src="assets/images/rdc.png" alt="RDC Logo" style="height: 100px; width: auto;">
    </div>
    <h2 class="entry-welcome">Welcome to Bicol Region Project<br> Tracking System (RPTS)</h2>
    <p class="entry-subtitle">Please select your portal to continue</p>

    <div class="portal-choices" id="choices-container">
        <a href="{{ route('projectDashboard.index') }}" class="portal-card" id="btn-citizen">
            <div class="portal-icon"><i data-lucide="eye"></i></div>
            <h3>Project Dashboard</h3>
            <p>Browse projects, track status updates, and view development maps.</p>
        </a>

        <button class="portal-card" id="btn-agency">
            <div class="portal-icon"><i data-lucide="building-2"></i></div>
            <h3>RDIP Inclusion</h3>
            <p>Submit Comprehensive Project Profile and other CIPG documentary requirements.</p>
        </button>
    </div>
</div>

