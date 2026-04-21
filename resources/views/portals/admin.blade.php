@extends('layouts.portal')

@section('title', 'Admin Portal')

@section('config')
    <script>
        // Portal Configuration
        window.APP_CONFIG = {
            sidebarPath: '{{ asset("components/sidebar.html") }}', // Still kept for backward ref but loader is disabled
            defaultPage: 'dashboards'
        };
    </script>
@endsection

@section('sidebar')
    @include('partials.portal.sidebar')
@endsection

@section('topnav')
    @include('partials.portal.topnav')
@endsection

@section('footer')
    @include('partials.portal.footer')
@endsection

@section('pages')
    {{-- Common Pages --}}
    @include('pages.profile')
    @include('pages.project-workspace')
    @include('pages.cpp-view')

    {{-- Admin Pages --}}
    @include('pages.admin.reports')
    @include('pages.admin.projects')
    @include('pages.admin.permissions')
    @include('pages.admin.endorse-year')
    @include('pages.admin.indicator')
    @include('pages.admin.activity-logs')
    @include('pages.admin.manage-submissions')
    @include('pages.admin.users')
    @include('pages.admin.roles')
    @include('pages.admin.rdp-chapter')
    @include('pages.admin.sector')
    @include('pages.admin.sub-sector')
    @include('pages.admin.status')
    @include('pages.admin.dashboards')
    @include('pages.admin.admin-comments-recommendations')
    @include('pages.admin.admin-referrals')
    @include('pages.admin.admin-project-assessment')
    @include('pages.admin.project-assessment-report')
    @include('pages.admin.project-assessment-report-form')
    @include('pages.admin.referrals')
    @include('pages.admin.funding-category')
    @include('pages.admin.test-and-evaluation')
    @include('pages.admin.test-and-evaluation-form')
    @include('pages.admin.comments-recommendations')
    @include('pages.admin.comments-recommendations-form')
    @include('pages.admin.agency')
    @include('pages.admin.component-project')

    {{-- Staff Pages --}}
    @include('pages.staff.staff-dashboard')
    @include('pages.staff.staff-comments-recommendations')
    @include('pages.staff.staff-project-assessment')
    @include('pages.staff.staff-referrals')
    @include('pages.staff.pdipbd-staff-project-assessment')

    {{-- Division Head Pages --}}
    @include('pages.division-head.division-head-dashboard')
    @include('pages.division-head.division-head-comments-recommendations')
    @include('pages.division-head.division-head-project-assessment')
    @include('pages.division-head.division-head-referrals')

    {{-- Agency Pages --}}
    @include('pages.agency.submissions')
    @include('pages.agency.agency-dashboard')
    @include('pages.agency.agency-comments')
    @include('pages.agency.cipg-guide')
    @include('pages.agency.cpp-form')
    @include('pages.agency.create-component-project')
@endsection

@section('content')
    <!-- Global Modals for Admin -->
    <div class="modal fade" id="addIndicatorModal" tabindex="-1" aria-labelledby="addIndicatorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 1rem;">
                <div style="height: 4px; background-color: #154A9A;"></div>
                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="addIndicatorModalLabel">Add Indicator</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="addIndicatorForm">
                        <div class="mb-0">
                            <label for="indicatorName" class="form-label small fw-semibold text-secondary mb-1">Indicator Name</label>
                            <input type="text" class="form-control" id="indicatorName" placeholder="Enter Indicator Name" style="border-radius: 0.75rem;" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addIndicatorForm" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="background-color: #154A9A; border-color: #154A9A; border-radius: 0.75rem !important;">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Core System Script (Type Module) -->
    <script type="module" src="{{ asset('js/common/main.js') }}"></script>
    <script src="{{ asset('js/admin/admin-referred-stage-loader.js') }}"></script>
    <script src="{{ asset('js/admin/admin-evaluation-stage-loader.js') }}"></script>
@endsection
