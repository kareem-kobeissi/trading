<?php
include 'header.php';
require_once 'config.php';
date_default_timezone_set('Asia/Beirut');

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$valid = false;
$username = '';

if ($token) {
    $escaped = $conn->real_escape_string($token);
    $result = $conn->query("SELECT id, username FROM users WHERE reset_token = '$escaped' AND reset_expires > NOW() LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $valid = true;
        $user = $result->fetch_assoc();
        $username = $user['username'];
    }
}
?>

<div class="form-container">
    <?php if (!$token || !$valid): ?>
        <div style="text-align:center;padding:3rem;">
            <div style="font-size:4rem;margin-bottom:1rem;">❌</div>
            <h2 style="color:#dc2626;margin-bottom:1rem;">Invalid or Expired Link</h2>
            <p style="color:var(--text-muted);margin-bottom:2rem;">This password reset link has expired or is invalid. Please request a new one.</p>
            <a href="login.php" style="display:inline-block;padding:1rem 2rem;background:white;color:#000;border-radius:10px;font-weight:bold;text-decoration:none;">Back to Login</a>
        </div>
    <?php else: ?>
        <div class="form-icon" style="text-align:center;font-size:3rem;margin-bottom:1rem;">🔐</div>
        <h2 class="form-title">Set New Password</h2>
        <p class="form-subtitle">Hello <strong style="color:var(--primary-color);"><?php echo htmlspecialchars($username); ?></strong>, enter your new password below</p>

        <div class="form-group">
            <label style="color:var(--text-muted);display:block;margin-bottom:0.5rem;">New Password</label>
            <div style="position:relative;">
                <input type="password" id="newPassword" placeholder="Enter new password" style="width:100%;padding:1rem;padding-right:3rem;background:rgba(0,212,255,0.05);border:2px solid rgba(0,212,255,0.2);border-radius:10px;color:var(--text-main);font-size:1rem;box-sizing:border-box;">
                <span onclick="togglePwd('newPassword','eye1')" id="eye1" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:1.2rem;">👁</span>
            </div>
        </div>

        <div class="form-group" style="margin-top:1.5rem;">
            <label style="color:var(--text-muted);display:block;margin-bottom:0.5rem;">Confirm New Password</label>
            <div style="position:relative;">
                <input type="password" id="confirmPassword" placeholder="Confirm new password" style="width:100%;padding:1rem;padding-right:3rem;background:rgba(0,212,255,0.05);border:2px solid rgba(0,212,255,0.2);border-radius:10px;color:var(--text-main);font-size:1rem;box-sizing:border-box;">
                <span onclick="togglePwd('confirmPassword','eye2')" id="eye2" style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:1.2rem;">👁</span>
            </div>
        </div>

        <div id="resetMessage" style="display:none;padding:1rem;border-radius:8px;margin:1rem 0;text-align:center;"></div>

        <button onclick="submitReset()" id="resetBtn" style="width:100%;padding:1.2rem;background:white;color:#000;border:none;border-radius:10px;font-weight:bold;font-size:1.1rem;cursor:pointer;margin-top:1.5rem;transition:all 0.3s ease;">Set New Password</button>
    <?php endif; ?>
</div>

<script>
    const resetToken = '<?php echo htmlspecialchars($token); ?>';

    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = '👁';
        } else {
            input.type = 'password';
            icon.textContent = '👁';
        }
    }

    async function submitReset() {
        const newPassword = document.getElementById('newPassword').value.trim();
        const confirmPassword = document.getElementById('confirmPassword').value.trim();
        const msgDiv = document.getElementById('resetMessage');
        const btn = document.getElementById('resetBtn');

        const showMsg = (msg, success) => {
            msgDiv.style.display = 'block';
            msgDiv.style.background = success ? 'rgba(0,255,136,0.2)' : 'rgba(220,38,38,0.2)';
            msgDiv.style.color = success ? '#00ff88' : '#dc2626';
            msgDiv.textContent = msg;
        };

        if (!newPassword || !confirmPassword) {
            showMsg('❌ Please fill in all fields', false); return;
        }
        if (newPassword.length < 6) {
            showMsg('❌ Password must be at least 6 characters', false); return;
        }
        if (newPassword !== confirmPassword) {
            showMsg('❌ Passwords do not match', false); return;
        }

        btn.disabled = true;
        btn.textContent = '⏳ Updating...';

        try {
            const formData = new FormData();
            formData.append('token', resetToken);
            formData.append('new_password', newPassword);

            const response = await fetch('do_reset_password.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (result.success) {
                showMsg('✅ Password updated successfully! Redirecting to login...', true);
                btn.textContent = '✓ Done!';
                setTimeout(() => window.location.href = 'login.php', 2500);
            } else {
                showMsg('❌ ' + result.message, false);
                btn.disabled = false;
                btn.textContent = 'Set New Password';
            }
        } catch (err) {
            showMsg('❌ Request failed. Please try again.', false);
            btn.disabled = false;
            btn.textContent = 'Set New Password';
        }
    }
</script>

<?php include 'footer.php'; ?>