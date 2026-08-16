<?php
/**
 * Halaman Riwayat Webhook
 */

// Filter
$filterType = $_GET['type'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$filterCloudId = $_GET['cloud_id'] ?? '';

// Build query
$where = '1=1';
$params = [];

if ($filterType) {
    $where .= ' AND type LIKE ?';
    $params[] = "%$filterType%";
}
if ($filterStatus) {
    $where .= ' AND status = ?';
    $params[] = $filterStatus;
}
if ($filterCloudId) {
    $where .= ' AND cloud_id LIKE ?';
    $params[] = "%$filterCloudId%";
}

// Pagination
$pagination = get_pagination('webhook_responses', $where, $params);

// Fetch data
$stmt = $pdo->prepare("SELECT * FROM webhook_responses WHERE $where ORDER BY received_at DESC LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}");
$stmt->execute($params);
$webhooks = $stmt->fetchAll();
?>

<h2 class="mb-4"><i class="bi bi-arrow-down-circle"></i> Riwayat Webhook</h2>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="webhook_logs">
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <input type="text" name="type" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterType); ?>" placeholder="attlog, get_userinfo...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Cloud ID</label>
                <input type="text" name="cloud_id" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterCloudId); ?>" placeholder="Cloud ID...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="received" <?php echo $filterStatus == 'received' ? 'selected' : ''; ?>>Received</option>
                    <option value="processed" <?php echo $filterStatus == 'processed' ? 'selected' : ''; ?>>Processed</option>
                    <option value="failed" <?php echo $filterStatus == 'failed' ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="?page=webhook_logs" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        Total: <?php echo $pagination['total']; ?> webhooks
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Cloud ID</th>
                    <th>Trans ID</th>
                    <th>Status</th>
                    <th>Diterima</th>
                    <th>Diproses</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($webhooks)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada riwayat webhook</td></tr>
                <?php else: ?>
                    <?php foreach ($webhooks as $i => $wh): ?>
                        <tr>
                            <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                            <td><code><?php echo htmlspecialchars($wh['type']); ?></code></td>
                            <td><small><?php echo htmlspecialchars($wh['cloud_id'] ?? '-'); ?></small></td>
                            <td><small><?php echo htmlspecialchars($wh['trans_id'] ?? '-'); ?></small></td>
                            <td><?php echo status_badge($wh['status']); ?></td>
                            <td><small><?php echo $wh['received_at']; ?></small></td>
                            <td><small><?php echo $wh['processed_at'] ?? '-'; ?></small></td>
                            <td>
                                <a href="?page=detail&table=webhook_responses&id=<?php echo $wh['id']; ?>" class="btn btn-sm btn-outline-info" title="Lihat Payload">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php if ($pagination['total_pages'] > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
            <li class="page-item <?php echo $i == $pagination['page'] ? 'active' : ''; ?>">
                <a class="page-link" href="?page=webhook_logs&p=<?php echo $i; ?>&type=<?php echo urlencode($filterType); ?>&status=<?php echo urlencode($filterStatus); ?>&cloud_id=<?php echo urlencode($filterCloudId); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
