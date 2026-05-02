/**
 * map-sync.js — Handles map-only synchronization logic.
 * Ensures map interactions (clicking provinces/municipalities) do NOT affect 
 * the rest of the dashboard charts or header metrics.
 */
window.MapSync = (function () {
    let _geoLayer = null;
    let _highlightedProvinceId = null;

    function init(geoLayer) {
        _geoLayer = geoLayer;
        // Chart patching removed to ensure map actions don't affect other dashboard elements
    }

    /**
     * Highlights a province on the map only.
     */
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
        // Highlighting on map only; no dashboard or chart updates as per user request
        highlightProvince(provinceId);
    }

    function onMapClearClick() {
        _clearHighlight();
    }

    function onRegionResetClick() {
        onMapClearClick();
    }

    function onMapMuniClick(muniName, data) {
        // No dashboard metrics update as per user request (maintain global context)
    }

    return { 
        init, 
        highlightProvince, 
        onMapProvinceClick, 
        onMapClearClick, 
        onRegionResetClick, 
        onMapMuniClick 
    };
})();
