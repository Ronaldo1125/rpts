

export async function loadComponent(containerId, filePath) {
    // Component loading is now handled via Blade @includes
    return;
}

export async function initComponentLoader(config = {}) {
    // Initial loading is now handled via Blade @includes in the portal views
    
    if (window.lucide) {
        window.lucide.createIcons();
    }
    if (window.TableSort) {
        window.TableSort.init();
    }
}
