<div class="offcanvas-md offcanvas-start original-sidebar" tabindex="-1" id="sidebarMenu"
    aria-labelledby="sidebarMenuLabel" data-bs-backdrop="false">
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
            <a href="#"
                class="nav-link d-flex align-items-center gap-2 mb-2 rounded-xl px-2 py-2 text-secondary hover-primary"
                data-page="agency-dashboard">
                <i data-lucide="layout-dashboard" width="18"></i>
                <span class="fw-semibold">Dashboard</span>
            </a>

            <a href="#"
                class="nav-link d-flex align-items-center gap-2 mb-1 rounded-xl px-2 py-2 text-secondary hover-primary"
                data-page="cipg-guide">
                <i data-lucide="book-open" width="18"></i>
                <span class="fw-semibold">CIPG Guide</span>
            </a>
            <!-- Guide Progress Indicator -->
            <div id="guide-progress-container" class="ms-4 mb-3 d-none">
                <div class="submenu-list ms-2">
                    <div class="submenu-link small py-1 opacity-75 guide-progress-item" data-section="cipg-intro">
                        Overview
                    </div>
                    <div class="submenu-link small py-1 opacity-75 guide-progress-item" data-section="cipg-framework">
                        Framework
                    </div>
                    <div class="submenu-link small py-1 opacity-75 guide-progress-item" data-section="cipg-process">
                        Process
                    </div>
                    <div class="submenu-link small py-1 opacity-75 guide-progress-item" data-section="cipg-resources">
                        Resources
                    </div>
                    <div class="submenu-link small py-1 opacity-75 guide-progress-item" data-section="cipg-faq">
                        FAQs
                    </div>
                </div>
            </div>
            <a href="#" class="nav-link d-flex align-items-center gap-2 mb-2 rounded-xl px-2 py-2 text-secondary hover-primary"
                data-page="submissions">
                <i data-lucide="send" width="18"></i>
                <span class="fw-semibold">Submit Project</span>
            </a>


            <a href="#"
                class="nav-link d-flex align-items-center gap-2 mb-1 rounded-xl px-2 py-2 text-secondary hover-primary d-none"
                id="agency-comments-menu"
                data-page="agency-comments">
                <i data-lucide="message-square" width="18"></i>
                <span class="fw-semibold">Comments & Recommendations</span>
            </a>
 
            <a href="#"
                class="nav-link d-flex align-items-center gap-2 mb-1 rounded-xl px-2 py-2 text-secondary hover-primary"
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
    /* Original Premium Sidebar Restoration */
    .original-sidebar {
        width: 250px !important;
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
        font-size: 0.875rem !important;
        padding: 0.5rem 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .submenu-link::before {
        content: '';
        width: 6px;
        height: 6px;
        background: #cbd5e1;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .submenu-link:hover::before,
    .submenu-link.active::before {
        background: #154A9A;
        transform: scale(1.3);
    }

    /* Progress Dot Specifics (Non-clickable) */
    .guide-progress-item {
        cursor: pointer !important;
        pointer-events: auto !important;
    }

    .logout-link:hover {
        background: rgba(239, 68, 68, 0.1) !important;
    }

    /* Custom Premium Scrollbar only for mobile */
    @media (max-width: 767.98px) {
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
    }
    
    /* Hide scrollbar completely on desktop since it shouldn't need to scroll */
    @media (min-width: 768px) {
        .original-sidebar::-webkit-scrollbar {
            display: none;
        }
        .original-sidebar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    }
</style>

