<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';
session_start();

define('GOOGLE_CLIENT_ID',     '438103912004-45o8vb794nl41fou14k91ubop38ga0qb.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
$isLocalhost = isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);
define('GOOGLE_REDIRECT_URI', $isLocalhost 
    ? 'http://localhost/trading/google_callback.php' 
    : 'https://thetradingroutine.com/google_callback.php');if (!isset($_GET['code'])) {
    header('Location: login.php');
    exit();
}

$code = $_GET['code'];

// ===== Step 1 — Exchange code for access token using cURL =====
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://oauth2.googleapis.com/token',
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code'
    ]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded']
]);
$tokenResponse = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($tokenResponse, true);

if (!isset($tokenData['access_token'])) {
    header('Location: login.php?error=google_token_failed');
    exit();
}

// ===== Step 2 — Get user info using cURL =====
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => 'https://www.googleapis.com/oauth2/v2/userinfo',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tokenData['access_token']]
]);
$userInfoResponse = curl_exec($ch);
curl_close($ch);

$googleUser = json_decode($userInfoResponse, true);

if (!isset($googleUser['email'])) {
    header('Location: login.php?error=google_user_failed');
    exit();
}

$email    = $conn->real_escape_string($googleUser['email']);
$name     = $conn->real_escape_string($googleUser['name'] ?? $googleUser['email']);

// ===== Step 3 — Check if user exists =====
$result = $conn->query("SELECT id, username FROM users WHERE email = '$email' LIMIT 1");

if ($result && $result->num_rows > 0) {
    $user     = $result->fetch_assoc();
    $userId   = $user['id'];
    $username = $user['username'];
} else {
    $username = $conn->real_escape_string(explode('@', $googleUser['email'])[0]);
    $password = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, email, password, created_at) VALUES ('$username', '$email', '$password', NOW())");
    $userId = $conn->insert_id;

    // Send registration notification to support@thetradingroutine.com
    require_once __DIR__ . '/notify_admin.php';
    notifySupportNewRegistration($username, $email, 'Google OAuth');
}

$conn->close();

// Set PHP session so server-side pages recognize the user
$_SESSION['user_id'] = $userId;
$_SESSION['username'] = $username;
$_SESSION['email'] = $email;
?>
<!DOCTYPE html>
<html>
<head><title>Logging in...</title></head>
<body>
<script>
    sessionStorage.setItem('userLogged', 'true');
    sessionStorage.setItem('currentUsername', <?php echo json_encode($username); ?>);
    sessionStorage.setItem('currentEmail', <?php echo json_encode($email); ?>);
    sessionStorage.setItem('userId', '<?php echo intval($userId); ?>');
    window.location.href = 'index.php';
</script>
</body>
</html>
