<?php
/**
 * Dashboard - Ringkasan data
 */

// Keep the pending count meaningful without introducing a worker/queue.
expire_pending_commands();

$totalAttlogs = $pdo->query("SELECT COUNT(*) FROM attlogs")->fetchColumn();
$totalUsers = $pdo->query("SELECT COUNT(*) FROM userinfos")->fetchColumn();
$totalPins = $pdo->query("SELECT COUNT(*) FROM pins")->fetchColumn();
$totalApiRequests = $pdo->query("SELECT COUNT(*) FROM api_requests")->fetchColumn();
$totalWebhooks = $pdo->query("SELECT COUNT(*) FROM webhook_responses")->fetchColumn();
$pendingCommands = $pdo->query("SELECT COUNT(*) FROM command_logs WHERE status = 'pending'")->fetchColumn();
$failedCommands = $pdo->query("SELECT COUNT(*) FROM command_logs WHERE status = 'failed'")->fetchColumn();
$successToday = $pdo->query("SELECT COUNT(*) FROM command_logs WHERE status = 'success' AND DATE(updated_at) = CURDATE()")->fetchColumn();
$recentApi = $pdo->query("SELECT * FROM api_requests ORDER BY created_at DESC LIMIT 5")->fetchAll();

$recentAttlogs = $pdo->query("SELECT * FROM attlogs ORDER BY created_at DESC LIMIT 5")->fetchAll();

$recentWebhooks = $pdo->query("SELECT * FROM webhook_responses ORDER BY received_at DESC LIMIT 5")->fetchAll();
?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
    <div><h2 class="mb-1"><i class="bi bi-grid-1x2"></i> Dashboard</h2><p class="text-muted mb-0">Real-time overview of Fingerspot integration activity</p></div>
    <small class="text-muted"><i class="bi bi-clock me-1"></i> Last updated: <?php echo date('H:i'); ?></small>
</div>

<?php
$configStatus = get_config_status();
$hasToken = $configStatus['token_configured'];
$hasCloudId = $configStatus['cloud_ids_configured'];
$webhookReady = $configStatus['webhook_ready'];
?>

<div class="card mb-4 integration-health">
    <div class="card-header"><i class="bi bi-activity me-2"></i>Integration Health</div>
    <div class="card-body py-3">
        <div class="row g-2">
            <?php
            $healthItems = [
                ['label' => 'API Token', 'ready' => $hasToken, 'icon' => 'bi-key'],
                ['label' => 'Cloud ID', 'ready' => $hasCloudId, 'icon' => 'bi-cloud'],
                ['label' => 'Webhook', 'ready' => $webhookReady, 'icon' => 'bi-arrow-down-up'],
            ];
            foreach ($healthItems as $item):
            ?>
                <div class="col-12 col-md-4">
                    <div class="health-item d-flex align-items-center justify-content-between gap-3 px-3 py-2">
                        <span class="text-secondary"><i class="bi <?php echo $item['icon']; ?> me-2"></i><?php echo $item['label']; ?></span>
                        <span class="badge <?php echo $item['ready'] ? 'bg-success' : 'bg-danger'; ?>">
                            ● <?php echo $item['ready'] ? 'Ready' : 'Not ready'; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4 stats-row">
    <div class="col-md-2">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-primary"><?php echo $totalAttlogs; ?></h3>
                <small class="text-muted">Data Absensi</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-success"><?php echo $totalUsers; ?></h3>
                <small class="text-muted">Data User</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-info"><?php echo $totalPins; ?></h3>
                <small class="text-muted">Data PIN</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-secondary"><?php echo $totalApiRequests; ?></h3>
                <small class="text-muted">API Requests</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3><?php echo $totalWebhooks; ?></h3>
                <small class="text-muted">Webhooks</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center stat-card">
            <div class="card-body">
                <h3 class="text-warning"><?php echo $pendingCommands; ?></h3>
                <small class="text-muted">Pending</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Attendance -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history"></i> Absensi Terbaru</span>
                <a href="?page=attlog" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>PIN</th>
                            <th>Waktu Scan</th>
                            <th>Device</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentAttlogs)): ?>
                            <tr><td colspan="3" class="text-center text-muted">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentAttlogs as $att): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($att['pin']); ?></td>
                                    <td><?php echo htmlspecialchars($att['scan_time']); ?></td>
                                    <td><small><?php echo htmlspecialchars($att['cloud_id']); ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Webhooks -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-arrow-down-circle"></i> Webhook Terbaru</span>
                <a href="?page=webhook_logs" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Device</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentWebhooks)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentWebhooks as $wh): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($wh['type']); ?></code></td>
                                    <td><small><?php echo htmlspecialchars($wh['cloud_id']); ?></small></td>
                                    <td><?php echo status_badge($wh['status']); ?></td>
                                    <td><small><?php echo $wh['received_at']; ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
