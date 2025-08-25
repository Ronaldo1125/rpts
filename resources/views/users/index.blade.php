@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h3 class="p-5">Manage Users</h3>
        <p class="text-end">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal"
            data-bs-target="#addUser">Create User</button>
        </p>

<!-- Hoverable Table rows -->
    <div class="card">
        <div class="table-responsive text-nowrap p-5">
            <table class="table table-hover" id="myTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Agency</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                  @foreach($users as $user)
                    <tr>
                        <td>
                          <i class="icon-base bx bxl-angular icon-md text-danger me-4"></i> <span>{{$user->name}}</span>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                          {{ $user->agency_id }}
                        </td>
                        <td><span class="badge bg-label-primary me-1">{{ $user->created_at->diffForHumans() }}</span></td>
                        <td>
                          <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                            <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                              <i class="icon-base bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                              <a class="dropdown-item" href="javascript:void(0);" 
                              data-bs-toggle="modal" data-bs-target="#editUser{{ $user->id }}"
                                ><i class="icon-base bx bx-edit-alt me-1"></i> Edit</a
                              >
                              @csrf
                              @method('DELETE')
                              <a class="dropdown-item" href="{{route('users.destroy', $user->id)}}" data-confirm-delete="true"
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

<!-- Add User Modal -->
<div class="modal fade" id="addUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Add User</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
      <form action="{{ route('users.store') }}" method="POST" id="add-form">
              @csrf
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email" />
          </div>
        </div>
        <div class="row">
           <div class="col mb-6">
              <label for="password" class="form-label">Password:</label>
              <input type="password" name="password" id="password" class="form-control" value="" placeholder="Password">
            </div>
        </div>
                           
        <div class="row">
           <div class="col mb-6">
              <label for="confirm-password" class="form-label pt-3">Confirm Password:</label>
              <input type="password" name="confirm-password" id="confirm-password" class="form-control" value="" placeholder="Confirm Password">
            </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="role" class="form-label">Role:</label>
              <select class="form-control" name="role" id="role">
                <option value="">Select role ...</option> 
                  @foreach ($roles as $role)
                    <option value="{{ $role }}">{{ $role }}</option>
                  @endforeach                
              </select>
          </div>
        </div>

         <div class="row">
          <div class="col mb-6">
            <label for="agency_id" class="form-label">Agency:</label>
              <select class="form-control" name="agency_id" id="agency_id">
                <option value="">Select agency ...</option> 
                  @foreach ($agencies as $key => $agency)
                    <option value="{{ $key }}">{{ $agency }}</option>
                  @endforeach                
              </select>
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

<!-- Edit Permission Modal -->
@foreach($users as $user)
<div class="modal fade" id="editUser{{$user->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel3">Edit User</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
      </div>
      <form action="{{ route('users.update', $user->id) }}" method="POST" id="add-form">
              @csrf
              @method('PUT')
      <div class="modal-body">
        <div class="row">
          <div class="col mb-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" placeholder="Enter Name" />
          </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" placeholder="Enter Email" />
          </div>
        </div>
        <div class="row">
           <div class="col mb-6">
              <label for="password" class="form-label">Password:</label>
              <input type="password" name="password" id="password" class="form-control" value="" placeholder="Password">
            </div>
        </div>
                           
        <div class="row">
           <div class="col mb-6">
              <label for="confirm-password" class="form-label pt-3">Confirm Password:</label>
              <input type="password" name="confirm-password" id="confirm-password" class="form-control" value="" placeholder="Confirm Password">
            </div>
        </div>

        <div class="row">
          <div class="col mb-6">
            <label for="role" class="form-label">Role:</label>
              <select class="form-control" name="role" id="role">
                <option value="">Select role ...</option> 
                  @foreach ($roles as $role)
                    <option value="{{ $role }}" {{ $user->hasRole($role) ? "selected" : "" }}>{{ $role }}</option>
                  @endforeach                
              </select>
          </div>
        </div>

         <div class="row">
          <div class="col mb-6">
            <label for="agency_id" class="form-label">Agency:</label>
              <select class="form-control" name="agency_id" id="agency_id">
                <option value="">Select agency ...</option> 
                  @foreach ($agencies as $key => $agency)
                    <option value="{{ $key }}" {{ ($key == $user->agency_id) ? "selected" : "" }}>{{ $agency }}</option>
                  @endforeach                
              </select>
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
{!! JsValidator::formRequest('App\Http\Requests\StoreUserRequest') !!}
@endsection