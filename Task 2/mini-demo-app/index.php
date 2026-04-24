<?php
/**
 * Fingerspot Mini Demo App: Attendance Monitoring Dashboard
 *
 * This file serves as the main user interface and controller for the
 * demonstration application. It integrates multiple features from the
 * Fingerspot Cloud API.
 */
require_once 'functions.php';

// --- Handle User Actions (POST) ---
$message = '';
$message_type = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $cloud_id = $_POST['cloud_id'] ?? '';

    switch ($action) {
        case 'add_employee':
            $pin = $_POST['pin'] ?? '';
            $name = $_POST['name'] ?? '';
            $res = push_employee($cloud_id, $pin, $name);
            $message = $res['message'] ?? ($res['status'] ? "Success: Employee $name (PIN $pin) queued for addition." : "Error: Failed to add employee.");
            $message_type = $res['status'] ? 'success' : 'danger';
            break;

        case 'delete_employee':
            $pin = $_POST['pin'] ?? '';
            $res = remove_employee($cloud_id, $pin);
            $message = $res['message'] ?? ($res['status'] ? "Success: Delete command for PIN $pin sent." : "Error: Failed to send delete command.");
            $message_type = $res['status'] ? 'success' : 'danger';
            break;

        case 'sync_time':
            $res = sync_device_time($cloud_id);
            $message = $res['message'] ?? ($res['status'] ? "Success: Time synchronization command sent." : "Error: Failed to sync time.");
            $message_type = $res['status'] ? 'success' : 'danger';
            break;
    }
}

// --- Fetch Data for Display ---
$page = $_GET['page'] ?? 'dashboard';
$device_res = get_all_devices();
$devices = (isset($device_res['status']) && $device_res['status'] && isset($device_res['data'])) ? $device_res['data'] : [];

$attendance_logs = [];
$selected_device = $_GET['device'] ?? '';

if ($page == 'dashboard' && !empty($selected_device)) {
    $today = date('Y-m-d');
    $log_res = get_device_logs($selected_device, $today, $today);
    if (isset($log_res['status']) && $log_res['status'] && isset($log_res['data'])) {
        $attendance_logs = $log_res['data'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <!-- Use Bootstrap for a professional appearance -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background-color: #2c3e50; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .sidebar { background: white; min-height: calc(100vh - 56px); padding-top: 20px; }
        .nav-link { color: #34495e; padding: 10px 20px; border-radius: 5px; margin: 5px 15px; }
        .nav-link:hover, .nav-link.active { background-color: #3498db; color: white; }
        .table thead { background-color: #ecf0f1; }
        .badge-online { background-color: #2ecc71; }
        .badge-offline { background-color: #e74c3c; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#"><?php echo APP_NAME; ?></a>
        <span class="navbar-text d-none d-md-block">Internship Project - Task 2</span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 p-0 sidebar shadow-sm">
            <nav class="nav flex-column">
                <a class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                    Dashboard
                </a>
                <a class="nav-link <?php echo $page == 'employees' ? 'active' : ''; ?>" href="index.php?page=employees">
                    Manage Employees
                </a>
                <a class="nav-link <?php echo $page == 'devices' ? 'active' : ''; ?>" href="index.php?page=devices">
                    Device Status
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <main class="col-md-9 col-lg-10 p-4">

            <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if ($page == 'dashboard'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Real-time Monitoring</h2>
                    <form action="index.php" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="page" value="dashboard">
                        <select name="device" class="form-select" required>
                            <option value="">Select a Device</option>
                            <?php foreach ($devices as $dev): ?>
                                <option value="<?php echo $dev['cloud_id']; ?>" <?php echo $selected_device == $dev['cloud_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dev['name']); ?> (<?php echo $dev['cloud_id']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>
                </div>

                <div class="card p-3">
                    <h5 class="card-title mb-3">Attendance Logs - Today (<?php echo date('d M Y'); ?>)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>PIN</th>
                                    <th>Timestamp</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($attendance_logs)): ?>
                                    <?php foreach ($attendance_logs as $log): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($log['pin']); ?></td>
                                        <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                        <td><span class="badge bg-light text-dark">Mode <?php echo htmlspecialchars($log['verify']); ?></span></td>
                                        <td><span class="badge bg-info text-dark"><?php echo $log['status_scan'] == '0' ? 'Check-In' : 'Check-Out'; ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <?php echo empty($selected_device) ? "Please select a device to view logs." : "No logs found for today."; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'employees'): ?>
                <h2 class="mb-4">Employee Management</h2>
                <div class="row g-4">
                    <!-- Add Employee Form -->
                    <div class="col-lg-5">
                        <div class="card">
                            <div class="card-header bg-white fw-bold">Push New Employee to Device</div>
                            <div class="card-body">
                                <form action="index.php?page=employees" method="POST">
                                    <input type="hidden" name="action" value="add_employee">
                                    <div class="mb-3">
                                        <label class="form-label">Target Device</label>
                                        <select name="cloud_id" class="form-select" required>
                                            <?php foreach ($devices as $dev): ?>
                                                <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">PIN (Numeric ID)</label>
                                        <input type="text" name="pin" class="form-control" placeholder="e.g. 101" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">Send to Machine</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Employee Form -->
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header bg-white fw-bold">Remove Employee from Device</div>
                            <div class="card-body">
                                <p class="text-muted small">Enter the device and PIN of the employee you wish to delete remotely.</p>
                                <form action="index.php?page=employees" method="POST" class="row g-3">
                                    <input type="hidden" name="action" value="delete_employee">
                                    <div class="col-md-6">
                                        <label class="form-label">Target Device</label>
                                        <select name="cloud_id" class="form-select" required>
                                            <?php foreach ($devices as $dev): ?>
                                                <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">PIN</label>
                                        <input type="text" name="pin" class="form-control" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure?')">Delete</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($page == 'devices'): ?>
                <h2 class="mb-4">Connected Devices</h2>
                <div class="card p-3">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Device Name</th>
                                    <th>Cloud ID (SN)</th>
                                    <th>Current Status</th>
                                    <th>Quick Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($devices)): ?>
                                    <?php foreach ($devices as $dev): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($dev['name']); ?></td>
                                        <td><code><?php echo htmlspecialchars($dev['cloud_id']); ?></code></td>
                                        <td>
                                            <?php if (($dev['status'] ?? '') == 'Online'): ?>
                                                <span class="badge badge-online">Online</span>
                                            <?php else: ?>
                                                <span class="badge badge-offline">Offline</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form action="index.php?page=devices" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="sync_time">
                                                <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Sync Time</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">No devices registered to your account.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
