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

    window.BicolMap = { init, invalidate, switchLayer, processRegionFeatures: _processRegionFeatures };

    // ── Init ─────────────────────────────────────────────────────
    function init() {
        if (_initialized) { invalidate(); return; }
        
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

        MapSync.highlightProvince(id);
        MapSync.onMapProvinceClick(id);
        MapDrilldown.drillInto(feature, e.target);
    }

    function _onEachMuniFeature(feature, layer) {
        const name = feature.properties.adm3_en || feature.properties.name || 'Unknown';
        const data = _getMuniData(name);

        // POPUP (Click) restored so details "load" on the map as requested
        const popupContent = `
            <div class="map-province-tooltip" style="border:none; box-shadow:none; padding: 0.2rem;">
                <strong>${name}</strong>
                <div style="color:#64748b; font-size:0.65rem; text-transform:uppercase; margin-bottom:0.4rem; letter-spacing:0.04em; font-weight:700;">Statistics</div>
                <div style="color:#1e293b; font-size:0.75rem; display:grid; grid-template-columns: 1fr auto; gap:0.5rem; border-bottom:1px solid #f1f5f9; padding-bottom:0.4rem; margin-bottom:0.4rem;">
                    <span>Projects:</span> <b style="color:#1e3a6e">${data.projects}</b>
                    <span>Total Cost:</span> <b style="color:#1e3a6e">₱${data.cost.toFixed(2)}M</b>
                </div>
            </div>
        `;
        layer.bindPopup(popupContent, { maxWidth: 220 });

        // Tooltips (Black squares on hover) removed as per previous instruction
        // layer.bindTooltip(tooltipContent, { sticky: true });

        layer.on({
            mouseover: (e) => { e.target.setStyle(MapConfig.hoverStyle); e.target.bringToFront(); },
            mouseout:  (e) => { 
                const color = MapChoropleth.getColor(name, MapChoropleth.getMode(), true);
                e.target.setStyle({ ...MapConfig.defaultStyle, fillColor: color }); 
            },
            click: (e) => {
                L.DomEvent.stopPropagation(e);
                if (window.MapSync && window.MapSync.onMapMuniClick) {
                    window.MapSync.onMapMuniClick(name, data);
                }
            }
        });
    }

    /**
     * Internal helper to generate consistent mock data for municipalities
     */
    function _getMuniData(name) {
        let hash = 0;
        for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
        hash = Math.abs(hash);
        
        // Return 0 projects occasionally for realism
        const hasProjects = (hash % 10) > 1; 
        return {
            projects: hasProjects ? (hash % 6) + 1 : 0,
            cost: hasProjects ? ((hash % 30) + 10) / 10 : 0
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
