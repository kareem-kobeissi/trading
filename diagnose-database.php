<?php
// Diagnostic script to check database and credentials
echo "=== Trading Routine Database Diagnostic ===\n\n";

// Check PHP version
echo "PHP Version: " . phpversion() . "\n";
echo "MySQLi Extension: " . (extension_loaded('mysqli') ? "✅ Enabled" : "❌ Disabled") . "\n\n";

// Read current config
echo "Current config.php settings:\n";
echo "═══════════════════════════════════════════════\n";
$config_content = file_get_contents('config.php');
preg_match("/define\('DB_HOST',\s*'([^']+)'\)/", $config_content, $host);
preg_match("/define\('DB_USER',\s*'([^']+)'\)/", $config_content, $user);
preg_match("/define\('DB_PASS',\s*'([^']+)'\)/", $config_content, $pass);
preg_match("/define\('DB_NAME',\s*'([^']+)'\)/", $config_content, $name);

echo "DB_HOST: " . ($host[1] ?? 'NOT FOUND') . "\n";
echo "DB_USER: " . ($user[1] ?? 'NOT FOUND') . "\n";
echo "DB_PASS: " . (($pass[1] ?? 'NOT FOUND') ? "***" : "EMPTY") . "\n";
echo "DB_NAME: " . ($name[1] ?? 'NOT FOUND') . "\n\n";

// Try different credential combinations
$credentials = [
    ['localhost', 'root', '', 'platform'],
    ['localhost', 'root', 'root', 'platform'],
    ['localhost', 'tr', 'Nevereat99$', 'platform'],
    ['127.0.0.1', 'root', '', 'platform'],
    ['localhost', 'root', '', 'trading'],
];

echo "Testing database connections...\n";
echo "═══════════════════════════════════════════════\n";

foreach ($credentials as $cred) {
    $host = $cred[0];
    $user = $cred[1];
    $pass = $cred[2];
    $db   = $cred[3];

    $status = "❌";
    try {
        $conn = new mysqli($host, $user, $pass);
        if (!$conn->connect_error) {
            $status = "✅";
            $conn->close();
        }
    } catch (Exception $e) {
        // Connection failed
    }

    echo "$status User: '$user', Host: '$host', DB: '$db'\n";
}

echo "\n📝 Configuration Recommendation:\n";
echo "═══════════════════════════════════════════════\n";
echo "If you're using XAMPP with default settings:\n";
echo "  DB_HOST='localhost'\n";
echo "  DB_USER='root'\n";
echo "  DB_PASS='' (empty)\n";
echo "  DB_NAME='trading' or 'platform'\n\n";
echo "This is typically set in: config.php\n";
