/**
 * toggle.js — Bridges external triggers (like the global dashboard toggle)
 * to the map's choropleth mode.
 */
window.MapToggle = (function () {
    let _geoLayer = null;

    function init(geoLayer) {
        _geoLayer = geoLayer;
    }

    /**
     * Set the map mode externally.
     * @param {string} mode - 'projects' or 'cost'
     */
    function setMode(mode) {
        if (!MapChoropleth) return;
        MapChoropleth.setMode(mode, _geoLayer);
    }

    return { init, setMode };
})();
