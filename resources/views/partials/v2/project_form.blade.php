<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-primary text-uppercase smaller ls-1 mb-4">Project Information</h6>

        <div class="mb-3">
            <label class="form-label fw-medium smaller text-dark">Project Title <span class="text-danger">*</span></label>
            <input type="text" name="project_title" class="form-control @error('project_title') is-invalid @enderror" 
                value="{{ old('project_title', $project->project_title ?? '') }}" placeholder="Enter project title" required {{ $isReadOnly ? 'disabled' : '' }}>
            @error('project_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-medium smaller text-dark">Project Description <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" 
                placeholder="Enter project description" required {{ $isReadOnly ? 'disabled' : '' }}>{{ old('description', $project->description ?? '') }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
             <div class="d-flex justify-content-between align-items-center mb-1">
                 <label class="form-label fw-medium smaller text-dark mb-0">Indicators <span class="text-danger">*</span></label>
                 @if(!$isReadOnly)
                 @can('indicator-create')
                 <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 d-flex align-items-center gap-1" style="font-size: 0.7rem;" onclick="openIndicatorModal()">
                     <i data-lucide="plus" width="12" height="12"></i> Add New
                 </button>
                 @endcan
                 @endif
             </div>
             <div class="position-relative" id="indicators-tag-container">
                 <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white min-h-38 shadow-sm {{ $isReadOnly ? '' : 'cursor-pointer' }}" id="f-indicators-box" tabindex="0">
                     @php
                         $selectedIndicators = isset($project) 
                             ? $project->project_indicator->pluck('indicator_id')->toArray() 
                             : [];
                         $selectedIndicatorNames = isset($project)
                             ? collect($indicators)->filter(function($name, $id) use ($selectedIndicators) {
                                 return in_array($id, $selectedIndicators);
                               })
                             : collect();
                     @endphp
                     <span class="text-muted smaller ms-1 no-selection {{ count($selectedIndicators) > 0 ? 'd-none' : '' }}">Select indicators...</span>
                     <div id="selected-indicators-tags" class="d-flex flex-wrap gap-1">
                         @foreach($selectedIndicatorNames as $id => $name)
                         <span class="badge bg-primary d-flex align-items-center gap-1 fw-medium smaller py-1 px-2 rounded-pill" title="{{ $name }}">
                             <span class="text-truncate" style="max-width: 450px;">{{ $name }}</span>
                             @if(!$isReadOnly)<i data-lucide="x" width="12" height="12" class="cursor-pointer remove-tag" data-id="{{ $id }}"></i>@endif
                         </span>
                         @endforeach
                     </div>
                     <input type="text" class="border-0 flex-grow-1 p-0 m-0 bg-transparent visually-hidden" id="indicators-tag-input" readonly>
                 </div>
                 <ul class="dropdown-menu w-100 shadow-sm mt-1" id="indicators-dropdown" style="max-height: 250px; overflow-y: auto; overflow-x: auto;">
                     @foreach($indicators as $id => $name)
                     <li>
                         <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $id }}" data-name="{{ $name }}">
                             <div class="d-flex align-items-center gap-2">
                                 <input class="form-check-input mt-0 pe-none" type="checkbox" {{ in_array($id, $selectedIndicators) ? 'checked' : '' }}>
                                 <span class="smaller" style="white-space: nowrap;">{{ $name }}</span>
                             </div>
                         </a>
                     </li>
                     @endforeach
                 </ul>
                 <div id="indicators-hidden-inputs">
                     @foreach($selectedIndicators as $iId)
                         <input type="hidden" name="indicators[]" value="{{ $iId }}">
                     @endforeach
                 </div>
                 @error('indicators')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
             </div>
        </div>

        <div class="row g-3 mb-4">
             <div class="col-md-3">
                 <label class="form-label fw-medium smaller text-dark">Agency <span class="text-danger">*</span></label>
                 <div class="position-relative custom-select-container">
                     @php
                         $currentAgency = old('agency_id', isset($project) ? $project->agency_id : '');
                         $agencyLabel = isset($agencies[$currentAgency]) ? $agencies[$currentAgency] : '-- Select --';
                     @endphp
                     <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="agency-display-box" tabindex="0">
                         <span class="text-truncate" id="agency-display-text">{{ $agencyLabel }}</span>
                         <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                     </div>
                     <ul class="dropdown-menu w-100 shadow-sm mt-1" id="agency-dropdown" style="max-height: 250px; overflow-y: auto;">
                         @foreach($agencies as $id => $acronym)
                         <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $id }}" data-name="{{ $acronym }}">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0 pe-none" type="radio" name="_agency_radio" {{ $currentAgency == $id ? 'checked' : '' }}>
                                    <span class="smaller">{{ $acronym }}</span>
                                </div>
                            </a>
                         </li>
                         @endforeach
                     </ul>
                     <input type="hidden" name="agency_id" id="agency_id" value="{{ $currentAgency }}" required>
                 </div>
                 @error('agency_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
             </div>
              <div class="col-md-4">
                 <label class="form-label fw-medium smaller text-dark">Sector <span class="text-danger">*</span></label>
                 <div class="position-relative custom-select-container">
                     @php
                         $currentSector = old('sector_id', isset($project) ? ($project->project_sector->sector_id ?? '') : '');
                         $sectorLabel = isset($sectors[$currentSector]) ? $sectors[$currentSector] : '-- Select --';
                     @endphp
                     <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="sector-display-box" tabindex="0">
                         <span class="text-truncate" id="sector-display-text">{{ $sectorLabel }}</span>
                         <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                     </div>
                     <ul class="dropdown-menu w-100 shadow-sm mt-1" id="sector-dropdown" style="max-height: 250px; overflow-y: auto;">
                         @foreach($sectors as $id => $name)
                         <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $id }}" data-name="{{ $name }}">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0 pe-none" type="radio" name="_sector_radio" {{ $currentSector == $id ? 'checked' : '' }}>
                                    <span class="smaller">{{ $name }}</span>
                                </div>
                            </a>
                         </li>
                         @endforeach
                     </ul>
                     <input type="hidden" name="sector_id" id="sector_id" value="{{ $currentSector }}" required>
                 </div>
                 @error('sector_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
             </div>
             <div class="col-md-5">
                <label class="form-label fw-medium smaller text-dark">Sub Sector <span class="text-danger">*</span></label>
                <div class="position-relative custom-select-container">
                    @php
                        $currentSubSector = old('sub_sector_id', isset($project) ? ($project->project_sector->sub_sector_id ?? '') : '');
                        $subSectorLabel = '-- Select --';
                        // Note: Sub-sector labels are loaded via JS, but we can set the initial one if we have it
                        if(isset($project->project_sector->sub_sector)) $subSectorLabel = $project->project_sector->sub_sector->subsector_name;
                    @endphp
                    <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="subsector-display-box" tabindex="0">
                        <span class="text-truncate" id="subsector-display-text">{{ $subSectorLabel }}</span>
                        <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                    </div>
                    <ul class="dropdown-menu w-100 shadow-sm mt-1" id="subsector-dropdown" style="max-height: 250px; overflow-y: auto;">
                        <li><a class="dropdown-item smaller text-muted" href="#">Loading...</a></li>
                    </ul>
                    <input type="hidden" name="sub_sector_id" id="sub_sector_id" value="{{ $currentSubSector }}" required>
                </div>
                @error('sub_sector_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                 <label class="form-label fw-medium smaller text-dark">Status <span class="text-danger">*</span></label>
                 <div class="position-relative custom-select-container">
                     @php
                         $currentStatus = old('status', isset($project) ? $project->getRawOriginal('status') : '');
                         $statusLabel = '-- Select --';
                         foreach($statuses as $s) if($s->value == $currentStatus) $statusLabel = $s->label();
                     @endphp
                     <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="status-display-box" tabindex="0">
                         <span class="text-truncate" id="status-display-text">{{ $statusLabel }}</span>
                         <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                     </div>
                     <ul class="dropdown-menu w-100 shadow-sm mt-1" id="status-dropdown" style="max-height: 250px; overflow-y: auto;">
                         @foreach($statuses as $status)
                         <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $status->value }}" data-name="{{ $status->label() }}">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0 pe-none" type="radio" name="_status_radio" {{ ($currentStatus == $status->value) ? 'checked' : '' }}>
                                    <span class="smaller">{{ $status->label() }}</span>
                                </div>
                            </a>
                         </li>
                         @endforeach
                     </ul>
                     <input type="hidden" name="status" id="status" value="{{ $currentStatus }}" required>
                 </div>
                 @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                 <label class="form-label fw-medium smaller text-dark">Funding Requirement <span class="text-danger">*</span></label>
                 <input type="text" name="funding_requirement" class="form-control" placeholder="Enter amount" 
                    value="{{ old('funding_requirement', $project->funding_requirement ?? '') }}" {{ $isReadOnly ? 'disabled' : '' }}>
            </div>
            <div class="col-md-3">
                 <label class="form-label fw-medium smaller text-dark">Funding Category <span class="text-danger">*</span></label>
                 <div class="position-relative custom-select-container">
                     @php
                         $currentFC = old('funding_category', isset($project) ? $project->getRawOriginal('funding_category') : '');
                         $fcLabel = '-- Select --';
                         foreach($funding_categories as $c) if($c->value == $currentFC) $fcLabel = $c->label();
                     @endphp
                     <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="funding_category-display-box" tabindex="0">
                         <span class="text-truncate" id="funding_category-display-text">{{ $fcLabel }}</span>
                         <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                     </div>
                     <ul class="dropdown-menu w-100 shadow-sm mt-1" id="funding_category-dropdown" style="max-height: 250px; overflow-y: auto;">
                         @foreach($funding_categories as $cat)
                         <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $cat->value }}" data-name="{{ $cat->label() }}">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0 pe-none" type="radio" name="_fc_radio" {{ ($currentFC == $cat->value) ? 'checked' : '' }}>
                                    <span class="smaller">{{ $cat->label() }}</span>
                                </div>
                            </a>
                         </li>
                         @endforeach
                     </ul>
                     <input type="hidden" name="funding_category" id="funding_category" value="{{ $currentFC }}" required>
                 </div>
                 @error('funding_category')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                 <label class="form-label fw-medium smaller text-dark">Fund Source <span class="text-danger">*</span></label>
                 <div class="position-relative custom-select-container">
                     @php
                         $currentFS = old('fund_source', isset($project) ? $project->getRawOriginal('fund_source') : '');
                         $fsLabel = '-- Select --';
                         foreach($fund_sources as $s) if($s->value == $currentFS) $fsLabel = $s->label();
                     @endphp
                     <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="fund_source-display-box" tabindex="0">
                         <span class="text-truncate" id="fund_source-display-text">{{ $fsLabel }}</span>
                         <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                     </div>
                     <ul class="dropdown-menu w-100 shadow-sm mt-1" id="fund_source-dropdown" style="max-height: 250px; overflow-y: auto;">
                         @foreach($fund_sources as $src)
                         <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $src->value }}" data-name="{{ $src->label() }}">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0 pe-none" type="radio" name="_fs_radio" {{ ($currentFS == $src->value) ? 'checked' : '' }}>
                                    <span class="smaller">{{ $src->label() }}</span>
                                </div>
                            </a>
                         </li>
                         @endforeach
                     </ul>
                     <input type="hidden" name="fund_source" id="fund_source" value="{{ $currentFS }}" required>
                 </div>
                 @error('fund_source')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                 <label class="form-label fw-medium smaller text-dark">Year of Endorsement</label>
                 <div class="position-relative custom-select-container">
                     @php
                         $currentEY = old('endorse_year_id', $project->project_endorsement->endorse_year_id ?? '');
                         $eyLabel = '-- Select --';
                         foreach($endorse_years as $id => $year) if($id == $currentEY) $eyLabel = $year;
                     @endphp
                     <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="endorse_year-display-box" tabindex="0">
                         <span class="text-truncate" id="endorse_year-display-text">{{ $eyLabel }}</span>
                         <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                     </div>
                     <ul class="dropdown-menu w-100 shadow-sm mt-1" id="endorse_year-dropdown" style="max-height: 250px; overflow-y: auto;">
                         @foreach($endorse_years as $id => $year)
                         <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $id }}" data-name="{{ $year }}">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input mt-0 pe-none" type="radio" name="_ey_radio" {{ ($currentEY == $id) ? 'checked' : '' }}>
                                    <span class="smaller">{{ $year }}</span>
                                </div>
                            </a>
                         </li>
                         @endforeach
                     </ul>
                     <input type="hidden" name="endorse_year_id" id="endorse_year_id" value="{{ $currentEY }}">
                 </div>
            </div>
             <div class="col-md-6">
                 <label class="form-label fw-medium smaller text-dark">RDC Endorsement Number</label>
                 <input type="text" name="rdc_endorsement_number" class="form-control" 
                    value="{{ old('rdc_endorsement_number', $project->project_endorsement->rdc_endorsement_number ?? '') }}" placeholder="Enter RDC number" {{ $isReadOnly ? 'disabled' : '' }}>
            </div>
        </div>

        <div class="mb-4">
             <label class="form-label fw-medium smaller text-dark">RDP Chapters <span class="text-danger">*</span></label>
             <div class="position-relative" id="chapters-tag-container">
                 <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white min-h-38 shadow-sm {{ $isReadOnly ? '' : 'cursor-pointer' }}" id="f-chapters-box" tabindex="0">
                     @php
                         $selectedChapters = isset($project) 
                             ? $project->project_chapter->pluck('chapter_id')->toArray() 
                             : [];
                         $selectedChapterNames = isset($project)
                             ? collect($chapters)->filter(function($name, $id) use ($selectedChapters) {
                                 return in_array($id, $selectedChapters);
                               })
                             : collect();
                     @endphp
                     <span class="text-muted smaller ms-1 no-selection {{ count($selectedChapters) > 0 ? 'd-none' : '' }}">Select RDP chapters...</span>
                     <div id="selected-chapters-tags" class="d-flex flex-wrap gap-1">
                         @foreach($selectedChapterNames as $id => $name)
                         <span class="badge bg-primary d-flex align-items-center gap-1 fw-medium smaller py-1 px-2 rounded-pill" title="{{ $name }}">
                             <span class="text-truncate" style="max-width: 450px;">{{ $name }}</span>
                             @if(!$isReadOnly)<i data-lucide="x" width="12" height="12" class="cursor-pointer remove-tag" data-id="{{ $id }}"></i>@endif
                         </span>
                         @endforeach
                     </div>
                     <input type="text" class="border-0 flex-grow-1 p-0 m-0 bg-transparent visually-hidden" id="chapters-tag-input" readonly>
                 </div>
                 <ul class="dropdown-menu w-100 shadow-sm mt-1" id="chapters-dropdown" style="max-height: 250px; overflow-y: auto;">
                     @foreach($chapters as $id => $name)
                     <li>
                         <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $id }}" data-name="{{ $name }}">
                             <div class="d-flex align-items-center gap-2">
                                 <input class="form-check-input mt-0 pe-none" type="checkbox" {{ in_array($id, $selectedChapters) ? 'checked' : '' }}>
                                 <span class="smaller">{{ $name }}</span>
                             </div>
                         </a>
                     </li>
                     @endforeach
                 </ul>
                 <div id="chapters-hidden-inputs">
                     @foreach($selectedChapters as $cId)
                         <input type="hidden" name="rdp_chapters[]" value="{{ $cId }}">
                     @endforeach
                 </div>
                 @error('rdp_chapters')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
             </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium small text-dark mb-2">Location <span class="text-danger">*</span></label>
            <div class="d-flex flex-wrap gap-4" id="location-radio-group">
                @php $currentLoc = old('location', isset($project) ? $project->getRawOriginal('location') : 'nationwide'); @endphp
                @foreach($project_location_types as $type)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="location" id="loc-{{ $type->value }}" value="{{ $type->value }}" 
                        {{ $currentLoc == $type->value ? 'checked' : '' }} {{ $isReadOnly ? 'disabled' : '' }}>
                    <label class="form-check-label smaller fw-semibold text-secondary" for="loc-{{ $type->value }}">{{ $type->label() }}</label>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Location Specific Fields -->
        <div id="locationSpecificFields" class="mt-3 {{ $currentLoc == 'locationspecific' ? '' : 'd-none' }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label smaller fw-medium text-dark">Province <span class="text-danger">*</span></label>
                    <div class="position-relative custom-select-container">
                        @php
                            $provinceId = isset($locationSpecific->province_id) ? $locationSpecific->province_id : (isset($project->project_location_specific) ? $project->project_location_specific->province_id : '');
                            $provinceName = '-- Select --';
                            if(isset($locationSpecific->province)) $provinceName = $locationSpecific->province->province_name;
                            elseif(isset($project->project_location_specific->province)) $provinceName = $project->project_location_specific->province->province_name;
                        @endphp
                        <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="province-display-box" tabindex="0">
                            <span class="text-truncate" id="province-display-text">{{ $provinceName }}</span>
                            <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                        </div>
                        <ul class="dropdown-menu w-100 shadow-sm mt-1" id="province-dropdown" style="max-height: 250px; overflow-y: auto;">
                            @foreach($provinces as $id => $name)
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $id }}" data-name="{{ $name }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="form-check-input mt-0 pe-none" type="radio" name="_p_radio" {{ $provinceId == $id ? 'checked' : '' }}>
                                        <span class="smaller">{{ $name }}</span>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <input type="hidden" name="province_id" id="province_id" value="{{ $provinceId }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label smaller fw-medium text-dark">District <span class="text-danger">*</span></label>
                    <div class="position-relative custom-select-container">
                        @php
                            $districtName = '-- Select --';
                            if(isset($locationSpecific->district)) $districtName = $locationSpecific->district->district_name;
                            elseif(isset($project->project_location_specific->district)) $districtName = $project->project_location_specific->district->district_name;
                        @endphp
                        <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="district-display-box" tabindex="0">
                            <span class="text-truncate" id="district-display-text">{{ $districtName }}</span>
                            <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                        </div>
                        <ul class="dropdown-menu w-100 shadow-sm mt-1" id="district-dropdown" style="max-height: 250px; overflow-y: auto;">
                            <li><a class="dropdown-item smaller text-muted" href="#">Please select a province first</a></li>
                        </ul>
                        <input type="hidden" name="district_id" id="district_id" value="{{ $locationSpecific->district_id ?? ($project->project_location_specific->district_id ?? '') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label smaller fw-medium text-dark">City/Municipality <span class="text-danger">*</span></label>
                    <div class="position-relative custom-select-container">
                        @php
                            $municipalityName = '-- Select --';
                            if(isset($locationSpecific->municipality)) $municipalityName = $locationSpecific->municipality->municipality_name;
                            elseif(isset($project->project_location_specific->municipality)) $municipalityName = $project->project_location_specific->municipality->municipality_name;
                        @endphp
                        <div class="form-control d-flex align-items-center justify-content-between bg-white shadow-sm {{ $isReadOnly ? 'bg-light' : 'cursor-pointer' }}" id="municipality-display-box" tabindex="0">
                            <span class="text-truncate" id="municipality-display-text">{{ $municipalityName }}</span>
                            <i data-lucide="chevron-down" width="16" class="text-muted ms-2"></i>
                        </div>
                        <ul class="dropdown-menu w-100 shadow-sm mt-1" id="municipality-dropdown" style="max-height: 250px; overflow-y: auto;">
                            <li><a class="dropdown-item smaller text-muted" href="#">Please select a district first</a></li>
                        </ul>
                        <input type="hidden" name="municipality_id" id="municipality_id" value="{{ $locationSpecific->municipality_id ?? ($project->project_location_specific->municipality_id ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Inter-Province Fields -->
        <div id="inter-province-fields" class="mt-3 {{ $currentLoc == 'inter-province' ? '' : 'd-none' }}">
            <label class="form-label smaller fw-semibold text-secondary mb-1">Provinces <span class="text-danger">*</span></label>
            <div class="position-relative" id="provinces-tag-container">
                <div class="form-control d-flex flex-wrap gap-1 align-items-center bg-white min-h-38 shadow-sm {{ $isReadOnly ? '' : 'cursor-pointer' }}" id="f-provinces-box" tabindex="0">
                    @php
                        $selectedInterProvinces = isset($project) && $project->location == 'inter-province' 
                            ? $project->project_location->pluck('province_id')->toArray() 
                            : [];
                        $selectedInterProvinceNames = isset($project)
                            ? collect($provinces)->filter(function($name, $id) use ($selectedInterProvinces) {
                                return in_array($id, $selectedInterProvinces);
                              })
                            : collect();
                    @endphp
                    <span class="text-muted smaller ms-1 no-selection {{ count($selectedInterProvinces) > 0 ? 'd-none' : '' }}">Select provinces...</span>
                    <div id="selected-provinces-tags" class="d-flex flex-wrap gap-1">
                        @foreach($selectedInterProvinceNames as $id => $name)
                        <span class="badge bg-primary d-flex align-items-center gap-1 fw-medium smaller py-1 px-2 rounded-pill" title="{{ $name }}">
                            <span>{{ $name }}</span>
                            @if(!$isReadOnly)<i data-lucide="x" width="12" height="12" class="cursor-pointer remove-tag" data-id="{{ $id }}"></i>@endif
                        </span>
                        @endforeach
                    </div>
                    <input type="text" class="border-0 flex-grow-1 p-0 m-0 bg-transparent visually-hidden" id="provinces-tag-input" readonly>
                </div>
                <ul class="dropdown-menu w-100 shadow-sm mt-1" id="provinces-dropdown" style="max-height: 250px; overflow-y: auto;">
                    @foreach($provinces as $id => $name)
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-between gap-2 py-2" href="#" data-id="{{ $id }}" data-name="{{ $name }}">
                            <div class="d-flex align-items-center gap-2">
                                <input class="form-check-input mt-0 pe-none" type="checkbox" {{ in_array($id, $selectedInterProvinces) ? 'checked' : '' }}>
                                <span class="smaller">{{ $name }}</span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
                <div id="provinces-hidden-inputs">
                    @foreach($selectedInterProvinces as $pId)
                        <input type="hidden" name="provinces[]" value="{{ $pId }}">
                    @endforeach
                </div>
                @error('provinces')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>

<!-- Physical Target -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <!-- Physical Target -->
        <h6 class="fw-bold text-primary text-uppercase smaller ls-1 mt-4 mb-3">Physical Target</h6>
        <div class="row g-2 mb-4">
            @php $years = ['2023', '2024', '2025', '2026', '2027', '2028', 'succeeding']; @endphp
            @foreach($years as $year)
            <div class="col">
                <label class="smaller text-muted mb-1 d-block text-capitalize">{{ $year == 'succeeding' ? 'Succeeding Years' : $year }}</label>
                <input type="number" name="{{ $year == 'succeeding' ? 'target_succeeding_years' : 'target_year_'.$year }}" class="form-control form-control-sm border-light-subtle bg-light-subtle"
                    value="{{ old($year == 'succeeding' ? 'target_succeeding_years' : 'target_year_'.$year, (isset($project) && isset($project->project_cost_target)) ? $project->project_cost_target->{$year == 'succeeding' ? 'target_succeeding_years' : 'target_year_'.$year} : '') }}" {{ $isReadOnly ? 'disabled' : '' }}>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Project Cost -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <!-- Project Cost -->
        <h6 class="fw-bold text-primary text-uppercase smaller ls-1 mb-3">Project Cost (PM)</h6>
        <div class="row g-2 mb-4">
            @foreach($years as $year)
            <div class="col">
                <label class="smaller text-muted mb-1 d-block text-capitalize">{{ $year == 'succeeding' ? 'Succeeding Years' : $year }}</label>
                <input type="number" name="{{ $year == 'succeeding' ? 'cost_succeeding_years' : 'cost_year_'.$year }}" class="form-control form-control-sm border-light-subtle bg-light-subtle"
                    value="{{ old($year == 'succeeding' ? 'cost_succeeding_years' : 'cost_year_'.$year, (isset($project) && isset($project->project_cost_target)) ? $project->project_cost_target->{$year == 'succeeding' ? 'cost_succeeding_years' : 'cost_year_'.$year} : '') }}" {{ $isReadOnly ? 'disabled' : '' }}>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Additional Info -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h6 class="fw-bold text-primary text-uppercase smaller ls-1 mb-4">Additional Information</h6>

        <div class="row g-4">
            <div class="col-md-7">
                <label class="form-label fw-medium smaller text-dark">Remarks</label>
                <textarea name="remarks" class="form-control" rows="5" placeholder="Enter remarks" {{ $isReadOnly ? 'disabled' : '' }}>{{ old('remarks', $project->remarks ?? '') }}</textarea>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-medium smaller text-dark">Attachments</label>
                @if(!$isReadOnly)
                <div id="dropzone" class="border border-2 border-dashed rounded-3 p-4 text-center bg-light-subtle d-flex flex-column align-items-center justify-content-center cursor-pointer mb-3" style="min-height: 120px;">
                    <i data-lucide="cloud-upload" class="text-primary mb-2" width="28"></i>
                    <p class="smaller text-muted mb-0">Drop files or click to upload</p>
                </div>
                @endif
                
                @php
                    $mediaItems = isset($project) 
                        ? $project->getMedia('document')->merge($project->getMedia('attachments'))->merge($project->getMedia('project_attachments'))
                        : collect();
                @endphp
                
                @if($mediaItems->count() > 0)
                <div class="d-flex flex-column gap-2 mt-3">
                    @foreach($mediaItems as $media)
                    <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-white smaller">
                        <div class="d-flex align-items-center gap-2 text-truncate">
                            <i data-lucide="file-text" width="14" class="text-muted"></i>
                            <span class="text-truncate">{{ $media->file_name }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <a href="{{ $media->getUrl() }}" download="{{ $media->file_name }}" class="btn btn-link btn-sm p-0 text-primary me-1" title="Download">
                                <i data-lucide="download" width="14"></i>
                            </a>
                            @if(!$isReadOnly)
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 delete-media" data-id="{{ $media->id }}" title="Remove">
                                <i data-lucide="x" width="14"></i>
                            </button>
                            <input type="hidden" name="document[]" value="{{ $media->file_name }}">
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @elseif($isReadOnly)
                <div class="text-center py-4 border rounded bg-light-subtle mt-3">
                    <i data-lucide="file-off" class="text-muted mb-2" width="24"></i>
                    <p class="smaller text-muted mb-0 fw-medium">No attachments available</p>
                </div>
                @endif
                <div id="file-previews" class="d-flex flex-column gap-2 mt-2"></div>
                <input type="file" id="fileInput" class="d-none" multiple>
            </div>
        </div>
    </div>
</div>
