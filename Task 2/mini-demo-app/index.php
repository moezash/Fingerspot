<?php
/**
 * Main Interface for Fingerspot Mini Demo App
 * Attendance Monitoring Dashboard
 */
require_once 'functions.php';

// Simple Router
$page = $_GET['page'] ?? 'dashboard';
$message = '';
$message_type = 'info';

// Handle Form Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_employee':
                $res = add_employee($_POST['cloud_id'], $_POST['pin'], $_POST['name']);
                $message = $res['message'] ?? ($res['status'] ? 'Employee added command sent' : 'Failed to add employee');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
            case 'delete_employee':
                $res = delete_employee($_POST['cloud_id'], $_POST['pin']);
                $message = $res['message'] ?? ($res['status'] ? 'Employee delete command sent' : 'Failed to delete employee');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
            case 'sync_time':
                $res = sync_time($_POST['cloud_id']);
                $message = $res['message'] ?? ($res['status'] ? 'Time sync command sent' : 'Failed to sync time');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
            case 'restart':
                $res = restart_device($_POST['cloud_id']);
                $message = $res['message'] ?? ($res['status'] ? 'Restart command sent' : 'Failed to restart device');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
        }
    }
}

// Global data needed for most pages
$devices_res = get_devices();
$devices = (isset($devices_res['status']) && $devices_res['status']) ? $devices_res['data'] : [];

// Dashboard specific data
$attendance = [];
$selected_device = $_GET['device'] ?? '';

if ($selected_device && $page == 'dashboard') {
    $today = date('Y-m-d');
    $att_res = get_attendance($selected_device, $today, $today);
    if (isset($att_res['status']) && $att_res['status'] && isset($att_res['data'])) {
        $attendance = $att_res['data'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-2 sidebar d-none d-md-block">
                <div class="px-3 mb-4">
                    <h5 class="fw-bold">Fingerspot Demo</h5>
                    <small class="text-muted">Internship Task 2</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">Dashboard</a>
                    <a class="nav-link <?php echo $page == 'employees' ? 'active' : ''; ?>" href="index.php?page=employees">Employees</a>
                    <a class="nav-link <?php echo $page == 'devices' ? 'active' : ''; ?>" href="index.php?page=devices">Devices</a>
                </nav>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-10 main-content">
                <!-- Notifications -->
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <strong>Status:</strong> <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($page == 'dashboard'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Attendance Monitoring</h2>
                        <span class="text-muted"><?php echo date('l, d F Y'); ?></span>
                    </div>

                    <div class="card p-3 mb-4">
                        <form method="GET" class="row g-2 align-items-center">
                            <input type="hidden" name="page" value="dashboard">
                            <div class="col-auto">
                                <label>Filter by Device:</label>
                            </div>
                            <div class="col-md-4">
                                <select name="device" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Choose Device --</option>
                                    <?php foreach ($devices as $dev): ?>
                                        <option value="<?php echo $dev['cloud_id']; ?>" <?php echo $selected_device == $dev['cloud_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dev['name']); ?> (<?php echo $dev['cloud_id']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Today's Scan Logs</span>
                            <?php if($selected_device): ?>
                                <span class="badge bg-primary">Device: <?php echo htmlspecialchars($selected_device); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Employee PIN</th>
                                            <th>Scan Time</th>
                                            <th>Verify Mode</th>
                                            <th class="pe-4 text-end">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($attendance)): ?>
                                            <?php foreach ($attendance as $log): ?>
                                                <tr>
                                                    <td class="ps-4 fw-bold"><?php echo htmlspecialchars($log['pin']); ?></td>
                                                    <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                                    <td>
                                                        <span class="text-muted small">Mode: <?php echo htmlspecialchars($log['verify']); ?></span>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <span class="badge rounded-pill <?php echo $log['status_scan'] == '0' ? 'bg-success' : 'bg-warning'; ?>">
                                                            <?php echo $log['status_scan'] == '0' ? 'Check In' : 'Check Out'; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-5 text-muted">
                                                    <?php echo $selected_device ? 'No scan data found for today.' : 'Please select a device to view logs.'; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($page == 'employees'): ?>
                    <h2 class="mb-4">Employee Management</h2>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-header">Register New Employee</div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="add_employee">
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Target Device</label>
                                            <select name="cloud_id" class="form-select" required>
                                                <?php foreach ($devices as $dev): ?>
                                                    <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Employee PIN</label>
                                            <input type="text" name="pin" class="form-control" placeholder="e.g. 101" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Full Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Sync to Machine</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="card">
                                <div class="card-header">Quick Actions</div>
                                <div class="card-body">
                                    <div class="alert alert-info border-0 bg-light">
                                        <h6 class="fw-bold">Information</h6>
                                        <p class="small mb-0">Adding or deleting employees are remote commands. The device will process them when it's online and report back via webhook.</p>
                                    </div>
                                    <hr>
                                    <h6 class="fw-bold mb-3">Delete Employee Remotely</h6>
                                    <form method="POST" class="row g-2">
                                        <input type="hidden" name="action" value="delete_employee">
                                        <div class="col-md-6">
                                            <select name="cloud_id" class="form-select" required>
                                                <option value="">Select Device</option>
                                                <?php foreach ($devices as $dev): ?>
                                                    <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="pin" class="form-control" placeholder="PIN" required>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-outline-danger w-100">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($page == 'devices'): ?>
                    <h2 class="mb-4">My Devices</h2>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Device Name</th>
                                            <th>Cloud ID / SN</th>
                                            <th>Status</th>
                                            <th class="pe-4 text-end">Remote Operations</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($devices)): ?>
                                            <?php foreach ($devices as $dev): ?>
                                                <tr>
                                                    <td class="ps-4">
                                                        <div class="fw-bold"><?php echo htmlspecialchars($dev['name']); ?></div>
                                                    </td>
                                                    <td><code><?php echo htmlspecialchars($dev['cloud_id']); ?></code></td>
                                                    <td>
                                                        <span class="badge rounded-pill badge-online">
                                                            <?php echo $dev['status'] ?? 'Online'; ?>
                                                        </span>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="action" value="sync_time">
                                                            <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-light border">Sync Time</button>
                                                        </form>
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="action" value="restart">
                                                            <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-light border text-danger" onclick="return confirm('Restart this device?')">Restart</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-5">No devices registered in your account.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
