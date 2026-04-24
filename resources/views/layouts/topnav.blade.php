<nav class="navbar navbar-expand floating-navbar px-4 py-4">
    <div class="d-flex align-items-center w-100">
        <button class="btn btn-link text-secondary me-3 d-md-none" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i data-lucide="menu"></i>
        </button>


        <div class="ms-auto d-flex align-items-center gap-2">
            <!-- Notification Bell -->
            <div class="dropdown me-1">
                <button class="btn btn-link text-secondary position-relative p-2 rounded-circle shadow-none border-0 hover-bg-light" 
                        type="button" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false">
                    <i data-lucide="bell" width="22"></i>
                    <span class="position-absolute translate-middle badge rounded-circle bg-danger border border-white"
                          style="top: 12px; right: 2px; width: 10px; height: 10px; padding: 0; display: none;">
                        <span class="visually-hidden">New notifications</span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-0 overflow-hidden" 
                     style="width: 320px; border-radius: 16px;" aria-labelledby="notificationBell">
                    <div class="px-4 py-3 bg-primary text-white d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-bold">Notifications</h6>
                        <span class="badge bg-white text-primary rounded-pill small fw-bold">3 New</span>
                    </div>
                    <div class="notification-list overflow-auto" style="max-height: 350px;">
                        <!-- Placeholder notifications -->
                        <a href="#" class="dropdown-item px-4 py-3 border-bottom d-flex gap-3 align-items-start whitespace-normal">
                            <div class="rounded-circle bg-soft-blue p-2 flex-shrink-0">
                                <i data-lucide="file-text" width="16" class="text-primary"></i>
                            </div>
                            <div>
                                <div class="small fw-bold text-dark mb-1">New Submission Received</div>
                                <div class="text-muted smaller" style="font-size: 0.75rem;">DPWH submitted "Bicol River Basin Dev..." for validation.</div>
                                <div class="text-primary mt-2" style="font-size: 0.65rem; font-weight: 600;">2 minutes ago</div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item px-4 py-3 border-bottom d-flex gap-3 align-items-start whitespace-normal">
                            <div class="rounded-circle bg-soft-yellow p-2 flex-shrink-0">
                                <i data-lucide="message-square" width="16" class="text-warning"></i>
                            </div>
                            <div>
                                <div class="small fw-bold text-dark mb-1">New Comment Added</div>
                                <div class="text-muted smaller" style="font-size: 0.75rem;">Division Head added comments on your recent project.</div>
                                <div class="text-primary mt-2" style="font-size: 0.65rem; font-weight: 600;">1 hour ago</div>
                            </div>
                        </a>
                        <a href="#" class="dropdown-item px-4 py-3 d-flex gap-3 align-items-start whitespace-normal">
                            <div class="rounded-circle bg-soft-green p-2 flex-shrink-0">
                                <i data-lucide="check-circle" width="16" class="text-success"></i>
                            </div>
                            <div>
                                <div class="small fw-bold text-dark mb-1">Validation Completed</div>
                                <div class="text-muted smaller" style="font-size: 0.75rem;">Completeness test for "Regional Health..." is verified.</div>
                                <div class="text-primary mt-2" style="font-size: 0.65rem; font-weight: 600;">Yesterday</div>
                            </div>
                        </a>
                    </div>
                    <div class="px-4 py-2 bg-light text-center">
                        <a href="#" class="small fw-bold text-primary text-decoration-none">View All Notifications</a>
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <div class="d-flex align-items-center cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="position-relative d-flex align-items-center justify-content-center rounded-circle bg-primary text-white shadow-sm"
                        style="width: 38px; height: 38px;">
                        <i data-lucide="user" width="18"></i>
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                            style="width: 10px; height: 10px;"></span>
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" style="border-radius: 12px;">
                    <li>
                        <div class="px-3 py-2 border-bottom mb-2 bg-light rounded-top">
                            <div class="fw-bold small text-dark topnav-user-name">{{ Auth::user()->name }}</div>
                            <div class="text-muted topnav-user-role" style="font-size: 0.75rem;">{{ Auth::user()->roles->first()->name }}</div>
                        </div>
                    </li>
                    <li><a href="{{ route('profiles.index_v2') }}" class="dropdown-item d-flex align-items-center gap-2 py-2" href="#" data-page="profile">
                            <i data-lucide="user" width="16"></i> Profile
                        </a></li>
                    <li>
                        <hr class="dropdown-divider mx-2">
                    </li>
                    <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();"
                              class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger logout-link" href="#">
                            <i data-lucide="log-out" width="16"></i> Logout
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                          @csrf
                      </form>
                </ul>
            </div>
        </div>
    </div>
</nav>

