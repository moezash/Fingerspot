<?php
/**
 * Mini Demo App: Attendance Monitoring Dashboard
 *
 * Features:
 * - Get Device List
 * - Get Attendance Logs
 * - Add/Delete Employee
 * - Sync Time
 */

require_once 'functions.php';

$page = $_GET['page'] ?? 'dashboard';
$message = '';
$message_type = 'info';

// --- Action Handling ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add_employee':
            $data = [
                'cloud_id'  => $_POST['cloud_id'],
                'pin'       => $_POST['pin'],
                'name'      => $_POST['name'],
                'privilege' => $_POST['privilege'] ?? '0'
            ];
            $result = fingerspot_request('set_userinfo', $data);
            if (is_success($result)) {
                $message = "Add employee command sent successfully.";
                $message_type = "success";
            } else {
                $message = "Error: " . ($result['message'] ?? 'Failed to send command');
                $message_type = "danger";
            }
            break;

        case 'delete_employee':
            $data = [
                'cloud_id' => $_POST['cloud_id'],
                'pin'      => $_POST['pin']
            ];
            $result = fingerspot_request('delete_userinfo', $data);
            if (is_success($result)) {
                $message = "Delete employee command sent successfully.";
                $message_type = "success";
            } else {
                $message = "Error: " . ($result['message'] ?? 'Failed to send command');
                $message_type = "danger";
            }
            break;

        case 'sync_time':
            $data = [
                'cloud_id' => $_POST['cloud_id'],
                'timezone' => '420',
                'set_time' => date('Y-m-d H:i:s')
            ];
            $result = fingerspot_request('set_time', $data);
            if (is_success($result)) {
                $message = "Time sync command sent successfully.";
                $message_type = "success";
            } else {
                $message = "Error: " . ($result['message'] ?? 'Failed to send command');
                $message_type = "danger";
            }
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fingerspot Attendance Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Fingerspot Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'employees' ? 'active' : ''; ?>" href="index.php?page=employees">Employees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'devices' ? 'active' : ''; ?>" href="index.php?page=devices">Devices</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
            <?php echo e($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($page === 'dashboard'): ?>
        <h2>Attendance Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Recent Attendance Logs</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // For demo purposes, we'll try to fetch devices first to get a cloud_id
                        $device_result = fingerspot_request('get_device');
                        if (is_success($device_result) && !empty($device_result['data'])):
                            $cloud_id = $device_result['data'][0]['cloud_id'];
                            $log_result = fingerspot_request('get_attlog', [
                                'cloud_id' => $cloud_id,
                                'start_date' => date('Y-m-d', strtotime('-1 day')),
                                'end_date' => date('Y-m-d')
                            ]);

                            if (is_success($log_result) && !empty($log_result['data'])):
                        ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>PIN</th>
                                            <th>Scan Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($log_result['data'] as $log): ?>
                                            <tr>
                                                <td><?php echo e($log['pin']); ?></td>
                                                <td><?php echo e($log['scan']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $log['status_scan'] == '0' ? 'bg-success' : 'bg-warning'; ?>">
                                                        <?php echo $log['status_scan'] == '0' ? 'Check-In' : 'Check-Out'; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center my-4">No recent logs found for device: <?php echo e($cloud_id); ?></p>
                        <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Please configure your API Token in <code>config.php</code> and ensure you have registered devices.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'employees'): ?>
        <h2>Employee Management</h2>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Add New Employee</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_employee">
                            <div class="mb-3">
                                <label for="cloud_id" class="form-label">Device</label>
                                <select name="cloud_id" id="cloud_id" class="form-select" required>
                                    <?php
                                    $devices = fingerspot_request('get_device');
                                    if (is_success($devices)):
                                        foreach ($devices['data'] as $dev):
                                    ?>
                                        <option value="<?php echo e($dev['cloud_id']); ?>"><?php echo e($dev['name']); ?> (<?php echo e($dev['cloud_id']); ?>)</option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="pin" class="form-label">PIN / Employee ID</label>
                                <input type="text" name="pin" id="pin" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="privilege" class="form-label">Privilege</label>
                                <select name="privilege" id="privilege" class="form-select">
                                    <option value="0">Normal User</option>
                                    <option value="1">Administrator</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send to Device</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Device Users (Delete Remote)</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">Enter PIN and Device ID to remotely delete a user.</p>
                        <form method="POST" class="row g-3">
                            <input type="hidden" name="action" value="delete_employee">
                            <div class="col-md-5">
                                <input type="text" name="cloud_id" class="form-control" placeholder="Cloud ID" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="pin" class="form-control" placeholder="PIN" required>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-danger w-100">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'devices'): ?>
        <h2>Registered Devices</h2>
        <div class="row mt-4">
            <?php
            $device_result = fingerspot_request('get_device');
            if (is_success($device_result)):
                foreach ($device_result['data'] as $device):
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title"><?php echo e($device['name']); ?></h5>
                                <span class="badge <?php echo ($device['status'] ?? '') === 'Online' ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo e($device['status'] ?? 'Offline'); ?>
                                </span>
                            </div>
                            <p class="card-text text-muted mb-1">Cloud ID: <code><?php echo e($device['cloud_id']); ?></code></p>
                            <p class="card-text small">Serial: <?php echo e($device['sn'] ?? 'N/A'); ?></p>

                            <form method="POST" class="mt-3">
                                <input type="hidden" name="action" value="sync_time">
                                <input type="hidden" name="cloud_id" value="<?php echo e($device['cloud_id']); ?>">
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">Sync Device Time</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="col-12">
                    <div class="alert alert-info">No devices found.</div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="mt-5 py-4 bg-light">
    <div class="container text-center text-muted small">
        &copy; <?php echo date('Y'); ?> Fingerspot Internship Project - Task 2
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
