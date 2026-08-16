<?php
/**
 * Halaman Riwayat Request API
 */

// Filter
$filterEndpoint = $_GET['endpoint'] ?? '';
$filterStatus = $_GET['status'] ?? '';

// Build query
$where = '1=1';
$params = [];

if ($filterEndpoint) {
    $where .= ' AND endpoint LIKE ?';
    $params[] = "%$filterEndpoint%";
}
if ($filterStatus) {
    $where .= ' AND status = ?';
    $params[] = $filterStatus;
}

// Pagination
$pagination = get_pagination('api_requests', $where, $params);

// Fetch data
$stmt = $pdo->prepare("SELECT * FROM api_requests WHERE $where ORDER BY created_at DESC LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}");
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<h2 class="mb-4"><i class="bi bi-arrow-up-circle"></i> Riwayat Request API</h2>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="api_logs">
            <div class="col-md-3">
                <label class="form-label">Endpoint</label>
                <input type="text" name="endpoint" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterEndpoint); ?>" placeholder="Cari endpoint...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="pending" <?php echo $filterStatus == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="success" <?php echo $filterStatus == 'success' ? 'selected' : ''; ?>>Success</option>
                    <option value="failed" <?php echo $filterStatus == 'failed' ? 'selected' : ''; ?>>Failed</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="?page=api_logs" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        Total: <?php echo $pagination['total']; ?> requests
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Endpoint</th>
                    <th>Cloud ID</th>
                    <th>Trans ID</th>
                    <th>Status</th>
                    <th>HTTP</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada riwayat API request</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $i => $log): ?>
                        <tr>
                            <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                            <td><code><?php echo htmlspecialchars($log['endpoint']); ?></code></td>
                            <td><small><?php echo htmlspecialchars($log['cloud_id'] ?? '-'); ?></small></td>
                            <td><small><?php echo htmlspecialchars($log['trans_id'] ?? '-'); ?></small></td>
                            <td><?php echo status_badge($log['status']); ?></td>
                            <td><?php echo $log['http_status'] ?? '-'; ?></td>
                            <td><small><?php echo $log['created_at']; ?></small></td>
                            <td>
                                <a href="?page=detail&table=api_requests&id=<?php echo $log['id']; ?>" class="btn btn-sm btn-outline-info" title="Lihat Detail Payload">
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
                <a class="page-link" href="?page=api_logs&p=<?php echo $i; ?>&endpoint=<?php echo urlencode($filterEndpoint); ?>&status=<?php echo urlencode($filterStatus); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
