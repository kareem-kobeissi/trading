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

    $GLOBALS['TTR_RUNTIME_SECRETS'] = $secrets;

    foreach ($secrets as $key => $value) {
        if (!is_string($key) || !preg_match('/^[A-Z][A-Z0-9_]*$/', $key)) continue;
        if (getenv($key) !== false && getenv($key) !== '') continue;
        $_ENV[$key] = (string) $value;
    }
}

function runtimeSecret($key, $default = '')
{
    $environmentValue = getenv($key);
    if ($environmentValue !== false && $environmentValue !== '') return $environmentValue;
    $secrets = $GLOBALS['TTR_RUNTIME_SECRETS'] ?? [];
    return array_key_exists($key, $secrets) && $secrets[$key] !== ''
        ? (string) $secrets[$key]
        : $default;
}

loadRuntimeSecrets();
