<?php
// login.php
include 'header.php';
?>

<div class="form-container">
    <div class="form-icon"></div>
    <h2 class="form-title" data-i18n="welcomeBack">Log in to thetradingroutine</h2>
    <p class="form-subtitle" data-i18n="loginAccess">Continue your path to professional trading</p>
    <form id="loginForm">
        <div class="form-group">
            <label for="email">
                <span class="label-icon"></span> <span data-i18n="emailAddress">Email</span>
            </label>
            <div class="input-wrapper">
                <input type="email" id="email" name="email" placeholder="Email" required>
                <span class="input-focus-border"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="password">
                <span class="label-icon"></span> <span data-i18n="password">Password</span>
            </label>
            <div class="input-wrapper" style="position:relative;">
                <input type="password" id="password" name="password" placeholder="Password" required style="padding-right:3rem;">
                <span class="input-focus-border"></span>
                <span onclick="togglePasswordVisibility()" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:1.2rem;user-select:none;" id="eyeIcon">👁</span>
            </div>
        </div>
      <div class="form-group checkbox-group" style="display:flex;justify-content:space-between;align-items:center;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" id="rememberMe" name="rememberMe">
                <label for="rememberMe" class="checkbox-label" data-i18n="rememberMe">Remember me</label>
            </div>
            <a href="javascript:void(0)" onclick="showForgotPassword()" style="color:var(--primary-color);font-size:0.9rem;text-decoration:none;font-weight:600;">Forgot Password?</a>
        </div>
        <button type="submit" class="btn btn-primary form-submit-btn">
            <span class="btn-text" data-i18n="loginNow">Login Now</span>
            <span class="btn-icon">→</span>
        </button>

        <!-- Database Connection Test Button -->
        
        <div id="testResultMessage" style="margin-top: 1rem; padding: 1rem; border-radius: 8px; display: none;"></div>

       <div class="form-divider">
            <span>or</span>
        </div>

        <!-- Google Login Button -->
        <a href="<?php 
$isLocalhost = isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
$redirectUri = $isLocalhost 
    ? 'http%3A%2F%2Flocalhost%2Ftrading%2Fgoogle_callback.php'
    : 'https%3A%2F%2Fthetradingroutine.com%2Fgoogle_callback.php';
echo 'https://accounts.google.com/o/oauth2/v2/auth?client_id=438103912004-45o8vb794nl41fou14k91ubop38ga0qb.apps.googleusercontent.com&redirect_uri=' . $redirectUri . '&response_type=code&scope=email%20profile&access_type=offline';
?>"
           style="display:flex;align-items:center;justify-content:center;gap:0.8rem;padding:1rem;border:2px solid rgba(255,255,255,0.15);border-radius:12px;background:rgba(255,255,255,0.05);color:var(--text-light);text-decoration:none;font-weight:600;font-size:1rem;transition:all 0.3s ease;margin-bottom:1rem;"
           onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.borderColor='rgba(255,255,255,0.3)'"
           onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.borderColor='rgba(255,255,255,0.15)'">
            <svg width="20" height="20" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
            </svg>
            Continue with Google
        </a>

        <div class="form-footer">
            <p data-i18n="noAccount">Don't have an account?</p>
            <a href="signup.php" class="form-link" data-i18n="createAccount">Create Account</a>
        </div>
    </form>
</div>

<style>
    .form-icon {
        font-size: 3.5rem;
        text-align: center;
        margin-bottom: 1rem;
        animation: bounce 2s infinite;
    }

    .form-subtitle {
        text-align: center;
        color: var(--text-muted);
        margin-bottom: 2rem;
        font-size: 1rem;
    }

    .label-icon {
        margin-right: 0.5rem;
    }

    .input-wrapper {
        position: relative;
    }

    .input-focus-border {
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--primary-color), #00b894);
        transition: all 0.4s ease;
        transform: translateX(-50%);
    }

    .input-wrapper input:focus+.input-focus-border {
        width: 100%;
    }

    .form-submit-btn {
        width: 100%;
        padding: 1.2rem;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .btn-icon {
        transition: transform 0.3s ease;
    }

    .form-submit-btn:hover .btn-icon {
        transform: translateX(5px);
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 1rem 0;
    }

    .checkbox-label {
        cursor: pointer;
        font-size: 0.95rem;
        color: var(--text-muted);
        margin: 0;
    }

    #rememberMe {
        cursor: pointer;
        accent-color: var(--primary-color);
    }

    .oauth-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        margin-top: 1.5rem;
    }

    .oauth-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        padding: 1rem;
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        background: rgba(26, 31, 58, 0.5);
        color: var(--text-light);
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .oauth-btn:hover {
        border-color: var(--primary-color);
        background: rgba(26, 31, 58, 0.8);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 212, 255, 0.2);
    }

    .oauth-icon {
        font-size: 1.3rem;
    }

    .form-footer {
        text-align: center;
        margin-top: 2rem;
        color: var(--text-muted);
    }

    .form-footer p {
        margin-bottom: 0.5rem;
    }

    .form-link {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .form-link:hover {
        color: #00b894;
        transform: translateY(-2px);
    }

    .form-divider {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
    }

    .form-divider::before,
    .form-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(0, 212, 255, 0.2);
    }

    .form-divider span {
        padding: 0 1rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .admin-login-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem;
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 12px;
        color: #ffd700;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .admin-login-link:hover {
        background: rgba(255, 215, 0, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(255, 215, 0, 0.2);
    }

    .admin-icon {
        font-size: 1.2rem;
    }
</style>
<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.85);z-index:10000;justify-content:center;align-items:center;padding:2rem;">
    <div style="background:#0a0e27;border:2px solid rgba(0,212,255,0.3);border-radius:15px;padding:2.5rem;width:100%;max-width:450px;position:relative;box-shadow:0 20px 60px rgba(0,212,255,0.3);">
        <button onclick="hideForgotPassword()" style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--text-muted);">✕</button>
        <h2 style="color:var(--primary-color);text-align:center;margin-bottom:0.5rem;">Forgot Password</h2>
        <p style="color:var(--text-muted);text-align:center;margin-bottom:2rem;font-size:0.95rem;">Enter your email and we'll send you a new password</p>
        <div class="form-group">
            <label style="color:var(--text-muted);">Email Address</label>
            <input type="email" id="forgotEmail" placeholder="Enter your email" style="width:100%;padding:1rem;background:rgba(0,212,255,0.05);border:2px solid rgba(0,212,255,0.2);border-radius:10px;color:var(--text-main);font-size:1rem;margin-top:0.5rem;box-sizing:border-box;">
        </div>
        <div id="forgotMessage" style="display:none;padding:1rem;border-radius:8px;margin:1rem 0;text-align:center;"></div>
        <button onclick="submitForgotPassword()" id="forgotSubmitBtn" style="width:100%;padding:1.2rem;background:white;color:#000;border:none;border-radius:10px;font-weight:bold;font-size:1.1rem;cursor:pointer;margin-top:1rem;transition:all 0.3s ease;">Send New Password</button>
    </div>
</div>

<script>
    function showForgotPassword() {
        const modal = document.getElementById('forgotPasswordModal');
        modal.style.display = 'flex';
        document.getElementById('forgotEmail').value = '';
        document.getElementById('forgotMessage').style.display = 'none';
    }

    function hideForgotPassword() {
        document.getElementById('forgotPasswordModal').style.display = 'none';
    }

    async function submitForgotPassword() {
        const email = document.getElementById('forgotEmail').value.trim();
        const msgDiv = document.getElementById('forgotMessage');
        const btn = document.getElementById('forgotSubmitBtn');

        if (!email) {
            msgDiv.style.display = 'block';
            msgDiv.style.background = 'rgba(220,38,38,0.2)';
            msgDiv.style.color = '#dc2626';
            msgDiv.textContent = '❌ Please enter your email address';
            return;
        }

        btn.disabled = true;
        btn.textContent = '⏳ Sending...';

        try {
            const formData = new FormData();
            formData.append('email', email);

            const response = await fetch('forgot_password.php', { method: 'POST', body: formData });
            const result = await response.json();

            msgDiv.style.display = 'block';
         if (result.success) {
                msgDiv.style.background = 'rgba(0,255,136,0.2)';
                msgDiv.style.color = '#00ff88';
msgDiv.textContent = '✅ Password reset link sent! Check your email.';                btn.textContent = '✓ Sent!';
                sessionStorage.setItem('forcePasswordChange', 'true');
                setTimeout(() => hideForgotPassword(), 3000);
            } else {
                msgDiv.style.background = 'rgba(220,38,38,0.2)';
                msgDiv.style.color = '#dc2626';
                msgDiv.textContent = '❌ ' + result.message;
                btn.disabled = false;
                btn.textContent = 'Send New Password';
            }
        } catch (err) {
            msgDiv.style.display = 'block';
            msgDiv.style.background = 'rgba(220,38,38,0.2)';
            msgDiv.style.color = '#dc2626';
            msgDiv.textContent = '❌ Request failed. Please try again.';
            btn.disabled = false;
            btn.textContent = 'Send New Password';
        }
    }

    // Close modal when clicking outside
    document.getElementById('forgotPasswordModal').addEventListener('click', function(e) {
        if (e.target === this) hideForgotPassword();
    });
</script>
<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = '👁';
        } else {
            input.type = 'password';
            icon.textContent = '👁';
        }
    }
</script>
<?php include 'footer.php'; ?>