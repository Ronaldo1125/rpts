@extends('layouts.app_v2')
@section('content')

<section id="cipg-guide" class="page-content active container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="font-size:1.5rem;">CIPG Guide</h2>
            <p class="text-muted mb-0" style="font-size:0.82rem;">
                Process overview, important information, and downloadable resources.
            </p>
        </div>
    </div>

    <!-- ── SECTION 1: What is the CIPG ── -->
    <div id="cipg-intro" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                    style="width:36px;height:36px;flex-shrink:0;">
                    <i data-lucide="book-open" width="16" class="text-primary"></i>
                </span>
                <h5 class="fw-bold mb-0" style="font-size:1rem;">What is the CIPG?</h5>
            </div>
            <p class="text-secondary mb-3" style="font-size:0.875rem;line-height:1.75;">
                The <strong>Comprehensive Investment Programming Guide (CIPG)</strong> is developed by the Bicol
                Regional Development Council to increase the number of projects being funded. The overall framework of
                the CIPG aims to synchronize the planning, investment programming, and budgeting processes. It provides
                the principles, processes, and requirements in the identification and prioritization of PPAs for
                inclusion in the Regional Development Investment Program (RDIP).
            </p>
            <p class="text-secondary mb-2" style="font-size:0.875rem;line-height:1.75;">
                The CIPG guides the identification of priority PPAs for inclusion in the RDIP. It also aims to:
            </p>
            <ol class="text-secondary mb-0 ps-4" style="font-size:0.875rem;line-height:1.75;">
                <li>Present the processes and requirements of the Bicol RDC for the inclusion of PPAs in the RDIP;</li>
                <li>Provide the criteria for inclusion of PPAs in the RDIP; and</li>
                <li>Monitor the status of funding of PPAs in the RDIP.</li>
            </ol>
        </div>
    </div>

    <!-- ── SECTION 2: CIPG Framework ── -->
    <div id="cipg-framework" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                    style="width:36px;height:36px;flex-shrink:0;">
                    <i data-lucide="network" width="16" class="text-primary"></i>
                </span>
                <h5 class="fw-bold mb-0" style="font-size:1rem;">CIPG Framework</h5>
            </div>

            <p class="text-secondary mb-3" style="font-size:0.875rem;line-height:1.75;">
                Investment programming is the systematic identification, preparation, selection, scheduling, or phasing
                of PPAs. The RDIP is the translation of the aspirations and objectives in the RDP into PPAs. The RDIP is
                the basis for approving projects in the annual agency budget proposal.
            </p>

            <p class="text-secondary mb-3" style="font-size:0.875rem;line-height:1.75;">
                The overall framework is shown below (Figure 1).
            </p>

            <div class="bg-white border rounded-4 shadow-sm mb-3 p-3" style="overflow-x:auto;">
                <img src="/assets/images/cipg-framework.png"
                     alt="CIPG Framework Diagram"
                     style="width:100%; min-width:680px; height:auto; display:block;"
                     onerror="this.src='../assets/images/cipg-framework.png'">
            </div>




            <p class="text-secondary mb-0" style="font-size:0.875rem;line-height:1.75;">
                Proponents are classified into two (2): direct and indirect. Direct proponents are Regional Line
                Agencies (RLAs), State Universities and Colleges (SUCs), Government-Owned and Controlled Corporations
                (GOCCs), and Government Finance Institutions (GFIs) that can submit directly to the RDC Secretariat
                requests for RDC approval to include PPAs in the RDIP. Indirect proponents, on the other hand, refer to
                requesting RLAs, LGUs, and private sector that need to submit the required documents to direct
                proponents for subsequent requests for RDC approval to be included in the RDIP.
            </p>
        </div>
    </div>

    <!-- ── SECTION 2: Submission Process (Step Timeline) ── -->
    <div id="cipg-process" class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-4">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                    style="width:36px;height:36px;flex-shrink:0;">
                    <i data-lucide="git-branch" width="16" class="text-primary"></i>
                </span>
                <h5 class="fw-bold mb-0" style="font-size:1rem;">Submission and Inclusion Process</h5>
            </div>

            <div class="cipg-steps">

                <div class="cipg-step">
                    <div class="cipg-step-num">1</div>
                    <div class="cipg-step-body">
                        <div class="cipg-step-title">Prepare the Comprehensive Project Profile (CPP)</div>
                        <div class="cipg-step-desc">
                            Complete all sections of the CPP form — project identification, technical
                            description, cost estimates, logical framework, and implementation schedule.
                            Use the downloadable templates below as a guide.
                        </div>
                    </div>
                </div>

                <div class="cipg-step">
                    <div class="cipg-step-num">2</div>
                    <div class="cipg-step-body">
                        <div class="cipg-step-title">Internal Agency Review &amp; Approval</div>
                        <div class="cipg-step-desc">
                            The CPP must be reviewed and signed off by the agency head or authorized
                            representative before submission. Ensure all supporting documents are attached.
                        </div>
                    </div>
                </div>

                <div class="cipg-step">
                    <div class="cipg-step-num">3</div>
                    <div class="cipg-step-body">
                        <div class="cipg-step-title">Submit via RPTS Portal</div>
                        <div class="cipg-step-desc">
                            Log in to the RPTS portal and navigate to <strong>CIPG Submissions</strong>.
                            Upload the completed CPP and all required attachments using the submission form.
                        </div>
                    </div>
                </div>

                <div class="cipg-step">
                    <div class="cipg-step-num">4</div>
                    <div class="cipg-step-body">
                        <div class="cipg-step-title">DepDev 5 Technical Review</div>
                        <div class="cipg-step-desc">
                            DepDev 5 planning staff will conduct a technical review of the submitted CPP.
                            Agencies may be called for clarification or asked to revise and resubmit.
                        </div>
                    </div>
                </div>

                <div class="cipg-step">
                    <div class="cipg-step-num">5</div>
                    <div class="cipg-step-body">
                        <div class="cipg-step-title">RDC Approval for RDIP Inclusion</div>
                        <div class="cipg-step-desc">
                            Projects that pass technical review are endorsed to the Regional Development
                            Council (RDC) for official approval and inclusion in the RDIP.
                        </div>
                    </div>
                </div>

                <div class="cipg-step cipg-step-last">
                    <div class="cipg-step-num cipg-step-num-done">
                        <i data-lucide="check" width="14"></i>
                    </div>
                    <div class="cipg-step-body">
                        <div class="cipg-step-title">Inclusion in the RDIP</div>
                        <div class="cipg-step-desc">
                            Endorsed projects are reflected in the Regional Development Investment Program
                            (RDIP 2023–2028) and can be tracked via the RPTS Project Dashboard.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- ── SECTION 4: Downloadable Resources ── -->
    <div id="cipg-resources" class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 p-0" id="headingResources">
            <button class="btn w-100 d-flex justify-content-between align-items-center p-4 shadow-none text-decoration-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseResources" aria-expanded="false" aria-controls="collapseResources">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                        style="width:36px;height:36px;flex-shrink:0;">
                        <i data-lucide="download-cloud" width="16" class="text-primary"></i>
                    </span>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size:1rem;">Downloadable Resources</h5>
                </div>
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                    <i data-lucide="chevron-down" width="18" class="text-secondary collapse-icon" style="transition: transform 0.3s ease;"></i>
                </div>
            </button>
        </div>
        <div id="collapseResources" class="collapse" aria-labelledby="headingResources">
            <div class="card-body p-4 pt-0">
                <div class="row g-3">

                <div class="col-md-6">
                    <div class="cipg-file-card">
                        <div class="cipg-file-icon bg-primary bg-opacity-10">
                            <i data-lucide="file-text" width="20" class="text-primary"></i>
                        </div>
                        <div class="cipg-file-info">
                            <div class="cipg-file-name">CPP Form Template</div>
                            <div class="cipg-file-meta">DOCX &middot; Comprehensive Project Profile</div>
                        </div>
                        <a href="#" class="btn btn-sm cipg-dl-btn">
                            <i data-lucide="download" width="13"></i> Download
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="cipg-file-card">
                        <div class="cipg-file-icon" style="background:rgba(16,185,129,0.1);">
                            <i data-lucide="table-2" width="20" style="color:#10b981;"></i>
                        </div>
                        <div class="cipg-file-info">
                            <div class="cipg-file-name">Project Cost Estimate Sheet</div>
                            <div class="cipg-file-meta">XLSX &middot; Detailed Cost Breakdown</div>
                        </div>
                        <a href="#" class="btn btn-sm cipg-dl-btn">
                            <i data-lucide="download" width="13"></i> Download
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="cipg-file-card">
                        <div class="cipg-file-icon" style="background:rgba(239,68,68,0.08);">
                            <i data-lucide="file-text" width="20" style="color:#ef4444;"></i>
                        </div>
                        <div class="cipg-file-info">
                            <div class="cipg-file-name">CIPG Submission Guidelines</div>
                            <div class="cipg-file-meta">PDF &middot; FY 2025–2026 Edition</div>
                        </div>
                        <a href="#" class="btn btn-sm cipg-dl-btn">
                            <i data-lucide="download" width="13"></i> Download
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="cipg-file-card">
                        <div class="cipg-file-icon" style="background:rgba(245,158,11,0.1);">
                            <i data-lucide="file-spreadsheet" width="20" style="color:#f59e0b;"></i>
                        </div>
                        <div class="cipg-file-info">
                            <div class="cipg-file-name">Logical Framework Template</div>
                            <div class="cipg-file-meta">XLSX &middot; Project Logframe Matrix</div>
                        </div>
                        <a href="#" class="btn btn-sm cipg-dl-btn">
                            <i data-lucide="download" width="13"></i> Download
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="cipg-file-card">
                        <div class="cipg-file-icon bg-primary bg-opacity-10">
                            <i data-lucide="presentation" width="20" class="text-primary"></i>
                        </div>
                        <div class="cipg-file-info">
                            <div class="cipg-file-name">CIPG Orientation Slides</div>
                            <div class="cipg-file-meta">PPTX &middot; Process Walkthrough</div>
                        </div>
                        <a href="#" class="btn btn-sm cipg-dl-btn">
                            <i data-lucide="download" width="13"></i> Download
                        </a>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="cipg-file-card">
                        <div class="cipg-file-icon" style="background:rgba(239,68,68,0.08);">
                            <i data-lucide="shield-check" width="20" style="color:#ef4444;"></i>
                        </div>
                        <div class="cipg-file-info">
                            <div class="cipg-file-name">RDC Resolution No. 4, s. 2025</div>
                            <div class="cipg-file-meta">PDF &middot; Official Endorsement Document</div>
                        </div>
                        <a href="#" class="btn btn-sm cipg-dl-btn">
                            <i data-lucide="download" width="13"></i> Download
                        </a>
                    </div>
                </div>

            </div>
        </div>
        </div>
    </div>

    <!-- ── SECTION 5: Frequently Asked Questions ── -->
    <div id="cipg-faq" class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-4">
                <span class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                    style="width:36px;height:36px;flex-shrink:0;">
                    <i data-lucide="help-circle" width="16" class="text-primary"></i>
                </span>
                <h5 class="fw-bold mb-0" style="font-size:1rem;">Frequently Asked Questions</h5>
            </div>

            <div class="accordion border-0" id="faqAccordion">

                <!-- FAQ 1 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h6 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold py-3 px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq1"
                            style="font-size:0.875rem; background: #fafafa;">
                            What is Comprehensive Investment Programming Guidelines (CIPG)?
                        </button>
                    </h6>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small py-3 px-4" style="line-height:1.7;">
                            <p class="mb-2">
                                The RDC Region 5 developed the CIPG to increase the number of projects being funded. The
                                overall framework of the CIPG aims to synchronize the planning, investment programming,
                                and budgeting processes. It provides the principles, processes, and requirements in the
                                identification and prioritization of PPAs for inclusion in investment programming
                                documents.
                            </p>
                            <p class="mb-0">
                                The CIPG guides the identification of priority programs, projects and activities (PPAs)
                                for inclusion in the Regional Development Investment Program (RDIP) and Agency
                                Investment Program (AIP). It also aims to: (1) present the processes and requirements
                                of the Bicol RDC for the inclusion of PPAs in the RDIP; (2) provide the criteria for
                                inclusion of PPAs in the RDIP; and (3) monitor the status of funding of PPAs in the
                                RDIP.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h6 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold py-3 px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq2"
                            style="font-size:0.875rem; background: #fafafa;">
                            What is Regional Development Investment Program (RDIP)?
                        </button>
                    </h6>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small py-3 px-4" style="line-height:1.7;">
                            The RDIP is a rolling document, thus submission of project proposals for inclusion in the
                            RDIP can be done anytime within the year. The RDIP is updated quarterly, as necessary, to
                            include new and expanded ongoing PPAs towards efficient and effective investment
                            programming and budgeting and to ensure consistency with the regional development
                            priorities.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h6 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold py-3 px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq3"
                            style="font-size:0.875rem; background: #fafafa;">
                            What are the documentary requirements for proposed programs, projects and activities (PPAs)
                            to be included in the RDIP?
                        </button>
                    </h6>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small py-3 px-4" style="line-height:1.7;">
                            <p class="mb-2">
                                For PPAs to be included in the RDIP, the following documents must be submitted by the
                                proponent agency:
                            </p>
                            <ul class="mb-0 ps-3">
                                <li>Official request for the project's inclusion in the RDIP.</li>
                                <li>
                                    Comprehensive Project Profile, including the accomplished sector-specific Gender
                                    and Development checklist of the Harmonized Gender and Development Guidelines
                                    (HGDG).
                                </li>
                                <li>
                                    Feasibility study for region-specific projects for Official Development Assistance
                                    (ODA) and Public-Private Partnership (PPP) funding; and
                                </li>
                                <li>
                                    Endorsement/s
                                    <ul class="mt-2 ps-3">
                                        <li>
                                            Sangguniang Panlalawigan resolution or ordinance approving the PPAs proposed
                                            by local development councils of the province, component cities, or
                                            municipalities.
                                        </li>
                                        <li>
                                            Pursuant to Section 56 (d) of the Local Government Code, the municipal
                                            ordinance or resolution approving the PPAs shall be considered a sufficient
                                            endorsement requirement and deemed valid if no action has been taken by the
                                            Sangguniang Panlalawigan within 30 days after submission of such ordinance
                                            or resolution, subject to submission of proof of transmittal to the
                                            Sangguniang Panlalawigan and confirmation once the review is completed.
                                        </li>
                                        <li>
                                            However, in cases when the municipal, city, or provincial legislative
                                            bodies fails to act to the request for endorsement of the proposed PPA/s,
                                            the proponent can submit copies of the letter request transmitted to the
                                            Sangguniang Panlalawigan and Sanggunian Bayan/Panlungsod, copy furnish the
                                            municipal/ city and provincial Local Development Councils requesting
                                            endorsement of the project. The letter request shall be considered as a
                                            sufficient requirement in lieu of the local endorsement if not acted by the
                                            concerned SP/SB within 30 calendar days from the receipt of the request.
                                        </li>
                                        <li>
                                            Sanggunian Panlungsod resolution or ordinance approving the PPAs proposed by
                                            local development councils of highly urbanized cities or independent
                                            component cities.
                                        </li>
                                        <li>
                                            Certification from the LGU planning office that the project is in their
                                            Local Development Investment Program as approved by their respective
                                            Sanggunian for national government projects that will be implemented within
                                            the jurisdiction of an LGU.
                                        </li>
                                        <li>
                                            Resolution of the Board of Trustees/ Regents endorsing the project and
                                            certifying that the project is in the Land Use Development and
                                            Infrastructure Plan (LUDIP) of the concerned SUC as approved by the Board
                                            of Trustees/ Regents in the case of infrastructure projects.
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h6 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold py-3 px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq4"
                            style="font-size:0.875rem; background: #fafafa;">
                            Can ALL types of projects be included in the RDIP?
                        </button>
                    </h6>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small py-3 px-4" style="line-height:1.7;">
                            <p class="mb-2">No. Projects should satisfy ALL the following criteria:</p>

                            <p class="mb-2"><strong class="text-dark">Typology of PPAs</strong></p>
                            <ul class="mb-3 ps-3">
                                <li>
                                    Capital investment PPAs to deliver public goods and services that contribute
                                    specifically to the region's productive capacity (e.g., infrastructure development
                                    projects, delivery of social services).
                                </li>
                                <li>Technical assistance, institutional development, human resource capacity building,
                                    or system/ process improvement PPAs.</li>
                                <li>Relending PPAs to LGUs or other target beneficiaries.</li>
                                <li>
                                    Government facilities that are part of the agencies' development strategies and
                                    contribute to the outcome and output targets contained in the RDP- Results Matrices
                                    (e.g., schools training centers, hospitals, research and development centers,
                                    community centers, environmental protection facilities, agricultural extension
                                    services, public housing projects, etc.).
                                </li>
                            </ul>

                            <p class="mb-2"><strong class="text-dark">Responsiveness</strong></p>
                            <ul class="mb-2 ps-3">
                                <li>Responsive to the Bicol RDP; and</li>
                                <li>
                                    Included in ANY of the following:
                                    <ul class="mt-2 ps-3">
                                        <li>National Expenditure Program</li>
                                        <li>Multi-Year Obligational Authority/ Multi-Year Contracting Authority</li>
                                        <li>Existing masterplan/ sector studies/ procurement plan</li>
                                        <li>List of RDC-endorsed NG PPAs</li>
                                        <li>Signed Agreements (e.g., Peace Agreements)</li>
                                        <li>Existing laws, rules, or regulations</li>
                                        <li>Regular programs except for infrastructure projects</li>
                                        <li>Regional Core PPAs</li>
                                        <li>PPAs contributing to Balik Probinsya Bagong Pag-asa Program</li>
                                        <li>
                                            Rehabilitation and Recovery Plans, mitigation of the spread of infectious
                                            diseases, alleviation/ elimination of insurgencies,
                                        </li>
                                    </ul>
                                </li>
                            </ul>

                            <p class="mb-2"><strong class="text-dark">Readiness</strong></p>
                            <p class="mb-2">With project preparation document as follows:</p>

                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th class="small text-muted fw-semibold" style="white-space:nowrap;">Level
                                                of Readiness</th>
                                            <th class="small text-muted fw-semibold">Status of Project Preparation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="small text-secondary">1</td>
                                            <td class="small text-secondary">With project preparation document completed
                                                <br>For implementation in the current Fiscal Year (FY)</td>
                                        </tr>
                                        <tr>
                                            <td class="small text-secondary">2</td>
                                            <td class="small text-secondary">With project preparation document completed
                                                <br>For inclusion in the NEP for the next</td>
                                        </tr>
                                        <tr>
                                            <td class="small text-secondary">3</td>
                                            <td class="small text-secondary">With project preparation document currently
                                                being prepared and to be completed in the current FY
                                                <br>and/ or for inclusion in the NEP for the succeeding FY</td>
                                        </tr>
                                        <tr>
                                            <td class="small text-secondary">4</td>
                                            <td class="small text-secondary">With project preparation document for
                                                completion in the next FY
                                                <br>and/ or for inclusion in the NEP for beyond FY of the current
                                                administration</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h6 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold py-3 px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq5"
                            style="font-size:0.875rem; background: #fafafa;">
                            What projects are excluded in the RDIP?
                        </button>
                    </h6>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small py-3 px-4" style="line-height:1.7;">
                            <p class="mb-2">The RDIP excludes the following:</p>
                            <ul class="mb-0 ps-3">
                                <li>
                                    Recurrent/non-recurrent spending for general administration and support to
                                    operations of agencies (e.g., standalone lease of office space,
                                    acquisition/procurement of supplies, equipment, and materials except those projects
                                    that are in line with the mandate/function of the agency, personnel services, etc.);
                                </li>
                                <li>Guarantee-related activities to private institutions;</li>
                                <li>PPAs to be financed purely from LGU funds and independent projects of the private
                                    sector;</li>
                                <li>Creation/establishment of an office or organizational unit, right-sizing and other
                                    reorganization-related activities;</li>
                                <li>
                                    Formulation/preparation of roadmap, masterplan, and ISSP of implementing agencies,
                                    including continuing or operating ICT expenses. However, priority PPAs in the
                                    aforementioned plans that are responsive to the PDP and its RMs should be included
                                    in the PIP;
                                </li>
                                <li>
                                    Stand-alone preparatory activities for infrastructure PPAs such as resettlement
                                    action plan, ROWA, pre-F/S, F/S and detailed engineering design, among others;
                                </li>
                                <li>
                                    Funding facilities managed by implementing agencies as part of their regular
                                    program/mandate (e.g., financing for project pre-investment activities, F/S Fund,
                                    Quick Response Fund);
                                </li>
                                <li>Acquisition of lots;</li>
                                <li>
                                    Disaggregated work items which should have been part or component of a bigger
                                    program or project such as construction, improvement, rehabilitation, restoration
                                    or maintenance of a single unit of a building/structure (e.g., office, room,
                                    elevator, basketball court, rappelling tower, etc.);
                                </li>
                                <li>
                                    Landscaping, site development, installation of perimeter fence or similar
                                    non-infrastructure items which may not contribute specifically to the country's
                                    productive capacity; and
                                </li>
                                <li>
                                    Government buildings that are non-developmental in nature (i.e., administrative
                                    buildings that do not directly provide service to or have no direct transactions
                                    with public and private clients, and are not responsive to the outcome and indicator
                                    statements in the PDP-RMs (e.g., building that solely provides office space to
                                    government personnel, and other facilities like canteen, gym or wellness area).
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h6 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold py-3 px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq6"
                            style="font-size:0.875rem; background: #fafafa;">
                            Who can submit proposed PPAs for RDIP inclusion?
                        </button>
                    </h6>
                    <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small py-3 px-4" style="line-height:1.7;">
                            <p class="mb-2">Proponents are classified into two (2): direct and indirect.</p>
                            <p class="mb-2">
                                Direct proponents are Regional Line Agencies (RLAs), State Universities and Colleges
                                (SUCs), Government-Owned and Controlled Corporations (GOCCs), and Government Finance
                                Institutions (GFIs) that can submit directly to the RDC Secretariat requests for RDC
                                approval to include PPAs in the RDIP.
                            </p>
                            <p class="mb-0">
                                Indirect proponents, on the other hand, refer to requesting RLAs, LGUs, and private
                                sector that need to submit the required documents to direct proponents for subsequent
                                requests for RDC approval to be included in the RDIP.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ 7 -->
                <div class="accordion-item border-0 mb-0 shadow-sm rounded-4 overflow-hidden">
                    <h6 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold py-3 px-4" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq7"
                            style="font-size:0.875rem; background: #fafafa;">
                            When should the agency submit the documents for RDIP inclusion?
                        </button>
                    </h6>
                    <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary small py-3 px-4" style="line-height:1.7;">
                            Since the RDIP is a rolling document, the agency can submit new PPAs anytime but must
                            observe the cut off period which is one month before the Sectoral Committee meeting.
                            Submission beyond the cut-off period means that the new PPAs will be considered in the next
                            update schedule. The timeline ensures a structured approach to reviewing and endorsing
                            agency budget proposals in alignment with regional development goals.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scoped styles -->
    <style>
        /* ── Step Timeline ── */
        .cipg-steps {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .cipg-step {
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
            padding-bottom: 1.75rem;
            position: relative;
        }

        /* Vertical connector line */
        .cipg-step:not(.cipg-step-last)::before {
            content: '';
            position: absolute;
            left: 17px;
            top: 36px;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .cipg-step-num {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #154A9A;
            color: #fff;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            box-shadow: 0 4px 10px rgba(21, 74, 154, 0.25);
        }

        .cipg-step-num-done {
            background: #10b981;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .cipg-step-body {
            flex: 1;
            padding-top: 0.4rem;
        }

        .cipg-step-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.3rem;
        }

        .cipg-step-desc {
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.7;
        }

        /* ── Framework Diagram (JointJS canvas) ── */
        .cipg-framework-canvas {
            width: 100%;
            height: 360px;
        }

        /* ── File Cards ── */
        .cipg-file-card {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 0.9rem 1rem;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            background: #fafafa;
            transition: all 0.18s ease;
        }

        .cipg-file-card:hover {
            border-color: #e2e8f0;
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .cipg-file-icon {
            flex-shrink: 0;
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cipg-file-info {
            flex: 1;
            min-width: 0;
        }

        .cipg-file-name {
            font-size: 0.855rem;
            font-weight: 600;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cipg-file-meta {
            font-size: 0.73rem;
            color: #94a3b8;
            margin-top: 0.1rem;
        }

        .cipg-dl-btn {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.75rem;
            font-weight: 600;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #154A9A;
            border-radius: 8px;
            padding: 0.4rem 0.75rem;
            transition: all 0.18s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .cipg-dl-btn:hover {
            background: #154A9A;
            border-color: #154A9A;
            color: #fff;
        }

        /* ── Collapse Icon Rotation ── */
        [data-bs-toggle="collapse"]:not(.collapsed) .collapse-icon {
            transform: rotate(180deg);
        }
    </style>

</section>
@endsection