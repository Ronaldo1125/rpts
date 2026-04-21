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
                <option>Ongoing</option>
                <option>Proposed</option>
                <option>Terminated</option>
                <option>Suspended</option>
                <option>Dropped</option>
                <option>Completed</option>
            </select>
        </div>

        <div class="sidebar-filter-group">
            <label>Funding Category</label>
            <select id="filter-funding-category">
                <option value="all">All Funding Categories</option>
                <option>Tier 1</option>
                <option>Tier 2</option>
                <option>Multi-Year Allocation</option>
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
                <option value="specific">Location-Specific</option>
            </select>
        </div>

        <div id="group-province" class="sidebar-filter-group" style="display: none;">
            <label>Province</label>
            <select id="filter-province">
                <option>All</option>
                <option>Albay</option>
                <option>Camarines Norte</option>
                <option>Camarines Sur</option>
                <option>Catanduanes</option>
                <option>Masbate</option>
                <option>Sorsogon</option>
            </select>
        </div>

        <div id="group-district" class="sidebar-filter-group" style="display: none;">
            <label>District</label>
            <select id="filter-district">
                <option>All</option>
            </select>
        </div>

        <div id="group-city" class="sidebar-filter-group" style="display: none;">
            <label>City / Municipality</label>
            <select id="filter-city">
                <option>All</option>
            </select>
        </div>

        <div class="sidebar-filter-group">
            <label>Implementing Agency</label>
            <select id="filter-agency">
                <option value="all">All Agencies</option>
                <option>BFP</option>
                <option>BJMP</option>
                <option>CSC</option>
                <option>DBM</option>
                <option>DEPDev</option>
                <option>DILG</option>
                <option>DOJ</option>
                <option>PNP</option>
                <option>DPWH</option>
                <option>DOH</option>
                <option>DA</option>
            </select>
        </div>

        <div class="sidebar-filter-group">
            <label>Sectors</label>
            <select id="filter-sectors">
                <option value="all">All Sectors</option>
                <option>Economic</option>
                <option>Social</option>
                <option>Infrastructure</option>
                <option>Development Administration</option>
            </select>
        </div>

        <button id="btn-reset-filters" class="btn-reset-v3">
            <i data-lucide="rotate-ccw"></i> Reset Filters
        </button>
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
                        <span class="m-val">56</span>
                        <span class="m-lbl">No. of Projects</span>
                    </div>
                    <div class="header-metric-item" id="metric-box-cost" style="opacity: 0.5;">
                        <span class="m-val">65.10</span>
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
                    <span id="results-count" style="color: #0f172a;">11</span> Matches Found
                </div>
            </div>

            <div class="v3-table-wrapper">
                <table class="v3-table project-table">
                    <thead>
                        <tr>
                            <th>Project Profile</th>
                            <th>Cost (M)</th>
                            <th>Spatial Context</th>
                            <th>Agency</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Construction of BFP Regional Training Center</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Construction of building facility (1,836 sqm) for fire trucks and equipment.</div>
                            </td>
                            <td style="font-weight: 600;">150.00</td>
                            <td>Tuburan, Ligao, Albay</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">BFP</span></td>
                            <td><span class="status-badge-v3" data-status="proposed">Proposed</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Acquisition of Aerial Ladder Firetrucks</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">One unit aerial-ladder firetruck for identified cities/municipalities.</div>
                            </td>
                            <td style="font-weight: 600;">630.00</td>
                            <td>Regionwide</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">BFP</span></td>
                            <td><span class="status-badge-v3" data-status="proposed">Proposed</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Upgrading of Sorsogon City District Jail</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Construction of jail building, kitchen, infirmary, and fencing.</div>
                            </td>
                            <td style="font-weight: 600;">409.77</td>
                            <td>Sorsogon City</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">BJMP</span></td>
                            <td><span class="status-badge-v3" data-status="proposed">Proposed</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">DILG Local Governance Convergence Center</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Convention and gathering place for planning and symposiums.</div>
                            </td>
                            <td style="font-weight: 600;">150.00</td>
                            <td>Legazpi City</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">DILG</span></td>
                            <td><span class="status-badge-v3" data-status="proposed">Proposed</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Camarines Sur Police Provincial HQ</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Standard provincial office building headquarters in Naga City.</div>
                            </td>
                            <td style="font-weight: 600;">98.50</td>
                            <td>Naga City</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">PNP</span></td>
                            <td><span class="status-badge-v3" data-status="proposed">Proposed</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Standardization of MPS (Baao)</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Three-story standard-type Municipal Police Station building.</div>
                            </td>
                            <td style="font-weight: 600;">10.50</td>
                            <td>Baao, Camarines Sur</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">PNP</span></td>
                            <td><span class="status-badge-v3" data-status="ongoing">Ongoing</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Camarines Sur Expressway</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Physical Connectivity - High-speed highway project.</div>
                            </td>
                            <td style="font-weight: 600;">9,240.00</td>
                            <td>Camarines Sur</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">DPWH</span></td>
                            <td><span class="status-badge-v3" data-status="ongoing">Ongoing</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">DonPiCaSo Tourism Highway</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Donsol-Pilar-Castilla-Sorsogon City Tourism Highway.</div>
                            </td>
                            <td style="font-weight: 600;">7,720.00</td>
                            <td>Sorsogon</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">DPWH</span></td>
                            <td><span class="status-badge-v3" data-status="ongoing">Ongoing</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Naga Airport Development Project</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Physical Connectivity - Airport modernization.</div>
                            </td>
                            <td style="font-weight: 600;">10,600.00</td>
                            <td>Naga City, Cam Sur</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">DOTr</span></td>
                            <td><span class="status-badge-v3" data-status="proposed">Proposed</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">New Masbate Airport Development Project</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Physical Connectivity - Masbate airport expansion.</div>
                            </td>
                            <td style="font-weight: 600;">10,800.00</td>
                            <td>Masbate</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">DOTr</span></td>
                            <td><span class="status-badge-v3" data-status="proposed">Proposed</span></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: #0f172a; margin-bottom: 0.25rem;">Pasacao-Balatan Tourism Coastal Highway</div>
                                <div style="font-size: 0.75rem; color: #64748b; line-height: 1.4; max-width: 400px;">Physical Connectivity - Coastal development highway.</div>
                            </td>
                            <td style="font-weight: 600;">14,970.00</td>
                            <td>Camarines Sur</td>
                            <td><span style="background: #f1f5f9; padding: 0.25rem 0.6rem; border-radius: 4px; font-weight: 700; font-size: 0.7rem;">DPWH</span></td>
                            <td><span class="status-badge-v3" data-status="ongoing">Ongoing</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
