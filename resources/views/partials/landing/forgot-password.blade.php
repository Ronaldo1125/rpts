<div class="login-form-container">
    <button class="btn-close-login" id="btn-back-login">
        <i data-lucide="arrow-left"></i>
    </button>
    <div class="login-top-bar">
        <h1 class="script-heading">RPTS</h1>
    </div>
    <div class="login-header-new">
        <h2 class="serif-subtext">Reset Your Password</h2>
        <p style="margin-top: 1rem; opacity: 0.7; font-size: 0.9rem;">Enter your email address and we'll send you a link to reset your password.</p>
    </div>
    <form class="login-form" id="forgot-password-form">
        <div class="form-group">
            <label for="reset-email">Email Address</label>
            <input type="email" id="reset-email" placeholder="you@agency.gov.ph" required>
        </div>
        <button type="submit" class="btn-login" id="btn-submit-reset">Send Reset Link</button>
        
        <div class="back-link-subtext text-center">
            <p>Remember your password? <a href="#" class="back-to-login">Sign In</a></p>
        </div>
    </form>

    <!-- Success Message (Initially Hidden) -->
    <div id="reset-success-message" style="display: none;" class="text-center forgot-password-success">
        <div class="success-icon-wrapper">
            <i data-lucide="mail-check" width="32" height="32"></i>
        </div>
        <h4 class="fw-bold mb-2">Check Your Email</h4>
        <p class="text-muted small mb-4">We have sent a password reset link to <strong id="sent-email-display" class="text-dark">your email</strong>. Please check your inbox and follow the instructions.</p>
        <button type="button" class="btn-login back-to-login mt-4">
            Back to Login
        </button>
    </div>
</div>

