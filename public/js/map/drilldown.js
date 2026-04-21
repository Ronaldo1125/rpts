/**
 * drilldown.js — Handles the transitions between province-level and municipality-level views.
 * Fetches municipality GeoJSON based on the selected province's PSGC code.
 */
window.MapDrilldown = (function () {
    let _map = null;
    let _breadcrumbStack = [];

    function init(leafletMap) {
        _map = leafletMap;
    }

    /**
     * Called when a province polygon is clicked.
     */
    function drillInto(feature, layer) {
        const provinceId = feature.properties.id; // 'Albay', 'Cam Sur', etc.
        const name = feature.properties.name;
        const psgc = MapConfig.provinceCodes[provinceId];

        if (!psgc) {
            console.warn(`[Drilldown] No PSGC code found for province: ${provinceId}`);
            return;
        }

        // Save current view state for breadcrumb
        _breadcrumbStack.push({ name, bounds: layer.getBounds() });
        _renderBreadcrumb();

        // Fetch municipality-level GeoJSON for this province
        const url = `${MapConfig.geojsonUrls.provinceBase}${psgc}.0.01.json`;
        
        _showSpinner();

        fetch(url)
            .then(r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(geojson => {
                _hideSpinner();
                // Switch the map layer to municipality view
                BicolMap.switchLayer(geojson, true);
                // Zoom into the province area
                _map.fitBounds(layer.getBounds(), { padding: [30, 30] });
            })
            .catch(err => {
                _hideSpinner();
                console.error(`[Drilldown] Failed to load muni data for ${name}:`, err.message);
                // Fallback: just zoom in on province
                _map.fitBounds(layer.getBounds(), { padding: [30, 30] });
            });
    }

    /**
     * Called when a breadcrumb item is clicked to go back up.
     */
    function drillUpTo(index) {
        if (index === -1) {
            // Reset to region view (provinces)
            _breadcrumbStack = [];
            _renderBreadcrumb();
            
            if (window.MapSync) window.MapSync.onRegionResetClick();

            _showSpinner();
            fetch(MapConfig.geojsonUrls.region)
                .then(r => r.json())
                .then(raw => {
                    _hideSpinner();
                    // IMPORTANT: We must re-process the raw region features to restore IDs
                    const processed = BicolMap.processRegionFeatures(raw);
                    BicolMap.switchLayer(processed, false);
                })
                .catch(err => {
                    _hideSpinner();
                    console.error('[Drilldown] Failed to reload region data:', err);
                });
        }
    }

    function _renderBreadcrumb() {
        const el = document.getElementById('map-breadcrumb');
        if (!el) return;

        if (_breadcrumbStack.length === 0) {
            el.classList.remove('visible');
            el.innerHTML = '';
            return;
        }

        el.classList.add('visible');

        let html = `<span class="map-breadcrumb-item" onclick="MapDrilldown._drillUpRoot()">Bicol Region</span>`;

        _breadcrumbStack.forEach((item, i) => {
            html += `<span class="map-breadcrumb-sep">›</span>`;
            if (i === _breadcrumbStack.length - 1) {
                html += `<span style="color:#334155; font-weight:700;">${item.name}</span>`;
            } else {
                html += `<span class="map-breadcrumb-item" onclick="MapDrilldown.drillUpTo(${i})">${item.name}</span>`;
            }
        });

        el.innerHTML = html;
    }

    function _drillUpRoot() { drillUpTo(-1); }

    function _showSpinner() {
        const container = document.getElementById('bicol-map');
        if (!container) return;
        const spinner = document.createElement('div');
        spinner.id = 'drilldown-loading';
        spinner.style.cssText = `position:absolute; inset:0; z-index:1000; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.4); pointer-events:none;`;
        spinner.innerHTML = `<div style="width:24px;height:24px;border:2px solid #1e3a6e33;border-top-color:#1e3a6e;border-radius:50%;animation:map-spin 0.6s linear infinite;"></div>`;
        container.appendChild(spinner);
    }

    function _hideSpinner() {
        const el = document.getElementById('drilldown-loading');
        if (el) el.remove();
    }

    return { init, drillInto, drillUpTo, _drillUpRoot };
})();
