@extends('layouts.portal')

@section('title', 'PDIPBD Staff Portal')

@section('config')
    <script>
        // Portal Configuration
        window.APP_CONFIG = {
            sidebarPath: '{{ asset("components/pdipbd-staff-sidebar.html") }}',
            defaultPage: 'manage-submissions'
        };
    </script>
@endsection

@section('sidebar')
    @include('partials.portal.pdipbd-staff-sidebar')
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

    {{-- PDIPBD Staff Specific List --}}
    @include('pages.admin.manage-submissions')
    @include('pages.admin.projects')
    @include('pages.admin.component-project')
    @include('pages.admin.test-and-evaluation')
    @include('pages.admin.test-and-evaluation-form')
    @include('pages.agency.cpp-form') {{-- Needed for view-only archive --}}
    @include('pages.admin.project-assessment-report')
    @include('pages.admin.project-assessment-report-form')
    @include('pages.admin.comments-recommendations')
    @include('pages.admin.comments-recommendations-form')

    {{-- Include Staff Pages as well for PDIPBD review role --}}
    @include('pages.staff.staff-dashboard')
    @include('pages.staff.staff-comments-recommendations')
    @include('pages.staff.staff-project-assessment')
    @include('pages.staff.staff-referrals')
    @include('pages.staff.pdipbd-staff-project-assessment')
@endsection

@section('scripts')
    <!-- Core System Script (Type Module) -->
    <script type="module" src="{{ asset('js/common/main.js') }}"></script>
    <script src="{{ asset('js/admin/admin-evaluation-stage-loader.js') }}"></script>
@endsection
