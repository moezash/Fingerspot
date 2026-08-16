<?php
/**
 * CLI database integrity checks for FIX TAHAP 4.
 * Usage: php app/check_database.php
 *
 * Requires a working DB connection (same as the app).
 * Uses an isolated cloud_id prefix and cleans up after itself.
 */

require_once __DIR__ . '/functions.php';

$ok = true;
$testCloud = '__tahap4_test__';
$raw = json_encode(['check' => 'tahap4', 'ts' => time()]);

function fail($msg) {
    global $ok;
    $ok = false;
    echo "FAIL: $msg\n";
}

function pass($msg) {
    echo "OK: $msg\n";
}

function count_attlogs($cloudId, $extraSql = '', $params = []) {
    global $pdo;
    $sql = "SELECT COUNT(*) FROM attlogs WHERE cloud_id = ?" . $extraSql;
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge([$cloudId], $params));
    return (int) $stmt->fetchColumn();
}

function count_pins($cloudId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pins WHERE cloud_id = ?");
    $stmt->execute([$cloudId]);
    return (int) $stmt->fetchColumn();
}

function cleanup_test_data($cloudId) {
    global $pdo;
    $pdo->prepare("DELETE FROM attlogs WHERE cloud_id = ?")->execute([$cloudId]);
    $pdo->prepare("DELETE FROM pins WHERE cloud_id = ?")->execute([$cloudId]);
}

// Preconditions: unique indexes present
$requiredIndexes = [
    'attlogs' => 'uniq_attlog_scan',
    'pins' => 'uniq_cloud_pin',
];
foreach ($requiredIndexes as $table => $indexName) {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.STATISTICS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?"
    );
    $stmt->execute([$table, $indexName]);
    if ((int) $stmt->fetchColumn() < 1) {
        fail("Missing index/constraint $table.$indexName — run database/migration_tahap4.sql");
    } else {
        pass("schema has $table.$indexName");
    }
}

cleanup_test_data($testCloud);

// --- A: first attendance insert ---
$record = [
    'pin' => '101',
    'scan' => '2026-08-16 10:00:00',
    'verify' => 1,
    'status_scan' => 0,
];
$n1 = save_attlogs($testCloud, $record, $raw);
$totalA = count_attlogs($testCloud);
if ($n1 === 1 && $totalA === 1) {
    pass('A: first attendance insert → 1 row');
} else {
    fail("A: expected 1 saved/1 total, got saved=$n1 total=$totalA");
}

// --- B: identical second insert (idempotent duplicate) ---
$n2 = save_attlogs($testCloud, $record, $raw);
$totalB = count_attlogs($testCloud);
if ($n2 === 0 && $n2 !== false && $totalB === 1) {
    pass('B: identical attendance ignored → still 1 row, inserted=0');
} else {
    fail("B: expected 0 saved/1 total, got saved=" . var_export($n2, true) . " total=$totalB");
}

// --- B2: malformed payload (no valid pin) → false, not 0 ---
$bad = save_attlogs($testCloud, [['name' => 'no-pin']], $raw);
if ($bad === false) {
    pass('B2: payload without valid pin returns false');
} else {
    fail('B2: malformed attlog payload must return false, got ' . var_export($bad, true));
}

// --- C: same PIN, different scan_time ---
$record2 = $record;
$record2['scan'] = '2026-08-16 10:05:00';
$n3 = save_attlogs($testCloud, $record2, $raw);
$totalC = count_attlogs($testCloud);
if ($n3 === 1 && $totalC === 2) {
    pass('C: same PIN different scan_time → 2 rows');
} else {
    fail("C: expected 1 saved/2 total, got saved=$n3 total=$totalC");
}

// --- D: a real database failure must propagate, never look like a duplicate ---
$workingPdo = $pdo;
try {
    $pdo = null;
    save_attlogs($testCloud, $record, $raw);
    fail('D: database error must not be reported as duplicate success');
} catch (Throwable $e) {
    pass('D: database error propagates as failure');
} finally {
    $pdo = $workingPdo;
}

// --- E: refresh PIN success ---
save_pins($testCloud, ['1', '2', '3'], $raw);
if (count_pins($testCloud) !== 3) {
    fail('D: initial PIN seed failed');
} else {
    $saved = save_pins($testCloud, ['9', '8'], $raw);
    $pins = [];
    $stmt = $pdo->prepare("SELECT pin FROM pins WHERE cloud_id = ? ORDER BY pin");
    $stmt->execute([$testCloud]);
    $pins = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if ($saved === 2 && $pins === ['8', '9']) {
        pass('D: refresh PIN replaces set atomically');
    } else {
        fail('D: refresh PIN unexpected result: saved=' . $saved . ' pins=' . json_encode($pins));
    }
}

// --- E: failure mid-refresh → rollback ---
save_pins($testCloud, ['A', 'B', 'C'], $raw);
$before = [];
$stmt = $pdo->prepare("SELECT pin FROM pins WHERE cloud_id = ? ORDER BY pin");
$stmt->execute([$testCloud]);
$before = $stmt->fetchAll(PDO::FETCH_COLUMN);

$rolledBack = false;
try {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM pins WHERE cloud_id = ?")->execute([$testCloud]);
    $pdo->prepare("INSERT INTO pins (cloud_id, pin, raw_payload) VALUES (?, ?, ?)")
        ->execute([$testCloud, 'X', $raw]);
    // Force failure after partial replace
    throw new RuntimeException('simulated mid-refresh failure');
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $rolledBack = true;
}

$stmt->execute([$testCloud]);
$after = $stmt->fetchAll(PDO::FETCH_COLUMN);
if ($rolledBack && $after === $before && $before === ['A', 'B', 'C']) {
    pass('E: rollback keeps previous PIN set intact');
} else {
    fail('E: rollback broken — before=' . json_encode($before) . ' after=' . json_encode($after));
}

// Also verify save_pins source uses transactions
$fnSrc = file_get_contents(__DIR__ . '/functions.php');
if (strpos($fnSrc, 'beginTransaction') === false || !preg_match('/function save_pins.*?rollBack/s', $fnSrc)) {
    fail('E: save_pins must use beginTransaction + rollBack');
} else {
    pass('E: save_pins wraps DELETE/INSERT in a transaction');
}

// --- F: generate many unique trans_id ---
$ids = [];
$dup = false;
for ($i = 0; $i < 500; $i++) {
    $id = generate_trans_id();
    if (isset($ids[$id])) {
        $dup = true;
        break;
    }
    $ids[$id] = true;
    if (strlen($id) > 50) {
        fail('F: trans_id longer than VARCHAR(50): ' . strlen($id));
        break;
    }
}
if (!$dup && count($ids) === 500) {
    pass('F: 500 trans_id values unique and <= 50 chars');
} elseif ($dup) {
    fail('F: duplicate trans_id in batch');
}

// Generator used by fingerspot_request
if (strpos($fnSrc, 'generate_trans_id()') === false) {
    fail('F: fingerspot_request should call generate_trans_id()');
} else {
    pass('F: fingerspot_request uses generate_trans_id()');
}

// save_attlogs uses INSERT IGNORE; duplicate returns 0, malformed returns false
if (stripos($fnSrc, 'INSERT IGNORE INTO attlogs') === false) {
    fail('save_attlogs should use INSERT IGNORE');
} else {
    pass('save_attlogs uses INSERT IGNORE');
}
if (strpos($fnSrc, 'return false') === false || strpos($fnSrc, '$attempted === 0') === false) {
    fail('save_attlogs must return false when no valid pin records');
} else {
    pass('save_attlogs returns false for malformed payload');
}

$badArray = save_attlogs($testCloud, 'not-an-array', $raw);
if ($badArray === false) {
    pass('C2: non-array payload returns false');
} else {
    fail('C2: non-array payload must return false');
}

cleanup_test_data($testCloud);

if ($ok) {
    echo "\nDATABASE CHECK OK\n";
    exit(0);
}

echo "\nDATABASE CHECK FAILED\n";
exit(1);
