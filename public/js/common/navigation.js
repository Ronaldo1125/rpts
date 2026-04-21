

export function initNavigation() {
    const pages = document.querySelectorAll('.page-content');

    // Expose global nav function
    window.switchPage = (pageId) => {
        // ... (original content of switchPage remains here, I'll provide the full merged version)
        // If navigating away from project-workspace, reset its state
        const currentActive = document.querySelector('.page-content.active');
        if (currentActive && currentActive.id === 'project-workspace' && pageId !== 'project-workspace') {
            if (typeof window.resetProjectWorkspace === 'function') {
                window.resetProjectWorkspace();
            }
        }

        // If navigating to project-workspace without project data, ensure it's clean
        if (pageId === 'project-workspace') {
            const workspaceContent = document.getElementById('workspace-form-content');
            if (workspaceContent && workspaceContent.innerHTML.trim() === '') {
                if (typeof window.resetProjectWorkspace === 'function') {
                    window.resetProjectWorkspace();
                }
            }
        }

        const targetPage = document.getElementById(pageId);
        if (targetPage) {
            // Close all active Bootstrap dropdowns before switching
            if (window.bootstrap) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                    const toggle = menu.previousElementSibling || menu.parentElement.querySelector('[data-bs-toggle="dropdown"]') || menu.parentElement;
                    const instance = window.bootstrap.Dropdown.getInstance(toggle);
                    if (instance) instance.hide();
                    else menu.classList.remove('show');
                });
            }

            pages.forEach(p => p.classList.remove('active'));
            targetPage.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Clear temporary view-only flags if moving away from source view
            if (pageId !== 'test-and-evaluation-form') {
                sessionStorage.removeItem('cte_no_edit_link');
            }

            // --- SYNC NAVIGATION ACTIVE STATE ---
            // Only update sidebar/topnav active styling if the target page is in the nav
            const navLink = document.querySelector(`[data-page="${pageId}"]`);
            if (navLink) {
                document.querySelectorAll('.nav-link, .submenu-link').forEach(l => l.classList.remove('active'));
                navLink.classList.add('active');

                // Handle parent activation for sub-navigation
                if (navLink.classList.contains('submenu-link')) {
                    const parentCollapse = navLink.closest('.collapse');
                    if (parentCollapse) {
                        const parentToggle = document.querySelector(`[data-bs-target="#${parentCollapse.id}"]`) || document.querySelector(`[href="#${parentCollapse.id}"]`);
                        if (parentToggle) {
                            parentToggle.classList.add('active');
                        }
                    }
                }
            }
        }
    };

    // Page Navigation (delegated so it also works for dynamically injected buttons)
    document.addEventListener('click', (e) => {
        const link = e.target.closest('[data-page]');
        if (!link) return;

        const targetPageId = link.getAttribute('data-page');
        if (!targetPageId) return;

        e.preventDefault();

        // Always navigate via switchPage so page-leave hooks run (e.g., resetProjectWorkspace)
        // This will now ALSO handle updating the nav state.
        window.switchPage(targetPageId);

        // Close offcanvas on mobile if open
        const offcanvasElement = document.getElementById('sidebarMenu');
        if (window.bootstrap && offcanvasElement) {
            const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasElement);
            if (offcanvasInstance) {
                offcanvasInstance.hide();
            }
        }
    });

    document.addEventListener('click', (e) => {
        if (e.target.closest('.logout-link')) {
            e.preventDefault();
            sessionStorage.clear();
            window.location.href = '/';
        }
    });

}
