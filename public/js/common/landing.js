async function loadComponent(id, path) {
    const response = await fetch(path);
    const html = await response.text();
    const container = document.getElementById(id);
    if (container) {
        container.innerHTML = html;
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }
}

async function init() {
    // Component loading is now handled server-side via Blade @includes
    initializeEvents();

    // Standalone Page Initializations
    const dashboardToggle = document.getElementById('dashboard-metric-toggle');
    if (dashboardToggle) {
        // We are on the standalone dashboard page
        const isCost = dashboardToggle.checked || false;
        initializeCharts(isCost);
        setupDashboardFilters();
        
        // Ensure standard scrolling and no-snap
        document.documentElement.classList.add('no-snap');
        document.body.style.overflow = 'auto';
    } else {
        // We are on the landing page/portal home
        showPortalHome();
    }
}

function initializeEvents() {
    //Lucide icons
    if (window.lucide) {
        window.lucide.createIcons();
    }

    // DOM Elements
    const menuToggle = document.getElementById('menu-toggle');
    const searchToggle = document.getElementById('search-toggle');
    const overlay = document.getElementById('sidebar-overlay');
    const searchInput = document.querySelector('.search-input');

    // Layout Containers
    const portalContainer = document.getElementById('portal-container');
    const loginWrapper = document.getElementById('login-container-wrapper');
    const forgotWrapper = document.getElementById('forgot-password-container-wrapper');
    const resetWrapper = document.getElementById('reset-new-password-wrapper');
    const publicWrapper = document.getElementById('public-container-wrapper');
    const rdipWrapper = document.getElementById('rdip-container-wrapper');
    const faqWrapper = document.getElementById('faq-container-wrapper');
    const heroWrapper = document.getElementById('hero-container-wrapper');
    const annSection = document.getElementById('announcements-section'); // injected before hero
    const landingFooter = document.getElementById('footer-container-wrapper');

    // UI Helpers
    const btnHeaderLogin = document.getElementById('btn-header-login');
    const topBar = document.querySelector('.top-bar');

    // Buttons (using querySelector since they are in loaded components)
    const btnCitizen = document.getElementById('btn-citizen');
    const btnAgency = document.getElementById('btn-agency');

    const hideAll = () => {
        [portalContainer, loginWrapper, forgotWrapper, resetWrapper, publicWrapper, rdipWrapper, faqWrapper, heroWrapper, annSection, landingFooter].forEach(el => {
            if (el) el.style.display = 'none';
        });
        if (rdipWrapper) {
            rdipWrapper.classList.remove('rdip-active');
            rdipWrapper.style.background = '';
        }
        if (faqWrapper) {
            faqWrapper.classList.remove('rdip-active');
            faqWrapper.classList.remove('no-bg-overlay');
            faqWrapper.style.background = '';
        }
        if (landingFooter) {
            landingFooter.classList.remove('footer-rdip-mode');
            landingFooter.style.display = 'block'; // Always show footer generally
        }
        if (btnHeaderLogin) btnHeaderLogin.style.display = 'block';

        const topBar = document.querySelector('.top-bar');
        if (topBar) topBar.classList.remove('faq-mode');
    };

    const showView = (targetView) => {
        hideAll();
        // Show target
        if (targetView) targetView.style.display = 'flex';
        document.body.style.overflow = 'auto';
        // Disable scroll-snap so scrollbar track clicks scroll naturally in sub-views
        document.documentElement.classList.add('no-snap');
        window.scrollTo({ top: 0, behavior: 'instant' });
    };

    const showRdipView = () => {
        showView(rdipWrapper); // hides all others, sets rdipWrapper to flex
        if (rdipWrapper) {
            rdipWrapper.style.display = 'block'; // override to block for page layout
            rdipWrapper.classList.add('rdip-active'); // enables background + layout CSS
        }
        if (landingFooter) {
            landingFooter.style.display = 'block'; // Ensure footer is visible
            landingFooter.classList.add('footer-rdip-mode'); // Switch footer to white mode
        }

        const topBar = document.querySelector('.top-bar');
        if (topBar) topBar.classList.add('faq-mode');

        if (window.lucide) window.lucide.createIcons();
        
        // Initialize Flipbook if not already done
        setTimeout(() => initFlipbook(), 100);
    };

    const showFaqView = () => {
        showView(faqWrapper);
        if (faqWrapper) {
            faqWrapper.style.display = 'block'; // block layout like rdip
            faqWrapper.style.background = '#ffffff'; // Force solid white to prevent gaps
            faqWrapper.classList.add('no-bg-overlay'); // Hide the building background pseudo-element
        }
        if (landingFooter) {
            landingFooter.style.display = 'block';
            landingFooter.classList.add('footer-rdip-mode');
        }

        const topBar = document.querySelector('.top-bar');
        if (topBar) topBar.classList.add('faq-mode');

        if (window.lucide) window.lucide.createIcons();
    };

    const showPortalHome = () => {
        hideAll();
        
        if (portalContainer) portalContainer.style.display = 'flex';
        if (heroWrapper) heroWrapper.style.display = 'flex';
        if (annSection) annSection.style.display = 'flex';
        if (landingFooter) landingFooter.style.display = 'block';

        document.body.style.overflow = 'auto';
        // Re-enable scroll-snap for the section-by-section landing experience
        document.documentElement.classList.remove('no-snap');
        window.scrollTo({ top: 0, behavior: 'instant' });
    };

    const showLoginForm = () => {
        showView(loginWrapper);
        if (window.lucide) window.lucide.createIcons();
    };

    const showForgotPasswordForm = () => {
        showView(forgotWrapper);
        // Reset state
        const form = document.getElementById('forgot-password-form');
        const success = document.getElementById('reset-success-message');
        if (form) form.style.display = '';
        if (success) success.style.display = 'none';
        if (window.lucide) window.lucide.createIcons();
    };

    const showSetNewPasswordForm = () => {
        showView(resetWrapper);
        const form = document.getElementById('reset-password-new-form');
        const success = document.getElementById('password-changed-success');
        if (form) form.style.display = '';
        if (success) success.style.display = 'none';
        if (window.lucide) window.lucide.createIcons();
    };

    const showPublicView = () => {
        showView(publicWrapper);
        if (landingFooter) {
            landingFooter.style.display = 'block'; // Enable footer for dashboard
            landingFooter.classList.add('footer-rdip-mode'); // Make footer white for dashboard
        }
        if (window.lucide) window.lucide.createIcons();
        const isCost = document.getElementById('dashboard-metric-toggle')?.checked || false;
        initializeCharts(isCost);
        setupDashboardFilters();
    };

    const setupDashboardFilters = () => {
        const filters = [
            'filter-location', 'filter-province', 'filter-district',
            'filter-city', 'filter-agency', 'filter-status'
        ];

        const searchInput = document.getElementById('public-search');
        const locationSelect = document.getElementById('filter-location');
        const groupsToToggle = {
            'province': document.getElementById('group-province'),
            'district': document.getElementById('group-district'),
            'city': document.getElementById('group-city')
        };

        const applyFilters = () => {
            const tableRows = document.querySelectorAll('.project-table tbody tr');
            const searchVal = (searchInput?.value || '').toLowerCase();
            const filterVals = {
                location: document.getElementById('filter-location')?.value || 'all',
                province: document.getElementById('filter-province')?.value || 'All',
                district: document.getElementById('filter-district')?.value || 'All',
                city: document.getElementById('filter-city')?.value || 'All',
                agency: document.getElementById('filter-agency')?.value || 'all',
                status: document.getElementById('filter-status')?.value || 'all'
            };

            let visibleCount = 0;

            tableRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 5) return;

                const profileText = cells[0].textContent.toLowerCase();
                const cost = cells[1].textContent.toLowerCase();
                const loc = cells[2].textContent.toLowerCase();
                const agen = cells[3].textContent.toLowerCase();
                const stat = cells[4].textContent.toLowerCase();

                // Search Match (Profile includes title and desc)
                const matchesSearch = !searchVal ||
                    profileText.includes(searchVal) ||
                    agen.includes(searchVal) ||
                    loc.includes(searchVal);

                // Dropdown Matches
                const matchesLocation = filterVals.location === 'all' ||
                    (filterVals.location === 'specific' && loc !== 'regionwide' && loc !== 'nationwide') ||
                    loc.includes(filterVals.location.toLowerCase());

                const matchesProvince = filterVals.province === 'All' || loc.includes(filterVals.province.toLowerCase());
                const matchesDistrict = filterVals.district === 'All' || loc.includes(filterVals.district.toLowerCase());
                const matchesCity = filterVals.city === 'All' || loc.includes(filterVals.city.toLowerCase());
                const matchesAgency = filterVals.agency === 'all' || agen.includes(filterVals.agency.toLowerCase());
                const matchesStatus = filterVals.status === 'all' || stat.includes(filterVals.status.toLowerCase());

                // Combine conditions
                let isVisible = matchesSearch && matchesAgency && matchesStatus;

                if (filterVals.location === 'specific') {
                    isVisible = isVisible && matchesProvince && matchesDistrict && matchesCity;
                } else if (filterVals.location !== 'all') {
                    isVisible = isVisible && matchesLocation;
                }

                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });

            // Update results count
            const resultsCount = document.getElementById('results-count');
            if (resultsCount) resultsCount.textContent = visibleCount;
        };

        // Add event listeners
        filters.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('change', applyFilters);
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyFilters);
        }

        // Location Toggle Logic
        if (locationSelect) {
            locationSelect.addEventListener('change', (e) => {
                const isSpecific = e.target.value === 'specific';
                Object.values(groupsToToggle).forEach(group => {
                    if (group) group.style.display = isSpecific ? 'block' : 'none';
                });
            });
        }

        // Mobile Filter Toggle
        const mobileToggle = document.getElementById('btn-mobile-filter-toggle');
        const sidebar = document.getElementById('v3-sidebar');
        if (mobileToggle && sidebar) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                const isActive = sidebar.classList.contains('active');
                mobileToggle.innerHTML = isActive 
                    ? '<i data-lucide="chevron-up"></i> Hide Filters' 
                    : '<i data-lucide="filter"></i> Show Filters';
                if (window.lucide) window.lucide.createIcons();
            });
        }
    };

    // btnCitizen and btnAgency listeners removed or modified
    if (btnAgency) btnAgency.addEventListener('click', () => {
        sessionStorage.setItem('portalType', 'agency');
        sessionStorage.setItem('loginTarget', 'cipg');
        showLoginForm();
    });

    // Sidebar navigation and Explore Button
    document.addEventListener('click', (e) => {
        const sidebarHome = e.target.closest('#sidebar-link-home, #footer-link-home, #footer-link-about');
        const sidebarDashboard = e.target.closest('#sidebar-link-dashboard, #footer-link-dashboard, #footer-link-dashboard-alt');
        const exploreBtn = e.target.closest('#btn-explore-projects');

        if (sidebarHome) {
            showPortalHome();
            document.body.classList.remove('sidebar-open');
            // Update active state
            document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
            const homeLink = document.getElementById('sidebar-link-home');
            if (homeLink) homeLink.classList.add('active');
        }

        // Dashboard/Explore links are now handled by native <a> hrefs
        // if (sidebarDashboard || exploreBtn) { ... removed ... }

        const sidebarFAQ = e.target.closest('#sidebar-link-faq');
        if (sidebarFAQ) {
            document.body.classList.remove('sidebar-open');
            document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
            const faqLink = document.getElementById('sidebar-link-faq');
            if (faqLink) faqLink.classList.add('active');
        }
        const sidebarContact = e.target.closest('#sidebar-link-contact, #footer-link-contact');

        if (sidebarContact) {
            e.preventDefault();
            // First return to portal home so landingFooter becomes visible,
            // then scroll to it after the DOM has painted.
            showPortalHome();
            document.body.classList.remove('sidebar-open');
            document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
            const contactLink = document.getElementById('sidebar-link-contact');
            if (contactLink) contactLink.classList.add('active');

            const footer = document.getElementById('footer-container-wrapper');
            if (footer) {
                // Small delay lets the display:block paint before scrollIntoView
                setTimeout(() => footer.scrollIntoView({ behavior: 'smooth' }), 80);
            }
        }

        // Dashboard Master Tab switching (Analytics vs Registry)
        const masterTab = e.target.closest('[data-master-tab]');
        if (masterTab) {
            const target = masterTab.getAttribute('data-master-tab');
            document.querySelectorAll('.m-tab').forEach(t => t.classList.remove('active'));
            masterTab.classList.add('active');
            document.querySelectorAll('.m-tab-panel').forEach(p => p.classList.remove('active'));
            const panel = document.getElementById('m-panel-' + target);
            if (panel) {
                panel.classList.add('active');
                if (window.lucide) window.lucide.createIcons();
                // Trigger resize for charts if switching to analytics
                if (target === 'analytics') {
                    setTimeout(() => window.dispatchEvent(new Event('resize')), 50);
                }
            }
        }

        // Dashboard Tab switching (Overview, Geo, Trends)
        const dashTab = e.target.closest('[data-dash-tab]');
        if (dashTab) {
            const target = dashTab.getAttribute('data-dash-tab');
            document.querySelectorAll('.dash-tab').forEach(t => t.classList.remove('active'));
            dashTab.classList.add('active');
            document.querySelectorAll('.dash-tab-panel').forEach(p => p.classList.remove('active'));
            const panel = document.getElementById('dash-panel-' + target);
            if (panel) {
                panel.classList.add('active');
                if (window.lucide) window.lucide.createIcons();
                setTimeout(() => window.dispatchEvent(new Event('resize')), 50);
                // Invalidate map size when Geo & Sector tab becomes active
                if (target === 'details' && window.BicolMap) {
                    setTimeout(() => window.BicolMap.invalidate(), 120);
                }
            }
        }


        // Announcements filter pills
        const annPill = e.target.closest('[data-ann-filter]');
        if (annPill) {
            document.querySelectorAll('[data-ann-filter]').forEach(p => p.classList.remove('active'));
            annPill.classList.add('active');
            const f = annPill.dataset.annFilter;
            const items = document.querySelectorAll('#ann-list .ann-item');
            const empty = document.getElementById('ann-empty');
            let visible = 0;
            items.forEach(item => {
                const show = f === 'all' || item.dataset.annType === f;
                item.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (empty) empty.style.display = visible === 0 ? 'block' : 'none';
        }
    });

    // Event delegation for dynamically loaded back buttons
    document.addEventListener('click', (e) => {
        // Toggle new filter menus
        const filterBtn = e.target.closest('.filter-btn');
        if (filterBtn) {
            const category = filterBtn.parentElement;
            const isOpen = category.classList.contains('active');

            // Close all other menus
            document.querySelectorAll('.filter-category').forEach(el => el.classList.remove('active'));

            if (!isOpen) {
                category.classList.add('active');
            }
            return;
        }

        // Close menus on outside click
        if (!e.target.closest('.filter-category')) {
            document.querySelectorAll('.filter-category').forEach(el => el.classList.remove('active'));
        }

        if (e.target.closest('#btn-reset-filters, #btn-reset-filters-new')) {
            // Reset all select elements to 'All'
            const selects = document.querySelectorAll('.dashboard-filters select');
            selects.forEach(select => {
                select.selectedIndex = 0;
            });

            // Hide conditional location groups
            const conditionalGroups = ['group-province', 'group-district', 'group-city'];
            conditionalGroups.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });

            // Uncheck all custom filter checkboxes (if any)
            const checkboxes = document.querySelectorAll('.filter-menu input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);

            // Clear search
            const mainSearch = document.getElementById('public-search');
            if (mainSearch) {
                mainSearch.value = '';
                // Trigger input event to refresh table/results
                mainSearch.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
        if (e.target.closest('#btn-back-choices')) {
            showPortalHome();
            const sidebarHome = document.getElementById('sidebar-link-home');
            if (sidebarHome) {
                document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
                sidebarHome.classList.add('active');
            }
        }

        if (e.target.closest('#btn-back-rdip') || e.target.closest('#btn-back-faq')) {
            showPortalHome();
            const sidebarHome = document.getElementById('sidebar-link-home');
            if (sidebarHome) {
                document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
                sidebarHome.classList.add('active');
            }
        }
        if (e.target.closest('#header-logo-home')) {
            showPortalHome();
            // Reset sidebar active state to home
            const sidebarHome = document.getElementById('sidebar-link-home');
            if (sidebarHome) {
                document.querySelectorAll('.sidebar-link').forEach(link => link.classList.remove('active'));
                sidebarHome.classList.add('active');
            }
        }
    });

    // Initial view set by init() based on page context
    // showPortalHome();

    document.addEventListener('click', (e) => {
        if (e.target.closest('.toggle-password')) {
            const icon = e.target.closest('.toggle-password');
            const input = icon.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            if (window.lucide) window.lucide.createIcons();
        }
    });

    if (btnHeaderLogin) btnHeaderLogin.addEventListener('click', () => {
        sessionStorage.setItem('loginTarget', 'dashboard');
        showLoginForm();
    });

    const loginForm = document.getElementById('login-form-element');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value.trim();

            if (!email || !password) {
                alert('Please fill in both your email address and password to continue.');
                return;
            }

            const target = sessionStorage.getItem('loginTarget');
            sessionStorage.removeItem('loginTarget');

            // 1. Check localforage system_users first (Dynamic Auth)
            localforage.getItem('system_users').then(async users => {
                users = users || [];

                // Versioned seed — same version key as users-loader.js.
                // If version is outdated, re-seed to overwrite stale data (e.g. wrong division).
                const SEED_VERSION = 'v2';
                const storedVersion = await localforage.getItem('system_users_version');
                if (users.length === 0 || storedVersion !== SEED_VERSION) {
                    const seedUsers = [
                        { id: 'USER-1', name: 'Emmanuel Llaguno', email: 'emmanuel.llaguno@depdev.gov.ph', role: 'staff', division: 'PDIPBD', agency: 'DEPDev 5', joined: '2024-01-15T10:00:00Z' },
                        { id: 'USER-2', name: 'Technical Staff 1', email: 'staff1@depdev.gov.ph', role: 'staff', division: 'PMED', agency: 'DEPDev 5', joined: '2024-02-10T14:30:00Z' },
                        { id: 'USER-3', name: 'Chief PMED', email: 'pmed.chief@depdev.gov.ph', role: 'division-head', division: 'PMED', agency: 'DEPDev 5', joined: '2023-12-05T08:00:00Z' },
                        { id: 'USER-4', name: 'System Admin', email: 'admin@depdev.gov.ph', role: 'admin', agency: 'DEPDev 5', joined: '2023-01-01T08:00:00Z' },
                        { id: 'USER-5', name: 'Agency Representative', email: 'rep@dpwh.gov.ph', role: 'agency', agency: 'DPWH Region V', joined: '2024-03-15T09:00:00Z' }
                    ];
                    const preserved = (storedVersion !== SEED_VERSION && users.length > 0)
                        ? users.filter(u => !['USER-1','USER-2','USER-3','USER-4','USER-5'].includes(u.id))
                        : [];
                    users = [...seedUsers, ...preserved];
                    await localforage.setItem('system_users', users);
                    await localforage.setItem('system_users_version', SEED_VERSION);
                }

                const found = users.find(u => u.email.toLowerCase() === email.toLowerCase());
                
                if (found) {
                    localStorage.setItem('currentUser', JSON.stringify({
                        email: found.email,
                        name: found.name,
                        agency: found.agency,
                        role: found.role,
                        division: found.division || ''
                    }));

                    if (found.role === 'admin') {
                        window.location.href = '/admin/portal';
                    } else if (found.role === 'division-head') {
                        window.location.href = '/division-head/portal';
                    } else if (found.role === 'staff' && (found.division || '').toUpperCase() === 'PDIPBD') {
                        window.location.href = '/staff/pdipbd-portal';
                    } else if (found.role === 'staff') {
                        window.location.href = '/staff/portal';
                    } else {
                        window.location.href = '/agency/portal';
                    }
                    return;
                }

                // 2. Fallback to hardcoded patterns if user not in localforage (Legacy support)
                const isStaff = email.toLowerCase().includes('staff') || email.toLowerCase().includes('llaguno') || email.toLowerCase().includes('posada');
                const isDivHead = email.toLowerCase().includes('divisionhead') || email.toLowerCase().includes('chief');
                const isAdmin = email.toLowerCase().includes('admin');
                const agency = (isStaff || isDivHead || isAdmin) ? 'NEDA 5' : (email.toLowerCase().includes('dict') ? 'DICT - Bicol' : 'DPWH Region V');
                
                let role = 'agency';
                let division = '';
                
                if (isAdmin) role = 'admin';
                else if (isDivHead) {
                    role = 'division-head';
                    division = email.toLowerCase().includes('pdipbd') ? 'PDIPBD' : 'PMED';
                }
                else if (isStaff) {
                    role = 'staff';
                    division = email.toLowerCase().includes('pdipbd') ? 'PDIPBD' : 'PMED';
                }
                
                const finalUser = {
                    email: email,
                    name: email.split('@')[0],
                    agency: agency,
                    role: role,
                    division: division
                };

                localStorage.setItem('currentUser', JSON.stringify(finalUser));

                // Always prioritize role-based redirection
                    if (role === 'admin') {
                        window.location.href = '/admin/portal';
                    } else if (role === 'division-head') {
                        window.location.href = '/division-head/portal';
                    } else if (role === 'staff' && division.toUpperCase() === 'PDIPBD') {
                        window.location.href = '/staff/pdipbd-portal';
                    } else if (role === 'staff') {
                        window.location.href = '/staff/portal';
                    } else {
                        window.location.href = '/agency/portal';
                    }
            });
        });
    }

    // Forgot Password Flow Handlers
    document.addEventListener('click', (e) => {
        // RDIP Tab Switching
        const tabBtn = e.target.closest('.rdip-tab-btn');
        if (tabBtn) {
            const tabId = tabBtn.getAttribute('data-rdip-tab');
            const container = tabBtn.closest('.rdip-island');
            
            // Toggle Buttons
            container.querySelectorAll('.rdip-tab-btn').forEach(btn => btn.classList.remove('active'));
            tabBtn.classList.add('active');
            
            // Toggle Panels
            container.querySelectorAll('.rdip-tab-panel').forEach(panel => panel.classList.remove('active'));
            const targetPanel = container.querySelector(`#rdip-panel-${tabId}`);
            if (targetPanel) targetPanel.classList.add('active');
            
            // Re-init icons
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        // Go to Forgot Password from Login
        if (e.target.closest('.forgot-link')) {
            e.preventDefault();
            showForgotPasswordForm();
        }
        // Go back to Login from Forgot Password
        if (e.target.closest('.back-to-login') || e.target.closest('#btn-back-login')) {
            e.preventDefault();
            showLoginForm();
        }
    });

    // Handle Forgot Password Form Submission
    document.addEventListener('submit', (e) => {
        if (e.target.id === 'forgot-password-form') {
            e.preventDefault();
            const email = document.getElementById('reset-email').value.trim();
            if (!email) {
                alert('Please enter your email address.');
                return;
            }

            // Simulate sending reset link
            const form = document.getElementById('forgot-password-form');
            const success = document.getElementById('reset-success-message');
            const displayEmail = document.getElementById('sent-email-display');

            if (form && success) {
                if (displayEmail) displayEmail.textContent = email;
                form.style.display = 'none';
                success.style.display = 'block';
                if (window.lucide) window.lucide.createIcons();
            }
        }

        if (e.target.id === 'reset-password-new-form') {
            e.preventDefault();
            const pass = document.getElementById('reset-new-password').value;
            const confirm = document.getElementById('reset-confirm-password').value;

            if (pass.length < 8) {
                alert('Password must be at least 8 characters long.');
                return;
            }

            if (pass !== confirm) {
                alert('Passwords do not match.');
                return;
            }

            // Simulate update
            const form = document.getElementById('reset-password-new-form');
            const success = document.getElementById('password-changed-success');

            if (form && success) {
                form.style.display = 'none';
                success.style.display = 'block';
                if (window.lucide) window.lucide.createIcons();
            }
        }
    });

    // Public Table Search Feature
    document.addEventListener('input', (e) => {
        if (e.target.id === 'public-search') {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('.project-table tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
    });

    // Sidebar toggle
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-open');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            document.body.classList.remove('sidebar-open');
        });
    }

    // Close sidebar on outside click
    document.addEventListener('mousedown', (e) => {
        const sidebar = document.getElementById('sidebar-container');
        if (document.body.classList.contains('sidebar-open') && 
            sidebar && 
            !sidebar.contains(e.target) && 
            !menuToggle.contains(e.target)) {
            document.body.classList.remove('sidebar-open');
        }
    });

    // Search overlay toggle
    if (searchToggle) {
        searchToggle.addEventListener('click', () => {
            const searchOverlay = document.getElementById('search-overlay');
            if (searchOverlay) {
                searchOverlay.classList.add('active');
                if (searchInput) searchInput.focus();
            }
        });
    }

    document.addEventListener('click', (e) => {
        if (e.target.closest('#btn-close-search')) {
            const searchOverlay = document.getElementById('search-overlay');
            if (searchOverlay) searchOverlay.classList.remove('active');
        }
    });

    // Handle scroll to top
    const scrollTopBtn = document.getElementById('btn-scroll-top');
    if (scrollTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.add('visible');
            } else {
                scrollTopBtn.classList.remove('visible');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    if (landingFooter && topBar) {
        const footerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    topBar.classList.add('hidden');
                }
            });
        }, { threshold: 0.1 });

        footerObserver.observe(landingFooter);
    }

    // Header Scroll behavior: Clean Exit on Down, Peek on Up
    let lastScrollTop = 0;
    let scrollThreshold = 10;

    window.addEventListener('scroll', () => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollDiff = scrollTop - lastScrollTop;

        // Background / Shadow transition
        if (scrollTop > 50) {
            topBar.classList.add('scrolled');
        } else {
            topBar.classList.remove('scrolled');
        }

        // Visibility Toggles
        if (scrollTop <= 100) {
            topBar.classList.remove('hidden');
        } else if (scrollDiff > scrollThreshold) {
            // Scrolling Down
            topBar.classList.add('hidden');
        } else if (scrollDiff < -scrollThreshold) {
            // Scrolling Up (Reveal Header)
            const footer = document.getElementById('footer-container-wrapper');
            const footerRect = footer ? footer.getBoundingClientRect() : null;
            // Check if footer exists, is actually displayed (offsetHeight > 0), and is in viewport
            const isFooterVisible = footer && footer.offsetHeight > 0 && footerRect.top < window.innerHeight;

            if (!isFooterVisible) {
                topBar.classList.remove('hidden');
            }
        }

        lastScrollTop = scrollTop;
    }, { passive: true });
}

// Global Logic (Moved from nested initializeEvents for standalone support)

// ━━━ RDP FLIPBOOK LOGIC (DearFlip) ━━━
function initFlipbook() {
    // v2.0 - Simple HTML-Only Approach
    console.log("RDP Flipbook: Activating auto-scanner...");
    
    if (typeof window.DFLIP === 'undefined') {
        setTimeout(initFlipbook, 500);
        return;
    }

    // Set global configurations so the HTML tag knows where to find workers
    const cdnBase = "https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@1.7.3/dflip/";
    window.dFlipLocation = cdnBase;
    
    // Ensure WebGL is the default for all detected books
    DFLIP.defaults.webgl = true;
    DFLIP.defaults.libPath = cdnBase + "js/libs/";
    DFLIP.defaults.scrollWheel = false;
    DFLIP.defaults.soundEnable = false;

    // Tell DearFlip to scan the DOM for new "_df_book" elements (crucial for dynamic loading)
    if (DFLIP.parseBooks) {
        console.log("RDP Flipbook: Parsing HTML books...");
        DFLIP.parseBooks();
    }
}

// Metric Toggle Listener for Dashboard (Moved to top-level)
document.addEventListener('change', (e) => {
    if (e.target && e.target.id === 'dashboard-metric-toggle') {
        const isCost = e.target.checked;

        const boxCount = document.getElementById('metric-box-count');
        const boxCost = document.getElementById('metric-box-cost');

        if (boxCount && boxCost) {
            if (isCost) {
                boxCount.style.opacity = '0.5';
                boxCost.style.opacity = '1';
            } else {
                boxCount.style.opacity = '1';
                boxCost.style.opacity = '0.5';
            }
        }
        // Update chart titles based on toggle
        const fundSourceTitle = document.getElementById('fund-source-chart-title');
        const agencyTitle = document.getElementById('agency-chart-title');
        if (fundSourceTitle) {
            fundSourceTitle.textContent = isCost
                ? 'Indicative Cost by Fund Source'
                : 'Number of Projects by Fund Source';
        }
        if (agencyTitle) {
            agencyTitle.textContent = isCost
                ? 'Indicative Cost by Agency'
                : 'Number of Projects by Agency';
        }
        initializeCharts(isCost);

        // Sync the map mode with the global toggle
        if (window.MapToggle) {
            window.MapToggle.setMode(isCost ? 'cost' : 'projects');
        }
    }
});

// Moved Helper Functions to Top-Level Scope
// Chart instances
let fundSourceChart = null;
let agencyChart = null;
let fundingSourcePieChart = null;
let statusDistChart = null;
let sectorChart = null;
let provinceChart = null;
let spatialCoverageChart = null;
let yearChart = null;
let rdpChapterChart = null;

function initializeCharts(isCost = false) {
    const fundSourceCtx = document.getElementById('fundSourceChart');
    const agencyCtx = document.getElementById('agencyChart');
    const fundingPieCtx = document.getElementById('fundingSourcePieChart');
    const statusDistCtx = document.getElementById('statusDistributionChart');

    if (fundSourceCtx) {
        if (fundSourceChart) fundSourceChart.destroy();
        fundSourceChart = new Chart(fundSourceCtx, {
            type: 'bar',
            data: {
                labels: ['GAA', 'ODA', 'PPP', 'COB'],
                datasets: [{
                    data: isCost ? [11.77, 0, 0, 0] : [51, 0, 0, 0],
                    backgroundColor: ['#1E3A8A', '#3B82F6', '#10B981', '#F59E0B'],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end', align: 'start', offset: 4, color: '#ffffff',
                        font: { weight: 'bold', size: 12 },
                        formatter: (value) => value > 0 ? value : ''
                    }
                },
                scales: {
                    x: { beginAtZero: true, grid: { display: false }, ticks: { color: '#475569' } },
                    y: { grid: { display: false }, ticks: { color: '#475569' } }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    if (agencyCtx) {
        if (agencyChart) agencyChart.destroy();
        agencyChart = new Chart(agencyCtx, {
            type: 'bar',
            data: {
                labels: ['BJMP', 'PNP', 'BFP', 'DILG', 'DEPDev'],
                datasets: [{
                    data: isCost ? [4.5, 3.2, 2.1, 1.2, 0.77] : [16, 15, 13, 6, 3],
                    backgroundColor: ['#EF4444', '#3B82F6', '#F97316', '#06B6D4', '#84CC16'],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end', align: 'start', offset: 4, color: '#ffffff',
                        font: { weight: 'bold', size: 12 },
                        formatter: (value) => value > 0 ? value : ''
                    }
                },
                scales: {
                    x: { beginAtZero: true, grid: { display: false }, ticks: { color: '#475569' } },
                    y: { grid: { display: false }, ticks: { color: '#475569' } }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    if (fundingPieCtx && !fundingSourcePieChart) {
        fundingSourcePieChart = new Chart(fundingPieCtx, {
            type: 'pie',
            data: {
                labels: ['Tier 1', 'Tier 2', 'Multi-Year Allocation'],
                datasets: [{
                    data: [12.5, 25, 62.5],
                    backgroundColor: ['#1E4E79', '#2E86C1', '#70E1E1'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, font: { size: 12, family: "'Inter', sans-serif", weight: 'bold' }, color: '#1a1a1a', usePointStyle: true, pointStyle: 'circle' }
                    },
                    datalabels: {
                        color: (context) => {
                            const label = context.chart.data.labels[context.dataIndex];
                            return (label === 'Tier 1' || label === 'Tier 2') ? '#fff' : '#000';
                        },
                        font: { weight: 'bold', size: 12 },
                        formatter: (value) => value + '%'
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    if (statusDistCtx && !statusDistChart) {
        statusDistChart = new Chart(statusDistCtx, {
            type: 'bar',
            data: {
                labels: ['Ongoing', 'Proposed', 'Terminated', 'Suspended', 'Dropped', 'Completed'],
                datasets: [{
                    data: [37.80, 3.83, 21.05, 14.83, 19.14, 3.35],
                    backgroundColor: ['#1e4a7a', '#e67e22', '#fa9d62', '#337ab7', '#fbc79a', '#c9ad3c'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: {
                        anchor: 'end', align: 'top', color: '#1a1a1a',
                        font: { weight: 'bold', size: 10 },
                        formatter: (value) => value + '%'
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 10 }, callback: (value) => value + '%' } }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    const sectorCtx = document.getElementById('sectorChart');
    const provinceCtx = document.getElementById('provinceChart');
    const spatialCtx = document.getElementById('spatialCoverageChart');
    const yearCtx = document.getElementById('yearChart');
    const rdpCtx = document.getElementById('rdpChapterChart');

    if (sectorCtx) {
        if (sectorChart) sectorChart.destroy();
        sectorChart = new Chart(sectorCtx, {
            type: 'bar',
            data: {
                labels: ['Infrastructure', 'Social', 'Economic', 'Dev\'t Ad'],
                datasets: [{
                    data: isCost ? [8.55, 1.25, 0.45, 1.52] : [25, 12, 10, 4],
                    backgroundColor: ['#1E3A8A', '#3B82F6', '#10B981', '#F59E0B'],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: { anchor: 'end', align: 'top', color: '#1e293b', font: { weight: 'bold' }, formatter: (value) => value }
                },
                scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
            },
            plugins: [ChartDataLabels]
        });
    }

    if (provinceCtx) {
        if (provinceChart) provinceChart.destroy();
        const provinceLabels = ['Albay', 'Cam Sur', 'Sorsogon', 'Masbate', 'Cam Norte', 'Catanduanes'];
        const provinceDataValues = provinceLabels.map(label => isCost ? MapConfig.provinceData[label].cost : MapConfig.provinceData[label].projects);

        provinceChart = new Chart(provinceCtx, {
            type: 'bar',
            data: {
                labels: provinceLabels,
                datasets: [{ data: provinceDataValues, backgroundColor: '#1E3A8A', borderRadius: 6 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: { anchor: 'end', align: 'top', color: '#1e293b', font: { weight: 'bold' }, formatter: (value) => value }
                },
                scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
            },
            plugins: [ChartDataLabels]
        });
    }

    if (spatialCtx) {
        if (spatialCoverageChart) spatialCoverageChart.destroy();
        spatialCoverageChart = new Chart(spatialCtx, {
            type: 'doughnut',
            data: {
                labels: ['Nationwide', 'Inter-Regional', 'Regionwide', 'Inter-Province', 'Location-Specific'],
                datasets: [{
                    data: isCost ? [3.2, 2.1, 8.5, 4.2, 12.27] : [10, 8, 35, 15, 29],
                    backgroundColor: ['#1E3A8A', '#3B82F6', '#6366F1', '#8B5CF6', '#10B981']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, font: { size: 10, weight: 'bold' }, color: '#1e293b', usePointStyle: true, pointStyle: 'rect' } },
                    datalabels: { color: '#fff', font: { weight: 'bold', size: 11 }, formatter: (value) => value }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    if (yearCtx) {
        if (yearChart) yearChart.destroy();
        yearChart = new Chart(yearCtx, {
            type: 'line',
            data: {
                labels: ['2023', '2024', '2025', '2026', '2027', '2028'],
                datasets: [{
                    data: isCost ? [10.2, 12.5, 14.8, 11.2, 9.5, 6.9] : [5, 12, 18, 14, 7, 4],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: { anchor: 'end', align: 'top', color: '#1e293b', font: { weight: 'bold' }, formatter: (value) => value }
                },
                scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
            },
            plugins: [ChartDataLabels]
        });
    }

    if (rdpCtx) {
        if (rdpChapterChart) rdpChapterChart.destroy();
        rdpChapterChart = new Chart(rdpCtx, {
            type: 'bar',
            data: {
                labels: ['Chapter 1', 'Chapter 2', 'Chapter 3', 'Chapter 4', 'Chapter 5', 'Chapter 6', 'Chapter 7', 'Chapter 8', 'Chapter 9', 'Chapter 10', 'Chapter 11', 'Chapter 12'],
                datasets: [{
                    data: isCost ? [0.1, 0.45, 1.2, 0.8, 1.5, 0.7, 2.1, 1.4, 1.2, 0.9, 0.8, 0.52] : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                    backgroundColor: '#3B82F6',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    datalabels: { anchor: 'end', align: 'top', color: '#1e293b', font: { weight: 'bold' }, formatter: (value) => value }
                },
                scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }
            },
            plugins: [ChartDataLabels]
        });
    }
}

function setupDashboardFilters() {
    const filters = [
        'filter-location', 'filter-province', 'filter-district',
        'filter-city', 'filter-agency', 'filter-status'
    ];

    const searchInput = document.getElementById('public-search');
    const locationSelect = document.getElementById('filter-location');
    const groupsToToggle = {
        'nationwide': [],
        'inter-regional': [],
        'regionwide': [],
        'inter-province': [],
        'specific': ['group-province', 'group-district', 'group-city']
    };

    if (locationSelect) {
        locationSelect.addEventListener('change', (e) => {
            const val = e.target.value;
            Object.keys(groupsToToggle).forEach(key => {
                groupsToToggle[key].forEach(groupId => {
                    const el = document.getElementById(groupId);
                    if (el) el.style.display = (key === val) ? 'block' : 'none';
                });
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.v3-table tbody tr');
            let count = 0;
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                    count++;
                } else {
                    row.style.display = 'none';
                }
            });
            const resultsCount = document.getElementById('results-count');
            if (resultsCount) resultsCount.textContent = count;
        });
    }
}


document.addEventListener('DOMContentLoaded', init);

// Custom Cursor Logic
const initializeCursor = () => {
    const cursorDot = document.querySelector('[data-cursor-dot]');
    const cursorOutline = document.querySelector('[data-cursor-outline]');

    if (!cursorDot || !cursorOutline) return;

    window.addEventListener('mousemove', function (e) {
        const posX = e.clientX;
        const posY = e.clientY;

        cursorDot.style.left = `${posX}px`;
        cursorDot.style.top = `${posY}px`;

        cursorOutline.animate({
            left: `${posX}px`,
            top: `${posY}px`
        }, { duration: 150, fill: "forwards" });
    });
}

initializeCursor();

// Hover effect for interactive elements
document.body.addEventListener('mouseover', (e) => {
    if (e.target.closest('a, button, input, textarea, select, .portal-card')) {
        document.body.classList.add('hovering');
    } else {
        document.body.classList.remove('hovering');
    }
});

// FAQ Logic
window.toggleFaqBox = function (btn) {
    const item = btn.parentElement;
    const answer = item.querySelector('.faq-answer-collapse');
    const isActive = item.classList.contains('active');

    // Close all other items
    document.querySelectorAll('.faq-item-box').forEach(other => {
        if (other !== item) {
            other.classList.remove('active');
            other.querySelector('.faq-answer-collapse').style.maxHeight = null;
        }
    });

    if (!isActive) {
        item.classList.add('active');
        answer.style.maxHeight = answer.scrollHeight + "px";
    } else {
        item.classList.remove('active');
        answer.style.maxHeight = null;
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }
};

// FAQ Category Tab Logic
window.toggleFaqCategory = function (btn, categoryId) {
    // Update pills
    document.querySelectorAll('.cat-pill').forEach(pill => pill.classList.remove('active'));
    btn.classList.add('active');

    // Show/Hide sections
    document.querySelectorAll('.faq-category-section').forEach(section => {
        section.style.display = 'none';
    });
    const target = document.getElementById(categoryId);
    if (target) {
        target.style.display = 'block';
        // Re-open first item in new section
        const firstToggle = target.querySelector('.faq-toggle');
        if (firstToggle) window.toggleFaqBox(firstToggle);
    }

    if (window.lucide) window.lucide.createIcons();
};
