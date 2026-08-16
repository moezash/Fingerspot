<?php
/**
 * CLI checks for async pending command expiration. No remote API calls.
 * Usage: php app/check_pending_timeout.php
 */

require_once __DIR__ . '/functions.php';

$ok = true;
$prefix = '__timeout_test_' . bin2hex(random_bytes(5));
$cloudId = '__timeout_test_cloud__';

function timeout_fail($message) {
    global $ok;
    $ok = false;
    echo "FAIL: $message\n";
}

function timeout_pass($message) {
    echo "OK: $message\n";
}

function timeout_row($transId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT status, notes, response_payload FROM command_logs WHERE trans_id = ?');
    $stmt->execute([$transId]);
    return $stmt->fetch();
}

$cases = [
    'A' => ['get_userinfo', 'pending', 0],
    'B' => ['set_userinfo', 'pending', 16],
    'C' => ['get_allpin', 'success', 16],
    'D' => ['delete_userinfo', 'failed', 16],
    'E' => ['get_attlog', 'pending', 16],
    'SYNC' => ['restart', 'pending', 16],
];

try {
    $insert = $pdo->prepare(
        "INSERT INTO command_logs
         (command_type, cloud_id, trans_id, status, request_payload, response_payload, notes, created_at, updated_at)
         VALUES (?, ?, ?, ?, '{}', ?, ?, DATE_SUB(NOW(), INTERVAL ? MINUTE), DATE_SUB(NOW(), INTERVAL ? MINUTE))"
    );

    foreach ($cases as $name => $case) {
        [$type, $status, $age] = $case;
        $response = $status === 'pending' ? null : '{"original":true}';
        $notes = $status === 'failed' ? 'Original failure' : null;
        $insert->execute([$type, $cloudId, $prefix . '_' . $name, $status, $response, $notes, $age, $age]);
    }

    $expired = expire_pending_commands(15);
    if ($expired < 1) {
        timeout_fail('No expired async command was updated');
    }

    $a = timeout_row($prefix . '_A');
    $b = timeout_row($prefix . '_B');
    $c = timeout_row($prefix . '_C');
    $d = timeout_row($prefix . '_D');
    $e = timeout_row($prefix . '_E');
    $sync = timeout_row($prefix . '_SYNC');

    $a['status'] === 'pending' ? timeout_pass('A: new async pending remains pending') : timeout_fail('A: new async pending changed');
    ($b['status'] === 'failed' && strpos((string) $b['notes'], 'Command timed out waiting for webhook') !== false)
        ? timeout_pass('B: old async pending expires with timeout reason')
        : timeout_fail('B: old async pending did not expire correctly');
    ($c['status'] === 'success' && $c['response_payload'] === '{"original":true}')
        ? timeout_pass('C: old success remains unchanged')
        : timeout_fail('C: old success changed');
    ($d['status'] === 'failed' && $d['notes'] === 'Original failure')
        ? timeout_pass('D: old failed remains unchanged')
        : timeout_fail('D: old failed changed');
    $e['status'] === 'pending' ? timeout_pass('E: old get_attlog remains pending') : timeout_fail('E: get_attlog was expired');
    $sync['status'] === 'pending' ? timeout_pass('E2: old restart remains pending') : timeout_fail('E2: restart was expired');

    $lateUpdated = update_command_log(
        $prefix . '_B',
        'success',
        '{"late_callback":true}',
        'set_userinfo',
        $cloudId
    );
    $afterLate = timeout_row($prefix . '_B');
    (!$lateUpdated && $afterLate['status'] === 'failed' && $afterLate['response_payload'] === null)
        ? timeout_pass('F: late webhook cannot overwrite expired state')
        : timeout_fail('F: late webhook changed expired command');
} catch (Throwable $e) {
    timeout_fail('Unexpected exception: ' . $e->getMessage());
} finally {
    $stmt = $pdo->prepare('DELETE FROM command_logs WHERE trans_id LIKE ?');
    $stmt->execute([$prefix . '%']);
}

if ($ok) {
    echo "\nPENDING TIMEOUT CHECK OK\n";
    exit(0);
}

echo "\nPENDING TIMEOUT CHECK FAILED\n";
exit(1);
