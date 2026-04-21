/**
 * map-sync.js — Bidirectional sync between the choropleth map
 * and the Province Distribution bar chart (provinceChart).
 */
window.MapSync = (function () {
    let _geoLayer = null;
    let _highlightedProvinceId = null;

    // GeoJSON id → chart label index mapping  
    // MUST match the order of labels in landing.js: labels: ['Albay', 'Cam Sur', 'Sorsogon', 'Masbate', 'Cam Norte', 'Catanduanes']
    const idToChartIndex = {
        'Albay':           0,
        'Cam Sur':         1,
        'Sorsogon':        2,
        'Masbate':         3,
        'Cam Norte':       4,
        'Catanduanes':     5
    };

    function init(geoLayer) {
        _geoLayer = geoLayer;
        _patchProvinceChart();
    }

    function highlightProvince(provinceId) {
        if (!_geoLayer) return;
        _clearHighlight();
        _highlightedProvinceId = provinceId;

        _geoLayer.eachLayer(layer => {
            if (layer.feature.properties && layer.feature.properties.id === provinceId) {
                layer.setStyle(MapConfig.highlightStyle);
                layer.bringToFront();
            }
        });
    }

    function _clearHighlight() {
        if (!_geoLayer || !_highlightedProvinceId) return;
        _geoLayer.eachLayer(layer => {
            const id = layer.feature.properties && layer.feature.properties.id;
            if (!id || !MapConfig.provinceData[id]) return;
            layer.setStyle({
                fillColor: MapChoropleth.getColor(id, MapChoropleth.getMode()),
                ...MapConfig.defaultStyle
            });
        });
        _highlightedProvinceId = null;
    }

    function onMapProvinceClick(provinceId) {
        const chart = _getProvinceChart();
        if (!chart) return;

        const idx = idToChartIndex[provinceId];
        if (idx === undefined) return;

        const dataset = chart.data.datasets[0];
        const originalColors = dataset._originalColors || dataset.backgroundColor;

        if (!dataset._originalColors) {
            dataset._originalColors = [...(Array.isArray(originalColors)
                ? originalColors
                : Array(chart.data.labels.length).fill(originalColors))];
        }

        dataset.backgroundColor = dataset._originalColors.map((c, i) =>
            i === idx ? '#f59e0b' : c + '99'
        );
        chart.update('none');

        // Functional Filter Update
        _updateDashboardFilters(provinceId);
    }

    function onMapClearClick() {
        const chart = _getProvinceChart();
        if (chart && chart.data.datasets[0]._originalColors) {
            chart.data.datasets[0].backgroundColor = [...chart.data.datasets[0]._originalColors];
            chart.update('none');
        }
        _clearHighlight();
        _updateHeaderMetrics(56, 65.10);
    }

    function onRegionResetClick() {
        onMapClearClick();
    }

    function onMapMuniClick(muniName, data) {
        // Load data into dashboard metrics ONLY (no filter alteration)
        if (data) {
            _updateHeaderMetrics(data.projects, data.cost);
        }
    }

    function _updateHeaderMetrics(count, cost) {
        const countEl = document.querySelector('#metric-box-count .m-val');
        const costEl = document.querySelector('#metric-box-cost .m-val');
        if (countEl) countEl.textContent = count;
        if (costEl) costEl.textContent = cost.toFixed(2);
    }

    function _updateDashboardFilters(provinceId) {
        // Dashboard filters are NO LONGER altered as per user request
        
        // update metrics
        const data = MapConfig.provinceData[provinceId];
        if (data) {
            _updateHeaderMetrics(data.projects, data.cost);
        }
    }

    function _patchProvinceChart() {
        const attempt = () => {
            const chart = _getProvinceChart();
            if (!chart) { setTimeout(attempt, 500); return; }

            const origOnClick = chart.options.onClick;
            chart.options.onClick = function (evt, elements) {
                if (origOnClick) origOnClick.call(this, evt, elements);
                if (!elements || elements.length === 0) {
                    onRegionResetClick(); // Reset when clicking background
                    return;
                }
                const idx = elements[0].index;
                const label = chart.data.labels[idx]; // 'Albay', 'Cam Sur', etc.
                if (label) {
                    highlightProvince(label);
                    onMapProvinceClick(label);
                    // Zoom feature removed as per request
                }
            };
            chart.update('none');
            
            // Also attach listener to the main title if it exists
            const titleEl = document.getElementById('dashboard-main-title');
            if (titleEl) {
                titleEl.onclick = () => {
                    onRegionResetClick();
                    if (window.MapDrilldown) window.MapDrilldown.drillUpTo(-1);
                };
            }
        };
        attempt();
    }

    function _getProvinceChart() {
        const canvas = document.getElementById('provinceChart');
        if (!canvas) return null;
        return Chart.getChart(canvas);
    }

    return { init, highlightProvince, onMapProvinceClick, onMapClearClick, onRegionResetClick, onMapMuniClick };
})();
