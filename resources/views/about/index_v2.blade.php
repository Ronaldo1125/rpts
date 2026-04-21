@extends('layouts.homeapp_v2')

@section('style')
    <style>
        /* Standalone Page Background Fix for About RDIP */
        body.landing-page {
            background: linear-gradient(rgba(0, 33, 71, 0.82), rgba(0, 33, 71, 0.92)),
                        url('{{ asset('assets/images/photoshop.webp') }}') center / cover fixed !important;
            overflow-y: auto !important;
            cursor: auto !important;
        }
        
        /* Hide custom cursor on this page for better usability */
        .cursor-dot, .cursor-outline { 
            display: none !important; 
        }

        /* Disable scroll snapping for About page */
        html {
            scroll-snap-type: none !important;
        }

        /* Ensure header is visible and properly styled for the white-card aesthetic */
        .top-bar {
            background: rgba(255, 255, 255, 0.95) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(15px) !important;
            padding: 1.2rem 4rem !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05) !important;
        }

        .top-bar .motto,
        .top-bar .top-left i,
        .top-bar .top-left svg,
        .top-bar #menu-toggle i,
        .top-bar #menu-toggle svg,
        .top-bar #search-toggle i,
        .top-bar .system-title,
        .top-bar .system-subtitle {
            color: #0f172a !important;
        }

        .top-bar .header-logo {
            filter: none !important;
            opacity: 0.85 !important;
        }
    </style>
@endsection

@section('content')
<!-- ===== ABOUT RDIP PAGE ===== -->
<div class="rdip-wrapper">

    <!-- ── HERO ISLAND ── -->
    <div class="rdip-hero-island">
        <a href="{{ route('landing') }}" id="btn-back-rdip" class="rdip-back-btn">
            <i data-lucide="arrow-left" width="14" height="14"></i>
            Back
        </a>

        <div class="rdip-hero-body">
            <p class="rdip-overline">Regional Development Investment Program</p>
            <h1 class="rdip-main-title">RDIP 2023 – 2028</h1>
            <p class="rdip-subtitle">
                The five-year investment blueprint of Bicol Region — translating the goals of the
                Regional Development Plan into concrete, RDC-endorsed programs, projects, and activities.
            </p>
            <a href="https://dro5.depdev.gov.ph/" target="_blank" class="rdip-pill-link">
                Visit Official DEPDev V Site <i data-lucide="external-link" width="13" height="13"></i>
            </a>
        </div>
    </div>

    <!-- ── STAT CARDS ROW ── -->
    <div class="rdip-stat-cards">
        <div class="rdip-stat-card">
            <span class="rdip-stat-num">3,537</span>
            <span class="rdip-stat-lbl">Endorsed Projects</span>
        </div>
        <div class="rdip-stat-card">
            <span class="rdip-stat-num">₱ 316.9B</span>
            <span class="rdip-stat-lbl">Total Investment Cost</span>
        </div>
        <div class="rdip-stat-card">
            <span class="rdip-stat-num">6</span>
            <span class="rdip-stat-lbl">Provinces Covered</span>
        </div>
        <div class="rdip-stat-card">
            <span class="rdip-stat-num">40+</span>
            <span class="rdip-stat-lbl">Implementing Agencies</span>
        </div>
        <div class="rdip-stat-card">
            <span class="rdip-stat-num">6</span>
            <span class="rdip-stat-lbl">Year Coverage</span>
        </div>
    </div>

    <!-- ── TWO-COLUMN CONTENT ISLANDS ── -->
    <div class="rdip-cards-row">

        <!-- What is the RDIP -->
        <div class="rdip-island">
            <p class="rdip-card-chip">OVERVIEW</p>
            <h2 class="rdip-card-title">What is the RDIP?</h2>
            <p class="rdip-card-body">
                The <strong>Regional Development Investment Program (RDIP)</strong> is the official
                multi-year investment programming document of DEPDev Regional Office V. It catalogs all
                public sector programs, projects, and activities (PPAs) recommended for implementation
                in the Bicol Region for the planning period — currently 2023 to 2028.
            </p>
            <p class="rdip-card-body">
                Projects are aligned with the <strong>Bicol Regional Development Plan 2023–2028</strong>
                and the national government's socioeconomic agenda. All projects are formally
                <strong>endorsed by the Regional Development Council (RDC V)</strong>, the region's
                highest policy-making body.
            </p>
            <p class="rdip-card-body">
                This tracking system serves as the digital complement to the RDIP — enabling real-time
                monitoring of project status, fund utilization, and agency accountability.
            </p>
        </div>

        <!-- How it Works — timeline -->
        <div class="rdip-island">
            <p class="rdip-card-chip">PROCESS</p>
            <h2 class="rdip-card-title">How the RDIP Works</h2>

            <div class="rdip-timeline">
                <div class="rdip-tl-item">
                    <div class="rdip-tl-badge">01</div>
                    <div class="rdip-tl-body">
                        <h4>Agency Submission (CPP)</h4>
                        <p>Implementing agencies submit proposed projects via the Comprehensive Project Profile (CPP) to
                            DEPDev RO V.</p>
                    </div>
                </div>
                <div class="rdip-tl-item">
                    <div class="rdip-tl-badge">02</div>
                    <div class="rdip-tl-body">
                        <h4>Technical Review</h4>
                        <p>DEPDev RO V evaluates submissions for RDP consistency, documentation completeness, and
                            endorsement readiness.</p>
                    </div>
                </div>
                <div class="rdip-tl-item">
                    <div class="rdip-tl-badge">03</div>
                    <div class="rdip-tl-body">
                        <h4>RDC Endorsement</h4>
                        <p>Projects passing review are formally endorsed by the Regional Development Council (RDC V).
                        </p>
                    </div>
                </div>
                <div class="rdip-tl-item">
                    <div class="rdip-tl-badge">04</div>
                    <div class="rdip-tl-body">
                        <h4>Budget Programming</h4>
                        <p>Endorsed projects form the basis for agency budget proposals to DBM, anchoring regional
                            priorities in the national budget.</p>
                    </div>
                </div>
                <div class="rdip-tl-item">
                    <div class="rdip-tl-badge">05</div>
                    <div class="rdip-tl-body">
                        <h4>Monitoring &amp; Updating</h4>
                        <p>DEPDev RO V and agencies track progress; the RDIP is updated periodically to reflect project
                            status changes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── IFP FORMULATION PROCESS ISLAND (Animated Graphic) ── -->
    <div class="rdip-island rdip-full-island">
        <div class="rdip-island-header" style="margin-bottom: 3rem;">
            <div>
                <p class="rdip-card-chip">PROCESS WORKFLOW</p>
                <h2 class="rdip-card-title" style="margin-bottom: 0;">IFP Formulation Process</h2>
            </div>
            <p class="rdip-island-intro" style="padding-top: 1rem;">The annual cycle for updating and approving the list of Infrastructure Flagship Projects (IFPs).</p>
        </div>

        <div class="ifp-graphic-container" style="display: flex; justify-content: center; width: 100%; padding: 1rem 0 2rem;">
            <img src="assets/images/ifp.gif.gif" alt="IFP Formulation Process" style="max-width: 100%; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.1);">
        </div>
    </div>

    <!-- ── STRATEGIC PILLARS ISLAND ── -->
    <div class="rdip-island rdip-full-island">
        <div class="rdip-island-header">
            <div>
                <p class="rdip-card-chip">STRATEGIC PILLARS</p>
                <h2 class="rdip-card-title">Development Sectors</h2>
            </div>
            <p class="rdip-island-intro">The RDIP is structured around the key development chapters of the Bicol RDP,
                each targeting specific outcomes for the region.</p>
        </div>

        <!-- Tabs Navigation -->
        <div class="rdip-tabs-nav">
            <button class="rdip-tab-btn active" data-rdip-tab="social">Social Development</button>
            <button class="rdip-tab-btn" data-rdip-tab="economic">Economic Development</button>
            <button class="rdip-tab-btn" data-rdip-tab="infra">Infrastructure</button>
            <button class="rdip-tab-btn" data-rdip-tab="governa">Dev. Administration</button>
        </div>

        <!-- Panels Container -->
        <div class="rdip-tab-container">
            <!-- Social Sector -->
            <div class="rdip-tab-panel active" id="rdip-panel-social">
                <div class="rdip-panel-header">
                    <h3>Social Development</h3>
                </div>
                <div class="rdip-sector-grid">
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="hospital" width="24" height="24"></i></div>
                        <h4>Healthcare</h4>
                        <p>Universal health coverage, health facilities modernization, and public health programs.</p>
                        <div class="rdip-agency-row"><span>DOH</span><span>PhilHealth</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="graduation-cap" width="24" height="24"></i></div>
                        <h4>Education</h4>
                        <p>School infrastructure, quality instruction, technical-vocational training, and higher education.</p>
                        <div class="rdip-agency-row"><span>DepEd</span><span>CHED</span><span>TESDA</span><span>SUCs</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="heart-handshake" width="24" height="24"></i></div>
                        <h4>Social Welfare and Development</h4>
                        <p>Protection of vulnerable groups, poverty reduction, and social safety nets.</p>
                        <div class="rdip-agency-row"><span>DSWD</span><span>NCIP</span><span>NAPC</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="briefcase" width="24" height="24"></i></div>
                        <h4>Labor and Employment</h4>
                        <p>Job promotion, worker protection, industrial peace, and skills matching.</p>
                        <div class="rdip-agency-row"><span>DOLE</span><span>OWWA</span><span>POEA</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="home" width="24" height="24"></i></div>
                        <h4>Housing/Land Development</h4>
                        <p>Socialized housing, land use regulation, and sustainable urban development.</p>
                        <div class="rdip-agency-row"><span>DHSUD</span><span>NHA</span><span>Pag-IBIG</span></div>
                    </div>
                </div>
            </div>

            <!-- Economic Sector -->
            <div class="rdip-tab-panel" id="rdip-panel-economic">
                <div class="rdip-panel-header">
                    <h3>Economic Development</h3>
                </div>
                <div class="rdip-sector-grid">
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="wheat" width="24" height="24"></i></div>
                        <h4>Agriculture, Fisheries & Forestry</h4>
                        <p>Modernization of agri-fishery production, irrigation, and sustainable forest management.</p>
                        <div class="rdip-agency-row"><span>DA</span><span>BFAR</span><span>DENR</span><span>DAR</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="palmtree" width="24" height="24"></i></div>
                        <h4>Tourism</h4>
                        <p>Strategic tourism destination development and industry promotion.</p>
                        <div class="rdip-agency-row"><span>DOT</span><span>TPB</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="factory" width="24" height="24"></i></div>
                        <h4>Trade and Industry</h4>
                        <p>SME development, investment promotion, and industrial competitiveness.</p>
                        <div class="rdip-agency-row"><span>DTI</span><span>BOI</span><span>PEZA</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="users-2" width="24" height="24"></i></div>
                        <h4>Livelihood</h4>
                        <p>Community-based enterprise support and income-generating opportunities.</p>
                        <div class="rdip-agency-row"><span>DOLE</span><span>DSWD</span><span>DTI</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="pickaxe" width="24" height="24"></i></div>
                        <h4>Mining</h4>
                        <p>Responsible mineral resource development and regulation of mining activities.</p>
                        <div class="rdip-agency-row"><span>DENR-MGB</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="building-2" width="24" height="24"></i></div>
                        <h4>Enterprise & Cooperative Development</h4>
                        <p>Strengthening of cooperatives and micro/small enterprises.</p>
                        <div class="rdip-agency-row"><span>CDA</span><span>DTI</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="microscope" width="24" height="24"></i></div>
                        <h4>Research and Development</h4>
                        <p>Scientific research, technology transfer, and regional innovation systems.</p>
                        <div class="rdip-agency-row"><span>DOST</span><span>DA-BAR</span><span>ERDB</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="tree-pine" width="24" height="24"></i></div>
                        <h4>Environment</h4>
                        <p>Pollution control, ecosystem protection, and environmental law enforcement.</p>
                        <div class="rdip-agency-row"><span>DENR-EMB</span><span>BCDA</span></div>
                    </div>
                </div>
            </div>

            <!-- Infrastructure Sector -->
            <div class="rdip-tab-panel" id="rdip-panel-infra">
                <div class="rdip-panel-header">
                    <h3>Infrastructure</h3>
                </div>
                <div class="rdip-sector-grid">
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="building" width="24" height="24"></i></div>
                        <h4>Buildings and Facilities</h4>
                        <p>Construction and maintenance of public administrative and social infrastructure.</p>
                        <div class="rdip-agency-row"><span>DPWH</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="truck" width="24" height="24"></i></div>
                        <h4>Land Transportation</h4>
                        <p>Roads, bridges, and efficient land-based transit systems.</p>
                        <div class="rdip-agency-row"><span>DPWH</span><span>DOTr</span><span>LTO</span><span>LTFRB</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="droplet" width="24" height="24"></i></div>
                        <h4>Water and Sanitation</h4>
                        <p>Safe water supply, sewerage systems, and public hygiene facilities.</p>
                        <div class="rdip-agency-row"><span>DPWH</span><span>DILG</span><span>LWUA</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="sprout" width="24" height="24"></i></div>
                        <h4>Water Resources/Irrigation</h4>
                        <p>Agricultural water management and large-scale irrigation systems.</p>
                        <div class="rdip-agency-row"><span>NIA</span><span>BSWM</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="plane" width="24" height="24"></i></div>
                        <h4>Aviation</h4>
                        <p>Development and modernization of airports and air navigation facilities.</p>
                        <div class="rdip-agency-row"><span>DOTr</span><span>CAAP</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="ship" width="24" height="24"></i></div>
                        <h4>Maritime</h4>
                        <p>Seports development, maritime safety, and inter-island connectivity.</p>
                        <div class="rdip-agency-row"><span>DOTr</span><span>PPA</span><span>PCG</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="waves" width="24" height="24"></i></div>
                        <h4>Flood Mitigation</h4>
                        <p>Flood control structures, drainage systems, and water impounding projects.</p>
                        <div class="rdip-agency-row"><span>DPWH</span><span>NIA</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="zap" width="24" height="24"></i></div>
                        <h4>Energy/Power Transmission</h4>
                        <p>Rural electrification, renewable energy development, and grid stability.</p>
                        <div class="rdip-agency-row"><span>DOE</span><span>NEA</span><span>NPC</span><span>TRANSCO</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="wifi" width="24" height="24"></i></div>
                        <h4>ICT</h4>
                        <p>Digital infrastructure, broadband connectivity, and e-governance systems.</p>
                        <div class="rdip-agency-row"><span>DICT</span><span>NTC</span></div>
                    </div>
                </div>
            </div>

            <!-- Development Administration Sector -->
            <div class="rdip-tab-panel" id="rdip-panel-governa">
                <div class="rdip-panel-header">
                    <h3>Development Administration</h3>
                </div>
                <div class="rdip-sector-grid">
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="landmark" width="24" height="24"></i></div>
                        <h4>Public Sector Management</h4>
                        <p>Bureaucratic efficiency, institutional reforms, and organizational development.</p>
                        <div class="rdip-agency-row"><span>DBM</span><span>CSC</span><span>NEDA</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="graduation-cap" width="24" height="24"></i></div>
                        <h4>Capacity Development</h4>
                        <p>LGU training, technical assistance, and human resource development in government.</p>
                        <div class="rdip-agency-row"><span>DILG</span><span>LGA</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="shield-check" width="24" height="24"></i></div>
                        <h4>Public Order and Safety</h4>
                        <p>Peace and security, law enforcement modernization, and community safety.</p>
                        <div class="rdip-agency-row"><span>PNP</span><span>AFP</span><span>BJMP</span><span>BFP</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="settings" width="24" height="24"></i></div>
                        <h4>Service Delivery Support</h4>
                        <p>Operational support for basic service delivery and government functions.</p>
                        <div class="rdip-agency-row"><span>Various Agencies</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="fingerprint" width="24" height="24"></i></div>
                        <h4>Governance</h4>
                        <p>Promoting transparency, accountability, and participatory development processes.</p>
                        <div class="rdip-agency-row"><span>DILG</span><span>NEDA</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="scale" width="24" height="24"></i></div>
                        <h4>Administration of Justice</h4>
                        <p>Rule of law, legal services, and improvement of the regional justice system.</p>
                        <div class="rdip-agency-row"><span>DOJ</span><span>PAO</span><span>BJMP</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="coins" width="24" height="24"></i></div>
                        <h4>Public Finance & Management</h4>
                        <p>Revenue generation, resource mobilization, and prudent fiscal management.</p>
                        <div class="rdip-agency-row"><span>DBM</span><span>DOF-BLGF</span><span>BIR</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="thermometer-sun" width="24" height="24"></i></div>
                        <h4>Climate Action</h4>
                        <p>Climate change adaptation, mitigation strategies, and resilient programming.</p>
                        <div class="rdip-agency-row"><span>CCC</span><span>DENR</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="alert-triangle" width="24" height="24"></i></div>
                        <h4>DRRM</h4>
                        <p>Disaster risk reduction, response readiness, and regional resilience building.</p>
                        <div class="rdip-agency-row"><span>OCD</span><span>DILG</span><span>DSWD</span></div>
                    </div>
                    <div class="rdip-sector-card">
                        <div class="rdip-sector-icon"><i data-lucide="trash-2" width="24" height="24"></i></div>
                        <h4>Solid Waste Management</h4>
                        <p>Implementation of ecological solid waste management systems region-wide.</p>
                        <div class="rdip-agency-row"><span>DENR-EMB</span><span>LGUs</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.rdip-island.rdip-full-island (Strategic Pillars) -->

    <!-- ── RDP PRIMER FLIPBOOK ISLAND ── -->
    <div class="rdip-island rdip-full-island">
        <div style="margin-bottom: 1.5rem;">
            <p class="rdip-card-chip">DOCUMENT PREVIEW</p>
            <h2 class="rdip-card-title">Bicol RDP 2023–2028 Primer</h2>
            <p class="rdip-card-body" style="margin-bottom: 0;">
                Interact with the official regional development primer. Use the toolbar on the bottom to zoom, read in fullscreen, or view thumbnails.
            </p>
        </div>

        <div class="flipbook-wrapper" style="padding: 0; background: #f8fafc; border-radius: 8px; overflow: hidden;">
            <!-- Simple HTML-only initialization as requested -->
            <div id="rdp-flipbook" class="_df_book" 
                 source="assets/files/Bicol-RDP-2023-2028-Primer.pdf"
                 webgl="true"
                 style="height: 580px; width: 100%;">
            </div>
        </div>
    </div><!-- /.rdip-island (Flipbook) -->

    <!-- ── OFFICIAL RDIP DOCUMENT ISLAND ── -->
    <div class="rdip-island rdip-full-island">
        <div class="rdip-cta-island" style="padding: 0; box-shadow: none; border: none; background: transparent;">
            <div class="rdip-cta-icon-wrap">
                <i data-lucide="file-text" width="24" height="24"></i>
            </div>
            <div class="rdip-cta-text">
                <p class="rdip-cta-label">Official RDIP Document</p>
                <p class="rdip-cta-sub">The full RDIP 2023–2028 and the RDP Primer are available on the official DEPDev Region V website, containing the complete list of endorsed projects and sector priorities.</p>
            </div>
            <a href="https://dro5.depdev.gov.ph/" target="_blank" class="rdip-cta-btn">
                Visit Official Site <i data-lucide="arrow-right" width="15" height="15"></i>
            </a>
        </div>
    </div><!-- /.rdip-island (Official Document CTA) -->


    <!-- ── UPDATED RDIP FUNDING TABLE ── -->
    <div class="rdip-island rdip-full-island">
        <div style="margin-bottom: 1.5rem;">
            <p class="rdip-card-chip">INVESTMENT SUMMARY</p>
            <h2 class="rdip-card-title" style="margin-bottom:0.5rem;">Updated RDIP CY 2023 to 2028</h2>
            <p style="font-size:0.875rem; color:#64748b; margin:0;">As of December 31, 2025 &nbsp;&middot;&nbsp; Funding Requirement in Million Pesos</p>
        </div>
        
        <div class="rdip-table-wrapper">
            <table class="rdip-summary-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="text-align: left; min-width: 250px;">Sector / Agency</th>
                        <th rowspan="2">Sub-total per Agency</th>
                        <th colspan="6">FUNDING REQUIREMENT (Million Pesos)</th>
                        <th rowspan="2">No. of PPAs</th>
                    </tr>
                    <tr>
                        <th>2023</th>
                        <th>2024</th>
                        <th>2025</th>
                        <th>2026</th>
                        <th>2027</th>
                        <th>2028</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Economic Sector -->
                    <tr class="sector-group-row"><td colspan="9">Economic Sector</td></tr>
                    <tr><td>BFAR</td><td class="amount">490.44</td><td class="amount">36.08</td><td class="amount">43.24</td><td class="amount">218.06</td><td class="amount">72.18</td><td class="amount">57.55</td><td class="amount">63.33</td><td class="chapter-num">19</td></tr>
                    <tr style="background:#f8fafc;"><td>CDA</td><td class="amount">100.76</td><td class="amount">6.60</td><td class="amount">2.60</td><td class="amount">3.38</td><td class="amount">4.46</td><td class="amount">65.92</td><td class="amount">17.80</td><td class="chapter-num">4</td></tr>
                    <tr><td>DA</td><td class="amount">42,994.61</td><td class="amount">10,538.71</td><td class="amount">6,201.39</td><td class="amount">6,059.77</td><td class="amount">4,046.82</td><td class="amount">7,716.49</td><td class="amount">8,431.43</td><td class="chapter-num">37</td></tr>
                    <tr style="background:#f8fafc;"><td>DAR</td><td class="amount">28,852.02</td><td class="amount">126.97</td><td class="amount">108.85</td><td class="amount">92.92</td><td class="amount">243.69</td><td class="amount">19.55</td><td class="amount">28,260.04</td><td class="chapter-num">8</td></tr>
                    <tr><td>DENR</td><td class="amount">1,515.13</td><td class="amount">90.81</td><td class="amount">209.03</td><td class="amount">236.96</td><td class="amount">376.90</td><td class="amount">328.49</td><td class="amount">272.94</td><td class="chapter-num">39</td></tr>
                    <tr style="background:#f8fafc;"><td>DOLE</td><td class="amount">510.32</td><td class="amount">84.55</td><td class="amount">84.89</td><td class="amount">85.22</td><td class="amount">85.22</td><td class="amount">85.22</td><td class="amount">85.22</td><td class="chapter-num">14</td></tr>
                    <tr><td>DOST</td><td class="amount">1,778.23</td><td class="amount">469.96</td><td class="amount">420.76</td><td class="amount">478.10</td><td class="amount">409.41</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">32</td></tr>
                    <tr style="background:#f8fafc;"><td>DOT</td><td class="amount">27,967.96</td><td class="amount">4,569.76</td><td class="amount">5,635.93</td><td class="amount">3,643.77</td><td class="amount">9,581.20</td><td class="amount">2,792.98</td><td class="amount">1,744.32</td><td class="chapter-num">7</td></tr>
                    <tr><td>DTI</td><td class="amount">1,061.56</td><td class="amount">120.12</td><td class="amount">144.83</td><td class="amount">369.07</td><td class="amount">119.76</td><td class="amount">120.44</td><td class="amount">187.34</td><td class="chapter-num">17</td></tr>
                    <tr style="background:#f8fafc;"><td>EMB</td><td class="amount">547.91</td><td class="amount">15.19</td><td class="amount">41.24</td><td class="amount">194.81</td><td class="amount">96.95</td><td class="amount">98.87</td><td class="amount">100.85</td><td class="chapter-num">11</td></tr>
                    <tr><td>FPA</td><td class="amount">95.44</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">18.60</td><td class="amount">73.58</td><td class="amount">3.26</td><td class="chapter-num">1</td></tr>
                    <tr style="background:#f8fafc;"><td>MGB</td><td class="amount">202.64</td><td class="amount">15.88</td><td class="amount">16.17</td><td class="amount">36.74</td><td class="amount">60.66</td><td class="amount">54.99</td><td class="amount">18.20</td><td class="chapter-num">4</td></tr>
                    <tr><td>NFA</td><td class="amount">204.85</td><td class="amount">77.13</td><td class="amount">53.60</td><td class="amount">74.13</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">19</td></tr>
                    <tr style="background:#f8fafc;"><td>PAGASA</td><td class="amount">190.70</td><td class="amount">153.70</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">14.50</td><td class="amount">22.50</td><td class="chapter-num">2</td></tr>
                    <tr><td>PCA</td><td class="amount">8,079.14</td><td class="amount">1,236.84</td><td class="amount">1,220.19</td><td class="amount">1,266.37</td><td class="amount">1,386.10</td><td class="amount">1,469.45</td><td class="amount">1,500.19</td><td class="chapter-num">57</td></tr>
                    <tr style="background:#f8fafc;"><td>PCIC</td><td class="amount">1,149.98</td><td class="amount">381.04</td><td class="amount">394.41</td><td class="amount">374.52</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">3</td></tr>
                    <tr><td>PhilFIDA</td><td class="amount">88.35</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">59.40</td><td class="amount">28.95</td><td class="chapter-num">3</td></tr>
                    <tr class="subtotal-row"><td>Sub-Total Economic Sector</td><td class="amount">115,830.04</td><td class="amount">17,923.34</td><td class="amount">14,577.14</td><td class="amount">13,133.82</td><td class="amount">16,501.94</td><td class="amount">12,957.44</td><td class="amount">40,736.36</td><td class="chapter-num">277</td></tr>

                    <!-- Infrastructure Sector -->
                    <tr class="sector-group-row"><td colspan="9">Infrastructure Sector</td></tr>
                    <tr><td>CAAP</td><td class="amount">76.20</td><td class="amount">&mdash;</td><td class="amount">46.20</td><td class="amount">30.00</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">12</td></tr>
                    <tr style="background:#f8fafc;"><td>DOE</td><td class="amount">24.62</td><td class="amount">&mdash;</td><td class="amount">24.62</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">1</td></tr>
                    <tr><td>DOTr</td><td class="amount">8,234.46</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">280.00</td><td class="amount">444.68</td><td class="amount">3,149.33</td><td class="amount">4,360.46</td><td class="chapter-num">27</td></tr>
                    <tr style="background:#f8fafc;"><td>DPWH</td><td class="amount">135,062.48</td><td class="amount">19,201.59</td><td class="amount">43,408.04</td><td class="amount">23,162.57</td><td class="amount">22,463.58</td><td class="amount">15,355.01</td><td class="amount">11,471.69</td><td class="chapter-num">403</td></tr>
                    <tr><td>LTO</td><td class="amount">644.80</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">644.80</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">11</td></tr>
                    <tr style="background:#f8fafc;"><td>MARINA</td><td class="amount">51.16</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">51.16</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">1</td></tr>
                    <tr><td>NEA</td><td class="amount">4,921.40</td><td class="amount">22.40</td><td class="amount">1,509.00</td><td class="amount">1,134.00</td><td class="amount">1,166.00</td><td class="amount">1,090.00</td><td class="amount">&mdash;</td><td class="chapter-num">3</td></tr>
                    <tr style="background:#f8fafc;"><td>NIA</td><td class="amount">18,066.86</td><td class="amount">3,709.78</td><td class="amount">2,979.14</td><td class="amount">1,037.24</td><td class="amount">6,764.90</td><td class="amount">1,558.95</td><td class="amount">2,016.85</td><td class="chapter-num">2044</td></tr>
                    <tr><td>NPC</td><td class="amount">895.38</td><td class="amount">&mdash;</td><td class="amount">47.62</td><td class="amount">271.75</td><td class="amount">&mdash;</td><td class="amount">199.00</td><td class="amount">377.00</td><td class="chapter-num">5</td></tr>
                    <tr style="background:#f8fafc;"><td>NTC</td><td class="amount">84.24</td><td class="amount">&mdash;</td><td class="amount">45.00</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">39.24</td><td class="amount">&mdash;</td><td class="chapter-num">4</td></tr>
                    <tr><td>PNR</td><td class="amount">8,765.85</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">8,765.85</td><td class="amount">&mdash;</td><td class="chapter-num">1</td></tr>
                    <tr style="background:#f8fafc;"><td>PPA</td><td class="amount">4,868.18</td><td class="amount">&mdash;</td><td class="amount">4,223.45</td><td class="amount">644.73</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">11</td></tr>
                    <tr class="subtotal-row"><td>Sub-Total Infrastructure Sector</td><td class="amount">181,695.63</td><td class="amount">22,933.77</td><td class="amount">52,334.23</td><td class="amount">27,205.09</td><td class="amount">30,839.16</td><td class="amount">30,157.38</td><td class="amount">18,226.00</td><td class="chapter-num">2523</td></tr>

                    <!-- Development Administration Sector -->
                    <tr class="sector-group-row"><td colspan="9">Development Administration Sector</td></tr>
                    <tr><td>DILG</td><td class="amount">2,908.18</td><td class="amount">1,720.00</td><td class="amount">552.50</td><td class="amount">200.00</td><td class="amount">200.00</td><td class="amount">235.68</td><td class="amount">&mdash;</td><td class="chapter-num">6</td></tr>
                    <tr style="background:#f8fafc;"><td>PNP</td><td class="amount">292.83</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">10.50</td><td class="amount">&mdash;</td><td class="amount">282.33</td><td class="amount">&mdash;</td><td class="chapter-num">17</td></tr>
                    <tr class="subtotal-row"><td>Sub-Total Development Administration</td><td class="amount">3,201.01</td><td class="amount">1,720.00</td><td class="amount">552.50</td><td class="amount">210.50</td><td class="amount">200.00</td><td class="amount">518.01</td><td class="amount">&mdash;</td><td class="chapter-num">60</td></tr>

                    <!-- Social Sector -->
                    <tr class="sector-group-row"><td colspan="9">Social Sector</td></tr>
                    <tr><td>BISCAST</td><td class="amount">228.51</td><td class="amount">25.00</td><td class="amount">15.00</td><td class="amount">12.50</td><td class="amount">102.25</td><td class="amount">36.88</td><td class="amount">36.88</td><td class="chapter-num">5</td></tr>
                    <tr style="background:#f8fafc;"><td>CatSU</td><td class="amount">1,335.00</td><td class="amount">&mdash;</td><td class="amount">30.00</td><td class="amount">234.00</td><td class="amount">&mdash;</td><td class="amount">750.00</td><td class="amount">321.00</td><td class="chapter-num">18</td></tr>
                    <tr><td>CBSUA</td><td class="amount">7,462.66</td><td class="amount">25.00</td><td class="amount">&mdash;</td><td class="amount">12.50</td><td class="amount">6,998.78</td><td class="amount">426.38</td><td class="amount">&mdash;</td><td class="chapter-num">45</td></tr>
                    <tr style="background:#f8fafc;"><td>CHED</td><td class="amount">30.00</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">30.00</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">3</td></tr>
                    <tr><td>CNSC</td><td class="amount">2,098.60</td><td class="amount">25.00</td><td class="amount">200.60</td><td class="amount">197.00</td><td class="amount">63.00</td><td class="amount">1,002.00</td><td class="amount">611.00</td><td class="chapter-num">31</td></tr>
                    <tr style="background:#f8fafc;"><td>DEBESMSCAT</td><td class="amount">3,001.80</td><td class="amount">25.00</td><td class="amount">&mdash;</td><td class="amount">10.00</td><td class="amount">536.00</td><td class="amount">420.00</td><td class="amount">2,010.80</td><td class="chapter-num">46</td></tr>
                    <tr><td>DOH</td><td class="amount">12,856.41</td><td class="amount">590.00</td><td class="amount">1,445.60</td><td class="amount">3,447.92</td><td class="amount">3,942.39</td><td class="amount">2,677.27</td><td class="amount">753.23</td><td class="chapter-num">32</td></tr>
                    <tr style="background:#f8fafc;"><td>DSWD</td><td class="amount">165.00</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">165.00</td><td class="amount">&mdash;</td><td class="chapter-num">3</td></tr>
                    <tr><td>NCIP</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">&mdash;</td></tr>
                    <tr style="background:#f8fafc;"><td>NHA</td><td class="amount">5,128.80</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">20.53</td><td class="amount">1,713.79</td><td class="amount">1,619.73</td><td class="amount">1,774.75</td><td class="chapter-num">5</td></tr>
                    <tr><td>ParSU</td><td class="amount">642.43</td><td class="amount">27.00</td><td class="amount">3.97</td><td class="amount">17.25</td><td class="amount">235.43</td><td class="amount">358.78</td><td class="amount">&mdash;</td><td class="chapter-num">28</td></tr>
                    <tr style="background:#f8fafc;"><td>PhilHealth</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">3</td></tr>
                    <tr><td>SorSU</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">&mdash;</td></tr>
                    <tr style="background:#f8fafc;"><td>TESDA</td><td class="amount">433.80</td><td class="amount">&mdash;</td><td class="amount">13.89</td><td class="amount">365.65</td><td class="amount">54.26</td><td class="amount">&mdash;</td><td class="amount">&mdash;</td><td class="chapter-num">8</td></tr>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td style="text-align:center;">GRAND TOTAL</td>
                        <td class="amount" colspan="1">316,914.51</td>
                        <td class="amount">43,303.61</td>
                        <td class="amount">68,924.93</td>
                        <td class="amount">47,314.28</td>
                        <td class="amount">56,220.30</td>
                        <td class="amount">50,076.62</td>
                        <td class="amount">64,554.27</td>
                        <td class="chapter-num">3,537</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
