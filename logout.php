<?php
session_start();

$_SESSION = [];
session_unset();
session_destroy();

// Delete cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
?>

<script>
sessionStorage.clear();
localStorage.clear();
window.location.href = "index.php";
</script>