import '../common/ui-utils.js';
import { initComponentLoader } from '../common/component-loader.js';
import { initNavigation } from '../common/navigation.js';
import { initProjectDetails } from '../common/project-details.js';
import { initAdminActions } from '../admin/admin-actions.js';
import { initFileDrop, createFileDrop } from '../common/file-drop.js';
import { initCppForm } from '../agency/cpp-form.js';
import { initTestAndEvaluationLoader } from '../admin/test-and-evaluation-loader.js';

document.addEventListener('DOMContentLoaded', async () => {
    const config = window.APP_CONFIG || {};
    await initComponentLoader(config);

    if (window.lucide) {
        window.lucide.createIcons();
    }
    initNavigation();
    initProjectDetails();
    initAdminActions();
    initFileDrop();

    // Determine portal type for role-based UI behaviour
    const _portalSidebar = (config.sidebarPath || '');
    const _isPdipbdStaffPortal = _portalSidebar.includes('pdipbd-staff-sidebar');
    const _isAgencyPortal = _portalSidebar.includes('agency-sidebar');

    // Dynamically update Topnav User Display
    const updateTopnavUser = () => {
        try {
            const user = JSON.parse(localStorage.getItem('currentUser') || '{}');

            // Always populate display name / role if available
            if (user && user.name) {
                const displayName = user.name.split(' ')[0] + (user.name.split(' ').length > 1 ? ` ${user.name.split(' ')[1][0]}.` : '');
                const userElements = document.querySelectorAll('.topnav-user-name');
                const roleElements = document.querySelectorAll('.topnav-user-role');
                userElements.forEach(el => el.textContent = displayName);
                roleElements.forEach(el => el.textContent = (user.role || '').toUpperCase().replace('-', ' '));
            }

            // Notification Bell Visibility:
            // — Hidden on Agency portal and PDIPB Staff portal
            // — Shown for Admin, Division Head, and other internal portals
            const bellParent = document.getElementById('notificationBell')?.closest('.dropdown');
            if (bellParent) {
                if (_isAgencyPortal || _isPdipbdStaffPortal || user.role === 'agency') {
                    bellParent.style.display = 'none';
                } else {
                    bellParent.style.display = 'block';
                }
            }
        } catch (e) {
            console.error('Error updating topnav user:', e);
        }
    };

    // We update once topnav is loaded
    setTimeout(() => updateTopnavUser(), 300);

    // Patch switchPage to trigger loaders on navigation
    const _origSwitch = window.switchPage;
    window.switchPage = (pageId) => {
        if (typeof _origSwitch === 'function') _origSwitch(pageId);

        if (pageId === 'cpp-form') {
            setTimeout(() => initCppForm(), 50);
        }
        if (pageId === 'submissions' || pageId === 'manage-submissions') {
            import('../agency/submissions-loader.js').then(m => {
                setTimeout(() => m.initSubmissionsLoader(), 50);
            });
        }
        if (pageId === 'cpp-view') {
            import('../agency/cpp-view.js').then(m => {
                setTimeout(() => m.initCppView(), 50);
            });
        }
        if (pageId === 'reports') {
            import('../admin/reports-loader.js').then(m => {
                setTimeout(() => m.initReportsFilter(), 50);
            });
        }

        if (pageId === 'test-and-evaluation' || pageId === 'test-and-evaluation-form') {
            if (_isPdipbdStaffPortal) {
                // PDIPB staff sees submitted CPP forms for completeness review
                import('../staff/pdipb-cte-loader.js').then(m => {
                    setTimeout(() => m.initPdipbCteLoader(), 60);
                });
            } else {
                setTimeout(() => initTestAndEvaluationLoader(), 60);
            }
        }

        // Initialize referrals functionality (Role-based)
        if (pageId === 'admin-referrals') {
            import('../admin/admin-referrals-loader.js').then(m => {
                setTimeout(() => m.initAdminReferralsLoader(), 50);
            });
        }
        if (pageId === 'division-head-referrals') {
            import('../division-head/division-head-referrals-loader.js').then(m => {
                setTimeout(() => m.initDivisionHeadReferralsLoader(), 50);
            });
        }
        if (pageId === 'staff-referrals') {
            import('../staff/staff-referrals-loader.js').then(m => {
                setTimeout(() => m.initStaffReferralsLoader(), 50);
            });
        }

        // Generic fallback for old referral links
        if (pageId === 'referrals') {
            import('../admin/referrals-loader.js').then(m => {
                setTimeout(() => m.initReferralsLoader(), 50);
            });
        }

        // Initialize project assessment report (PAR) functionality
        if (pageId === 'project-assessment-report' || 
            pageId === 'project-assessment-report-form' ||
            pageId === 'admin-project-assessment' ||
            pageId === 'division-head-project-assessment' ||
            pageId === 'staff-project-assessment' ||
            pageId === 'pdipbd-staff-project-assessment') {
            import('../admin/project-assessment-loader.js').then(m => {
                setTimeout(() => m.initProjectAssessmentLoader(), 50);
            });
        }

        // Initialize comments and recommendations functionality
        if (pageId === 'comments-recommendations' || 
            pageId === 'comments-recommendations-form' ||
            pageId === 'admin-comments-recommendations' ||
            pageId === 'staff-comments-recommendations' ||
            pageId === 'division-head-comments-recommendations') {
            import('../admin/comments-recommendations-loader.js').then(m => {
                setTimeout(() => m.initCommentsRecommendationsLoader(), 50);
            });
        }

        // Initialize users management functionality
        if (pageId === 'users') {
            import('../admin/users-loader.js').then(m => {
                setTimeout(() => m.initUsersLoader(), 50);
            });
        }

        // Handle role-based restrictions for Projects pages
        setTimeout(() => {
            try {
                const currentUser = JSON.parse(localStorage.getItem('currentUser') || '{}');
                if (currentUser.role === 'agency' && (pageId === 'projects' || pageId === 'component-project')) {
                    document.querySelectorAll('.create-project-btn').forEach(btn => btn.style.display = 'none');
                    document.querySelectorAll('.agency-col').forEach(col => col.style.display = 'none');
                    document.querySelectorAll('.project-action-edit, .project-action-delete').forEach(el => {
                        if (el.parentElement && el.parentElement.tagName === 'LI') {
                            el.parentElement.style.display = 'none';
                        } else {
                            el.style.display = 'none';
                        }
                    });

                    // Filter project rows to only display those belonging to the logged-in agency
                    document.querySelectorAll('.clickable-row').forEach(row => {
                        const rowAgency = row.getAttribute('data-agency');
                        if (rowAgency && rowAgency !== currentUser.agency) {
                            row.style.display = 'none';
                        }
                    });
                }
            } catch (e) {
                console.error('Error applying role restrictions:', e);
            }
        }, 150);

        // Initialize dashboards with live data
        if (pageId === 'dashboards') {
            // Side-effect import: registers window.initReferredStage
            import('../admin/admin-referred-stage-loader.js');
            import('../admin/admin-dashboard-loader.js').then(m => {
                setTimeout(() => m.initAdminDashboard(), 80);
            });
        }
        if (pageId === 'staff-dashboard') {
            import('../staff/staff-loader.js').then(m => {
                setTimeout(() => m.initStaffDashboard(), 80);
            });
        }
        if (pageId === 'division-head-dashboard') {
            import('../division-head/division-head-loader.js').then(m => {
                setTimeout(() => m.initDivisionHeadDashboard(), 80);
            });
        }
        
        // Re-initialize icons for the new page content
        setTimeout(() => {
            if (window.lucide) window.lucide.createIcons();
        }, 100);
    };

    // initialize component project drop zone if present
    try { createFileDrop('componentDropZone', 'componentFileInput', 'componentFileList'); } catch (e) { }

    if (config.defaultPage && window.switchPage) {
        window.switchPage(config.defaultPage);
    }
});
