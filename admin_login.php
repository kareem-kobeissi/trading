<?php
// admin_login.php
include 'header.php';
?>

<div class="form-container admin-form">
    <div class="form-icon admin-icon-badge">👑</div>
    <h2 class="form-title" data-i18n="adminPortal">Admin Portal</h2>
    <p class="form-subtitle" data-i18n="secureAccess">Secure access to dashboard</p>
    <form id="loginForm" data-admin-login="true">
        <div class="form-group">
            <label for="email">
                <span class="label-icon">📧</span> <span data-i18n="adminEmail">Admin Email</span>
            </label>
            <div class="input-wrapper">
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars(getenv('ADMIN_EMAIL') ?: '', ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="username">
                <span class="input-focus-border admin-border"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="password">
                <span class="label-icon">🔐</span> <span data-i18n="password">Password</span>
            </label>
            <div class="input-wrapper">
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <span class="input-focus-border admin-border"></span>
            </div>
        </div>
        <button type="submit" class="btn btn-admin form-submit-btn">
            <span class="btn-text" data-i18n="accessDashboard">Access Dashboard</span>
            <span class="btn-icon">🔓</span>
        </button>
        <div class="form-footer">
            <a href="login.php" class="back-link" data-i18n="backUserLogin">
                <span class="back-arrow">←</span> Back to User Login
            </a>
        </div>
    </form>
</div>

<style>
    .admin-form {
        border-color: rgba(255, 215, 0, 0.3) !important;
    }

    .admin-form::before {
        background: linear-gradient(90deg, transparent, #ffd700, transparent) !important;
    }

    .admin-icon-badge {
        font-size: 3.5rem;
        text-align: center;
        margin-bottom: 1rem;
        animation: bounce 2s infinite;
        filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.5));
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

    .admin-border {
        background: linear-gradient(90deg, #ffd700, #ffaa00) !important;
    }

    .input-wrapper input:focus+.input-focus-border {
        width: 100%;
    }

    .input-wrapper input[readonly] {
        background: rgba(255, 215, 0, 0.1);
        border-color: rgba(255, 215, 0, 0.3);
        color: #ffd700;
    }

    .btn-admin {
        width: 100%;
        padding: 1.2rem;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
        background: linear-gradient(135deg, #ffd700, #ffaa00, #ff8800) !important;
        color: #000 !important;
    }

    .btn-admin:hover {
        box-shadow: 0 15px 40px rgba(255, 215, 0, 0.4) !important;
    }

    .btn-icon {
        transition: transform 0.3s ease;
    }

    .btn-admin:hover .btn-icon {
        transform: scale(1.2);
    }

    .form-footer {
        text-align: center;
        margin-top: 2rem;
    }

    .back-link {
        color: var(--text-muted);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        color: var(--primary-color);
    }

    .back-arrow {
        transition: transform 0.3s ease;
    }

    .back-link:hover .back-arrow {
        transform: translateX(-5px);
    }
</style>

<?php include 'footer.php'; ?>
