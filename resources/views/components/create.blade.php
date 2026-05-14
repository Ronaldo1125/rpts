@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Component Project {{ ($component_project_id == 0) ? '(New Project)' : '(Add Sub-Project)'}}</h3>
         <p class="text-end">
          <a href="{{ route('components.index')}}"><button class="btn btn-success btn-sm"><< Back to Component Project List</button></a>
        </p>
        
<!-- Hoverable Table rows -->
    <div class="row">
  <!-- FormValidation -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        

          <div class="col-12">
            <h5>Component Project Details</h5>
            <hr class="mt-0"/>
          </div>
          @if($component_project_id != 0)
          <div class="col-md-12">
            <div class="row">
              <h5>Component Project Title: <span class="text-info">{{ $component_project->component_project_title }}</span></h5>
              <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTableProject">
                <thead>
                    <tr>
                        <th>Sub-Project Title</th>
                        <th>Description</th>
                        <th>Funding Req't</th>
                        <th>Status</th>
                        <th>Funding Category</th>
                         <th>Documents</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($component_project->project as $project)
                    <tr>
                        <td>
                          {{$project->project_title }}
                        </td>
                        <td>{{ $project->description }}</td>
                        @php
                          $funding_requirement = $project->project_cost_target?->cost_year_2023 + $project->project_cost_target?->cost_year_2024 
                                                   + $project->project_cost_target?->cost_year_2025 + $project->project_cost_target?->cost_year_2026 
                                                   + $project->project_cost_target?->cost_year_2027 + $project->project_cost_target?->cost_year_2028 
                                                   +  $project->project_cost_target?->cost_succeeding_years;
                        @endphp
                        <td>{{ $funding_requirement }}</td>
                        <td>{{ $project->status }}</td>
                        <td>
                            {{ $project->funding_category }}
                        </td>
                         @php
                          $medias = $project->getMedia('document');
                        @endphp

                        <td class="text-center">
                          @foreach ($medias as $media)
                              <a href="{{ $media->getUrl() }}" data-toggle="tooltip" data-placement="bottom" title="{{ $media->name }}"><p class="mb-2 small"><i class="menu-icon tf-icons bx bx-paperclip"></i></p></a>
                          @endforeach
                        </td>
                        
                        <td><span class="badge bg-label-primary me-1">{{ $project->created_at->diffForHumans() }}</span></td>
                        <td>
                          <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="{{ route('components.editSubProject', [ 'component_id' => $project->component_project_id,  'id' => $project->id ] ) }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{ route('components.subProjectDestroy', ['component_id' => $component_project_id, 'id' => $project->id]) }}" data-confirm-delete="true"
                                ><i class="icon-base bx bx-trash me-1"></i> Delete</a>
                            </div>
                          </div>
                        </td>
                      </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
            </div>
          </div>
          @endif

      <form action="{{ route('components.store') }}" class="row g-5" method="POST" enctype="multipart/form-data">
        <!-- Account Details -->
          @csrf
          <input type="hidden" name="component_project_id" value="{{ $component_project_id }}" />
          @if($component_project_id == 0)
             <div class="col-md-12 form-control-validation">
              <label class="form-label" for="component_project_title">Component Project Title</label>
              <input type="text" id="component_project_title" class="form-control" placeholder="Enter Component Project Title" name="component_project_title" />
            </div>
          @else
          <hr>
          <div class="col-md-12 form-control-validation">
            <h5 class="bg-primary text-white p-2">Add Another Sub-Project</h5>
          </div>
          @endif
            <div class="col-md-12 form-control-validation">
            <label class="form-label" for="project_title">Sub-Project Title</label>
            <input type="text" id="project_title" class="form-control" placeholder="Enter Project Title" name="project_title" />
            </div>
          
          <div class="col-md-12 form-control-validation">
            <label class="form-label" for="description">Sub-Project Description</label>
            <textarea id="description" name="description" class="form-control" placeholder="Enter Project Description"></textarea>
          </div>

          <div class="row mt-5">
            <div class="col-md-10 form-control-validation2">
               <label class="form-label" for="indicator_id">Indicator</label>
                <select id="indicator_id" name="indicator_id" class="form-select js-indicator-single">
                      <option value="">-- Select Indicator --</option>
                    @foreach($indicators as $key => $indicator)
                      <option value="{{ $key }}">{{ $indicator }}</option>
                    @endforeach
                </select>  
            </div>
          </div>
          
          <div class="row mt-5">
            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="agency_id">Agency</label>
                <select id="agency_id" name="agency_id" class="form-select">
                      <option value="">-- Select Agency --</option>
                    @foreach($agencies as $key => $agency_acronym)
                      <option value="{{ $key }}">{{ $agency_acronym }}</option>
                    @endforeach
                </select>  
            </div>

            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="sector_id">Sector</label>
                <select id="sector_id" name="sector_id" class="form-select">
                      <option value="">-- Select Sector --</option>
                    @foreach($sectors as $key => $sector_name)
                      <option value="{{ $key }}">{{ $sector_name }}</option>
                    @endforeach
                </select>  
            </div>

            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="sub_sector_id">Sub-Sector</label>
                <select id="sub_sector_id" name="sub_sector_id" class="form-select">
                      <option value="">-- Select Sub-Sector --</option>
                </select>  
            </div>

          </div>

           <div class="col-md-3 form-control-validation">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
                  <option value="">-- Select Status --</option>
                @foreach($statuses as $status)
                  <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>  
          </div>

          <div class="col-md-3 form-control-validation">
            <label class="form-label" for="funding_category">Funding Category</label>
            <select id="funding_category" name="funding_category" class="form-select">
              <option value="">-- Select Funding Category --</option>
              @foreach($funding_categories as $funding_category)
              <option value="{{ $funding_category->value }}">{{ $funding_category->label() }}</option>
              @endforeach
            </select>  
          </div>

          <div class="col-md-3 form-control-validation">
            <label class="form-label" for="fund_source">Fund Source</label>
            <select id="fund_source" name="fund_source" class="form-select">
              <option value="">-- Select Fund Source --</option>
              @foreach($fund_sources as $fund_source)
              <option value="{{ $fund_source->value }}">{{ $fund_source->label() }}</option>
              @endforeach
            </select>  
          </div>

          <div class="col-md-3 form-control-validation">
            <label class="form-label" id="other_fund_source_label" for="other_fund_source">Other Fund Source</label>
              <input type="text" name="other_fund_source" class="form-control" id="other_fund_source" />
          </div>

          <!-- Year of Endorsement Input Forms -->

          <div class="row mt-5">
            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="endorse_year_id">Year of Endorsement</label>
                <select id="endorse_year_id" name="endorse_year_id" class="form-select">
                      <option value="">-- Select Year of Endorsement --</option>
                    @foreach($endorse_years as $key => $endorse_year)
                      <option value="{{ $key }}">{{ $endorse_year }}</option>
                    @endforeach
                </select>  
            </div>

            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="rdc_endorsement_number">RDC Endorsement Number</label>
                 <input type="text" class="form-control" name="rdc_endorsement_number" id="rdc_endorsement_number" /> 
            </div>

          </div>

       

          <div class="row mt-5">
            <div class="col-md-10 form-control-validation2">
              <label class="form-label" for="rdp_chapters">RDP Chapter</label>
            <select id="rdp_chapters" name="rdp_chapters[]" class="form-select js-rdp-chapter-multiple" multiple="multiple">
                @foreach($chapters as $key => $chapter)
                <option value="{{ $key }}">{{ $chapter }}</option>
                @endforeach
              </select>  
            </div>
          </div>

          <div class="row mt-5">
                <div class="col-md-12 form-control-validation">
                        <span class="fw-medium d-block">Location</span>
                        @foreach($project_location_types as $project_location_type)
                        <div class="form-check form-check-inline mt-4">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="location"
                            id="inlineRadio1"
                            value="{{ $project_location_type->value }}" {{ ($project_location_type->value == 'nationwide') ? 'checked="checked"' : '' }} />
                          <label class="form-check-label" for="inlineRadio1">{{ $project_location_type->label() }}</label>
                        </div>
                        @endforeach
                       
                      </div>
           
          </div>

          <div class="row mt-5" id="interProvince">
            <div class="col-md-8 form-control-validation2">
              <label class="form-label" for="rdp_chapters">Provinces</label>
            <select id="provinces" name="provinces[]" class="form-select js-province-multiple" multiple="multiple">
                @foreach($provinces as $key => $province_name)
                <option value="{{ $key }}">{{ $province_name }}</option>
                @endforeach
              </select>  
            </div>
          </div>

          <div class="row mt-5" id="provincewide">
            <div class="col-md-8 form-control-validation2">
              <label class="form-label" for="province">Province</label>
            <select id="province" name="province" class="form-select js-province-single">
               <option value="">-- Select Province --</option>
                @foreach($provinces as $key => $province_name)
                <option value="{{ $key }}">{{ $province_name }}</option>
                @endforeach
              </select>  
            </div>
          </div>

          <div class="row mt-5" id="locationSpecific">
              <div class="col-md-4 form-control-validation mt-2">
              <label class="form-label" for="province_id">Province</label>
            <select id="province_id" name="province_id" class="form-select">
                <option value="">-- Select Province --</option>
                @foreach($provinces as $key => $province)
                <option value="{{ $key }}">{{ $province }}</option>
                @endforeach
              </select>  
            </div>

            <div class="col-md-4 form-control-validation mt-2">
              <label class="form-label" for="district_id">District</label>
              <select class="form-select" name="district_id" id="district_id">
                <option value="">-- Select District --</option>
              </select>  
            </div>

            <div class="col-md-4 form-control-validation mt-2">
              <label class="form-label" for="municipality_id">City/Municipality</label>
              <select class="form-select" name="municipality_id" id="municipality_id">
                <option value="">-- Select City/Municipality --</option>
              </select>   
            </div>
          </div>

           <!-- Maps Input Forms -->

          <div class="row mt-5">
           
            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="latitude">Latitude</label>
                 <input type="number" step="any" min="-90" max="90" class="form-control" name="latitude" id="latitude" /> 
            </div>


            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="longtitude">Longtitude</label>
                 <input type="number" step="any" min="-180" max="180" class="form-control" name="longtitude" id="longtitude" /> 
            </div>

          </div>

          <div class="row mt-5">
                <span class="mb-0">Physical Target</span>
              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="target_year_2023">2023</label>
                <input type="number" class="form-control" name="target_year_2023" step="any" id="target_year_2023" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="target_year_2024">2024</label>
                <input type="number" class="form-control" name="target_year_2024" step="any" id="target_year_2024" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="target_year_2025">2025</label>
                <input type="number" class="form-control" name="target_year_2025" step="any" id="target_year_2025" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="target_year_2026">2026</label>
                <input type="number" class="form-control" name="target_year_2026" step="any" id="target_year_2026" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="target_year_2027">2027</label>
                <input type="number" class="form-control" name="target_year_2027" step="any" id="target_year_2027" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="target_year_2028">2028</label>
                <input type="number" class="form-control" name="target_year_2028" step="any" id="target_year_2028" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="target_succeeding_years">Succeeding Years</label>
                <input type="number" class="form-control" name="target_succeeding_years" step="any" id="target_succeeding_years" />  
              </div>

              <span class="mb-0">Project Cost(PM)</span>
              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="cost_year_2023">2023</label>
                <input type="number" class="form-control" name="cost_year_2023" step="any" id="cost_year_2023" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="cost_year_2024">2024</label>
                <input type="number" class="form-control" name="cost_year_2024" step="any" id="cost_year_2024" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="cost_year_2025">2025</label>
                <input type="number" class="form-control" name="cost_year_2025" step="any" id="cost_year_2025" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="cost_year_2026">2026</label>
                <input type="number" class="form-control" name="cost_year_2026" step="any" id="cost_year_2026" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="cost_year_2027">2027</label>
                <input type="number" class="form-control" name="cost_year_2027" step="any" id="cost_year_2027" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="cost_year_2028">2028</label>
                <input type="number" class="form-control" name="cost_year_2028" step="any" id="cost_year_2028" />  
              </div>

              <div class="col-md-2 form-control-validation mt-2">
                <label class="form-label" for="cost_succeeding_years">Succeeding Years</label>
                <input type="number" class="form-control" name="cost_succeeding_years" step="any" id="cost_succeeding_years" />  
              </div>
          </div>
          
          <div class="row mt-5">
                <span class="mb-0">Attachments</span>
                <div class="col-md-8 mt-3 pl-3 form-control-validation dropzone" id="myAttach">
              {{-- <div id="dZUpload" class="dropzone">
                    <div class="dz-default dz-message"></div>
                </div> --}}
            </div>
          </div>
          

          {{-- <div class="row">
             <div class="col-md-6 mt-5 form-control-validation">
            <label for="attachments" class="form-label">Attachments</label>
            <input class="form-control" type="file" id="attachments" name="attachments[]" multiple="multiple" />
          </div>
          </div> --}}
         
        <div class="row mt-5">
            <div class="col-md-12 form-control-validation">
                    <label class="form-label" for="remarks">Remarks</label>
                    <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter Project Remarks"></textarea>
            </div>
        </div>
          

         
          <div class="col-12 form-control-validation">
            <button type="submit" name="submitButton" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /FormValidation -->
</div>

</div>

@endsection

@section('jsvalidator')

{!! JsValidator::formRequest('App\Http\Requests\StoreComponentRequest') !!}

@endsection

@section('script')

<script type="text/javascript">

$(document).ready(function() {

  $('#myTableProject').DataTable({
    autoWidth: false,
    columns: [
        { width: '15%' },
        { width: '20%' },
        { width: '11%' },
        { width: '11%' },
        { width: '11%' },
        { width: '11%' },
        { width: '10%' },
        { width: '11%' },
    ]
  });
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});

$(document).ready(function() {

var province 			 = $("#province_id"); 
var district 			 = $("#district_id"); 
var municipality	 = $("#municipality_id"); 
var sector         = $("#sector_id");
var subsector      = $("#sub_sector_id");

// Disable Elements on load
	$(district).attr({disabled:"disabled"});
	$(municipality).attr({disabled:"disabled"});
  $(subsector).attr({disabled:"disabled"});

  // Onchange Event
	
	$(province).change( function(){
			if(this.value == ''){
					$(district).html("<option value=''>-- Select District --</option>").attr({disabled:"disabled"});
					$(municipality).html("<option value=''>-- Select City/Municipality --</option>").attr({disabled:"disabled"});
			}else{		 	
					getDistricts(district,this.value,false); 
					$(district).removeAttr("disabled"); 	
					$(municipality).html("<option value=''>-- Select City/Municipality --</option>").attr({disabled:"disabled"});
			}
	});

	$(district).change( function(){
			if(this.value == ''){					
					$(municipality).html("<option value=''>-- Select City/Municipality --</option>").attr({disabled:"disabled"});
			}else{				  	
					getMunicipalities(municipality,$(province).val(),this.value, false);
					$(municipality).removeAttr("disabled"); 
			}
	});

  $(sector).change( function(){
    if(this.value == ''){
        $(subsector).html("<option value=''>-- Select Sub-Sector --</option>").attr({disabled:"disabled"});
    } else {
        getSubSectors(subsector, this.value, false);
        $(subsector).removeAttr("disabled");
    }


  });


  function getDistricts(el, provinceValue, preselect){
		var selectedDistrict 	=	$("#sdistrict_id"); 
		$(el).html("<option value=''>Loading Districts...</option>");

    $.ajax({
        url: "{{ route('location.getDistricts') }}",
        data: {
          province_id: provinceValue
        },
        success: function (data) {

          $(el).html('<option value="">-- Select District --</option>');
         
          $.each(data, function (id, value){
            $(el).append('<option value="' + value.id + '">' + value.district_name + '</option>')
          });
          $(el).removeAttr('disabled');
        }
    });
  }

  function getMunicipalities(el, provinceValue, districtValue, preselect) {
    $(el).html("<option value=''>Loading City/Municipality</option>");

    $.ajax({
      url: "{{ route('location.getMunicipalities') }}",
      data: {
        province_id: provinceValue,
        district_id: districtValue
      },
      success: function(data) {

        // console.log(data);
        $(el).html("<option value=''>-- Select City/Municipality --</option>");

        $.each(data, function(id, value) {
          $(el).append('<option value="' + value.id + '">' + value.municipality_name + '</option>');
        });
        $(el).removeAttr('disabled');
      }

    });
  }

  function getSubSectors(el, sectorValue, preselect) {

    $(el).html("<option value=''>Loading Sub-Sector</option>");

    $.ajax({
        url: "{{ route('projects.getSubSectors') }}",
        data: {
          sector_id: sectorValue
        },
        success: function (data) {

          $(el).html('<option value="">-- Select Sub-Sector --</option>');
         
          $.each(data, function (id, value){
            $(el).append('<option value="' + value.id + '">' + value.subsector_name + '</option>')
          });
          $(el).removeAttr('disabled');
        }
    });

  }
});

$(document).ready(function() {
    $('.js-rdp-chapter-multiple').select2({
        placeholder: 'Select RDP Chapters'
    });

    $('.js-province-multiple').select2({
        placeholder: 'Select Provinces'
    });

     $('.js-province-single').select2({
        placeholder: 'Select Province'
    });

    $(".js-indicator-single").select2({
      placeholder: 'Select Indicator',
      dropdownAutoWidth: true,
    });
});

$(document).ready(function()
{
  let otherFundSource = $("#other_fund_source");
  let otherFundSourceLabel = $("#other_fund_source_label");
  let fundSource = $("#fund_source");
  otherFundSource.hide();
  otherFundSourceLabel.hide();

  $(fundSource).change( function(){

    if(this.value === 'others')
    {
      
      otherFundSource.show();
      otherFundSourceLabel.show();
    } else {
      otherFundSource.hide();
      otherFundSourceLabel.hide();
    }

    console.log(this.value);
  });
});

$(document).ready(function(){

  // On Load Hide
  $("#locationSpecific").hide();
  $("#interProvince").hide();
  $("#provincewide").hide();
  $('input[name="location"]').on("change", function(){
      
    let location = $('input[name="location"]:checked').val();
   
     if(location == "nationwide" || location == "inter-regional" || location == "regionwide") {
        $("#locationSpecific").hide();
        $("#interProvince").hide();
        $("#provincewide").hide();
      } else if(location == "inter-province"){
        $("#locationSpecific").hide();
        $("#interProvince").show();
        $("#provincewide").hide();
      } else if(location == "provincewide") {
         $("#locationSpecific").hide();
         $("#interProvince").hide();
         $("#provincewide").show();
      } else {
        $("#locationSpecific").show();
        $("#interProvince").hide();
         $("#provincewide").hide();
      }
  });
});


$(document).ready(function () {
    var uploadedDocumentMap = {};
    Dropzone.autoDiscover = false;
   $("div#myAttach").dropzone({ 
        url: "{{ url('projects/media') }}",
        maxFilesize: 2, // MB
        maxFiles: 3,
        addRemoveLinks: true,
        headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
    success: function (file, response) {
    //console.log(response.name);
      $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
      uploadedDocumentMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedDocumentMap[file.name]
      }
      //console.log(name);
      $('form').find('input[name="document[]"][value="' + name + '"]').remove()
    },
    init: function () {
      @if(isset($project) && $project->document)
        var files = [];
          {!! json_encode($project->document) !!}
        for (var i in files) {
          var file = files[i]
          this.options.addedfile.call(this, file)
          file.previewElement.classList.add('dz-complete')
          $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
        }
      @endif
    }
    });
});


 


</script>
@endsection