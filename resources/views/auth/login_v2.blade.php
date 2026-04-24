@extends('layouts.homeapp_v2')

@section('style')
    <style>
        /* Standalone Page Background Fix for Login */
        body.landing-page {
            background: linear-gradient(rgba(0, 33, 71, 0.82), rgba(0, 33, 71, 0.92)),
                        url('{{ asset('assets/images/photoshop.webp') }}') center / cover fixed !important;
            overflow-y: auto !important;
            cursor: auto !important;
        }

        /* Disable scroll snapping for login to allow footer access */
        html {
            scroll-snap-type: none !important;
        }
        
        /* Hide custom cursor */
        .cursor-dot, .cursor-outline { 
            display: none !important; 
        }

        #page-content {
            padding: 5rem 0 3rem;
            min-height: calc(100vh - 150px);
            display: flex;
            align-items: center;
        }

        .login-form-container {
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }
    </style>
@endsection

@section('content')
<div class="login-form-container">
    <div class="login-top-bar">
        <h1 class="script-heading">RPTS</h1>
    </div>
    <div class="login-header-new">
        <h2 class="serif-subtext">Official Access Portal</h2>
        <p style="margin-top: 1rem; opacity: 0.7; font-size: 0.9rem;">Please enter your credentials to verify your identity.</p>
    </div>

    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.85rem;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="login-form" id="login-form-element">
        @csrf
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <i data-lucide="eye" class="toggle-password"></i>
            </div>
        </div>
        <div class="form-options">
            <label class="remember-me" for="remember">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}> <span>Remember Me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
            @endif
        </div>
        <button type="submit" class="btn-login" id="btn-submit-login">Sign In</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Enable white footer mode for the login page
        const landingFooter = document.getElementById('footer-container-wrapper');
        if (landingFooter) {
            landingFooter.classList.add('footer-rdip-mode');
        }
    });
</script>
@endsection
