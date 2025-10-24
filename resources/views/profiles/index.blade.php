@extends('layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h3 class="p-5">My Profiles</h3>
         <div class="row gy-6">
         <!-- Vertical Scrollbar -->
                <div class="col-md-6 col-sm-12">
                    
                    <div class="card">
                        <div class="card-body">
                          <div class="badge bg-label-primary p-4 rounded mb-4">
                             <div class="avatar flex-shrink-0">
                      <img
                        src="../assets/img/icons/unicons/employee.png"
                        alt="wallet info"
                        class="rounded" />
                    </div>
                          </div>
                        
                          <div class="text-center">
                            <h5>User Profile</h5>
                              <img src="{{asset('/images')}}/{{$userProfile->picture}}" alt="avatar" class="rounded-circle bg-dark img-fluid" style="width: 150px;">
                            <div class="row justify-content-center p-2">
                                <a href="javascript:void(0)" id="upload_pic" class="text-lg text-bold" data-bs-toggle="modal" data-bs-target="#ProfilePicModal">
                                    <i class="icon-base icon-lg bx bx-pencil"></i>
                                </a>
                            </div>
                                <h5 class="my-3 font-weight-bold">{{$userInfo->name}}</h5>
                                <p class="text-sm mb-1">{{$userInfo->email}}</p>
                                <p class="text-sm mb-1">{{$userProfile->mobile_number}}</p>
                                <p class="text-sm mb-1">{{$userProfile->address}}</p>
                                <a href="javascript:void(0)" class="mt-3 btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#proInfoModal">Edit Profile Info</a>
                          </div>
                            
                        </div>
                        <div class="card-footer text-muted"></div>
                    </div>
                </div>
                <!--/ Vertical Scrollbar -->

                <!-- Horizontal Scrollbar -->
                <div class="col-md-6 col-sm-12">
                  <div class="card">
                    
                    <div class="card-body" id="horizontal-example">
                      <div class="badge bg-label-primary p-4 mb-4">
                             <div class="avatar flex-shrink-0">
                      <img
                        src="../assets/img/icons/unicons/engagement.png"
                        alt="wallet info"
                        class="rounded" />
                    </div>
                          </div>
                          <h5 class="card-header text-center">Change Password</h5>
                     <p>Ensure your account is using a long, random password to stay secure.</p>
                    <form action="{{ route('profiles.updatePassword', $userInfo->id) }}" method="POST" id="update-form" enctype="multipart/form-data">
                      @csrf
                      @method('POST')
                        <div class="form-group">
                            <div class="input-group">
                              <input type="password" name="current_password" id="current-pass" class="form-control" value="" placeholder="Current Password">
                                <div class="input-group-text">
                                  <input class="form-check-input mt-0" type="checkbox" onclick="togglePasswordVisibility('current-pass')"/>
                                </div>
                            </div>
                          
                          <div class="input-group mt-3">
                            <input type="password" name="password" id="new-pass" class="form-control" value="" placeholder="New Password">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" onclick="togglePasswordVisibility('new-pass')"/>
                              </div>
                          </div>
                            
                          <div class="input-group mt-3">
                            <input type="password" name="confirm_password" id="confirm-pass" class="form-control" value="" placeholder="Confirm Password">
                            <div class="input-group-text">
                                <input class="form-check-input mt-0" type="checkbox" onclick="togglePasswordVisibility('confirm-pass')"/>
                              </div>
                          </div>

                            <div class="row-3 mt-10 text-end">  
                              <button type="submit" class="btn btn-sm btn-primary" id="formSubmit">Update Password</button>
                            </div>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!--/ Horizontal Scrollbar -->
         </div>
    </div>
</div>

<!--Update Profile Pic Modal -->
<div class="modal fade" id="ProfilePicModal" tabindex="-1" aria-hidden="true">
   <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel3">Update Profile Picture</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"></button>
          </div>
          <hr>
          <div class="modal-body">
            <div class="row justify-content-center">
              <div class="col mb-6">
                    <img src="{{asset('/images')}}/{{$userProfile->picture}}" alt="avatar" class="rounded-circle bg-dark img-fluid" style="width: 150px;">
              </div>
              <div class="col-md-6 pt-5">
                  <form id="edit-form" enctype="multipart/form-data" action="{{route('profiles.updatePic')}}" method="POST">
                      @csrf
                          <input type="hidden" name="user_id" value="{{ $userInfo->id }}">
                          <div class="row justify-content-center">
                          <input id="avatar" type="file" name="avatar" class="form-control">
                          </div>
              </div>
            </div>
          </div>
            
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save Picture</button> 
          </div>
            </form>
        </div>
      </div>
</div>

<!--Update Profile Info Modal -->
<div class="modal fade" id="proInfoModal" tabindex="-1" role="dialog" aria-hidden="true">
     <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel3">Update Profile Info</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"></button>
          </div>
           <hr>
              <form action="{{ route('profiles.update') }}" method="POST" id="edit-form">
                <input type="hidden" name="id" value="{{ $userProfile->id }}"/>
                <input type="hidden" name="user_id" value="{{ $userProfile->user_id }}"/>
              @csrf
        
          <div class="modal-body">
            <div class="row">
              <div class="col mb-6">
                <label for="mobile_number" class="form-label">Mobile Number</label>
                <input type="number" name="mobile_number" id="mobile_number" class="form-control" value="{{ $userProfile->mobile_number }}" placeholder="Enter Mobile Number" />
              </div>
            </div>

            <div class="row">
              <div class="col mb-6">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address" class="form-control" value="{{ $userProfile->address }}" placeholder="Enter Address" />
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

@endsection





@section('script')
<script type="text/javascript" charset="utf8" src="{{ url('/dist/js/datatables/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">

function togglePasswordVisibility(id) {
  var x = document.getElementById(id); // Get the password input element
    if (x.type === "password") {
      x.type = "text"; // Show the password
    } else {
      x.type = "password"; // Hide the password
    }
}

</script>
@endsection

@section('jsvalidator')

{!! JsValidator::formRequest('App\Http\Requests\ProfileUpdateRequest'); !!}
{{-- {!! JsValidator::formRequest('App\Http\Requests\TransportationStoreRequest', '#edit-form'); !!} --}}

@endsection