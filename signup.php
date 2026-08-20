<?php
// signup.php
include 'header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@29.1.0/dist/css/intlTelInput.css">

<div class="form-container">
    <div class="form-icon"></div>
    <h2 class="form-title" data-i18n="joinUs">Start Your Professional Trading Journey</h2>
    <p class="form-subtitle" data-i18n="startJourney">Access professional trading education and market tools</p>
    <form id="signupForm">
        <div class="form-group">
            <label for="username">
                <span class="label-icon"></span> <span data-i18n="fullName">Full Name</span>
            </label>
            <div class="input-wrapper">
                <input type="text" id="username" name="username" placeholder="Full Name" required>
                <span class="input-focus-border"></span>
            </div>
        </div>
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
            <label for="signupPhone"><span class="label-icon"></span> Phone Number</label>
            <div class="input-wrapper">
                <input type="tel" id="signupPhone" name="phone" placeholder="Your phone number" autocomplete="tel" required>
                <span class="input-focus-border"></span>
            </div>

        </div>
        <div class="form-group" id="codeInputGroup" style="display: none;">
            <label for="verificationCode">
                <span class="label-icon"></span> <span data-i18n="verificationCode">Verification Code</span>
            </label>
            <div class="input-wrapper">
                <input type="text" id="verificationCode" name="verificationCode" placeholder="Enter 6-digit code" maxlength="6">
                <span class="input-focus-border"></span>
            </div>
            <small class="timer" id="codeTimer"></small>
        </div>
        <div class="form-group">
            <label for="password">
                <span class="label-icon"></span> <span data-i18n="password">Password</span>
            </label>
            <div class="input-wrapper">
                <input type="password" id="password" name="password" placeholder="Min. 6 characters" required>
                <span class="input-focus-border"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="confirmPassword">
                <span class="label-icon"></span> <span data-i18n="confirmPassword">Confirm Password</span>
            </label>
            <div class="input-wrapper">
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••" required>
                <span class="input-focus-border"></span>
            </div>
        </div>
        <button type="submit" class="btn btn-primary form-submit-btn">
            <span class="btn-text" data-i18n="createAccount">Create Account</span>
        </button>
        <div class="form-footer">
            <p data-i18n="haveAccount">Already have an account?</p>
            <a href="login.php" class="form-link" data-i18n="loginHere">Login Here</a>
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
        transform: scale(1.2) rotate(15deg);
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

    .btn-verify-email {
        margin-top: 0.5rem;
        padding: 0.6rem 1.2rem;
        background: linear-gradient(135deg, var(--primary-color), #00b894);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-verify-email:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
    }

    .btn-verify-email:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    #codeInputGroup {
        position: relative;
    }

    .timer {
        display: block;
        margin-top: 0.5rem;
        color: var(--primary-color);
        font-size: 0.85rem;
    }
    .iti { width: 100%; }
    .iti input { width: 100%; }
    .iti__country-container { color: #222; }
</style>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@29.1.0/dist/js/intlTelInput.min.js"></script>
<script>
    window.signupPhoneInput = null;
    const signupPhoneElement = document.getElementById('signupPhone');
    if (signupPhoneElement && window.intlTelInput) {
        window.signupPhoneInput = window.intlTelInput(signupPhoneElement, {
            initialCountry: 'lb',
            separateDialCode: true,
            countrySearch: true,
            countryOrder: ['lb', 'ae', 'sa', 'qa', 'kw', 'in', 'us', 'gb']
        });
    }
</script>

<?php include 'footer.php'; ?>
