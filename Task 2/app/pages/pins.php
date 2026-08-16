<?php
/**
 * Halaman Data PIN
 */

// Filter
$filterCloudId = $_GET['cloud_id'] ?? '';
$filterPin = $_GET['pin'] ?? '';

// Build query
$where = '1=1';
$params = [];

if ($filterCloudId) {
    $where .= ' AND cloud_id LIKE ?';
    $params[] = "%$filterCloudId%";
}
if ($filterPin) {
    $where .= ' AND pin LIKE ?';
    $params[] = "%$filterPin%";
}

// Pagination
$pagination = get_pagination('pins', $where, $params);

// Fetch data
$stmt = $pdo->prepare("SELECT * FROM pins WHERE $where ORDER BY created_at DESC LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}");
$stmt->execute($params);
$pins = $stmt->fetchAll();
?>

<h2 class="mb-4"><i class="bi bi-key"></i> Data PIN</h2>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="pins">
            <div class="col-md-3">
                <label class="form-label">Cloud ID</label>
                <input type="text" name="cloud_id" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterCloudId); ?>" placeholder="Cloud ID...">
            </div>
            <div class="col-md-3">
                <label class="form-label">PIN</label>
                <input type="text" name="pin" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterPin); ?>" placeholder="Cari PIN...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="?page=pins" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        Total: <?php echo $pagination['total']; ?> PINs
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>PIN</th>
                    <th>Cloud ID</th>
                    <th>Tanggal Diterima</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pins)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data PIN</td></tr>
                <?php else: ?>
                    <?php foreach ($pins as $i => $pin): ?>
                        <tr>
                            <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($pin['pin']); ?></strong></td>
                            <td><?php echo htmlspecialchars($pin['cloud_id']); ?></td>
                            <td><?php echo $pin['created_at']; ?></td>
                            <td>
                                <a href="?page=detail&table=pins&id=<?php echo $pin['id']; ?>" class="btn btn-sm btn-outline-info" title="Lihat Detail">
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
                <a class="page-link" href="?page=pins&p=<?php echo $i; ?>&cloud_id=<?php echo urlencode($filterCloudId); ?>&pin=<?php echo urlencode($filterPin); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
