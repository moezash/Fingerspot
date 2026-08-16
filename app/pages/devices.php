<?php
/**
 * Halaman Devices - Daftar mesin absensi
 */

$devicesResult = get_devices();
$devices = $devicesResult['data'] ?? [];
$defaultCloudId = get_configured_cloud_ids()[0] ?? '';
?>

<h2 class="mb-4"><i class="bi bi-hdd-network"></i> Daftar Mesin Absensi</h2>

<?php if (empty(get_configured_cloud_ids())): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    <strong>Cloud ID belum dikonfigurasi.</strong>
    Set <code>FINGERSPOT_CLOUD_IDS</code> di environment atau file <code>.env</code>
    (dari dashboard <a href="https://developer.fingerspot.io" target="_blank">developer.fingerspot.io</a> → Mesin Saya).
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Mesin Terdaftar</span>
        <a href="?page=devices" class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-clockwise"></i> Refresh</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Cloud ID</th>
                    <th>Nama Mesin</th>
                    <th>Status API</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($devices)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <?php echo htmlspecialchars($devicesResult['message'] ?? 'Tidak ada mesin dikonfigurasi.'); ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($devices as $i => $dev): ?>
                        <?php $apiOk = is_api_success($dev['api']); ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><code><?php echo htmlspecialchars($dev['cloud_id']); ?></code></td>
                            <td><?php echo htmlspecialchars($dev['name']); ?></td>
                            <td>
                                <?php if ($apiOk): ?>
                                    <span class="badge bg-success">Terhubung</span>
                                <?php else: ?>
                                    <span class="badge bg-danger" title="<?php echo htmlspecialchars(api_error_message($dev['api'])); ?>">Error</span>
                                    <small class="text-danger d-block"><?php echo htmlspecialchars(api_error_message($dev['api'])); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;" class="me-1">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="action" value="set_time">
                                    <input type="hidden" name="redirect_page" value="devices">
                                    <input type="hidden" name="cloud_id" value="<?php echo htmlspecialchars($dev['cloud_id']); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Sync Time">
                                        <i class="bi bi-clock"></i> Sync Time
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;" class="me-1">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="action" value="restart">
                                    <input type="hidden" name="redirect_page" value="devices">
                                    <input type="hidden" name="cloud_id" value="<?php echo htmlspecialchars($dev['cloud_id']); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Restart" onclick="return confirm('Yakin restart mesin ini?')">
                                        <i class="bi bi-arrow-repeat"></i> Restart
                                    </button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="action" value="get_allpin">
                                    <input type="hidden" name="redirect_page" value="devices">
                                    <input type="hidden" name="cloud_id" value="<?php echo htmlspecialchars($dev['cloud_id']); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Get All PIN">
                                        <i class="bi bi-key"></i> Get PINs
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    <div class="alert alert-info mb-0">
        <i class="bi bi-info-circle"></i>
        <strong>Webhook URL:</strong> <code><?php echo htmlspecialchars(WEBHOOK_URL); ?></code><br>
        Daftarkan URL ini di dashboard developer.fingerspot.io agar data dari mesin bisa masuk ke aplikasi.
        Untuk development lokal, gunakan <strong>ngrok</strong> agar webhook bisa diakses dari internet.
    </div>
</div>
