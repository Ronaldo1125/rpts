@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Add Project</h3>
         <p class="text-end">
          <a href="{{ route('projects.index')}}"><button class="btn btn-success btn-sm"><< Back to Project List</button></a>
        </p>
        
<!-- Hoverable Table rows -->
    <div class="row">
  <!-- FormValidation -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('projects.store') }}" class="row g-5" method="POST" enctype="multipart/form-data">
          <!-- Account Details -->
          @csrf
          <div class="col-12">
            <h6>Project Details</h6>
            <hr class="mt-0"/>
          </div>
      
            <div class="col-md-12 form-control-validation">
            <label class="form-label" for="project_title">Project Title</label>
            <input type="text" id="project_title" class="form-control" placeholder="Enter Project Title" name="project_title" />
            </div>
          
          <div class="col-md-12 form-control-validation">
            <label class="form-label" for="description">Project Description</label>
            <textarea id="description" name="description" class="form-control" placeholder="Enter Project Description"></textarea>
          </div>

          <div class="row mt-5">
            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="indicator_id">Indicator</label>
                <select id="indicator_id" name="indicator_id" class="form-select">
                      <option value="">-- Select Indicator --</option>
                    @foreach($indicators as $key => $indicator)
                      <option value="{{ $key }}">{{ $indicator }}</option>
                    @endforeach
                </select>  
            </div>
            <div class="col-md-4 form-control-validation">
               <label class="form-label" for="indicator_quantity">Indicator Quantity</label>
                 <input type="text" class="form-control" name="indicator_quantity" id="indicator_quantity" /> 
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

           <div class="col-md-4 form-control-validation">
            <label class="form-label" for="status_id">Status</label>
            <select id="status_id" name="status_id" class="form-select">
                  <option value="">-- Select Status --</option>
                @foreach($statuses as $key => $status)
                  <option value="{{ $key }}">{{ $status }}</option>
                @endforeach
            </select>  
          </div>
           

          <div class="col-md-4 form-control-validation">
            <label class="form-label" for="funding_requirement">Funding Requirement</label>
            <input type="number" class="form-control" name="funding_requirement" step="any" id="funding_requirement" />  
          </div>

          <div class="col-md-4 form-control-validation">
            <label class="form-label" for="funding_category_id">Funding Category</label>
            <select id="funding_category_id" name="funding_category_id" class="form-select">
              <option value="">-- Select Funding Category --</option>
              @foreach($funding_categories as $key => $category_name)
              <option value="{{ $key }}">{{ $category_name }}</option>
              @endforeach
            </select>  
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
                <div class="col-md-8 form-control-validation">
                        <span class="fw-medium d-block">Location</span>
                        <div class="form-check form-check-inline mt-4">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="location"
                            id="inlineRadio1"
                            value="regionwide" checked="checked" />
                          <label class="form-check-label" for="inlineRadio1">Regionwide</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="location"
                            id="inlineRadio2"
                            value="interprovince" />
                          <label class="form-check-label" for="inlineRadio2">Inter-Province</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="location"
                            id="inlineRadio3"
                            value="locationspecific" />
                          <label class="form-check-label" for="inlineRadio3">Location Specific</label>
                        </div>
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

{!! JsValidator::formRequest('App\Http\Requests\StoreProjectRequest') !!}

@endsection

@section('script')

<script type="text/javascript">

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
});

$(document).ready(function(){

  // On Load Hide
  $("#locationSpecific").hide();
  $("#interProvince").hide();
  $('input[name="location"]').on("click", function(){
      
    var location = $('input[name="location"]:checked').val();
   
      if(location == "regionwide") {
        $("#locationSpecific").hide();
        $("#interProvince").hide();
      } else if(location == "interprovince"){
        $("#locationSpecific").hide();
        $("#interProvince").show();
      } else {
        $("#locationSpecific").show();
        $("#interProvince").hide();
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