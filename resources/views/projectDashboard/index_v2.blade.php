@extends('layouts.homeapp_v2')

@section('style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="{{ asset('css/project-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/map.css') }}">
    
    <style>
        /* Standalone Page Background Fix for Project Dashboard */
        body.landing-page {
            background: linear-gradient(rgba(0, 33, 71, 0.75), rgba(0, 33, 71, 0.85)),
                        url('{{ asset('assets/images/photoshop.webp') }}') center / cover fixed !important;
            overflow-y: auto !important;
            cursor: auto !important;
        }

        /* Hide custom cursor */
        .cursor-dot, .cursor-outline { 
            display: none !important; 
        }

        /* Disable scroll snapping */
        html {
            scroll-snap-type: none !important;
        }

        .dashboard-v3-container {
            margin-top: 10rem !important; /* Added more distance to clear header */
        }
        
        .dashboard-v3-sidebar {
            top: 12rem !important; /* Adjust sticky position for filters */
        }

        /* Independent Scrollable Table for Dashboard */
        .v3-table-wrapper {
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden; /* Container doesn't scroll, children do */
        }

        .project-table {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .project-table thead, 
        .project-table tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed; /* Ensures columns align between head and body */
            cursor: pointer;
            transition: background 0.2s;
        }

        .project-table tbody tr:hover {
            background: #f8fafc !important;
        }

        .project-table tbody {
            display: block;
            max-height: 550px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }

        .project-table tbody::-webkit-scrollbar {
            width: 6px;
        }
        .project-table tbody::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .project-table tbody::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }

        .project-table thead th {
            background: #f8fafc;
            z-index: 20;
            box-shadow: inset 0 -1px 0 #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
        }

        /* Column Widths for Fixed Layout */
        .project-table thead th:nth-child(1), .project-table tbody td:nth-child(1) { width: 40%; }
        .project-table thead th:nth-child(2), .project-table tbody td:nth-child(2) { width: 15%; text-align: right; }
        .project-table thead th:nth-child(3), .project-table tbody td:nth-child(3) { width: 20%; }
        .project-table thead th:nth-child(4), .project-table tbody td:nth-child(4) { width: 12%; text-align: center; }
        .project-table thead th:nth-child(5), .project-table tbody td:nth-child(5) { width: 13%; text-align: center; }

        /* Registry Loader */
        #registry-loader {
            display: none;
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 50;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
            border-radius: 12px;
        }
        
        .registry-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e2e8f0;
            border-top: 3px solid #1e3a8a;
            border-radius: 50%;
            animation: registry-spin 0.8s linear infinite;
        }

        @keyframes registry-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* Project Details Modal */
        #project-details-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content-v3 {
            background: #ffffff;
            width: 100%;
            max-width: 800px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
            transform: scale(1);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .modal-header-v3 {
            padding: 2rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .modal-body-v3 {
            padding: 2rem;
            overflow-y: auto;
        }

        .modal-close-v3 {
            background: #f1f5f9;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
        }

        .modal-close-v3:hover {
            background: #e2e8f0;
            transform: rotate(90deg);
        }

        .modal-tag {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 1rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .detail-item label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .detail-item span {
            font-size: 1rem;
            font-weight: 600;
            color: #0f172a;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-v3-container">
    <button id="btn-mobile-filter-toggle" class="btn-mobile-filter">
        <i data-lucide="filter"></i> Show Filters
    </button>
    <!-- ━━━ SIDEBAR FILTERS ━━━ -->
    <aside class="dashboard-v3-sidebar" id="v3-sidebar">
        <div class="sidebar-title-group">
            <i data-lucide="filter"></i>
            <h2>Filters</h2>
        </div>

        <div class="sidebar-filter-group">
            <label>Status</label>
            <select id="filter-status">
                <option value="all">All Statuses</option>
                <option value="ongoing">Ongoing</option>
                <option value="proposed">Proposed</option>
                <option value="terminated">Terminated</option>
                <option value="suspended">Suspended</option>
                <option value="dropped">Dropped</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <div class="sidebar-filter-group">
            <label>Funding Category</label>
            <select id="filter-funding-category">
                <option value="all">All Funding Categories</option>
                <option value="tier1">Tier 1</option>
                <option value="tier2">Tier 2</option>
                <option value="multi-year">Multi-Year Allocation</option>
            </select>
        </div>

        <div class="sidebar-filter-group">
            <label>Location</label>
            <select id="filter-location">
                <option value="all">All Coverage</option>
                <option value="nationwide">Nationwide</option>
                <option value="inter-regional">Inter-Regional</option>
                <option value="regionwide">Regionwide</option>
                <option value="inter-province">Inter-Province</option>
                <option value="locationspecific">Location-Specific</option>
            </select>
        </div>

        <div id="group-province" class="sidebar-filter-group" style="display: none;">
            <label>Province</label>
            <select id="filter-province">
                <option value="">All Provinces</option>
                @foreach($provinces as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        <div id="group-district" class="sidebar-filter-group" style="display: none;">
            <label>District</label>
            <select id="filter-district" disabled>
                <option value="">All Districts</option>
            </select>
        </div>

        <div id="group-city" class="sidebar-filter-group" style="display: none;">
            <label>City / Municipality</label>
            <select id="filter-city" disabled>
                <option value="">All Municipalities</option>
            </select>
        </div>

        <div class="sidebar-filter-group">
            <label>Implementing Agency</label>
            <select id="filter-agency">
                <option value="all">All Agencies</option>
                @foreach($agencies as $agency)
                    <option value="{{ $agency->agency_acronym }}">{{ $agency->agency_acronym }}</option>
                @endforeach
            </select>
        </div>

        <div class="sidebar-filter-group">
            <label>Sectors</label>
            <select id="filter-sectors">
                <option value="all">All Sectors</option>
                @foreach($sectors as $sector)
                    <option value="{{ $sector->sector_name }}">{{ $sector->sector_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex flex-column gap-2 mt-4">
            <button id="btn-apply-filters" class="btn-apply-v3 w-100">
                <i data-lucide="filter" width="16"></i> Apply Filters
            </button>
            <button id="btn-reset-filters" class="btn-reset-v3 w-100 mt-0">
                <i data-lucide="rotate-ccw"></i> Reset Filters
            </button>
        </div>
    </aside>

    <!-- ━━━ MAIN CONTENT area ━━━ -->
    <main class="dashboard-v3-main">
        
        <!-- HEADER & TOP METRICS -->
        <header class="dashboard-v3-header">
            <div class="header-text d-flex justify-content-between align-items-center w-100">
                <div>
                    <h1 id="dashboard-main-title">RPTS Project Insights</h1>
                    <p>Regional Project Tracking System Dashboard</p>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 3rem;">
                <!-- Metric Toggle integrated into header flow -->
                <div class="metric-toggle-container">
                    <span class="toggle-label">Counts</span>
                    <label class="switch">
                        <input type="checkbox" id="dashboard-metric-toggle">
                        <span class="slider"></span>
                    </label>
                    <span class="toggle-label secondary">Cost</span>
                </div>

                <div class="header-metrics-row">
                    <div class="header-metric-item" id="metric-box-count">
                        <span class="m-val">{{ number_format($stats['total_count']) }}</span>
                        <span class="m-lbl">No. of Projects</span>
                    </div>
                    <div class="header-metric-item" id="metric-box-cost" style="opacity: 0.5;">
                        <span class="m-val">{{ number_format($stats['total_cost'] / 1000, 2) }}</span>
                        <span class="m-lbl">Indicative Total Cost (B)</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- MASTER NAVIGATION TABS -->
        <nav class="master-tab-nav">
            <button class="m-tab active" data-master-tab="analytics">
                <i data-lucide="bar-chart-2"></i> Visual Analytics
            </button>
            <button class="m-tab" data-master-tab="registry">
                <i data-lucide="database"></i> Project Registry
            </button>
        </nav>

        <!-- SUB-TAB 1: ANALYTICS -->
        <div class="m-tab-panel active" id="m-panel-analytics">
            
            <div class="analytics-island">
                <!-- Integration of the Tab System I built earlier within this panel -->
                <div class="dashboard-tab-nav">
                    <button class="dash-tab active" data-dash-tab="status">
                        <i data-lucide="check-circle"></i> <span>Status & Categories</span>
                    </button>
                    <button class="dash-tab" data-dash-tab="overview">
                        <i data-lucide="pie-chart"></i> <span>Finance Overview</span>
                    </button>
                    <button class="dash-tab" data-dash-tab="details">
                        <i data-lucide="map"></i> <span>Geo & Sector</span>
                    </button>
                    <button class="dash-tab" data-dash-tab="trends">
                        <i data-lucide="trending-up"></i> <span>Trends & RDP</span>
                    </button>
                </div>

                <div class="dashboard-tab-content">
                <!-- Status & Categories Panel (The separated charts) -->
                <div class="dash-tab-panel active" id="dash-panel-status">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="premium-v3-card">
                            <div class="v3-card-header">
                                <h3 class="v3-card-title">Project Status Milestone</h3>
                            </div>
                            <div style="height: 350px;"><canvas id="statusDistributionChart"></canvas></div>
                        </div>
                        <div class="premium-v3-card">
                            <div class="v3-card-header">
                                <h3 class="v3-card-title">Funding Categories</h3>
                            </div>
                            <div style="height: 350px;"><canvas id="fundingSourcePieChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <!-- Overview Panel -->
                <div class="dash-tab-panel" id="dash-panel-overview">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="premium-v3-card">
                            <div class="v3-card-header">
                                <h3 class="v3-card-title" id="fund-source-chart-title">Fund Source Distribution</h3>
                            </div>
                            <div style="height: 300px;"><canvas id="fundSourceChart"></canvas></div>
                        </div>
                        <div class="premium-v3-card">
                            <div class="v3-card-header">
                                <h3 class="v3-card-title" id="agency-chart-title">Agency Allocation</h3>
                            </div>
                            <div style="height: 300px;"><canvas id="agencyChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <!-- Geo Panel -->
                <div class="dash-tab-panel" id="dash-panel-details">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="premium-v3-card">
                            <div class="v3-card-header"><h3 class="v3-card-title">Major Sectors</h3></div>
                            <div style="height: 300px;"><canvas id="sectorChart"></canvas></div>
                        </div>
                        <div class="premium-v3-card">
                            <div class="v3-card-header"><h3 class="v3-card-title">Province Distribution</h3></div>
                            <div style="height: 300px;"><canvas id="provinceChart"></canvas></div>
                        </div>
                        <!-- Row 2: Spatial Coverage + Choropleth Map -->
                        <div id="geo-row-2" style="grid-column: span 2;">
                            <!-- Spatial Coverage Type -->
                            <div class="premium-v3-card">
                                <div class="v3-card-header"><h3 class="v3-card-title">Spatial Coverage Type</h3></div>
                                <div style="height: 320px;"><canvas id="spatialCoverageChart"></canvas></div>
                            </div>

                            <!-- Project Distribution Map -->
                            <div class="premium-v3-card" id="map-card">
                                <!-- Card header: title -->
                                <div class="map-card-header">
                                    <h3 class="v3-card-title" style="margin:0;">Project Distribution Map</h3>
                                </div>
                                <!-- Breadcrumb (shown on drilldown) -->
                                <div class="map-breadcrumb" id="map-breadcrumb"></div>
                                <!-- Leaflet map canvas -->
                                <div id="bicol-map"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Trends Panel -->
                <div class="dash-tab-panel" id="dash-panel-trends">
                    <div class="premium-v3-card">
                        <div class="v3-card-header"><h3 class="v3-card-title">Investment Projects by Year</h3></div>
                        <div style="height: 350px;"><canvas id="yearChart"></canvas></div>
                    </div>
                    <div class="premium-v3-card" style="margin-top: 1.5rem;">
                        <div class="v3-card-header"><h3 class="v3-card-title">RDP Chapter Alignment</h3></div>
                        <div style="height: 450px;"><canvas id="rdpChapterChart"></canvas></div>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- SUB-TAB 2: PROJECT REGISTRY -->
        <div class="m-tab-panel" id="m-panel-registry">
            <div class="registry-controls">
                <div class="v3-search-box">
                    <i data-lucide="search"></i>
                    <input type="text" id="public-search" placeholder="Search by title, agency, or location...">
                </div>
                <div class="results-info" style="color: #64748b; font-weight: 600;">
                    <span id="results-count" style="color: #0f172a;">{{ $projects->count() }}</span> Matches Found
                </div>
            </div>

            <div class="v3-table-wrapper">
                <div id="registry-loader">
                    <div class="registry-spinner"></div>
                </div>
                <table class="v3-table project-table">
                    <thead>
                        <tr>
                            <th>Project Profile</th>
                            <th>Cost (M)</th>
                            <th>Location</th>
                            <th>Agency</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="dashboard-project-list">
                        <!-- Loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- PROJECT DETAILS MODAL -->
<div id="project-details-modal">
    <div class="modal-content-v3">
        <header class="modal-header-v3">
            <div style="flex: 1;">
                <div id="modal-status-tag" class="modal-tag">Ongoing</div>
                <h2 id="modal-title" style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #0f172a; line-height: 1.2;">Project Title Placeholder</h2>
                <div id="modal-component-group" style="display: none; margin-top: 0.25rem;">
                    <span id="modal-component-title" style="font-size: 0.75rem; font-weight: 700; color: #1e3a8a; background: #dbeafe; padding: 0.1rem 0.5rem; border-radius: 4px; text-transform: uppercase;"></span>
                </div>
                <p id="modal-agency-full" style="margin: 0.5rem 0 0; color: #64748b; font-weight: 600;">Agency Name Placeholder</p>
            </div>
            <button class="modal-close-v3" onclick="closeProjectModal()">
                <i data-lucide="x" style="width: 20px; height: 20px; color: #475569;"></i>
            </button>
        </header>
        <div class="modal-body-v3">
            <div style="margin-bottom: 2rem;">
                <h4 style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 0.75rem;">Project Description</h4>
                <p id="modal-description" style="color: #334155; line-height: 1.6; margin: 0; font-size: 1rem;">Detailed description goes here...</p>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <label>Total Cost (M)</label>
                    <span id="modal-cost">0.00</span>
                </div>
                <div class="detail-item">
                    <label>Location</label>
                    <span id="modal-location">Specific Address</span>
                </div>
                <div class="detail-item">
                    <label>Implementing Agency</label>
                    <span id="modal-agency">Agency</span>
                </div>
                <div class="detail-item">
                    <label>Current Status</label>
                    <span id="modal-status">Status</span>
                </div>
                <div class="detail-item">
                    <label>Funding Category</label>
                    <span id="modal-funding-category">Category</span>
                </div>
                <div class="detail-item">
                    <label>Fund Source</label>
                    <span id="modal-fund-source">Source</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')



    <script>
        window.dashboardStats = @json($stats);
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            // Enable white footer mode for Project Dashboard
            const landingFooter = document.getElementById('footer-container-wrapper');
            if (landingFooter) {
                landingFooter.classList.add('footer-rdip-mode');
            }
            
            if (window.map) window.map.invalidateSize();

            // ━━━ DASHBOARD LOCATION DRILLING ━━━
            const provinceSelect = document.getElementById('filter-province');
            const districtSelect = document.getElementById('filter-district');
            const municipalitySelect = document.getElementById('filter-city');

            const loadDistricts = async () => {
                const id = provinceSelect.value;
                
                // Reset District
                districtSelect.innerHTML = '<option value="">All Districts</option>';
                districtSelect.value = "";
                districtSelect.disabled = true;
                
                // Reset City
                municipalitySelect.innerHTML = '<option value="">All Municipalities</option>';
                municipalitySelect.value = "";
                municipalitySelect.disabled = true;
                
                if (!id) return;

                districtSelect.innerHTML = '<option value="">Loading...</option>';
                try {
                    const response = await fetch(`/location/getDistricts?province_id=${id}`);
                    const data = await response.json();
                    districtSelect.innerHTML = '<option value="">All Districts</option>';
                    districtSelect.disabled = false;
                    data.forEach(val => {
                        const opt = document.createElement('option');
                        opt.value = val.id;
                        opt.textContent = val.district_name;
                        districtSelect.appendChild(opt);
                    });
                } catch (err) {
                    console.error('Error loading districts:', err);
                    districtSelect.innerHTML = '<option value="">Error loading</option>';
                }
            };

            const loadMunicipalities = async () => {
                const pid = provinceSelect.value;
                const did = districtSelect.value;
                
                // Reset City
                municipalitySelect.innerHTML = '<option value="">All Municipalities</option>';
                municipalitySelect.value = "";
                municipalitySelect.disabled = true;
                
                if (!did || !pid) return;

                municipalitySelect.innerHTML = '<option value="">Loading...</option>';
                try {
                    const response = await fetch(`/location/getMunicipalities?province_id=${pid}&district_id=${did}`);
                    const data = await response.json();
                    municipalitySelect.innerHTML = '<option value="">All Municipalities</option>';
                    municipalitySelect.disabled = false;
                    data.forEach(val => {
                        const opt = document.createElement('option');
                        opt.value = val.id;
                        opt.textContent = val.municipality_name;
                        municipalitySelect.appendChild(opt);
                    });
                } catch (err) {
                    console.error('Error loading municipalities:', err);
                    municipalitySelect.innerHTML = '<option value="">Error loading</option>';
                }
            };

            if (provinceSelect) {
                provinceSelect.addEventListener('change', loadDistricts);
            }
            if (districtSelect) {
                districtSelect.addEventListener('change', loadMunicipalities);
            }
        });
    </script>
@endsection