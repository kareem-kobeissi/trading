<?php
// Test calling auth_signup.php directly
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost/trading/auth_signup.php',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => 'action=verify_and_create&username=UniqueUser_' . time() . '&email=newtest_380714685@test.com&password=TestPass123&code=694076',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
]);

$response = curl_exec($curl);
$error = curl_error($curl);
$info = curl_getinfo($curl);
curl_close($curl);

header('Content-Type: application/json');
echo json_encode([
    'response' => $response,
    'error' => $error,
    'http_code' => $info['http_code'],
    'total_time' => $info['total_time']
]);
