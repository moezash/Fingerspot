<?php
/**
 * CLI lifecycle checks for sync vs async commands (no DB required).
 * Usage: php app/check_lifecycle.php
 */

$functionsSrc = file_get_contents(__DIR__ . '/functions.php');
$webhookSrc = file_get_contents(__DIR__ . '/webhook.php');
$ok = true;

function fail($msg) {
    global $ok;
    $ok = false;
    echo "FAIL: $msg\n";
}

// Extract and eval command_needs_webhook()
if (!preg_match('/function command_needs_webhook\([^)]*\)\s*\{(?:[^{}]|\{[^{}]*\})*\}/s', $functionsSrc, $m)) {
    fail('Could not parse command_needs_webhook()');
} else {
    eval($m[0]);

    $async = ['get_userinfo', 'get_allpin', 'set_userinfo', 'delete_userinfo', 'set_time', 'reg_online'];
    $sync = ['get_attlog', 'restart'];

    foreach ($async as $cmd) {
        if (!command_needs_webhook($cmd)) {
            fail("$cmd should be asynchronous (needs webhook)");
        }
    }
    foreach ($sync as $cmd) {
        if (command_needs_webhook($cmd)) {
            fail("$cmd should be synchronous (must not wait for webhook)");
        }
    }
}

// Realtime attlog must not finalize command_logs
if (!preg_match('/function process_attlog\([^)]*\)\s*\{(.*?)\n\}/s', $webhookSrc, $pm)) {
    fail('Could not parse process_attlog()');
} else {
    $bodyNoComments = preg_replace('/\/\/.*$/m', '', $pm[1]);
    if (preg_match('/\bupdate_command_log\s*\(/', $bodyNoComments)) {
        fail('process_attlog must not call update_command_log');
    }
    if (strpos($bodyNoComments, 'save_attlogs') === false) {
        fail('process_attlog must still save attendance');
    }
}

if (strpos($functionsSrc, "command_type <> 'get_attlog'") === false) {
    fail("update_command_log must refuse to finalize get_attlog via webhook");
}

// Sync completion path still present in fingerspot_request
if (strpos($functionsSrc, '!command_needs_webhook($commandType)') === false) {
    fail('fingerspot_request must complete sync commands from API response');
}

if ($ok) {
    echo "LIFECYCLE CHECK OK\n";
    echo "- sync: get_attlog, restart (API response finalizes command_logs)\n";
    echo "- async: get_userinfo, get_allpin, set_userinfo, delete_userinfo, set_time, reg_online\n";
    echo "- realtime attlog webhook does not resolve get_attlog\n";
    exit(0);
}

echo "LIFECYCLE CHECK FAILED\n";
exit(1);
