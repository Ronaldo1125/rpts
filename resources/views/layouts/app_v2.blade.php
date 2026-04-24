<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPTS | @yield('title', 'Portal')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
    
    <link rel="stylesheet" href="{{ asset('css/app-shell.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cpp-form.css') }}">
    @yield('styles')
</head>

<body>
    <div id="app-shell">
        <!-- Sidebar Container -->
        <div id="sidebar-container">
            @include("layouts.sidebar_v2")
        </div>
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
        <div class="detail-drawer-overlay" id="detail-drawer-overlay"></div>

        <!-- Main Content Area -->
        <main class="main">
            <!-- Top Navigation Container -->
            <div id="topnav-container">
                @include("layouts.topnav")
            </div>

            <!-- Dynamic Content Area -->
            <div id="page-sections-container">
                @yield('content')
            </div>

            <!-- Global Footer Container -->
            <div id="footer-container">
                @include("layouts.footer")
            </div>
        </main>
    </div>

    @include('partials.toast-notifications')
    @include('partials.delete-confirm-modal')

    <!-- localForage (IndexedDB Wrapper) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/localforage/1.10.0/localforage.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <!-- Excel Generation Library (SheetJS) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <!-- DOCX Generation Library (html-docx-js) -->
    <script src="https://unpkg.com/html-docx-js@0.3.1/dist/html-docx.js"></script>


    <!-- Table Sorting Utility -->
    <script src="{{ asset('js/common/table-sort.js') }}"></script>
    <!-- RPTS Global Utilities -->
    <script src="{{ asset('js/rpts-utils.js') }}"></script>

    @yield('scripts')

    <script>
    (function () {
      const initIcons = () => {
        if (window.lucide) {
          window.lucide.createIcons();
        }
      };

      // Try immediately
      initIcons();

      // Try when DOM is ready
      document.addEventListener("DOMContentLoaded", initIcons);

      // Final fallback for slow-loading JS
      setTimeout(initIcons, 500);
    })();
  </script>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
