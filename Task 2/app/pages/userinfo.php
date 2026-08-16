<?php
/**
 * Halaman Data User / Userinfo
 */

// Filter
$filterPin = $_GET['pin'] ?? '';
$filterName = $_GET['name'] ?? '';
$filterCloudId = $_GET['cloud_id'] ?? '';

// Build query
$where = '1=1';
$params = [];

if ($filterPin) {
    $where .= ' AND pin LIKE ?';
    $params[] = "%$filterPin%";
}
if ($filterName) {
    $where .= ' AND name LIKE ?';
    $params[] = "%$filterName%";
}
if ($filterCloudId) {
    $where .= ' AND cloud_id LIKE ?';
    $params[] = "%$filterCloudId%";
}

// Pagination
$pagination = get_pagination('userinfos', $where, $params);

// Fetch data
$stmt = $pdo->prepare("SELECT * FROM userinfos WHERE $where ORDER BY updated_at DESC LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<h2 class="mb-4"><i class="bi bi-people"></i> Data User (Userinfo)</h2>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="userinfo">
            <div class="col-md-2">
                <label class="form-label">PIN</label>
                <input type="text" name="pin" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterPin); ?>" placeholder="Cari PIN...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Nama</label>
                <input type="text" name="name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterName); ?>" placeholder="Cari nama...">
            </div>
            <div class="col-md-2">
                <label class="form-label">Cloud ID</label>
                <input type="text" name="cloud_id" class="form-control form-control-sm" value="<?php echo htmlspecialchars($filterCloudId); ?>" placeholder="Cloud ID...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="?page=userinfo" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        Total: <?php echo $pagination['total']; ?> users
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>PIN</th>
                    <th>Nama</th>
                    <th>Privilege</th>
                    <th>Finger</th>
                    <th>Face</th>
                    <th>RFID</th>
                    <th>Cloud ID</th>
                    <th>Updated</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="10" class="text-center text-muted py-4">Belum ada data user</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($user['pin']); ?></strong></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td>
                                <?php echo $user['privilege'] == '14' ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-secondary">User</span>'; ?>
                            </td>
                            <td><?php echo $user['finger']; ?></td>
                            <td><?php echo $user['face']; ?></td>
                            <td><?php echo htmlspecialchars($user['rfid'] ?: '-'); ?></td>
                            <td><small><?php echo htmlspecialchars($user['cloud_id']); ?></small></td>
                            <td><small><?php echo $user['updated_at']; ?></small></td>
                            <td>
                                <a href="?page=detail&table=userinfos&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-info" title="Lihat Detail">
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
                <a class="page-link" href="?page=userinfo&p=<?php echo $i; ?>&pin=<?php echo urlencode($filterPin); ?>&name=<?php echo urlencode($filterName); ?>&cloud_id=<?php echo urlencode($filterCloudId); ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
