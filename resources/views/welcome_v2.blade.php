@extends('layouts.homeapp_v2')

@section('content')
    <!-- Main Dynamic Sections -->
    <div id="portal-container" class="entry-modal-overlay">
        @include('partials.landing.portal')
    </div>


    <section id="hero-container-wrapper" class="hero-container">
        @include('partials.landing.hero')
    </section>

    <!-- News & Announcements Section -->
    <section id="announcements" class="news-section">
        @include('partials.landing.announcements')
    </section>
@endsection