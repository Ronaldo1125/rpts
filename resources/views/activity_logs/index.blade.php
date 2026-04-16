@extends('layouts.app')

@section('content')




<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">User Activity Logs</h3>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTableProject">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Log Name</th>
                        <th>Event</th>
                        <th>Subject ID</th>
                        <th>Properties</th>
                        <th>Causer ID</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($activities as $activity)
                    <tr>
                        <td>
                          {{$activity->created_at }}
                        </td>
                         <td>
                          {{$activity->log_name }}
                        </td><td>
                          {{$activity->description }}
                        </td>
                        <td>
                            {{ $activity->subject_id }}
                        </td>
                       
                        <td>
                          {{ $activity->properties }}
                        </td>
                         <td>
                            {{ $activity->causer_id }}
                        </td>

                        
                        <td><span class="badge bg-label-primary me-1">{{ $activity->created_at->diffForHumans() }}</span></td>
                      </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Hoverable Table rows -->
    </div>
</div>

<!-- Large Modal -->
<div class="modal fade" id="addProject" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add Project</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
           <div class="modal-footer">
            {{-- <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Save</button> --}}
          </div>
         
      </div>
     
    </div>   
  </div>
</div>
@endsection

@section('jsvalidator')

{!! JsValidator::formRequest('App\Http\Requests\StoreProjectRequest') !!}

@endsection

@section('script')

<script type="text/javascript">


$(document).ready(function() {

  $('#myTableProject').DataTable({
    autoWidth: false,
    columns: [
        { width: '15%' },
        { width: '10%' },
        { width: '10%' },
        { width: '10%' },
        { width: '40%' },
        { width: '10%' },
        { width: '15%' },
      
    ],
    ordering: false,
  });
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

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
        placeholder: 'Select RDP Chapters',
        dropdownParent: $('#addProject .modal-body')
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