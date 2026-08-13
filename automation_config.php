<?php

/**
 * Phase 1 automation configuration.
 *
 * Configure these values as Hostinger environment variables. Secrets must
 * never be committed to this repository or placed in a browser-accessible JS
 * file.
 */
function automationConfig($key, $default = '')
{
    if (function_exists('runtimeSecret')) return runtimeSecret($key, $default);
    $value = getenv($key);
    return $value === false || $value === '' ? $default : $value;
}

function automationIsConfigured()
{
    return automationConfig('TTR_AUTOMATION_WEBHOOK_URL') !== ''
        && automationConfig('TTR_AUTOMATION_WEBHOOK_SECRET') !== '';
}
