<?php
/**
 * Halaman Kirim Command
 * Form untuk mengirim berbagai command API ke mesin
 */

$defaultCloudId = get_configured_cloud_ids()[0] ?? '';

// Lightweight cleanup when command history is viewed; no worker is required.
expire_pending_commands();

// Fetch command logs
$pagination = get_pagination('command_logs', '1=1', []);
$stmt = $pdo->prepare("SELECT * FROM command_logs ORDER BY created_at DESC LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}");
$stmt->execute();
$commandLogs = $stmt->fetchAll();
?>

<h2 class="mb-4"><i class="bi bi-terminal"></i> Kirim Command ke Mesin</h2>

<?php if (empty(get_configured_cloud_ids())): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-octagon"></i>
    <strong>Cloud ID belum diisi!</strong> Set <code>FINGERSPOT_CLOUD_IDS</code> di environment atau file <code>.env</code> (Cloud ID mesin dari dashboard Fingerspot).
    Tanpa Cloud ID, semua command API akan gagal (ERR_02: parameter salah).
</div>
<?php endif; ?>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    Command <strong>Get Userinfo</strong>, <strong>Get All PIN</strong>, <strong>Set/Delete Userinfo</strong>, dll membutuhkan
    <strong>Webhook URL</strong> yang sudah didaftarkan di dashboard Fingerspot (gunakan ngrok untuk localhost).
    Cek status di halaman <a href="?page=api_logs">Riwayat API</a> dan <a href="?page=webhook_logs">Riwayat Webhook</a>.
</div>

<div class="row mb-4">
    <!-- Get Attlog -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-clock-history"></i> Get Attlog
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="get_attendance">
                    <input type="hidden" name="redirect_page" value="commands">
                    <div class="mb-2">
                        <input type="text" name="cloud_id" class="form-control form-control-sm" placeholder="Cloud ID" value="<?php echo htmlspecialchars($defaultCloudId); ?>" required>
                    </div>
                    <div class="mb-2">
                        <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" required>
                    </div>
                    <div class="mb-2">
                        <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Kirim</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Get Userinfo -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <i class="bi bi-person-check"></i> Get Userinfo
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="get_userinfo">
                    <input type="hidden" name="redirect_page" value="commands">
                    <div class="mb-2">
                        <input type="text" name="cloud_id" class="form-control form-control-sm" placeholder="Cloud ID" value="<?php echo htmlspecialchars($defaultCloudId); ?>" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="pin" class="form-control form-control-sm" placeholder="PIN" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm w-100">Kirim</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Set Userinfo -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <i class="bi bi-person-plus"></i> Set Userinfo
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="set_userinfo">
                    <input type="hidden" name="redirect_page" value="commands">
                    <div class="mb-2">
                        <input type="text" name="cloud_id" class="form-control form-control-sm" placeholder="Cloud ID" value="<?php echo htmlspecialchars($defaultCloudId); ?>" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="pin" class="form-control form-control-sm" placeholder="PIN" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Nama" required>
                    </div>
                    <div class="mb-2">
                        <select name="privilege" class="form-select form-select-sm">
                            <option value="0">User</option>
                            <option value="14">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-info btn-sm w-100">Kirim</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Userinfo -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <i class="bi bi-person-x"></i> Delete Userinfo
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="delete_userinfo">
                    <input type="hidden" name="redirect_page" value="commands">
                    <div class="mb-2">
                        <input type="text" name="cloud_id" class="form-control form-control-sm" placeholder="Cloud ID" value="<?php echo htmlspecialchars($defaultCloudId); ?>" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="pin" class="form-control form-control-sm" placeholder="PIN" required>
                    </div>
                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Yakin hapus user ini?')">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Online -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-fingerprint"></i> Register Online
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="register_online">
                    <input type="hidden" name="redirect_page" value="commands">
                    <div class="mb-2">
                        <input type="text" name="cloud_id" class="form-control form-control-sm" placeholder="Cloud ID" value="<?php echo htmlspecialchars($defaultCloudId); ?>" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="pin" class="form-control form-control-sm" placeholder="PIN" required>
                    </div>
                    <div class="mb-2">
                        <select name="verification" class="form-select form-select-sm">
                            <option value="0">Finger 1</option>
                            <option value="1">Finger 2</option>
                            <option value="2">Finger 3</option>
                            <option value="12">Face</option>
                            <option value="13">Vein</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm w-100">Register</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Get All PIN -->
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-key"></i> Get All PIN
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="action" value="get_allpin">
                    <input type="hidden" name="redirect_page" value="commands">
                    <div class="mb-2">
                        <input type="text" name="cloud_id" class="form-control form-control-sm" placeholder="Cloud ID" value="<?php echo htmlspecialchars($defaultCloudId); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm w-100">Get All PIN</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Command Logs -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-journal-text"></i> Riwayat Command (Total: <?php echo $pagination['total']; ?>)
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-sm mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Command</th>
                    <th>Cloud ID</th>
                    <th>Trans ID</th>
                    <th>PIN</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($commandLogs)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">Belum ada riwayat command</td></tr>
                <?php else: ?>
                    <?php foreach ($commandLogs as $i => $cmd): ?>
                        <tr>
                            <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                            <td><code><?php echo htmlspecialchars($cmd['command_type']); ?></code></td>
                            <td><small><?php echo htmlspecialchars($cmd['cloud_id']); ?></small></td>
                            <td><small><?php echo htmlspecialchars($cmd['trans_id']); ?></small></td>
                            <td><?php echo htmlspecialchars($cmd['pin'] ?? '-'); ?></td>
                            <td><?php echo status_badge($cmd['status']); ?></td>
                            <td><small><?php echo $cmd['created_at']; ?></small></td>
                            <td>
                                <a href="?page=detail&table=command_logs&id=<?php echo $cmd['id']; ?>" class="btn btn-sm btn-outline-info" title="Lihat Detail" aria-label="Lihat detail command">
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
