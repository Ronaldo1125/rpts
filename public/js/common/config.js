/**
 * config.js — Shared configuration + embedded GeoJSON for the Bicol choropleth map.
 * Data is ALIGNED with landing.js chart labels.
 */
window.MapConfig = {
    // Leaflet map initial view
    center: [12.5, 123.5],
    zoom: 7,
    minZoom: 6,
    maxZoom: 14,

    // Accurate GeoJSON URLs from faeldon/philippines-json-maps
    geojsonUrls: {
        region: 'https://raw.githubusercontent.com/faeldon/philippines-json-maps/master/2023/geojson/regions/medres/provdists-region-500000000.0.01.json',
        provinceBase: 'https://raw.githubusercontent.com/faeldon/philippines-json-maps/master/2023/geojson/provdists/medres/municities-provdist-'
    },

    // mapping of internal-id (chart labels) to PSGC code (for drilldown)
    provinceCodes: {
        'Albay':            '500500000',
        'Cam Norte':        '501600000',
        'Cam Sur':          '501700000',
        'Catanduanes':      '502000000',
        'Masbate':          '504100000',
        'Sorsogon':         '506200000'
    },

    // Province data — Keys match EXACTLY with labels in landing.js provinceChart
    provinceData: {
        'Albay':            { projects: 15, cost: 3.5,   label: 'Albay' },
        'Cam Norte':        { projects: 5,  cost: 1.1,   label: 'Camarines Norte' },
        'Cam Sur':          { projects: 12, cost: 2.8,   label: 'Camarines Sur' },
        'Catanduanes':      { projects: 5,  cost: 1.67,  label: 'Catanduanes' },
        'Masbate':          { projects: 6,  cost: 1.2,   label: 'Masbate' },
        'Sorsogon':         { projects: 8,  cost: 1.5,   label: 'Sorsogon' }
    },

    // Colour scale — navy gradient matching dashboard palette
    colorScale: {
        projects: [
            { min: 0,  max: 3,          color: '#dbeafe' },
            { min: 4,  max: 7,          color: '#93c5fd' },
            { min: 8,  max: 11,         color: '#3b82f6' },
            { min: 12, max: 14,         color: '#1d4ed8' },
            { min: 15, max: Infinity,   color: '#1e3a6e' }
        ],
        cost: [
            { min: 0,   max: 1.2,       color: '#dbeafe' },
            { min: 1.2, max: 1.8,       color: '#93c5fd' },
            { min: 1.8, max: 2.5,       color: '#3b82f6' },
            { min: 2.5, max: 3.2,       color: '#1d4ed8' },
            { min: 3.2, max: Infinity,   color: '#1e3a6e' }
        ]
    },

    defaultStyle:   { fillOpacity: 0.72, weight: 1.5, color: '#ffffff', opacity: 1 },
    hoverStyle:     { weight: 2.5, color: '#1e3a6e', fillOpacity: 0.9 },
    highlightStyle: { weight: 3,   color: '#f59e0b', fillOpacity: 0.9 }
};

