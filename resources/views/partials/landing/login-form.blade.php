<div class="login-form-container">
    <button class="btn-close-login" id="btn-back-choices">
        <i data-lucide="x"></i>
    </button>
    <div class="login-top-bar">
        <h1 class="script-heading">RPTS</h1>
    </div>
    <div class="login-header-new">
        <h2 class="serif-subtext">Official Access Portal</h2>
        <p style="margin-top: 1rem; opacity: 0.7; font-size: 0.9rem;">Please enter your credentials to verify your
            identity.</p>
    </div>
    <form method="POST" action="{{ route('login') }}" class="login-form" id="login-form-element">
        @csrf
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>
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
                <input type="checkbox" id="remember" name="remember"> <span>Remember Me</span>
            </label>
            <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
        </div>
        <button type="submit" class="btn-login" id="btn-submit-login">Sign In</button>
    </form>
</div>
