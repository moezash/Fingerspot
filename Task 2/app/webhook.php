<?php
/**
 * Webhook Receiver for Fingerspot
 *
 * Daftarkan URL publik ini di dashboard developer.fingerspot.io.
 *
 * Realtime attlog  → event terpisah (tidak menyelesaikan get_attlog sync).
 * Async callbacks  → cocokkan pending command_logs via trans_id + cloud_id + type.
 * Unknown types    → logged as failed/unhandled (bukan "processed" sukses).
 */
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

// Shared-secret gate — reject before any DB write. Never log the secret.
if (!verify_webhook_request()) {
    http_response_code(401);
    echo json_encode(['status' => false, 'message' => 'Unauthorized']);
    exit;
}

$json = file_get_contents('php://input');
if ($json === false || trim($json) === '') {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Empty payload']);
    exit;
}

$data = json_decode($json, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'Invalid JSON']);
    exit;
}

$typeRaw = isset($data['type']) ? trim((string) $data['type']) : '';
$type = $typeRaw !== '' ? $typeRaw : 'unknown';
$cloudId = isset($data['cloud_id']) ? trim((string) $data['cloud_id']) : null;
if ($cloudId === '') {
    $cloudId = null;
}
$transId = isset($data['trans_id']) ? trim((string) $data['trans_id']) : null;
if ($transId === '') {
    $transId = null;
}

try {
    $stmt = $pdo->prepare("INSERT INTO webhook_responses (type, cloud_id, trans_id, raw_payload, status) VALUES (?, ?, ?, ?, 'received')");
    $stmt->execute([$type, $cloudId, $transId, $json]);
    $webhookId = $pdo->lastInsertId();
} catch (PDOException $e) {
    error_log('Webhook DB Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Database error']);
    exit;
}

$handling = 'processed';
$httpMessage = 'Webhook received and processed';

try {
    switch ($type) {
        case 'attlog':
            process_attlog($data, $json);
            break;

        case 'get_userinfo':
            process_userinfo_callback($data, $json);
            break;

        case 'get_allpin':
        case 'get_userid_list':
            process_pin_list_callback($data, $json, $type);
            break;

        case 'set_userinfo':
        case 'delete_userinfo':
        case 'set_time':
        case 'reg_online':
        case 'restart':
            process_command_callback($data, $json, $type);
            break;

        default:
            // Schema has no "unhandled" status — use failed + clear reason.
            $handling = 'unhandled';
            $httpMessage = 'Webhook received (unhandled type)';
            $stmt = $pdo->prepare(
                "UPDATE webhook_responses
                 SET status = 'failed', error_message = ?, processed_at = NOW()
                 WHERE id = ?"
            );
            $stmt->execute(['Unhandled webhook type: ' . $type, $webhookId]);
            break;
    }

    if ($handling === 'processed') {
        $stmt = $pdo->prepare("UPDATE webhook_responses SET status = 'processed', processed_at = NOW() WHERE id = ?");
        $stmt->execute([$webhookId]);
    }
} catch (Throwable $e) {
    $handling = 'failed';
    $httpMessage = 'Webhook received (processing error)';
    $stmt = $pdo->prepare("UPDATE webhook_responses SET status = 'failed', error_message = ?, processed_at = NOW() WHERE id = ?");
    $stmt->execute([$e->getMessage(), $webhookId]);
    error_log('Webhook Processing Error: ' . $e->getMessage());
}

// Always acknowledge receipt safely — do not claim a command succeeded.
http_response_code(200);
echo json_encode([
    'status' => true,
    'message' => $httpMessage,
    'handling' => $handling,
]);

/**
 * Realtime attendance push — independent of get_attlog command lifecycle.
 */
function process_attlog($data, $rawJson) {
    $cloudId = trim((string) ($data['cloud_id'] ?? ''));
    if ($cloudId === '') {
        throw new InvalidArgumentException('attlog webhook missing cloud_id');
    }

    $payload = $data['data'] ?? null;
    if ($payload === null || $payload === '') {
        throw new InvalidArgumentException('attlog webhook missing data');
    }

    // Realtime event only — never finalize get_attlog command_logs from this path,
    // even when a trans_id field is present on the payload.
    $saved = save_attlogs($cloudId, $payload, $rawJson);
    if ($saved === false) {
        throw new InvalidArgumentException('attlog webhook data has no valid pin records');
    }
    // $saved === 0: idempotent duplicate (INSERT IGNORE) — success, not failure
}

/**
 * Async callback for get_userinfo.
 */
function process_userinfo_callback($data, $rawJson) {
    $cloudId = trim((string) ($data['cloud_id'] ?? ''));
    $transId = webhook_read_trans_id($data);

    if ($cloudId === '') {
        throw new InvalidArgumentException('get_userinfo webhook missing cloud_id');
    }
    if ($transId === null) {
        throw new InvalidArgumentException('get_userinfo webhook missing trans_id');
    }

    if (webhook_indicates_failure($data)) {
        update_command_log($transId, 'failed', $rawJson, 'get_userinfo', $cloudId);
        return;
    }

    $userData = $data['data'] ?? null;
    if (!is_array($userData) || empty($userData['pin'])) {
        update_command_log($transId, 'failed', $rawJson, 'get_userinfo', $cloudId);
        throw new InvalidArgumentException('get_userinfo webhook missing valid user data.pin');
    }

    save_userinfo($cloudId, $userData, $rawJson);
    update_command_log($transId, 'success', $rawJson, 'get_userinfo', $cloudId);
}

/**
 * Async callback for get_allpin / get_userid_list (equivalent PIN list).
 */
function process_pin_list_callback($data, $rawJson, $webhookType) {
    $cloudId = trim((string) ($data['cloud_id'] ?? ''));
    $transId = webhook_read_trans_id($data);

    if ($cloudId === '') {
        throw new InvalidArgumentException($webhookType . ' webhook missing cloud_id');
    }
    if ($transId === null) {
        throw new InvalidArgumentException($webhookType . ' webhook missing trans_id');
    }

    if (webhook_indicates_failure($data)) {
        update_command_log($transId, 'failed', $rawJson, 'get_allpin', $cloudId);
        return;
    }

    $rawList = $data['data'] ?? ($data['pins'] ?? ($data['userids'] ?? null));
    $pins = normalize_pin_list_payload($rawList);
    if ($pins === null) {
        update_command_log($transId, 'failed', $rawJson, 'get_allpin', $cloudId);
        throw new InvalidArgumentException($webhookType . ' webhook missing valid PIN/user id list');
    }

    // Empty list is a valid successful response (device has no users).
    save_pins($cloudId, $pins, $rawJson);
    update_command_log($transId, 'success', $rawJson, 'get_allpin', $cloudId);
}

/**
 * Async status callbacks: set/delete userinfo, set_time, reg_online, restart.
 * restart: only updates if still pending — already-synced success is left intact.
 */
function process_command_callback($data, $rawJson, $commandType) {
    $cloudId = trim((string) ($data['cloud_id'] ?? ''));
    $transId = webhook_read_trans_id($data);

    if ($cloudId === '') {
        throw new InvalidArgumentException($commandType . ' webhook missing cloud_id');
    }
    if ($transId === null) {
        throw new InvalidArgumentException($commandType . ' webhook missing trans_id');
    }

    $status = webhook_indicates_failure($data) ? 'failed' : 'success';

    // Only updates if still pending — duplicate callbacks / already-synced restart are no-ops.
    update_command_log($transId, $status, $rawJson, $commandType, $cloudId);
}

function webhook_read_trans_id($data) {
    if (!isset($data['trans_id'])) {
        return null;
    }
    $transId = trim((string) $data['trans_id']);
    return $transId === '' ? null : $transId;
}
