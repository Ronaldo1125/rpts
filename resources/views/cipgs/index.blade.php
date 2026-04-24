@extends('layouts.app_v2')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage CIPG Submissions</h3>
         <p class="text-end">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addCipg"><i class="icon-base bx bx-send icon-sm"></i>Submit CIPG</button>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>CIPG Documents</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($cipgs as $cipg)
                    <tr>
                        <td>
                            {{ $cipg->title }}
                        </td>
                        <td>
                            {{ $cipg->description }}
                        </td>
                        <td>
                            
                        </td>
                        <td><span class="badge bg-label-primary me-1">{{ $cipg->created_at->diffForHumans() }}</span></td>
                        <td>
                          <form action="{{ route('cipgs.destroy', $cipg->id) }}" method="POST>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);" 
                              data-bs-toggle="modal" data-bs-target="#editCipg{{ $cipg->id }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{route('cipgs.destroy', $cipg->id)}}" data-confirm-delete="true"
                                ><i class="icon-base bx bx-trash me-1"></i> Delete</a
                              >
                            </div>
                          </div>
                          </form>
                          
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Hoverable Table rows -->
    </div>
</div>

<!-- Add Indicator Modal -->
<div class="modal fade" id="addCipg" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Submit CIPG</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('cipgs.store') }}" method="POST" id="add-form" enctype="multipart/form-data">
              @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Title" />
          </div>
        </div>

        <div class="row">
            <div class="col md-6">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" placeholder="Enter Description"></textarea>
            </div>
        </div> 

        <div class="row">
          <div class="col md-6">
            <label for="myAttach" class="form-label">CIPG Documents</label>
                <div class="col-md-8 mt-3 pl-3 form-control-validation dropzone" id="myAttach">
              {{-- <div id="dZUpload" class="dropzone">
                    <div class="dz-default dz-message"></div>
                </div> --}}
            </div>
          </div>     
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>
    </form>
  </div>
</div>

{{-- <!-- Edit Sector Modal -->
@foreach($indicators as $indicator)
<div class="modal fade" id="editIndicator{{$indicator->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Update Indicator</h5>
         
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('indicators.update', $indicator->id) }}" method="POST" id="add-form">
              @csrf
              @method('PUT')
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="indicator_name" class="form-label">Indicator Name</label>
            <input type="text" name="indicator_name" id="indicator_name" class="form-control" value="{{ $indicator->indicator_name }}" placeholder="Enter Indicator Name" />
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>
    </form>
  </div>
</div>
@endforeach --}}
@endsection

@section('jsvalidator')
{!! JsValidator::formRequest('App\Http\Requests\StoreCipgRequest') !!}
@endsection

@section('script')

<script type="text/javascript">

$(document).ready(function () {
    var uploadedDocumentMap = {};
    Dropzone.autoDiscover = false;
  var myDropzone = new Dropzone("div#myAttach", { 
  // $("div#myAttach").dropzone({ 
        url: "{{ url('cipgs/media') }}",
        //autoProcessQueue: false, // Prevents automatic upload
        maxFilesize: 2, // MB
        maxFiles: 3,
        addRemoveLinks: true,
        headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
    success: function (file, response) {
    console.log(response.name);
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


    document.getElementById('add-form').addEventListener('submit', function (e) {
    if (myDropzone.getQueuedFiles().length === 0 && myDropzone.getAcceptedFiles().length === 0) {
        e.preventDefault(); // Stop the form submission
        alert("Please upload at least one file.");
        // You can display a more user-friendly error message here
    } else {
        // If you need to process files separately via AJAX first
        //e.preventDefault();
        myDropzone.options.autoProcessQueue = true;
        //myDropzone.processQueue();
    }
});

});


</script>

@endsection