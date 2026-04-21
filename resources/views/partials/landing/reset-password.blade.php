<div class="login-form-container">
    <div class="login-top-bar">
        <h1 class="script-heading">RPTS</h1>
    </div>
    <div class="login-header-new">
        <h2 class="serif-subtext">Set New Password</h2>
        <p style="margin-top: 1rem; opacity: 0.7; font-size: 0.9rem;">Choose a strong password to secure your account.</p>
    </div>
    <form class="login-form" id="reset-password-new-form">
        <div class="form-group mb-3">
            <label>New Password</label>
            <div class="password-wrapper">
                <input type="password" id="reset-new-password" required>
                <i data-lucide="eye" class="toggle-password"></i>
            </div>
        </div>
        <div class="form-group">
            <label>Confirm New Password</label>
            <div class="password-wrapper">
                <input type="password" id="reset-confirm-password" required>
                <i data-lucide="eye" class="toggle-password"></i>
            </div>
        </div>
        <button type="submit" class="btn-login" id="btn-submit-new-password">Update Password</button>
    </form>

    <!-- Success Message (Initially Hidden) -->
    <div id="password-changed-success" style="display: none;" class="text-center forgot-password-success">
        <div class="success-icon-wrapper" style="background-color: #f0fdf4; color: #16a34a;">
            <i data-lucide="shield-check" width="32" height="32"></i>
        </div>
        <h4 class="fw-bold mb-2">Password Updated</h4>
        <p class="text-muted small mb-4">Your password has been successfully reset. You can now use your new password to sign in.</p>
        <button type="button" class="btn-login back-to-login" style="margin-top: 0;">
            Sign In Now
        </button>
    </div>
</div>

