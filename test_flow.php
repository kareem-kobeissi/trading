<?php
header('Content-Type: application/json');

$log = [];
$log[] = "=== COMPLETE FLOW TEST ===";

// Generate unique test data
$testEmail = 'flowtest_' . time() . '_' . rand(1000, 9999) . '@test.com';
$testUsername = 'FlowUser_' . time();
$testPassword = 'TestPass123';

try {
    // Step 1: Send code
    $log[] = "STEP 1: Requesting verification code for $testEmail";
    $ch1 = curl_init();
    curl_setopt_array($ch1, [
        CURLOPT_URL => 'http://localhost/trading/auth_signup.php',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => 'action=send_code&email=' . urlencode($testEmail),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $sendResult = curl_exec($ch1);
    curl_close($ch1);
    $sendData = json_decode($sendResult, true);
    $log[] = "Send code response: " . $sendResult;

    if (!$sendData['success']) {
        throw new Exception("Failed to send code: " . $sendData['message']);
    }

    // Step 2: Get code from database
    $log[] = "STEP 2: Retrieving verification code from database";
    sleep(1); // Give it a moment
    $ch2 = curl_init();
    curl_setopt_array($ch2, [
        CURLOPT_URL => 'http://localhost/trading/get_code.php?email=' . urlencode($testEmail),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $codeResult = curl_exec($ch2);
    curl_close($ch2);
    $codeData = json_decode($codeResult, true);
    $log[] = "Get code response: " . $codeResult;

    if (!isset($codeData['code'])) {
        throw new Exception("Failed to get code from database");
    }

    $testCode = $codeData['code'];
    $log[] = "Retrieved code: $testCode";

    // Step 3: Verify code and create account
    $log[] = "STEP 3: Creating account with credentials: username=$testUsername, email=$testEmail, password=$testPassword, code=$testCode";
    $postData = 'action=verify_and_create&username=' . urlencode($testUsername) . '&email=' . urlencode($testEmail) . '&password=' . urlencode($testPassword) . '&code=' . urlencode($testCode);
    $log[] = "POST data: $postData";

    $ch3 = curl_init();
    curl_setopt_array($ch3, [
        CURLOPT_URL => 'http://localhost/trading/auth_signup.php',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
    ]);
    $verifyResult = curl_exec($ch3);
    curl_close($ch3);
    $log[] = "Verify result: " . $verifyResult;

    $verifyData = json_decode($verifyResult, true);

    if (!$verifyData['success']) {
        throw new Exception("Failed to create account: " . $verifyData['message']);
    }

    $log[] = "✓ COMPLETE FLOW SUCCESSFUL!";
} catch (Exception $e) {
    $log[] = "✗ ERROR: " . $e->getMessage();
}

echo json_encode($log, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
