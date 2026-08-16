<?php
/**
 * CLI configuration check (no secrets printed).
 * Usage: php app/check_config.php
 */
require_once __DIR__ . '/config.php';

$status = get_config_status();

echo "Fingerspot configuration check\n";
echo "==============================\n";
echo 'APP_ENV: ' . APP_ENV . "\n";
echo 'API token: ' . ($status['token_configured'] ? 'SET' : 'MISSING') . "\n";
echo 'Cloud IDs: ' . ($status['cloud_ids_configured'] ? 'SET' : 'MISSING') . "\n";
echo 'Webhook secret: ' . (!empty($status['webhook_secret_configured']) ? 'SET' : 'MISSING') . "\n";
echo 'SSL verify: ' . (!empty($status['ssl_verify']) ? 'ON' : 'OFF') . "\n";
echo 'Webhook ready: ' . ($status['webhook_ready'] ? 'YES' : 'NO') . "\n";
echo 'Webhook level: ' . $status['webhook']['level'] . "\n";
echo 'Webhook message: ' . $status['webhook']['message'] . "\n";

if (!empty($status['errors'])) {
    echo "\nErrors:\n";
    foreach ($status['errors'] as $err) {
        echo '  - ' . $err . "\n";
    }
}

if (!empty($status['warnings'])) {
    echo "\nWarnings:\n";
    foreach ($status['warnings'] as $warn) {
        echo '  - ' . $warn . "\n";
    }
}

echo "\nOverall: " . ($status['ok'] ? 'OK' : 'NOT READY') . "\n";
exit($status['ok'] ? 0 : 1);
