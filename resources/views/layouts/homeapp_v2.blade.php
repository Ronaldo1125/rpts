<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPTS | Regional Project Tracking System</title>
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..900;1,6..96,400..900&family=Pinyon+Script&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Intercept createIcons to prevent re-processing SVGs and console errors
        if (window.lucide) {
            const origCreateIcons = window.lucide.createIcons;
            window.lucide.createIcons = function(options) {
                document.querySelectorAll('svg[data-lucide]').forEach(svg => {
                    svg.setAttribute('data-lucide-rendered', svg.getAttribute('data-lucide'));
                    svg.removeAttribute('data-lucide');
                });
                origCreateIcons(options);
            };
        }
    </script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/about-rdip.css') }}">
    <link rel="stylesheet" href="{{ asset('css/announcements.css') }}">
    <link rel="stylesheet" href="{{ asset('css/project-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/map.css') }}">

    <!-- DearFlip CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@1.7.3/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/@dearhive/dearhive-icons@1.0.0/themify-icons.min.css" rel="stylesheet" type="text/css">
    
    @yield('style')
</head>

<body class="landing-page">

    <div class="cursor-dot" data-cursor-dot></div>
    <div class="cursor-outline" data-cursor-outline></div>

    <nav id="sidebar-container" class="landing-sidebar">
        @include('partials.landing.sidebar')
    </nav>
    

    <!-- Global Header -->
    <header id="header-container" class="top-bar">
        @include('partials.landing.header')
    </header>
    
    <div id="page-content">
        @yield('content')
    </div>

    <!-- Global Landing Footer -->
    <footer id="footer-container-wrapper" class="landing-footer">
        @include('partials.landing.footer')
    </footer>

    <button id="btn-scroll-top" class="btn-scroll-top">
        <i data-lucide="arrow-up"></i>
    </button>
    

    <!-- localForage (IndexedDB Wrapper) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/localforage/1.10.0/localforage.min.js"></script>

    <!-- jQuery (Required by DearFlip) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DearFlip JS -->
    <script src="https://cdn.jsdelivr.net/npm/@dearhive/dearflip-jquery-flipbook@1.7.3/dflip/js/dflip.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Choropleth Map Modules (load order matters) -->
    <script src="{{ asset('js/common/config.js') }}"></script>
    <script src="{{ asset('js/map/choropleth.js') }}"></script>
    <script src="{{ asset('js/map/legend.js') }}"></script>
    <script src="{{ asset('js/common/toggle.js') }}"></script>
    <script src="{{ asset('js/map/drilldown.js') }}"></script>
    <script src="{{ asset('js/map/map-sync.js') }}"></script>
    <script src="{{ asset('js/map/map.js') }}"></script>

    <!-- Core Landing Logic -->
    <script src="{{ asset('js/common/landing.js?v=2.0') }}"></script>
</body>

</html>
