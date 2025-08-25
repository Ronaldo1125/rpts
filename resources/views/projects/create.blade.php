@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Add Project</h3>
         <p class="text-end">
          <a href="{{ route('projects.index')}}"><button class="btn btn-success btn-sm"><< Back</button></a>
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
            <hr class="mt-0" />
          </div>
      
            <div class="col-md-12 form-control-validation">
            <label class="form-label" for="project_title">Project Title</label>
            <input type="text" id="project_title" class="form-control" placeholder="Enter Project Title" name="project_title" />
            </div>
          
          <div class="col-md-12 form-control-validation">
            <label class="form-label" for="description">Project Description</label>
            <textarea id="description" name="description" class="form-control" placeholder="Enter Project Description"></textarea>
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
            <label class="form-label" for="endorsement_id">Endorsement</label>
            <select id="endorsement_id" name="endorsement_id" class="form-select">
              <option value="">-- Select Endorsement --</option>
              @foreach($endorsements as $key => $endorsement)
              <option value="{{ $key }}">{{ $endorsement }}</option>
              @endforeach
            </select>  
          </div>

          <div class="col-md-4 form-control-validation">
            <label class="form-label" for="funding_requirement">Funding Requirement</label>
            <input type="number" class="form-control" name="funding_requirement" step="any" id="funding_requirement" />  
          </div>

          <div class="row">
            <div class="col-md-10 form-control-validation2 mt-5">
              <label class="form-label" for="rdp_chapters">RDP Chapter</label>
            <select id="rdp_chapters" name="rdp_chapters[]" class="form-select js-example-basic-multiple" multiple="multiple">
                @foreach($chapters as $key => $chapter)
                <option value="{{ $key }}">{{ $chapter }}</option>
                @endforeach
              </select>  
            </div>
          </div>

          <span class="mb-0">Location</span>
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

          <span class="mb-0">Attachments</span>
          <div class="row">
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
         

          <div class="col-md-12 form-control-validation">
            <label class="form-label" for="remarks">Remarks</label>
            <textarea id="remarks" name="remarks" class="form-control" placeholder="Enter Project Remarks"></textarea>
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

// Disable Elements on load
	$(district).attr({disabled:"disabled"});
	$(municipality).attr({disabled:"disabled"});

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


});

$(document).ready(function() {
    $('.js-example-basic-multiple').select2({
        placeholder: 'Select RDP Chapters'
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