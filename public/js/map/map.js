/**
 * map.js — Main entry point. Handles accurate Bicol GeoJSON fetching and robust matching.
 */
(function () {
    'use strict';

    // Robust mapping for official GeoJSON names to landing.js specific chart labels
    const OFFICIAL_TO_ID_MAP = {
        'ALBAY':            'Albay',
        'CAMARINES NORTE':  'Cam Norte',
        'CAMARINES SUR':    'Cam Sur',
        'CATANDUANES':      'Catanduanes',
        'MASBATE':          'Masbate',
        'SORSOGON':         'Sorsogon'
    };

    let _map     = null;
    let _geoLayer = null;
    let _initialized = false;

    window.BicolMap = { 
        init, 
        invalidate, 
        switchLayer, 
        processRegionFeatures: _processRegionFeatures,
        recalculateScales: _recalculateScales,
        updateStats
    };

    // ── Init ─────────────────────────────────────────────────────
    function updateStats() {
        if (window.dashboardStats && window.dashboardStats.province) {
            Object.keys(window.dashboardStats.province).forEach(key => {
                if (MapConfig.provinceData[key]) {
                    MapConfig.provinceData[key].projects = window.dashboardStats.province[key].count;
                    MapConfig.provinceData[key].cost = window.dashboardStats.province[key].cost;
                }
            });
            _recalculateScales();
            if (window.MapChoropleth && _geoLayer) {
                MapChoropleth.refreshLayer(_geoLayer);
                if (window.MapLegend) window.MapLegend.update(MapChoropleth.getMode());
            }
        }
    }

    function init() {
        if (_initialized) { invalidate(); return; }

        // Sync Province data with real stats from backend
        updateStats();
        
        const container = document.getElementById('bicol-map');
        if (!container) return;
        
        _initialized = true;

        if (typeof L === 'undefined') { 
            _initialized = false;
            setTimeout(init, 300); 
            return; 
        }

        _map = L.map('bicol-map', {
            center: MapConfig.center,
            zoom:    MapConfig.zoom,
            minZoom: MapConfig.minZoom,
            maxZoom: MapConfig.maxZoom,
            zoomControl:       true,
            attributionControl: false
        });

        // Use Voyager tiles for better label visibility
        L.tileLayer(
            'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
            { subdomains: 'abcd', maxZoom: 19 }
        ).addTo(_map);

        _showLoading(container);

        // Fetch Region 5 GeoJSON
        fetch(MapConfig.geojsonUrls.region)
            .then(r => r.json())
            .then(raw => _processRegionFeatures(raw))
            .then(geojson => {
                _hideLoading(container);
                _buildLayer(geojson);
            })
            .catch(err => {
                _hideLoading(container);
                console.error('[BicolMap] Remote fetch failed and no locally cached data available.', err.message);
            });

        _map.on('click', (e) => {
            if (e.originalEvent.target.id === 'bicol-map') {
                MapSync.onMapClearClick();
            }
        });
    }

    // ── Data Processing ──────────────────────────────────────────
    function _processRegionFeatures(raw) {
        if (!raw || !raw.features) return raw;

        const features = raw.features.filter(f => {
            const name = (f.properties.adm2_en || '').toUpperCase().trim();
            return OFFICIAL_TO_ID_MAP.hasOwnProperty(name);
        }).map(f => {
            const nameKey = (f.properties.adm2_en || '').toUpperCase().trim();
            const internalId = OFFICIAL_TO_ID_MAP[nameKey];
            
            return {
                ...f,
                properties: {
                    ...f.properties,
                    id: internalId, // Aligns with MapConfig.provinceData keys
                    name: MapConfig.provinceData[internalId]?.label || f.properties.adm2_en
                }
            };
        });

        if (features.length === 0) return raw; // Fallback to raw if filtering fails
        return { type: 'FeatureCollection', features };
    }

    /**
     * Dynamically adjust MapConfig thresholds based on real data.
     * If names are provided, it scales specifically for those features (Drilldown context).
     */
    function _recalculateScales(names = null) {
        const stats = window.dashboardStats;
        if (!stats) return;

        let maxP, maxC;

        if (names && names.length > 0) {
            // Context: DRILLDOWN (Scale based on these specific municipalities)
            const mData = names.map(n => stats.municipality[n] || { count: 0, cost: 0 });
            maxP = Math.max(...mData.map(m => m.count), 5);
            maxC = Math.max(...mData.map(m => m.cost), 1);
        } else {
            // Context: REGION (Scale based on provinces)
            const pCounts = Object.values(stats.province || {}).map(p => p.count);
            maxP = Math.max(...pCounts, 10);
            const pCosts = Object.values(stats.province || {}).map(p => p.cost);
            maxC = Math.max(...pCosts, 5);
        }

        // 1. Project Count Ranges
        const stepP = Math.ceil(maxP / 5);
        MapConfig.colorScale.projects = [
            { min: 0,             max: stepP,         color: '#dbeafe' },
            { min: stepP + 1,      max: stepP * 2,     color: '#93c5fd' },
            { min: stepP * 2 + 1,  max: stepP * 3,     color: '#3b82f6' },
            { min: stepP * 3 + 1,  max: stepP * 4,     color: '#1d4ed8' },
            { min: stepP * 4 + 1,  max: Infinity,      color: '#1e3a6e' }
        ];

        // 2. Cost Ranges
        const stepC = (maxC / 5).toFixed(2);
        const fStepC = parseFloat(stepC) || 1;
        MapConfig.colorScale.cost = [
            { min: 0,             max: fStepC,         color: '#dbeafe' },
            { min: fStepC + 0.01,  max: fStepC * 2,     color: '#93c5fd' },
            { min: fStepC * 2 + 0.01, max: fStepC * 3,  color: '#3b82f6' },
            { min: fStepC * 3 + 0.01, max: fStepC * 4,  color: '#1d4ed8' },
            { min: fStepC * 4 + 0.01, max: Infinity,   color: '#1e3a6e' }
        ];

        // Refresh legend UI if it exists
        if (window.MapLegend) window.MapLegend.update(MapChoropleth.getMode());
    }

    // ── Build Leaflet layer ───────────────────────────────────────
    function _buildLayer(geojson, isMunicipality = false) {
        if (_geoLayer) _map.removeLayer(_geoLayer);

        _geoLayer = L.geoJSON(geojson, {
            style: MapChoropleth.styleFeature,
            onEachFeature: isMunicipality ? _onEachMuniFeature : _onEachProvinceFeature
        }).addTo(_map);

        if (!isMunicipality) {
            _map.fitBounds(_geoLayer.getBounds(), { padding: [20, 20] });
        }

        // Initialize sub-modules
        MapLegend.init(_map); 
        MapToggle.init(_geoLayer);
        MapDrilldown.init(_map);
        MapSync.init(_geoLayer);

        // Sync mode with the global toggle immediately
        const globalToggle = document.getElementById('dashboard-metric-toggle');
        if (globalToggle) {
            MapToggle.setMode(globalToggle.checked ? 'cost' : 'projects');
        }
    }

    // ── Interaction Handlers ──────────────────────────────────────
    function _onEachProvinceFeature(feature, layer) {
        const props = feature.properties;
        const data  = MapConfig.provinceData[props.id] || {};

        // Tooltips removed as per request to declutter highlighted areas
        // layer.bindTooltip(_buildTooltip(props, data), { ... });

        layer.on({
            mouseover: _onHover,
            mouseout:  _onHoverOut,
            click:     _onProvinceClick
        });
    }

    function _onProvinceClick(e) {
        L.DomEvent.stopPropagation(e);
        const { feature } = e.target;
        const id = feature.properties.id;
        
        if (!id) {
            console.error('[BicolMap] Clicked feature has no internal ID:', feature.properties);
            return;
        }

        // Only handle internal map visual state
        MapSync.highlightProvince(id);
        MapDrilldown.drillInto(feature, e.target);
    }

    function _onEachMuniFeature(feature, layer) {
        const rawName = feature.properties.adm3_en || feature.properties.name || 'Unknown';
        // Aggressive Normalization:
        const nameKey = rawName.toUpperCase().trim()
            .replace(/^CITY\sOF\s/g, '')      // "CITY OF LEGAZPI" -> "LEGAZPI"
            .replace(/^MUNICIPALITY\sOF\s/g, '')
            .replace(/\sCITY$/g, '')          // "LEGAZPI CITY" -> "LEGAZPI"
            .replace(/\sMUNICIPALITY$/g, '');

        const data = _getMuniData(nameKey);

        // POPUP (Click) restored so details "load" on the map as requested
        const popupContent = `
            <div class="map-province-tooltip" style="border:none; box-shadow:none; padding: 0.2rem;">
                <strong>${rawName}</strong>
                <div style="color:#64748b; font-size:0.65rem; text-transform:uppercase; margin-bottom:0.4rem; letter-spacing:0.04em; font-weight:700;">Statistics</div>
                <div style="color:#1e293b; font-size:0.75rem; display:grid; grid-template-columns: 1fr auto; gap:0.5rem; border-bottom:1px solid #f1f5f9; padding-bottom:0.4rem; margin-bottom:0.4rem;">
                    <span>Projects:</span> <b style="color:#1e3a6e">${data.projects.toLocaleString()}</b>
                    <span>Total Cost:</span> <b style="color:#1e3a6e">₱${data.cost.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}M</b>
                </div>
            </div>
        `;
        layer.bindPopup(popupContent, { maxWidth: 220 });

        layer.on({
            mouseover: (e) => { e.target.setStyle(MapConfig.hoverStyle); e.target.bringToFront(); },
            mouseout:  (e) => { 
                const color = MapChoropleth.getColor(nameKey, MapChoropleth.getMode(), true);
                e.target.setStyle({ ...MapConfig.defaultStyle, fillColor: color }); 
            },
            click: (e) => {
                L.DomEvent.stopPropagation(e);
                if (window.MapSync && window.MapSync.onMapMuniClick) {
                    window.MapSync.onMapMuniClick(nameKey, data);
                }
            }
        });
    }

    /**
     * Fetches real data for municipalities from the global stats object
     */
    function _getMuniData(nameKey) {
        const stats = (window.dashboardStats && window.dashboardStats.municipality) ? window.dashboardStats.municipality : {};
        const d = stats[nameKey] || { count: 0, cost: 0 };
        
        return {
            projects: d.count,
            cost: d.cost
        };
    }

    function _onHover(e) {
        const l = e.target;
        if (l.options.color === MapConfig.highlightStyle.color) return;
        l.setStyle(MapConfig.hoverStyle);
        l.bringToFront();
    }

    function _onHoverOut(e) {
        const l  = e.target;
        const id = (l.feature.properties && l.feature.properties.id);
        if (!id || !MapConfig.provinceData[id]) {
            l.setStyle({ ...MapConfig.defaultStyle, fillColor: '#cbd5e1' });
            return;
        }
        if (l.options.color !== MapConfig.highlightStyle.color) {
            l.setStyle({
                fillColor: MapChoropleth.getColor(id, MapChoropleth.getMode()),
                ...MapConfig.defaultStyle
            });
        }
    }

    // ── Public API Helpers ───────────────────────────────────────────
    function switchLayer(geojson, isMunicipality = false) {
        _buildLayer(geojson, isMunicipality);
    }

    function invalidate() {
        if (_map) setTimeout(() => _map.invalidateSize(), 150);
    }

    function _showLoading(container) {
        if (document.getElementById('map-loading')) return;
        const el = document.createElement('div');
        el.id = 'map-loading';
        el.style.cssText = `position:absolute; inset:0; z-index:1000; display:flex; align-items:center; justify-content:center; background:rgba(248,250,252,0.85); border-radius:8px; flex-direction:column; gap:0.5rem;`;
        el.innerHTML = `<div style="width:28px;height:28px;border:3px solid #e2e8f0;border-top-color:#1e3a6e;border-radius:50%;animation:map-spin 0.8s linear infinite;"></div>`;
        container.appendChild(el);
    }

    function _hideLoading(container) {
        const el = document.getElementById('map-loading');
        if (el) el.remove();
    }

    function _startObserver() {
        const panel = document.getElementById('dash-panel-details');
        if (panel) {
            new MutationObserver(() => {
                if (panel.classList.contains('active') && !_initialized) init();
            }).observe(panel, { attributes: true, attributeFilter: ['class'] });
            if (panel.classList.contains('active') && !_initialized) init();
        } else {
            setTimeout(_startObserver, 500);
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _startObserver);
    } else {
        _startObserver();
    }

})();
