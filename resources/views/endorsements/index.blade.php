@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Endorsements</h3>
         <p class="text-end">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addEndorsement">Create Endorsement</button>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>RDC Resolution</th>
                        <th>Resolution Date</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($endorsements as $endorsement)
                    <tr>
                        <td>
                          {{-- <i class="icon-base bx bxl-angular icon-md text-danger me-4"></i> --}} <span>{{ $endorsement->rdc_resolution }}</span> 
                        </td>
                        <td class="text-center">{{ $endorsement->resolution_date }}</td>
                        <td><span class="badge bg-label-primary me-1">{{ $endorsement->created_at->diffForHumans() }}</span></td>
                        
                        <td>
                          <form action="{{ route('endorsements.destroy', $endorsement->id) }}" method="POST">
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);" 
                              data-bs-toggle="modal" data-bs-target="#editEndorsement{{ $endorsement->id }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{route('endorsements.destroy', $endorsement->id)}}" data-confirm-delete="true"
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

<!-- Add  Modal -->
<div class="modal fade" id="addEndorsement" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add Endorsement</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('endorsements.store') }}" method="POST" id="add-form">
              @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="rdc_resolution" class="form-label">RDC Resolution</label>
            <input type="text" name="rdc_resolution" id="rdc_resolution" class="form-control" placeholder="Enter RDC Resolution" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="resolution_date" class="form-label">Resolution Date</label>
            <input type="date" name="resolution_date" id="resolution_date" class="form-control" placeholder="Enter Resolution Date" />
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

<!-- Edit  Modal -->
@foreach($endorsements as $endorsement)
<div class="modal fade" id="editEndorsement{{$endorsement->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Edit Endorsement</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
         <hr>
      <form action="{{ route('endorsements.update', $endorsement->id) }}" method="POST" id="add-form">
              @csrf
              @method('PUT')
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="rdc_resolution" class="form-label">RDC Resolution</label>
            <input type="text" name="rdc_resolution" id="rdc_resolution" class="form-control" value="{{ $endorsement->rdc_resolution }}" placeholder="Enter RDC Resolution" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="resolution_date" class="form-label">Resolution Date</label>
            <input type="date" name="resolution_date" id="resolution_date" class="form-control" value="{{ $endorsement->resolution_date }}" placeholder="Enter Resolution Date" />
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </div>
    </form>
  </div>
</div>
@endforeach
@endsection

@section('jsvalidator')
{!! JsValidator::formRequest('App\Http\Requests\StoreEndorsementRequest') !!}
@endsection