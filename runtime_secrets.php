<?php

/**
 * Load production secrets from a PHP file outside the public web directory.
 * Environment variables always take precedence when provided by the server.
 */
function loadRuntimeSecrets()
{
    $candidate = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'ttr-secrets.php';
    if (!is_file($candidate)) return;

    $secrets = require $candidate;
    if (!is_array($secrets)) return;

    foreach ($secrets as $key => $value) {
        if (!is_string($key) || !preg_match('/^[A-Z][A-Z0-9_]*$/', $key)) continue;
        if (getenv($key) !== false && getenv($key) !== '') continue;
        $stringValue = (string) $value;
        putenv($key . '=' . $stringValue);
        $_ENV[$key] = $stringValue;
    }
}

loadRuntimeSecrets();
