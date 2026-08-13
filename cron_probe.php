<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

echo 'TTR cron probe OK at ' . date('Y-m-d H:i:s T') . PHP_EOL;
