@extends('layouts.app_v2')
@section('content')

<section id="profile" class="page-content active container-fluid py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-0">My Profile</h2>
    </div>

    <div class="row g-4">
        <!-- User Profile Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem;">
                <div class="card-body p-4 p-xl-5">
                    <!-- Top Icon Square -->
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center shadow-sm square-56 bg-soft-blue rounded-12">
                        <i data-lucide="user" class="text-primary-rpts" width="28" height="28"></i>
                    </div>

                    <div class="text-center">
                        <h5 class="fw-bold mb-4">User Profile</h5>
                        
                        <!-- Avatar with Pencil Icon -->
                        <div class="position-relative d-inline-block mb-3">
                            <div class="rounded-pill overflow-hidden shadow-sm square-156 border border-4 border-white outline-light">
                                <img src="https://api.dicebear.com/7.x/initials/svg?seed=EC" 
                                     class="w-100 h-100 object-fit-cover" alt="User Profile Picture">
                            </div>
                            <div class="position-absolute bottom-0 end-0 mb-1 me-1">
                                <button class="btn btn-white shadow-sm border rounded-circle p-2 bg-white square-36" 
                                        data-bs-toggle="modal" data-bs-target="#updateProfilePictureModal">
                                    <i data-lucide="pencil" class="text-primary-rpts" width="18" height="18"></i>
                                </button>
                            </div>
                        </div>

                        <h4 class="fw-bold mb-1">Emmanuel Chivic O. Llaguno</h4>
                        <p class="text-muted mb-4 small">eollaguno@depdev.gov.ph</p>

                        <button class="btn btn-primary px-4 py-2 fw-semibold shadow-sm bg-primary-rpts-v2 border-primary-rpts-v2 rounded-8"
                                data-bs-toggle="modal" data-bs-target="#updateProfileInfoModal">
                            Edit Profile Info
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password Card -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem;">
                <div class="card-body p-4 p-xl-5">
                    <!-- Top Icon Square -->
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center shadow-sm square-56 bg-soft-blue rounded-12">
                        <i data-lucide="shield-check" class="text-primary-rpts" width="28" height="28"></i>
                    </div>

                    <h5 class="fw-bold mb-2 text-center text-lg-start">Change Password</h5>
                    <p class="text-muted small mb-4 text-center text-lg-start">Ensure your account is using a long, random password to stay secure.</p>

                    <form>
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password" class="form-control border-end-0 py-3 ps-4 rounded-left-12 fs-0-95" placeholder="Current Password">
                                <span class="input-group-text bg-white border-start-0 pe-4 toggle-password rounded-right-12 pointer">
                                    <i data-lucide="eye" class="text-secondary opacity-50" width="20" height="20"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="password" class="form-control border-end-0 py-3 ps-4 rounded-left-12 fs-0-95" placeholder="New Password">
                                <span class="input-group-text bg-white border-start-0 pe-4 toggle-password rounded-right-12 pointer">
                                    <i data-lucide="eye" class="text-secondary opacity-50" width="20" height="20"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="password" class="form-control border-end-0 py-3 ps-4 rounded-left-12 fs-0-95" placeholder="Confirm Password">
                                <span class="input-group-text bg-white border-start-0 pe-4 toggle-password rounded-right-12 pointer">
                                    <i data-lucide="eye" class="text-secondary opacity-50" width="20" height="20"></i>
                                </span>
                            </div>
                        </div>

                        <div class="text-center text-lg-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm bg-primary-rpts-v2 border-primary-rpts-v2 rounded-8">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Profile Picture Modal -->
    <div class="modal fade" id="updateProfilePictureModal" tabindex="-1" aria-labelledby="updateProfilePictureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden rounded-16">
                <!-- Header Accent -->
                <div class="header-accent-blue"></div>

                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="updateProfilePictureModalLabel">Update Profile Picture</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <div class="d-flex align-items-center gap-4">
                        <div class="rounded-pill overflow-hidden border shadow-sm square-120 flex-shrink-0">
                            <img src="https://api.dicebear.com/7.x/initials/svg?seed=EC" class="w-100 h-100 object-fit-cover" alt="Profile Preview">
                        </div>
                        <div class="flex-grow-1">
                            <div class="input-group">
                                <label class="btn btn-outline-secondary px-3 py-2 rounded-left-12" for="profilePictureInput">Choose File</label>
                                <input type="text" class="form-control bg-light border-start-0 rounded-right-12 no-caret" placeholder="No file chosen" readonly>
                                <input type="file" id="profilePictureInput" class="d-none">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-secondary px-4 py-2 fw-semibold" style="border-radius: 0.5rem; background-color: #6c757d; border-color: #6c757d;" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm bg-primary-rpts-v2 border-primary-rpts-v2 rounded-8">Save Picture</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Profile Info Modal -->
    <div class="modal fade" id="updateProfileInfoModal" tabindex="-1" aria-labelledby="updateProfileInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden rounded-16">
                <!-- Header Accent -->
                <div class="header-accent-blue"></div>

                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="updateProfileInfoModalLabel">Update Profile Info</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="updateProfileInfoForm">
                        <div class="mb-4">
                            <label class="form-label small fw-semibold text-secondary mb-1">Mobile Number</label>
                            <input type="text" class="form-control py-2 px-3 rounded-12" placeholder="Enter Mobile Number">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-semibold text-secondary mb-1">Address</label>
                            <input type="text" class="form-control py-2 px-3 rounded-12" placeholder="Enter Address">
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="updateProfileInfoForm" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm bg-primary-rpts-v2 border-primary-rpts-v2 rounded-8">Save</button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection