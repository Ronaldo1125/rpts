@extends('layouts.app_v2')
@section('content')
    <link rel="stylesheet" href="/css/assessment.css">
    <section id="project-assessment-report-form" class="page-content active container-fluid py-4 text-dark">

        <div class="d-flex justify-content-between align-items-start mb-4 no-print">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge-soft-pill badge-soft-blue">FM-PDI-02 | PAR Form</span>
                    <span class="text-muted small">Programs, Activities and Projects for Inclusion in the RDIP</span>
                </div>
                <h2 class="fw-bold mb-0 text-dark fs-1-3">Project Assessment Report (PAR)</h2>
                <p class="text-muted small mb-0">Regional Development Council — Bicol Region</p>
            </div>
            <div class="d-flex gap-2 no-print">
                <button class="btn btn-outline-secondary btn-sm px-4 rounded-pill" onclick="window.print()">
                    <i data-lucide="printer" class="me-1" width="16"></i> Print Assessment
                </button>
                <button class="btn btn-sm px-4 rounded-pill fw-semibold text-white" id="btnDownloadParDocx"
                    style="background:#154A9A; border-color:#154A9A;">
                    <i data-lucide="file-down" class="me-1" width="16"></i> Download DOCX
                </button>
                <button class="btn-back-list" id="parFormBackToList">
                    <i data-lucide="chevron-left" width="16"></i> Back to List
                </button>
            </div>
        </div>


        <div class="card border-0 shadow-sm card-16">
            <div class="header-accent-blue"></div>
            <div class="card-body p-4 p-md-5">
                <!-- Source Linkage Info (Populated Dynamically) -->
                <div id="connected-validation-info"
                    class="alert alert-info border-0 shadow-sm mb-4 rounded-12 bg-soft-blue-o5">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                <i data-lucide="link" class="text-primary" width="20"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark fs-0-85">Comprehensive Project Profile: <span
                                        id="connected-source-title"
                                        class="text-primary italic">{{ $submission->project_title ?? 'Unknown' }}</span>
                                </h6>
                                <p class="mb-0 text-muted small">This assessment is linked to the submitted project profile.
                                </p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold fs-0-75"
                            id="viewSourceCppBtn" onclick="window.open('{{ route('v2.cipg_submissions.show', $submission->id) }}', '_blank')">
                            <i data-lucide="file-text" class="me-1" width="14"></i> View CPP
                        </button>
                    </div>
                </div>

                <form id="parFormMain" action="{{ isset($report) ? route('project-assessment-reports.update', $report->id) : route('project-assessment-reports.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf
                    @if(isset($report))
                        @method('PUT')
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <i data-lucide="alert-circle" width="24"></i>
                                <div class="fw-bold">Please correct the following errors:</div>
                            </div>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <input type="hidden" name="report_status" id="reportStatusInput" value="{{ $report->status ?? 'Draft' }}">
                    <input type="hidden" name="cpp_submission_id" value="{{ $submission->id }}">

                    <!-- Project Basic Info -->
                    <div class="row g-4 mb-5">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">1. Project Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="projectTitle" class="form-control"
                                placeholder="Enter full project title..." value="{{ $submission->project_title ?? '' }}" disabled>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-semibold text-secondary">2. Proponent / Implementing Agency
                                <span class="text-danger">*</span></label>
                            <select class="form-select" name="implementingAgency" id="implementingAgencySelect" disabled>
                                <option value="">-- Select Agency --</option>
                                @if(isset($submission->user->agency))
                                    <option value="{{ $submission->user->agency->agency_name }}" selected>
                                        {{ $submission->user->agency->agency_name }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary mb-2">3. Project Location <span
                                    class="text-danger">*</span></label>

                            <div class="mb-3">
                                <div class="d-flex gap-4" id="coverage-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="project-coverage"
                                            id="coverage-regionwide" value="Regionwide" {{ ($submission->project_coverage ?? '') === 'Regionwide' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label small fw-medium"
                                            for="coverage-regionwide">Regionwide</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="project-coverage"
                                            id="coverage-inter-province" value="Inter-Province" {{ ($submission->project_coverage ?? '') === 'Inter-Province' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label small fw-medium"
                                            for="coverage-inter-province">Inter-Province</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="project-coverage"
                                            id="coverage-location-specific" value="Location-Specific" {{ ($submission->project_coverage ?? '') === 'Location-Specific' ? 'checked' : '' }} disabled>
                                        <label class="form-check-label small fw-medium"
                                            for="coverage-location-specific">Location-Specific</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Specific Fields -->
                            <div id="location-specific-fields"
                                style="display: {{ ($submission->project_coverage ?? '') === 'Location-Specific' ? 'block' : 'none' }};"
                                class="mt-3">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-secondary">Province <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="f-province" name="province" disabled>
                                            <option value="">-- Select --</option>
                                            @php
                                                $provinces = ['Albay', 'Camarines Norte', 'Camarines Sur', 'Catanduanes', 'Masbate', 'Sorsogon'];
                                                $selectedProvince = $submission->locations->first()?->province?->province_name ?? '';
                                            @endphp
                                            @foreach($provinces as $prov)
                                                <option {{ $selectedProvince === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-secondary">District <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="f-district" name="district" disabled>
                                            <option value="">-- Select --</option>
                                            @if($submission->locations->first()?->district)
                                                <option selected>{{ $submission->locations->first()?->district?->district_name }}</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-secondary">City / Municipality <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="f-municipality" name="municipality" disabled>
                                            <option value="">-- Select --</option>
                                            @if($submission->locations->first()?->municipality)
                                                <option selected>{{ $submission->locations->first()?->municipality?->municipality_name }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold text-secondary">Barangay</label>
                                        <input type="text" class="form-control" name="barangay"
                                            placeholder="Enter Barangay..."
                                            value="{{ $submission->locations->first()?->barangay?->barangay_name ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Inter-Province Fields -->
                            <div id="inter-province-fields" style="display: none;" class="mt-3">
                                <label class="form-label small fw-semibold text-secondary mb-1">Provinces <span
                                        class="text-danger">*</span></label>
                                <div class="position-relative" id="provinces-tag-container">
                                    <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white min-h-38 pointer"
                                        id="f-provinces-box" tabindex="0">
                                        <input type="text"
                                            class="border-0 flex-grow-1 p-0 m-0 bg-transparent min-w-120 pointer caret-transparent"
                                            id="provinces-tag-input" placeholder="Select provinces..." autocomplete="off"
                                            disabled>
                                    </div>
                                    <ul class="dropdown-menu w-100 shadow-sm max-h-200 overflow-y-auto"
                                        id="provinces-dropdown">
                                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                data-prov="Albay"><input class="form-check-input mt-0 pe-none"
                                                    type="checkbox"> Albay</a></li>
                                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                data-prov="Camarines Norte"><input class="form-check-input mt-0 pe-none"
                                                    type="checkbox"> Camarines Norte</a></li>
                                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                data-prov="Camarines Sur"><input class="form-check-input mt-0 pe-none"
                                                    type="checkbox"> Camarines Sur</a></li>
                                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                data-prov="Catanduanes"><input class="form-check-input mt-0 pe-none"
                                                    type="checkbox"> Catanduanes</a></li>
                                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                data-prov="Masbate"><input class="form-check-input mt-0 pe-none"
                                                    type="checkbox"> Masbate</a></li>
                                        <li><a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                                data-prov="Sorsogon"><input class="form-check-input mt-0 pe-none"
                                                    type="checkbox"> Sorsogon</a></li>
                                    </ul>
                                    <input type="hidden" id="f-provinces" name="interProvince" value="" disabled>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- SECTION I -->
                    <div class="form-section-header">
                        <span class="form-section-num">I</span> Documentary requirements: (please place tick mark)
                    </div>
                    <div class="criteria-group">
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="doc_request" value="1" id="doc1_f" {{ (isset($report) && $report->doc_request) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="doc1_f">Official request for the project's
                                inclusion in the RDIP</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="doc_cpp_fs" value="1" id="doc2_f" {{ (isset($report) && $report->doc_cpp_fs) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="doc2_f">Comprehensive Project Profile /
                                Feasibility Study / Pre-Feasibility Study</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="doc_endorsements" value="1" id="doc3_f" {{ (isset($report) && $report->doc_endorsements) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="doc3_f">Endorsements (any of the following when
                                applicable)</label>

                            <div class="sub-criteria">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="endorsement_checks[]" value="SP Resolution" id="endo1_f" {{ (isset($report) && is_array($report->endorsement_data['checks'] ?? null) && in_array('SP Resolution', $report->endorsement_data['checks'])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="endo1_f">Sangguniang Panlalawigan Resolution
                                        approving the PAP: <input type="text" name="endo_sp_res_text"
                                            class="form-control d-inline-block border-0 border-bottom bg-transparent py-0 rounded-pill px-3 w-300px h-auto" value="{{ $report->endorsement_data['sp_res_text'] ?? '' }}"></label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="endorsement_checks[]" value="SB Resolution" id="endo2_f" {{ (isset($report) && is_array($report->endorsement_data['checks'] ?? null) && in_array('SB Resolution', $report->endorsement_data['checks'])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="endo2_f">Sangguniang Panlungsod resolution or
                                        ordinance approving the PAPs</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="endorsement_checks[]" value="Board Endorsement" id="endo3_f" {{ (isset($report) && is_array($report->endorsement_data['checks'] ?? null) && in_array('Board Endorsement', $report->endorsement_data['checks'])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="endo3_f">Endorsement of the Board of
                                        Trustees/Regents for projects to be implemented by state universities and
                                        colleges</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="endorsement_checks[]" value="Other" id="endo4_f" {{ (isset($report) && is_array($report->endorsement_data['checks'] ?? null) && in_array('Other', $report->endorsement_data['checks'])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="endo4_f">Other endorsements (pls specify) <input
                                            type="text" name="endo_other_text"
                                            class="form-control d-inline-block border-0 border-bottom bg-transparent py-0 rounded-pill px-3 w-300px h-auto" value="{{ $report->endorsement_data['other_text'] ?? '' }}"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION II -->
                    <div class="form-section-header">
                        <span class="form-section-num">II</span> Criteria for inclusion in the RDIP
                    </div>
                    <div class="criteria-group">
                        <h6 class="fw-bold text-dark mb-3">1. Typology</h6>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="typology_checks[]" value="Capital investment PAP" id="type1_f" {{ (isset($report) && is_array($report->typology_data) && in_array('Capital investment PAP', $report->typology_data)) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="type1_f">Capital investment PAP</label>
                            <div class="sub-criteria">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="typology_checks[]" value="ICT PAPs" id="type1a_f" {{ (isset($report) && is_array($report->typology_data) && in_array('ICT PAPs', $report->typology_data)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type1a_f">For ICT PAPs, capital outlay components
                                        of the Information Systems Strategic Plan of the agency</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="typology_checks[]" value="Culture PAPs" id="type1b_f" {{ (isset($report) && is_array($report->typology_data) && in_array('Culture PAPs', $report->typology_data)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type1b_f">For culture PAPs, capital outlay
                                        components are required for the conservation of cultural properties as defined by RA
                                        10066, S. 2009 or at the National Cultural Heritage Act of 2009</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="typology_checks[]" value="Resiliency" id="type1c_f" {{ (isset($report) && is_array($report->typology_data) && in_array('Resiliency', $report->typology_data)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type1c_f">Resiliency to withstand natural
                                        calamities is factored into infrastructure capital investments</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="typology_checks[]" value="Pre-investment" id="type1d_f" {{ (isset($report) && is_array($report->typology_data) && in_array('Pre-investment', $report->typology_data)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type1d_f">Requirements for pre-investment
                                        activities (e.g., master plans, F.S., etc.) must be undertaken</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="typology_checks[]" value="Timelines and costs" id="type1e_f" {{ (isset($report) && is_array($report->typology_data) && in_array('Timelines and costs', $report->typology_data)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type1e_f">Timelines and costs on the right of way,
                                        resettlement shall be included in the project cost</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="typology_checks[]" value="Technical assistance" id="type2_f" {{ (isset($report) && is_array($report->typology_data) && in_array('Technical assistance', $report->typology_data)) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="type2_f">Technical assistance, institutional
                                development, human resource capacity building or system/process improvement PAPs</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="typology_checks[]" value="Relending PAPs" id="type3_f" {{ (isset($report) && is_array($report->typology_data) && in_array('Relending PAPs', $report->typology_data)) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="type3_f">Relending PAPs to LGUs or other target
                                beneficiaries</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="typology_checks[]" value="Government facilities" id="type4_f" {{ (isset($report) && is_array($report->typology_data) && in_array('Government facilities', $report->typology_data)) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="type4_f">Government facilities that are part of
                                the agency's development strategies and contribute to the outcome and output targets
                                contained in the RDP-Results Matrices</label>
                        </div>

                        <h6 class="fw-bold text-dark mt-4 mb-3">2. Responsiveness</h6>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Bicol RDP" id="resp1_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Bicol RDP', $report->responsiveness_data)) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="resp1_f">Responsiveness to the Bicol
                                RDP</label>
                        </div>
                        <div class="form-check criteria-item">
                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Included" id="resp2_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Included', $report->responsiveness_data)) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="resp2_f">Included in any of the
                                following:</label>
                        <div class="sub-criteria">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="NEP" id="inc1_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('NEP', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc1_f">National Expenditure Program</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="MYOA" id="inc2_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('MYOA', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc2_f">Multi-Year Obligational Authority / Multi-Year Contracting Authority</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Masterplan" id="inc3_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Masterplan', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc3_f">Existing masterplan/sector studies/procurement plan</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="RDC-endorsed" id="inc4_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('RDC-endorsed', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc4_f">List of RDC-endorsed NG PAPs</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Signed agreements" id="inc5_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Signed agreements', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc5_f">Signed agreements (e.g., peace agreements, etc.)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Laws" id="inc6_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Laws', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc6_f">Existing laws, rules and regulations</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Regional programs" id="inc7_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Regional programs', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc7_f">Regional programs (e.g., HFEP, PAMANA)</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Balik Probinsya" id="inc8_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Balik Probinsya', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc8_f">Balik Probinsya Bagong Pag-asa Program</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Recovery Program" id="inc9_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Recovery Program', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc9_f">Regional Recovery Program</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="responsiveness_checks[]" value="Other RDC" id="inc10_f" {{ (isset($report) && is_array($report->responsiveness_data) && in_array('Other RDC', $report->responsiveness_data)) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="inc10_f">Other programs endorsed by the RDC.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <h6 class="fw-bold text-dark mt-4 mb-3">3. Readiness</h6>
                        <div class="sub-criteria ms-0">
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="Completed preparation" id="ready1_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('Completed preparation', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready1_f">With completed project preparation
                                    documents</label>
                            </div>
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="NEP next year" id="ready2_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('NEP next year', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready2_f">For inclusion in the NEP for the next fiscal
                                    year</label>
                            </div>
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="Preparing documents" id="ready3_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('Preparing documents', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready3_f">With project preparation document currently
                                    being prepared and to be completed in the current fiscal year</label>
                            </div>
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="NEP succeeding year" id="ready4_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('NEP succeeding year', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready4_f">For inclusion in the NEP for the succeeding
                                    fiscal year</label>
                            </div>
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="Documents next year" id="ready5_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('Documents next year', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready5_f">With project preparation documents for
                                    completion in the next fiscal year</label>
                            </div>
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="NEP beyond" id="ready6_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('NEP beyond', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready6_f">For inclusion in the NEP for beyond fiscal
                                    year of the current administration</label>
                            </div>
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="Completed ROW/RAP" id="ready7_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('Completed ROW/RAP', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready7_f">With completed Right of Way acquisition and
                                    Resettlement Action Plan (when applicable)</label>
                            </div>
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="Ongoing ROW/RAP" id="ready8_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('Ongoing ROW/RAP', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready8_f">With ongoing Right of Way acquisition and
                                    Resettlement Action Plan (when applicable)</label>
                            </div>
                            <div class="form-check criteria-item">
                                <input class="form-check-input" type="checkbox" name="readiness_checks[]" value="Without ROW/RAP" id="ready9_f" {{ (isset($report) && is_array($report->readiness_data) && in_array('Without ROW/RAP', $report->readiness_data)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ready9_f">Without Right of Way acquisition and
                                    Resettlement Action Plan (when applicable)</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION III -->
                    <div class="form-section-header">
                        <span class="form-section-num">III</span> Assessment of the PAP
                    </div>
                    <div class="criteria-group">
                        <h6 class="fw-bold text-dark mb-3">1. Brief of the PAP</h6>
                        <div class="mb-4">
                            <label class="form-label text-secondary">A. Background</label>
                            <textarea class="form-control" name="par_background" rows="4"
                                placeholder="Detail the project background and rationale...">{{ $report->par_background ?? '' }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-secondary">B. Project components, Cost and Financing, and
                                Implementation Schedule</label>
                            <textarea class="form-control" name="par_components" rows="4"
                                placeholder="Detail the components, budget, and timeline...">{{ $report->par_components ?? '' }}</textarea>
                        </div>

                        <h6 class="fw-bold text-dark mt-4 mb-3">2. Assessment</h6>
                        <div class="mb-4">
                            <label class="form-label text-secondary">A. Project's Regional and Spatial Context</label>
                            <textarea class="form-control" name="par_spatial" rows="4"
                                placeholder="Assess spatial alignment and regional impact...">{{ $report->par_spatial ?? '' }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-secondary">B. Qualitative Technical, Market, Economic, Social, and
                                Environmental Evaluation</label>
                            <textarea class="form-control" name="par_qualitative" rows="4"
                                placeholder="Provide multi-dimensional qualitative assessment...">{{ $report->par_qualitative ?? '' }}</textarea>
                        </div>

                        <h6 class="fw-bold text-dark mt-4 mb-3">3. Recommendations</h6>
                        <div class="mb-4">
                            <textarea class="form-control" name="par_recommendations" rows="3"
                                placeholder="Enter initial recommendations...">{{ $report->par_recommendations ?? '' }}</textarea>
                        </div>

                        <h6 class="fw-bold text-dark mt-4 mb-3">4. Final Recommendations</h6>
                        <div class="mb-4">
                            <textarea class="form-control border-primary bg-soft-blue-o2 rounded-12" name="par_final_recs"
                                rows="4" placeholder="Enter final consolidated recommendation for RDC...">{{ $report->final_recommendation ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- SECTION IV: Annex A -->
                    <div class="form-section-header mt-5">
                        <span class="form-section-num">IV</span> Annex A: Project Summary & Budgetary Requirements
                    </div>
                    <!-- Single Project Container -->
                    <div id="parSingleProjectContainer" class="mb-4 mt-3">
                        <div class="border rounded-3 overflow-hidden shadow-sm p-4 bg-white">
                            <!-- Brief Description -->
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary">Brief Description</label>
                                <textarea class="form-control" name="annex_desc" rows="3"
                                    placeholder="Provide a summary of project scope and objectives...">{{ $report->annex_description ?? '' }}</textarea>
                            </div>

                            <!-- Budgetary Requirements Table -->
                            <label class="form-label small fw-semibold text-secondary mb-2">Budgetary Requirements (in
                                Million)</label>
                            <div class="table-responsive border rounded-3 overflow-hidden mb-0">
                                <table class="table table-bordered table-sm align-middle mb-0 text-center"
                                    style="font-size: 0.75rem; min-width: 600px;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-2">2023</th>
                                            <th class="py-2">2024</th>
                                            <th class="py-2">2025</th>
                                            <th class="py-2">2026</th>
                                            <th class="py-2">2027</th>
                                            <th class="py-2">2028</th>
                                            <th class="py-2 bg-info-subtle">TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="budgetRowAnnex">
                                            <td class="p-0"><input type="number" step="0.01"
                                                    class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2"
                                                    name="annex_2023" placeholder="0.00" value="{{ $report->budget_breakdown['2023'] ?? 0 }}"></td>
                                            <td class="p-0"><input type="number" step="0.01"
                                                    class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2"
                                                    name="annex_2024" placeholder="0.00" value="{{ $report->budget_breakdown['2024'] ?? 0 }}"></td>
                                            <td class="p-0"><input type="number" step="0.01"
                                                    class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2"
                                                    name="annex_2025" placeholder="0.00" value="{{ $report->budget_breakdown['2025'] ?? 0 }}"></td>
                                            <td class="p-0"><input type="number" step="0.01"
                                                    class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2"
                                                    name="annex_2026" placeholder="0.00" value="{{ $report->budget_breakdown['2026'] ?? 0 }}"></td>
                                            <td class="p-0"><input type="number" step="0.01"
                                                    class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2"
                                                    name="annex_2027" placeholder="0.00" value="{{ $report->budget_breakdown['2027'] ?? 0 }}"></td>
                                            <td class="p-0"><input type="number" step="0.01"
                                                    class="form-control form-control-sm border-0 bg-transparent text-center budgetary-year-input py-2"
                                                    name="annex_2028" placeholder="0.00" value="{{ $report->budget_breakdown['2028'] ?? 0 }}"></td>
                                            <td class="p-0 bg-info-subtle"><input type="number" step="0.01"
                                                    class="form-control form-control-sm border-0 bg-transparent text-center fw-bold project-total-input py-2"
                                                    name="annex_total" placeholder="0.00" value="{{ $report->total_project_cost ?? 0 }}" readonly></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Signatories -->
                    <div class="row g-4 mt-2 mb-5">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Prepared by:</label>
                            <input type="text" name="prepared_by" class="form-control form-control-sm mb-1"
                                placeholder="Name" value="{{ $report->prepared_by ?? '' }}">
                            <input type="text" name="prepared_by_pos" class="form-control form-control-sm"
                                placeholder="Designation" value="{{ $report->prepared_by_pos ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Reviewed by:</label>
                            <input type="text" name="reviewed_by" class="form-control form-control-sm mb-1"
                                placeholder="Name" value="{{ $report->reviewed_by ?? '' }}">
                            <input type="text" name="reviewed_by_pos" class="form-control form-control-sm"
                                placeholder="Designation" value="{{ $report->reviewed_by_pos ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-secondary">Approved by:</label>
                            <input type="text" name="approved_by" class="form-control form-control-sm mb-1"
                                placeholder="Name" value="{{ $report->approved_by ?? '' }}">
                            <input type="text" name="approved_by_pos" class="form-control form-control-sm"
                                placeholder="Designation" value="{{ $report->approved_by_pos ?? '' }}">
                        </div>
                    </div>


                    <!-- Right-Center Floating Action Buttons (Icon Only) -->
                    <div class="assessment-action-bar no-print">
                        <button type="button" class="btn-status-save btn-comments-fab" id="btnOpenCommentsModal" title="Findings & Recommendations"
                            data-bs-toggle="modal" data-bs-target="#parCommentsModal">
                            <i data-lucide="message-square" width="20" height="20"></i>
                            <span>Comments</span>
                        </button>

                        @if(Auth::user()->hasRole('staff') && Auth::user()->division?->name !== 'PDIPBD')
                            <button type="button" class="btn-status-save btn-evaluated" id="btnSaveEvaluated">
                                <i data-lucide="clock" width="20" height="20"></i>
                                <span>Save as Assessed</span>
                            </button>
                        @endif

                        @if(Auth::user()->hasRole(['division_chief', 'chief', 'division_head']))
                            <button type="button" class="btn-status-save btn-approved" id="btnSaveApproved">
                                <i data-lucide="check-circle" width="20" height="20"></i>
                                <span>Save as Evaluated</span>
                            </button>
                        @endif

                        @if(Auth::user()->hasRole('staff') && Auth::user()->division?->name === 'PDIPBD')
                            <button type="button" class="btn-status-save btn-reviewed" id="btnSaveReviewed">
                                <i data-lucide="clipboard-check" width="20" height="20"></i>
                                <span>Save as Reviewed</span>
                            </button>
                        @endif

                        @if(Auth::user()->hasRole(['admin', 'administrator']))
                            <button type="button" class="btn-status-save btn-final" id="btnSaveFinal"
                                data-bs-toggle="modal" data-bs-target="#uploadFinalParModal"
                                style="background:#059669;">
                                <i data-lucide="check-square" width="20" height="20"></i>
                                <span>Save as Final</span>
                            </button>
                        @endif
                    </div>

                    {{-- Hidden container: findings/recommendations are synced here from the modal before submit --}}
                    <div id="parFindingsHiddenContainer" style="display:none;"></div>

    <!-- FINDINGS & RECOMMENDATIONS MODAL -->

    <div class="modal fade no-print" id="parCommentsModal" tabindex="-1" aria-labelledby="parCommentsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow card-16">
                <div class="header-accent-blue" style="height: 6px; background: #154A9A;"></div>
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="parCommentsModalLabel">Findings & Recommendations</h5>
                        <p class="text-muted small mb-0">Record technical observations and required actions for this
                            evaluation.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="parFindingsContainer" class="d-flex flex-column gap-3">
                        @if(isset($comments) && $comments->count() > 0)
                            @foreach($comments as $comment)
                                <div class="finding-row border rounded-3 p-3 bg-light position-relative">
                                    @if(!$loop->first)
                                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-remove-finding" style="font-size: 0.7rem;"></button>
                                    @endif
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary mb-1">Finding / Observation</label>
                                            <textarea class="form-control form-control-sm" name="findings[]" rows="2" placeholder="Describe what was found..." required>{{ $comment->finding }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-secondary mb-1">Recommendation / Action Required</label>
                                            <textarea class="form-control form-control-sm" name="recommendations[]" rows="2" placeholder="Describe the recommended action..." required>{{ $comment->recommendation }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Default Row (Mandatory) -->
                            <div class="finding-row border rounded-3 p-3 bg-light position-relative">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-secondary mb-1">Finding / Observation</label>
                                        <textarea class="form-control form-control-sm" name="findings[]" rows="2" placeholder="Describe what was found..." required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-secondary mb-1">Recommendation / Action Required</label>
                                        <textarea class="form-control form-control-sm" name="recommendations[]" rows="2" placeholder="Describe the recommended action..." required></textarea>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4"
                            id="btnAddFindingRow">
                            <i data-lucide="plus-circle" width="16" class="me-2"></i> Add New Finding
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 d-flex justify-content-between align-items-center">
                    <div class="text-muted small" id="parCommentsDateStatus"></div>
                    <div>
                        <button type="button" class="btn btn-light rounded-pill px-4 text-secondary fw-bold"
                            data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold"
                            id="btnSaveCommentsOnly" data-bs-dismiss="modal">
                            <i data-lucide="check-circle" width="18" class="me-2"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UPLOAD FINAL PAR MODAL -->
    <div class="modal fade no-print" id="uploadFinalParModal" tabindex="-1" aria-labelledby="uploadFinalParModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="header-accent-green" style="height: 6px; background: #059669;"></div>
                <div class="modal-header border-0 pt-4 px-4 pb-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10"
                            style="width: 48px; height: 48px;">
                            <i data-lucide="upload-cloud" class="text-success" width="24"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="uploadFinalParModalLabel">Final Technical
                                Report</h5>
                            <p class="text-muted small mb-0">Upload the scanned or digital final document</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="upload-zone p-4 rounded-4 text-center mb-3"
                        style="border: 2px dashed #e2e8f0; background: #f8fafc; cursor: pointer;"
                        onclick="document.getElementById('finalParFileInput').click()">
                        <i data-lucide="file-text" width="32" class="text-muted mb-2"></i>
                        <p class="small fw-semibold text-dark mb-1">Click to browse or drag & drop</p>
                        <p class="x-small text-muted mb-0">PDF or DOCX files accepted (Max 10MB)</p>
                        <input type="file" id="finalParFileInput" name="final_par_file" class="d-none" accept=".pdf,.doc,.docx">
                    </div>

                    <div id="finalParFileNameDisplay"
                        class="alert alert-success d-none py-2 px-3 rounded-3 small border-0 shadow-sm align-items-center gap-2">
                        <i data-lucide="file-check" width="16"></i>
                        <span class="text-truncate flex-grow-1" id="finalParNameSpan"></span>
                        <i data-lucide="x" width="16" style="cursor: pointer;" id="btnRemoveFinalParFile"></i>
                    </div>

                    <div class="mt-4 p-3 rounded-4 d-flex align-items-center justify-content-between" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                        <div>
                            <label class="small fw-bold text-dark mb-0 d-block" for="finalParSectoralCheckbox" style="cursor: pointer;">For Sectoral Presentation</label>
                            <p class="mb-0 text-muted" style="font-size: 0.7rem;">Flag this report for the next SecCom meeting</p>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" id="finalParSectoralCheckbox" name="is_sectoral"
                                style="width: 38px; height: 20px; cursor: pointer; margin-left: 0;">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="small fw-bold text-dark mb-2 d-block">Completion Notes</label>
                        <textarea id="finalParNotes" name="finalization_notes" class="form-control border-0 bg-light rounded-3 small" rows="3"
                            style="resize: none;" placeholder="Provide any final remarks regarding this PAR..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 flex-grow-1 fw-bold text-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success rounded-pill px-4 flex-grow-1 fw-bold shadow-sm"
                        id="btnConfirmFinalizePar" style="background: #059669; border: none;">
                        <i data-lucide="check-circle" width="18" class="me-2"></i> Finalize PAR
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const findingsContainer = document.getElementById('parFindingsContainer');
    const addFindingBtn = document.getElementById('btnAddFindingRow');

    if (addFindingBtn) {
        addFindingBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'finding-row border rounded-3 p-3 bg-light position-relative';
            newRow.innerHTML = `
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 btn-remove-finding" style="font-size: 0.7rem;"></button>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-secondary mb-1">Finding / Observation</label>
                        <textarea class="form-control form-control-sm" name="findings[]" rows="2" placeholder="Describe what was found..."></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-secondary mb-1">Recommendation / Action Required</label>
                        <textarea class="form-control form-control-sm" name="recommendations[]" rows="2" placeholder="Describe the recommended action..."></textarea>
                    </div>
                </div>
            `;
            findingsContainer.appendChild(newRow);
            
            // Re-initialize Lucide icons if any
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    }

    // Handle removal of rows using delegation
    findingsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-finding')) {
            const rows = findingsContainer.querySelectorAll('.finding-row');
            if (rows.length > 1) {
                e.target.closest('.finding-row').remove();
            } else {
                if (window.showSimpleAlert) {
                    window.showSimpleAlert('At least one finding row is required.', 'warning');
                } else {
                    alert('At least one finding row is required.');
                }
            }
        }
    });

    // Handle Status Button Clicks
    const form = document.getElementById('parFormMain');
    const statusInput = document.getElementById('reportStatusInput');

    const statusMap = {
        'btnSaveEvaluated': 'Assessed',
        'btnSaveApproved': 'Evaluated',
        'btnSaveReviewed': 'Reviewed',
        'btnSaveCommittee': 'SecCom',
        'btnSaveFinal': 'Final'
    };

    /**
     * Sync findings & recommendations from the modal into hidden inputs inside the form.
     * Bootstrap moves modals outside the <form> in the DOM, so we must do this before submit.
     */
    function syncFindingsToForm() {
        const hiddenContainer = document.getElementById('parFindingsHiddenContainer');
        if (!hiddenContainer) return;

        // Clear previous hidden inputs
        hiddenContainer.innerHTML = '';

        // Read all rows from the modal (Bootstrap may have moved it outside the <form>)
        const modalContainer = document.getElementById('parFindingsContainer');
        if (!modalContainer) return;

        const findingTextareas = modalContainer.querySelectorAll('textarea[name="findings[]"]');
        const recTextareas     = modalContainer.querySelectorAll('textarea[name="recommendations[]"]');

        findingTextareas.forEach(function(ta, i) {
            const fInput = document.createElement('input');
            fInput.type  = 'hidden';
            fInput.name  = 'findings[]';
            fInput.value = ta.value;
            hiddenContainer.appendChild(fInput);

            const rInput = document.createElement('input');
            rInput.type  = 'hidden';
            rInput.name  = 'recommendations[]';
            rInput.value = recTextareas[i] ? recTextareas[i].value : '';
            hiddenContainer.appendChild(rInput);
        });

        // Remove name attributes from the modal textareas so they are NOT
        // double-submitted alongside the hidden inputs above.
        findingTextareas.forEach(ta => ta.removeAttribute('name'));
        recTextareas.forEach(ta => ta.removeAttribute('name'));
    }

    Object.keys(statusMap).forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.addEventListener('click', function() {
                statusInput.value = statusMap[btnId];

                if (btnId === 'btnSaveFinal') {
                    return;
                }

                if (window.showConfirmModal) {
                    window.showConfirmModal({
                        title: 'Confirm Save',
                        message: `Are you sure you want to save this report as ${statusMap[btnId]}?`,
                        confirmClass: btnId === 'btnSaveApproved' ? 'btn-success' : 'btn-primary',
                        onConfirm: () => {
                            syncFindingsToForm();
                            form.submit();
                        }
                    });
                } else {
                    if (confirm(`Are you sure you want to save this report as ${statusMap[btnId]}?`)) {
                        syncFindingsToForm();
                        form.submit();
                    }
                }
            });
        }
    });

    // Budget Auto-calculation
    const budgetInputs = document.querySelectorAll('.budgetary-year-input');
    const totalInput = document.querySelector('.project-total-input');

    function calculateTotal() {
        let total = 0;
        budgetInputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            total += val;
        });
        
        if (totalInput) {
            totalInput.value = total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    budgetInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Initial calculation if editing
    calculateTotal();

    // Handle Finalize Confirmation in Modal
    const btnConfirmFinalizePar = document.getElementById('btnConfirmFinalizePar');
    if (btnConfirmFinalizePar) {
        btnConfirmFinalizePar.addEventListener('click', function() {
            statusInput.value = 'Final';
            syncFindingsToForm();
            form.submit();
        });
    }

    // Auto-toggle Sectoral Presentation if no comments exist
    const uploadFinalParModal = document.getElementById('uploadFinalParModal');
    if (uploadFinalParModal) {
        uploadFinalParModal.addEventListener('show.bs.modal', function () {
            const findingsContainer = document.getElementById('parFindingsContainer');
            const findingTextareas = findingsContainer ? findingsContainer.querySelectorAll('textarea') : [];
            
            let hasComments = false;
            findingTextareas.forEach(ta => {
                if (ta.value.trim().length > 0) {
                    hasComments = true;
                }
            });

            const sectoralSwitch = document.getElementById('finalParSectoralCheckbox');
            if (sectoralSwitch && !hasComments) {
                sectoralSwitch.checked = true;
            }
        });
    }

    // Final PAR File Selection UX
    const finalParInput = document.getElementById('finalParFileInput');
    const finalParDisplay = document.getElementById('finalParFileNameDisplay');
    const finalParNameSpan = document.getElementById('finalParNameSpan');
    const btnRemoveFile = document.getElementById('btnRemoveFinalParFile');

    if (finalParInput) {
        finalParInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                finalParNameSpan.textContent = this.files[0].name;
                finalParDisplay.classList.remove('d-none');
                finalParDisplay.classList.add('d-flex');
            }
        });
    }

    if (btnRemoveFile) {
        btnRemoveFile.addEventListener('click', function(e) {
            e.stopPropagation();
            finalParInput.value = '';
            finalParDisplay.classList.remove('d-flex');
            finalParDisplay.classList.add('d-none');
        });
    }
});
</script>

@endsection