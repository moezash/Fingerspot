<?php
/**
 * Halaman Data Absensi / Attlog
 */

// Filter
$filterPin = $_GET['pin'] ?? '';
$filterCloudId = $_GET['cloud_id'] ?? '';
$filterDateFrom = $_GET['date_from'] ?? '';
$filterDateTo = $_GET['date_to'] ?? '';

// Build query
$where = '1=1';
$params = [];

if ($filterPin) {
    $where .= ' AND pin LIKE ?';
    $params[] = "%$filterPin%";
}
if ($filterCloudId) {
    $where .= ' AND cloud_id LIKE ?';
    $params[] = "%$filterCloudId%";
}
if ($filterDateFrom) {
    $where .= ' AND scan_time >= ?';
    $params[] = $filterDateFrom . ' 00:00:00';
}
if ($filterDateTo) {
    $where .= ' AND scan_time <= ?';
    $params[] = $filterDateTo . ' 23:59:59';
}

// Pagination
$pagination = get_pagination('attlogs', $where, $params);

// Fetch data
$stmt = $pdo->prepare("SELECT * FROM attlogs WHERE $where ORDER BY scan_time DESC LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}");
$stmt->execute($params);
$attlogs = $stmt->fetchAll();
?>

<h2 class="mb-4"><i class="bi bi-clock-history"></i> Data Absensi (Attlog)</h2>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="attlog">
            <div class="col-md-2">
                <label class="form-label">PIN</label>
                <input type="text" name="pin" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterPin); ?>" placeholder="Cari PIN...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Cloud ID</label>
                <input type="text" name="cloud_id" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterCloudId); ?>" placeholder="Cloud ID...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterDateFrom); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterDateTo); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="?page=attlog" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Total: <?php echo $pagination['total']; ?> records</span>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>PIN</th>
                    <th>Waktu Scan</th>
                    <th>Verifikasi</th>
                    <th>Status</th>
                    <th>Cloud ID</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attlogs)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data absensi</td></tr>
                <?php else: ?>
                    <?php foreach ($attlogs as $i => $att): ?>
                        <tr>
                            <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($att['pin']); ?></strong></td>
                            <td><?php echo htmlspecialchars($att['scan_time']); ?></td>
                            <td>
                                <?php
                                $verifyLabels = ['Finger', 'Password', 'Card', '', '', '', '', '', '', '', '', '', 'Face'];
                                echo $verifyLabels[$att['verify']] ?? 'Unknown';
                                ?>
                            </td>
                            <td>
                                <?php
                                $statusLabels = ['Check-In', 'Check-Out', 'Break-Out', 'Break-In', 'OT-In', 'OT-Out'];
                                echo $statusLabels[$att['status_scan']] ?? 'Status ' . $att['status_scan'];
                                ?>
                            </td>
                            <td><small><?php echo htmlspecialchars($att['cloud_id']); ?></small></td>
                            <td>
                                <a href="?page=detail&table=attlogs&id=<?php echo $att['id']; ?>" class="btn btn-sm btn-outline-info" title="Lihat Detail">
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
                <a class="page-link" href="?page=attlog&p=<?php echo $i; ?>&pin=<?php echo urlencode($filterPin); ?>&cloud_id=<?php echo urlencode($filterCloudId); ?>&date_from=<?php echo urlencode($filterDateFrom); ?>&date_to=<?php echo urlencode($filterDateTo); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
