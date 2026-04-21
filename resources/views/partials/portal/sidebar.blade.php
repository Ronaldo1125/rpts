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
            </div>
        </div>

        <nav class="nav flex-column ps-3 pe-2 py-3 flex-grow-1 nav-menu">
            <a href="#" class="nav-link active d-flex align-items-center gap-2 mb-2 rounded-xl px-2 py-2"
                data-page="dashboards">
                <i data-lucide="layout-dashboard" width="18"></i>
                <span class="fw-semibold">Dashboard</span>
            </a>

            <a href="#" class="nav-link d-flex align-items-center gap-2 mb-2 rounded-xl px-2 py-2"
                data-page="manage-submissions">
                <i data-lucide="list-checks" width="18"></i>
                <span class="fw-semibold">Manage Projects for RDIP Inclusion</span>
            </a>

            <div class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center gap-2 rounded-xl px-2 py-2 text-secondary hover-primary collapsed"
                    href="#projects-submenu" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="projects-submenu">
                    <i data-lucide="layers" width="18"></i>
                    <span class="fw-semibold">Projects</span>
                    <i data-lucide="chevron-right" class="ms-auto arrow-icon" width="14"></i>
                </a>
                <div class="collapse" id="projects-submenu" data-bs-parent=".nav-menu">
                    <ul class="nav flex-column submenu-list ps-3">
                        <li class="nav-item">
                            <a href="#" class="nav-link submenu-link" data-page="projects">Single Project</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link submenu-link" data-page="component-project">Component
                                Project</a>
                        </li>
                    </ul>
                </div>
            </div>



            <div class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center gap-2 rounded-xl px-2 py-2 text-secondary hover-primary collapsed"
                    href="#rdc-review-validation-submenu" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="rdc-review-validation-submenu">
                    <i data-lucide="clipboard-list" width="18"></i>
                    <span class="fw-semibold" style="font-size: 0.9rem;">RDC Review & Validation</span>
                    <i data-lucide="chevron-right" class="ms-auto arrow-icon" width="14"></i>
                </a>
                <div class="collapse" id="rdc-review-validation-submenu" data-bs-parent=".nav-menu">
                    <ul class="nav flex-column submenu-list ps-3">
                        <li class="nav-item">
                            <a href="#" class="nav-link submenu-link" data-page="admin-referrals">Referrals</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link submenu-link" data-page="admin-project-assessment">Project Assessment Report</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="nav-item mb-1 mt-1">
                <a class="nav-link d-flex align-items-center gap-1 rounded-xl px-2 py-2 text-secondary hover-primary collapsed"
                    href="#admin-management-submenu" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="admin-management-submenu">
                    <i data-lucide="settings" width="18"></i>
                    <span class="fw-semibold px-1">Admin Management</span>
                    <i data-lucide="chevron-right" class="ms-auto arrow-icon" width="14"></i>
                </a>
                <div class="collapse" id="admin-management-submenu" data-bs-parent=".nav-menu">
                    <ul class="nav flex-column submenu-list ps-3">
                        <li><a href="#" class="nav-link submenu-link" data-page="agency">Agencies</a></li>
                        <li><a href="#" class="nav-link submenu-link" data-page="rdp-chapter">RDP Chapters</a></li>
                        <li><a href="#" class="nav-link submenu-link" data-page="sector">Sectors</a></li>
                        <li><a href="#" class="nav-link submenu-link" data-page="sub-sector">Sub-Sectors</a></li>

                        <li><a href="#" class="nav-link submenu-link" data-page="endorse-year">Endorse Years</a></li>
                        <li><a href="#" class="nav-link submenu-link" data-page="indicator">Indicators</a></li>
                        <li><a href="#" class="nav-link submenu-link" data-page="activity-logs">Activity Logs</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center gap-2 rounded-xl px-2 py-2 text-secondary hover-primary collapsed"
                    href="#user-mgmt-submenu" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="user-mgmt-submenu">
                    <i data-lucide="users" width="18"></i>
                    <span class="fw-semibold">User Management</span>
                    <i data-lucide="chevron-right" class="ms-auto arrow-icon" width="14"></i>
                </a>
                <div class="collapse" id="user-mgmt-submenu" data-bs-parent=".nav-menu">
                    <ul class="nav flex-column submenu-list ps-3">
                        <li><a href="#" class="nav-link submenu-link" data-page="users">Users</a></li>
                        <li><a href="#" class="nav-link submenu-link" data-page="profile">Profile</a></li>
                    </ul>
                </div>
            </div>

            <div class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center gap-2 rounded-xl px-2 py-2 text-secondary hover-primary collapsed"
                    href="#role-mgmt-submenu" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="role-mgmt-submenu">
                    <i data-lucide="shield" width="18"></i>
                    <span class="fw-semibold">Role Management</span>
                    <i data-lucide="chevron-right" class="ms-auto arrow-icon" width="14"></i>
                </a>
                <div class="collapse" id="role-mgmt-submenu" data-bs-parent=".nav-menu">
                    <ul class="nav flex-column submenu-list ps-3">
                        <li><a href="#" class="nav-link submenu-link" data-page="roles">Roles</a></li>
                        <li><a href="#" class="nav-link submenu-link" data-page="permissions">Permissions</a></li>
                    </ul>
                </div>
            </div>

            <div class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center gap-2 rounded-xl px-2 py-2 text-secondary hover-primary collapsed"
                    href="#reports-submenu" data-bs-toggle="collapse" role="button" aria-expanded="false"
                    aria-controls="reports-submenu">
                    <i data-lucide="pie-chart" width="18"></i>
                    <span class="fw-semibold">Reports</span>
                    <i data-lucide="chevron-right" class="ms-auto arrow-icon" width="14"></i>
                </a>
                <div class="collapse" id="reports-submenu" data-bs-parent=".nav-menu">
                    <ul class="nav flex-column submenu-list ps-3">
                        <li class="nav-item">
                            <a href="#" class="nav-link submenu-link" data-page="reports">Project Reports</a>
                        </li>
                    </ul>
                </div>
            </div>

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
    /* Original Premium Sidebar Restoration */
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

    .rounded-xl {
        border-radius: 12px !important;
    }

    /* Nav Menu Styles */
    .nav-menu .nav-link {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: #64748b;
        position: relative;
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
        white-space: normal !important;
        line-height: 1.2 !important;
    }
    
    .nav-menu .nav-link:not(.submenu-link) {
        /* Removed white-space: nowrap to allow wrapping */
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

    .nav-menu .nav-link.active:not(.submenu-link) i {
        color: #fff !important;
    }

    .nav-menu .submenu-link.active {
        background: transparent !important;
        font-weight: 600;
    }

    /* Arrow rotation */
    .nav-link:not(.collapsed) .arrow-icon {
        transform: rotate(90deg);
    }

    .arrow-icon {
        transition: transform 0.3s ease;
    }

    /* Submenu Dot Indicators */
    .submenu-list {
        border-left: 1px solid rgba(0, 0, 0, 0.05);
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }

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
        flex-shrink: 0;
        margin-top: 0.45rem;
    }

    .submenu-link:hover::before,
    .submenu-link.active::before {
        background: #154A9A;
        transform: scale(1.3);
    }

    .logout-link:hover {
        background: rgba(239, 68, 68, 0.1) !important;
    }

    /* Custom Premium Scrollbar */
    .original-sidebar::-webkit-scrollbar {
        width: 5px;
    }

    .original-sidebar::-webkit-scrollbar-track {
        background: transparent;
    }

    .original-sidebar::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
    }

    .original-sidebar:hover::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
    }

    .original-sidebar::-webkit-scrollbar-thumb:hover {
        background: rgba(21, 74, 154, 0.4);
    }
</style>

