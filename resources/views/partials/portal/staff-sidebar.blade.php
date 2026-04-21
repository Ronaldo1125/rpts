<div class="offcanvas-md offcanvas-start original-sidebar" tabindex="-1" id="sidebarMenu"
    aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <div class="d-flex align-items-center gap-1">
            <span class="fw-black" style="font-size:1.3rem;letter-spacing:-1px;color:#154A9A;">R</span><span
                class="fw-black" style="font-size:1.3rem;letter-spacing:-1px;color:#002279;">PTS</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0 d-flex flex-column h-100">
        <div class="sidebar-brand p-4 px-4 mb-2">
            <div>
                <div>
                    <span class="fw-black" style="font-size:1.5rem;letter-spacing:-1.5px;color:#154A9A;">R</span><span
                        class="fw-black" style="font-size:1.5rem;letter-spacing:-1.5px;color:#002279;">PTS</span>
                </div>
                <div class="text-muted"
                    style="font-size:0.6rem;letter-spacing:0.1em;text-transform:uppercase;margin-top:-2px;">Regional
                    Project Tracking System</div>
                <div class="badge bg-primary bg-opacity-10 text-primary mt-2 border-0" style="font-size: 0.6rem;">STAFF PORTAL</div>
            </div>
        </div>

        <nav class="nav flex-column ps-3 pe-2 py-3 flex-grow-1 nav-menu">
            <!-- Dashboard -->
            <a href="#" class="nav-link active d-flex align-items-center gap-2 mb-2 rounded-xl px-2 py-2"
                data-page="staff-dashboard">
                <i data-lucide="layout-dashboard" width="18"></i>
                <span class="fw-semibold">Dashboard</span>
            </a>

            <!-- <div class="sidebar-heading px-2 mb-2 mt-3 text-muted small fw-bold text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.05em;">Operations</div> -->

            <!-- CIPG Submissions -->
            <a href="#"
                class="nav-link d-flex align-items-center gap-2 mb-1 rounded-xl px-2 py-2 text-secondary hover-primary"
                data-page="manage-submissions">
                <i data-lucide="layers" width="18"></i>
                <span class="fw-semibold">Manage Projects for RDIP Inclusion</span>
            </a>

            <!-- RDC Review and Validation -->
            <div class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center gap-2 rounded-xl px-2 py-2 text-secondary hover-primary collapsed"
                    href="#rdc-review-validation-submenu" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="rdc-review-validation-submenu">
                    <i data-lucide="clipboard-list" width="18"></i>
                    <span class="fw-semibold">RDC Review and Validation</span>
                    <i data-lucide="chevron-right" class="ms-auto arrow-icon" width="14"></i>
                </a>
                <div class="collapse" id="rdc-review-validation-submenu" data-bs-parent=".nav-menu">
                    <ul class="nav flex-column submenu-list ps-3">
                        <li class="nav-item">
                            <a href="#" class="nav-link submenu-link" data-page="staff-referrals">Referrals</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link submenu-link" data-page="staff-project-assessment">Project Assessment Report</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Profile -->
            <a href="#" class="nav-link d-flex align-items-center gap-2 mb-1 rounded-xl px-2 py-2 text-secondary hover-primary"
                data-page="profile">
                <i data-lucide="user-circle" width="18"></i>
                <span class="fw-semibold">Profile</span>
            </a>

        </nav>

        <div class="p-3 mb-2">
            <a href="#"
                class="nav-link d-flex align-items-center gap-2 rounded-xl px-2 py-2 text-danger logout-link shadow-hover">
                <i data-lucide="power" width="18"></i>
                <span class="fw-semibold">Logout</span>
            </a>
        </div>
    </div>
</div>

<style>
    .original-sidebar {
        width: 280px !important;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.5) !important;
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.02);
        z-index: 1050;
        transition: transform 0.3s ease;
        overflow-y: auto;
    }
    .rounded-xl { border-radius: 12px !important; }
    .nav-menu .nav-link {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #64748b;
        position: relative;
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
        white-space: normal !important;
        line-height: 1.2 !important;
    }
    .nav-menu .nav-link:hover {
        background: rgba(21, 74, 154, 0.07);
        color: #154A9A !important;
        transform: translateX(4px);
    }
    .nav-menu .nav-link.active:not(.submenu-link) {
        background: #154A9A !important;
        color: #fff !important;
        box-shadow: 0 10px 15px -3px rgba(21, 74, 154, 0.25);
    }
    .nav-menu .nav-link.active:not(.submenu-link) i { color: #fff !important; }
    
    .nav-menu .submenu-link.active {
        background: transparent !important;
        font-weight: 600;
        color: #154A9A !important;
    }

    .arrow-icon { transition: transform 0.3s ease; }
    .nav-link:not(.collapsed) .arrow-icon { transform: rotate(90deg); }
    
    .submenu-list { border-left: 1px solid rgba(0, 0, 0, 0.05); margin-top: 0.5rem; margin-bottom: 0.5rem; }
    
    .submenu-link { 
        font-size: 0.85rem !important; 
        padding: 0.6rem 0.75rem !important; 
        display: flex !important; 
        align-items: flex-start !important; 
        gap: 10px !important; 
        white-space: normal !important;
        line-height: 1.3 !important;
    }
    .submenu-link::before { 
        content: ''; 
        width: 6px; 
        height: 6px; 
        background: #cbd5e1; 
        border-radius: 50%; 
        transition: all 0.3s ease;
        margin-top: 0.45rem; 
        flex-shrink: 0; 
    }
    .submenu-link:hover::before,
    .submenu-link.active::before { 
        background: #154A9A; 
        transform: scale(1.3);
    }
</style>

