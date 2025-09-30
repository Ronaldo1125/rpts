@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage RDP Chapters</h3>
         <p class="text-end">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addChapter">Create RDP Chapter</button>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>RDP Chapter Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($chapters as $chapter)
                    <tr>
                        <td>
                           <span>{{ $chapter->chapter_name }}</span>
                        </td>
                        <td><span class="badge bg-label-primary me-1">{{ $chapter->created_at->diffForHumans() }}</span></td>
                        <td>
                          <form action="{{ route('chapters.destroy', $chapter->id) }}" method="POST>
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);" 
                              data-bs-toggle="modal" data-bs-target="#editChapter{{ $chapter->id }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{route('chapters.destroy', $chapter->id)}}" data-confirm-delete="true"
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

<!-- Add Sector Modal -->
<div class="modal fade" id="addChapter" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add RDP Chapter Name</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('chapters.store') }}" method="POST" id="add-form">
              @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="chapter_name" class="form-label">RDP Chapter Name</label>
            <input type="text" name="chapter_name" id="chapter_name" class="form-control" placeholder="Enter Chapter Name" />
          </div>
        </div>

        <div class="row">
         
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

<!-- Edit Sector Modal -->
@foreach($chapters as $chapter)
<div class="modal fade" id="editChapter{{$chapter->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Edit RDP Chapter Name</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('chapters.update', $chapter->id) }}" method="POST" id="add-form">
              @csrf
              @method('PUT')
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="chapter_name" class="form-label">RDP Chapter Name</label>
            <input type="text" name="chapter_name" id="chapter_name" class="form-control" value="{{ $chapter->chapter_name }}" placeholder="Enter Chapter Name" />
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
@endforeach
@endsection

@section('jsvalidator')
{!! JsValidator::formRequest('App\Http\Requests\StoreChapterRequest') !!}
@endsection