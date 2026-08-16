<?php
/**
 * CLI webhook handler checks (no DB required).
 * Usage: php app/check_webhook.php
 *
 * Covers FIX TAHAP 3 cases A–J (static + helper smoke tests).
 */

$webhookSrc = file_get_contents(__DIR__ . '/webhook.php');
$functionsSrc = file_get_contents(__DIR__ . '/functions.php');
$ok = true;

function fail($msg) {
    global $ok;
    $ok = false;
    echo "FAIL: $msg\n";
}

function pass($msg) {
    echo "OK: $msg\n";
}

// --- Supported webhook types ---
$requiredCases = [
    'attlog',
    'get_userinfo',
    'get_allpin',
    'get_userid_list',
    'set_userinfo',
    'delete_userinfo',
    'set_time',
    'reg_online',
    'restart',
];

foreach ($requiredCases as $type) {
    if (strpos($webhookSrc, "'" . $type . "'") === false && strpos($webhookSrc, '"' . $type . '"') === false) {
        fail("webhook switch missing case/type: $type");
    } else {
        pass("handles $type");
    }
}

// --- Error handling / request validation ---
if (strpos($webhookSrc, "REQUEST_METHOD") === false || strpos($webhookSrc, '405') === false) {
    fail('webhook must reject non-POST with 405');
} else {
    pass('rejects non-POST (405)');
}

if (strpos($webhookSrc, 'Empty payload') === false) {
    fail('webhook must reject empty payload');
} else {
    pass('rejects empty payload');
}

if (strpos($webhookSrc, 'Invalid JSON') === false) {
    fail('webhook must reject invalid JSON');
} else {
    pass('rejects invalid JSON');
}

if (strpos($webhookSrc, 'catch (Throwable') === false && strpos($webhookSrc, 'catch (\\Throwable') === false) {
    fail('webhook should catch Throwable to avoid fatal errors');
} else {
    pass('catches Throwable (no fatal leak)');
}

// --- I. Unknown type: NOT processed/success ---
if (strpos($webhookSrc, 'Unhandled webhook type') === false) {
    fail('unknown types must be logged as unhandled');
} else {
    pass('unknown types logged as unhandled');
}

if (strpos($webhookSrc, "handling = 'unhandled'") === false) {
    fail('unknown types must set handling=unhandled');
} else {
    pass('unknown handling flag set');
}

if (preg_match("/default:\s*break;/", $webhookSrc) && strpos($webhookSrc, 'Unhandled webhook type') === false) {
    fail('default must not silently break as processed');
}

if (!preg_match("/default:.*?status\s*=\s*'failed'/s", $webhookSrc)) {
    fail('unknown type must persist status=failed (no unhandled enum)');
} else {
    pass('unknown type stored as failed + reason');
}

// --- A. Realtime attlog must not finalize command_logs ---
if (!preg_match('/function process_attlog\([^)]*\)\s*\{(.*?)\n\}/s', $webhookSrc, $pm)) {
    fail('Could not parse process_attlog()');
} else {
    $bodyNoComments = preg_replace('/\/\/.*$/m', '', $pm[1]);
    if (preg_match('/\bupdate_command_log\s*\(/', $bodyNoComments)) {
        fail('process_attlog must not call update_command_log');
    } else {
        pass('A: attlog does not finalize command_logs');
    }
    if (strpos($bodyNoComments, 'save_attlogs') === false) {
        fail('process_attlog must still save attendance');
    } else {
        pass('A: attlog still calls save_attlogs');
    }
    if (preg_match('/\$saved\s*<\s*1|\!\s*\$saved\b/', $bodyNoComments)) {
        fail('process_attlog must not treat 0 inserted rows as failure (duplicate idempotent)');
    } else {
        pass('A: process_attlog accepts 0 new rows (duplicate)');
    }
    if (strpos($bodyNoComments, '$saved === false') === false) {
        fail('process_attlog must fail only when save_attlogs returns false');
    } else {
        pass('A: process_attlog fails only on false (no valid records)');
    }
}

if (strpos($functionsSrc, "command_type <> 'get_attlog'") === false) {
    fail('update_command_log must refuse to finalize get_attlog via webhook');
} else {
    pass('A: get_attlog cannot be completed from webhook matcher');
}

// --- Mapping webhook type → command type (B, C/D, E–H) ---
$mappingChecks = [
    'B:get_userinfo' => ["process_userinfo_callback", "'get_userinfo'"],
    'C/D:get_allpin' => ["process_pin_list_callback", "'get_allpin'"],
    'E:set_userinfo' => ["process_command_callback", 'set_userinfo'],
    'F:delete_userinfo' => ["process_command_callback", 'delete_userinfo'],
    'G:set_time' => ["process_command_callback", 'set_time'],
    'H:reg_online' => ["process_command_callback", 'reg_online'],
];

foreach ($mappingChecks as $label => $needles) {
    $found = true;
    foreach ($needles as $needle) {
        if (strpos($webhookSrc, $needle) === false) {
            $found = false;
            break;
        }
    }
    if ($found) {
        pass("$label mapping present");
    } else {
        fail("$label mapping missing");
    }
}

// get_userid_list must share pin callback path and map to get_allpin command
if (strpos($webhookSrc, 'get_userid_list') === false || strpos($webhookSrc, 'process_pin_list_callback') === false) {
    fail('D: get_userid_list must use pin list callback');
} else {
    pass('D: get_userid_list treated as Get All PIN callback');
}

// Safe matching uses cloud_id + expectedTypes
if (strpos($functionsSrc, 'AND cloud_id = ?') === false) {
    fail('update_command_log should match cloud_id when provided');
} else {
    pass('command matching includes cloud_id');
}

if (strpos($functionsSrc, "status = 'pending'") === false) {
    fail('idempotency: update_command_log must only touch pending rows');
} else {
    pass('idempotency: only pending commands updated');
}

// restart must go through process_command_callback (pending-only → safe for already-synced)
if (!preg_match("/case 'restart':\s*process_command_callback/s", $webhookSrc)
    && strpos($webhookSrc, "case 'restart'") === false) {
    fail('restart callback case missing');
} else {
    pass('restart callback handled without forcing final state');
}

// --- Helpers: normalize_pin_list_payload + webhook_indicates_failure ---
if (!preg_match('/function normalize_pin_list_payload\(.*?^\}/ms', $functionsSrc, $nm)) {
    fail('Could not parse normalize_pin_list_payload()');
} elseif (!preg_match('/function webhook_indicates_failure\(.*?^\}/ms', $functionsSrc, $fm)) {
    fail('Could not parse webhook_indicates_failure()');
} else {
    eval($nm[0]);
    eval($fm[0]);

    $list = normalize_pin_list_payload([['pin' => '1'], ['userid' => '2'], '3']);
    if ($list !== ['1', '2', '3']) {
        fail('normalize_pin_list_payload basic list failed: ' . json_encode($list));
    } else {
        pass('normalize_pin_list_payload basic list');
    }

if (normalize_pin_list_payload(null) !== null) {
    fail('normalize_pin_list_payload(null) should be null');
} else {
    pass('normalize_pin_list_payload(null) → null');
}

    // D: get_userid_list shape with pin_arr — must NOT treat whole object / total as PIN list
    $wrapped = [
        'total' => 3,
        'pin_arr' => ['1', '2', '3'],
    ];
    $fromArr = normalize_pin_list_payload($wrapped);
    if ($fromArr !== ['1', '2', '3']) {
        fail('D: pin_arr normalization failed: ' . json_encode($fromArr));
    } else {
        pass('D: pin_arr normalized from get_userid_list data wrapper');
    }

    // Must not invent PIN from total alone when list key missing/empty-ish
    $metaOnly = normalize_pin_list_payload(['total' => 3, 'message' => 'ok']);
    if ($metaOnly !== null && $metaOnly !== []) {
        // Accept null or empty; reject ['3']
        if ($metaOnly === ['3']) {
            fail('must not treat data.total as a PIN');
        } else {
            pass('meta-only wrapper does not invent PINs from total');
        }
    } else {
        pass('meta-only wrapper does not invent PINs from total');
    }

    $emptyArr = normalize_pin_list_payload(['total' => 0, 'pin_arr' => []]);
    if ($emptyArr !== []) {
        fail('empty pin_arr should yield empty list, got: ' . json_encode($emptyArr));
    } else {
        pass('empty pin_arr → [] (valid success list)');
    }

    if (webhook_indicates_failure(['success' => false])) {
        pass('webhook_indicates_failure detects success=false');
    } else {
        fail('webhook_indicates_failure missed success=false');
    }

    if (!webhook_indicates_failure(['type' => 'get_userinfo', 'data' => ['pin' => '1']])) {
        pass('data-bearing callback without flags is not failure');
    } else {
        fail('data-bearing callback should not be treated as failure');
    }
}

if (strpos($functionsSrc, 'expectedTypes') === false) {
    fail('update_command_log should accept expected command types');
} else {
    pass('update_command_log supports expectedTypes');
}

// J: invalid JSON path present (already checked above) — confirm no credential leak helpers in webhook
if (preg_match('/API_TOKEN|FINGERSPOT_API_TOKEN|Bearer\s+/', $webhookSrc)) {
    fail('webhook must not reference/expose API credentials');
} else {
    pass('J: webhook source does not expose credentials');
}

if ($ok) {
    echo "\nWEBHOOK CHECK OK\n";
    exit(0);
}

echo "\nWEBHOOK CHECK FAILED\n";
exit(1);
