<?php

require_once __DIR__ . '/runtime_secrets.php';

function coursePriceUsd()
{
    $configured = (float) runtimeSecret('COURSE_PRICE_USD', '200');
    return $configured > 0 ? $configured : 200.0;
}

