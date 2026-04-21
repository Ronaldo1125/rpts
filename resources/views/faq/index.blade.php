@extends('layouts.homeapp_v2')

@section('style')
    <style>
        /* Standalone Page Background Fix for FAQ */
        body.landing-page {
            background: #ffffff !important; /* FAQ uses clean white background */
            overflow-y: auto !important;
            cursor: auto !important;
        }
        
        /* Hide custom cursor */
        .cursor-dot, .cursor-outline { 
            display: none !important; 
        }

        /* Disable scroll snapping */
        html {
            scroll-snap-type: none !important;
        }

        /* Sticky header fix for white background */
        .top-bar {
            background: rgba(255, 255, 255, 0.95) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
            backdrop-filter: blur(15px) !important;
            padding: 1.2rem 4rem !important;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05) !important;
        }

        .top-bar .motto,
        .top-bar .top-left i,
        .top-bar .top-left svg,
        .top-bar #menu-toggle i,
        .top-bar #menu-toggle svg,
        .top-bar #search-toggle i,
        .top-bar .system-title,
        .top-bar .system-subtitle {
            color: #0f172a !important;
        }

        .top-bar .header-logo {
            filter: none !important;
            opacity: 0.85 !important;
        }
    </style>
@endsection

@section('content')
    @include('partials.landing.faq')
@endsection
