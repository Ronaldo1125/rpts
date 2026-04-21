/**
 * legend.js — Builds and updates the Leaflet legend control.
 * Ensures only one legend instance exists at a time.
 */
window.MapLegend = (function () {
    let _legend = null;
    let _map = null;

    function _getLabel(mode, threshold) {
        if (mode === 'projects') {
            if (threshold.max === Infinity) return `${threshold.min}+ projects`;
            if (threshold.min === 0) return `0–${threshold.max} projects`;
            return `${threshold.min}–${threshold.max} projects`;
        } else {
            // Cost is in Millions for the legend
            if (threshold.max === Infinity) return `₱${threshold.min}M+`;
            if (threshold.min === 0) return `₱0–${threshold.max}M`;
            return `₱${threshold.min}–${threshold.max}M`;
        }
    }

    function init(leafletMap) {
        // If a legend already exists, don't create a new one, just update it
        if (_legend && _map === leafletMap) {
            update(MapChoropleth.getMode());
            return;
        }

        // If switching maps or starting fresh
        if (_legend && _map) {
            _map.removeControl(_legend);
        }

        _map = leafletMap;
        _legend = L.control({ position: 'bottomright' });

        _legend.onAdd = function () {
            const div = L.DomUtil.create('div', 'map-legend');
            div.id = 'map-legend-control';
            return div;
        };

        _legend.addTo(_map);
        update(MapChoropleth.getMode());
    }

    function update(mode) {
        const el = document.getElementById('map-legend-control');
        if (!el) return;

        const thresholds = MapConfig.colorScale[mode];
        let html = `<div class="map-legend-title" style="margin-bottom:8px; font-weight:700; font-size:0.75rem; color:#1e293b; text-transform:uppercase; letter-spacing:0.025em;">${mode === 'projects' ? 'No. of Projects' : 'Indicative Cost'}</div>`;

        thresholds.forEach(t => {
            html += `
                <div class="map-legend-item" style="display:flex; align-items:center; gap:8px; margin-bottom:4px; font-size:0.7rem; color:#475569;">
                    <span class="map-legend-swatch" style="width:12px; height:12px; border-radius:2px; background:${t.color}"></span>
                    <span>${_getLabel(mode, t)}</span>
                </div>`;
        });

        el.innerHTML = html;
    }

    return { init, update };
})();
