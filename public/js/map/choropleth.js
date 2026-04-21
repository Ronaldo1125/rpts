/**
 * choropleth.js — Colours each province polygon based on data + current mode
 */
window.MapChoropleth = (function () {
    let _currentMode = 'projects';

    function getColor(id, mode, isMuni = false) {
        if (isMuni) return _getMuniColor(id, mode);
        
        const data = MapConfig.provinceData[id];
        if (!data) return '#e2e8f0';

        const value = mode === 'projects' ? data.projects : data.cost;
        const thresholds = MapConfig.colorScale[mode];

        for (let i = thresholds.length - 1; i >= 0; i--) {
            if (value >= thresholds[i].min) return thresholds[i].color;
        }
        return '#e2e8f0';
    }

    /**
     * Generates a consistent pseudo-random color for municipalities based on name hash
     */
    function _getMuniColor(name, mode) {
        if (!name) return '#cbd5e1';
        let hash = 0;
        for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
        }
        
        const thresholds = MapConfig.colorScale[mode];
        const index = Math.abs(hash) % thresholds.length;
        return thresholds[index].color;
    }

    function styleFeature(feature) {
        const props = feature.properties;
        // Provincial features have 'id', Municipality features often have 'adm3_en' or 'name' 
        const isMuni = !!(props.adm3_en || (!MapConfig.provinceData[props.id] && props.name));
        const identifier = props.id || props.adm3_en || props.name;

        return {
            fillColor: getColor(identifier, _currentMode, isMuni),
            ...MapConfig.defaultStyle
        };
    }

    function refreshLayer(geoLayer) {
        if (!geoLayer) return;
        geoLayer.eachLayer(layer => {
            const props = layer.feature.properties;
            const isMuni = !!(props.adm3_en || (!MapConfig.provinceData[props.id] && props.name));
            const identifier = props.id || props.adm3_en || props.name;

            layer.setStyle({
                fillColor: getColor(identifier, _currentMode, isMuni),
                ...MapConfig.defaultStyle
            });
        });
    }

    function setMode(mode, geoLayer) {
        _currentMode = mode;
        refreshLayer(geoLayer);
        if (window.MapLegend) MapLegend.update(mode);
    }

    function getMode() { return _currentMode; }

    return { styleFeature, setMode, getMode, getColor, refreshLayer };
})();
