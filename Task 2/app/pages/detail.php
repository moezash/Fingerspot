<?php
/**
 * Halaman Detail Payload
 * Menampilkan raw JSON payload dari record yang dipilih
 */

$table = $_GET['table'] ?? '';
$id = (int)($_GET['id'] ?? 0);

// Validate table name (prevent SQL injection)
if (!is_allowed_db_table($table) || $id <= 0) {
    echo '<div class="alert alert-danger">Data tidak ditemukan.</div>';
    echo '<a href="?page=dashboard" class="btn btn-secondary">Kembali</a>';
    return;
}

// Fetch record
$stmt = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    echo '<div class="alert alert-danger">Record tidak ditemukan.</div>';
    echo '<a href="?page=dashboard" class="btn btn-secondary">Kembali</a>';
    return;
}

// Determine back link
$backPages = [
    'attlogs' => 'attlog',
    'userinfos' => 'userinfo',
    'pins' => 'pins',
    'api_requests' => 'api_logs',
    'webhook_responses' => 'webhook_logs',
    'command_logs' => 'commands'
];
$backPage = $backPages[$table] ?? 'dashboard';

// Table display names
$tableNames = [
    'attlogs' => 'Data Absensi',
    'userinfos' => 'Data User',
    'pins' => 'Data PIN',
    'api_requests' => 'API Request',
    'webhook_responses' => 'Webhook Response',
    'command_logs' => 'Command Log'
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-file-text"></i> Detail: <?php echo $tableNames[$table] ?? $table; ?> #<?php echo $id; ?></h2>
    <a href="?page=<?php echo $backPage; ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row">
    <!-- Record Fields -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-list-ul"></i> Data Fields</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 detail-fields-table">
                    <tbody>
                        <?php foreach ($record as $key => $value): ?>
                            <?php if (in_array($key, ['raw_payload', 'request_payload', 'response_payload'])) continue; ?>
                            <tr>
                                <th><?php echo htmlspecialchars($key); ?></th>
                                <td>
                                    <?php if (in_array($key, ['status'])): ?>
                                        <?php echo status_badge($value); ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($value ?? '-'); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Raw Payload -->
    <div class="col-md-7">
        <?php
        $payloadFields = ['raw_payload', 'request_payload', 'response_payload'];
        foreach ($payloadFields as $field):
            if (isset($record[$field]) && $record[$field]):
        ?>
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-code-square"></i> <?php echo ucwords(str_replace('_', ' ', $field)); ?>
                <button type="button" class="btn btn-sm btn-outline-secondary float-end" data-copy-target="<?php echo $field; ?>">
                    <i class="bi bi-clipboard"></i> <span class="copy-label" aria-live="polite">Copy</span>
                </button>
            </div>
            <div class="card-body">
                <pre id="<?php echo $field; ?>" class="payload-preview" style="max-height:400px; white-space:pre-wrap; word-wrap:break-word;"><?php
                    $decoded = json_decode($record[$field], true);
                    if ($decoded !== null) {
                        echo htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    } else {
                        echo htmlspecialchars($record[$field]);
                    }
                ?></pre>
            </div>
        </div>
        <?php
            endif;
        endforeach;
        ?>
    </div>
</div>
