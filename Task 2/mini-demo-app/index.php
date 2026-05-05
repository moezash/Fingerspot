<?php
/**
 * Main entry point for the Fingerspot Mini Demo App
 *
 * This application demonstrates the integration of multiple Fingerspot
 * Cloud API features in a single dashboard.
 *
 * Features integrated:
 * - Get Device List
 * - Get Attendance Logs (Scan Logs)
 * - Add New Employee
 * - Delete Employee
 * - Sync Device Time
 */

require_once 'functions.php';

// Simple Router logic
$page = $_GET['page'] ?? 'dashboard';
$message = '';
$message_type = 'info';

// Handle Actions (POST requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_employee':
                $res = add_employee($_POST['cloud_id'], $_POST['pin'], $_POST['name']);
                $message = $res['message'] ?? ($res['status'] ? 'Employee registration command sent to machine' : 'Failed to send command');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;

            case 'delete_employee':
                $res = delete_employee($_POST['cloud_id'], $_POST['pin']);
                $message = $res['message'] ?? ($res['status'] ? 'Employee deletion command sent to machine' : 'Failed to send command');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;

            case 'sync_time':
                $res = sync_time($_POST['cloud_id']);
                $message = $res['message'] ?? ($res['status'] ? 'Time synchronization command sent' : 'Failed to send command');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
        }
    }
}

// Global Data Fetching
$device_res = get_devices();
$devices = (isset($device_res['status']) && $device_res['status']) ? $device_res['data'] : [];

// Dashboard Logic: Fetch Attendance
$attendance = [];
$selected_device = $_GET['device'] ?? ($devices[0]['cloud_id'] ?? '');

if ($selected_device) {
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
    <!-- Bootstrap 5 CSS for professional look -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; padding-top: 20px; position: fixed; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: white; background: #34495e; border-left: 4px solid #3498db; }
        .main-content { margin-left: 16.666667%; padding: 30px; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .card-header { background-color: white; border-bottom: 1px solid #eee; font-weight: bold; }
        .status-online { color: #2ecc71; }
        .status-offline { color: #e74c3c; }
        .demo-badge { background: #f39c12; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="text-center mb-4">
                    <i class="bi bi-person-check-fill fs-1"></i>
                    <h5 class="mt-2"><?php echo APP_NAME; ?></h5>
                    <?php if (DEMO_MODE): ?>
                        <span class="demo-badge">Demo Mode</span>
                    <?php endif; ?>
                </div>
                <hr>
                <a href="index.php?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a href="index.php?page=employees" class="<?php echo $page == 'employees' ? 'active' : ''; ?>">
                    <i class="bi bi-people me-2"></i> Employees
                </a>
                <a href="index.php?page=devices" class="<?php echo $page == 'devices' ? 'active' : ''; ?>">
                    <i class="bi bi-cpu me-2"></i> Device Management
                </a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show shadow-sm" role="alert">
                        <i class="bi <?php echo $message_type == 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'; ?> me-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($page == 'dashboard'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Attendance Monitor (Today)</h2>
                        <form method="GET" class="d-flex">
                            <input type="hidden" name="page" value="dashboard">
                            <select name="device" class="form-select me-2" onchange="this.form.submit()">
                                <?php foreach ($devices as $dev): ?>
                                    <option value="<?php echo $dev['cloud_id']; ?>" <?php echo $selected_device == $dev['cloud_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dev['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Employee PIN</th>
                                            <th>Scan Time</th>
                                            <th>Verify Method</th>
                                            <th class="pe-4">Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($attendance)): ?>
                                            <?php foreach ($attendance as $log): ?>
                                                <tr>
                                                    <td class="ps-4"><strong><?php echo htmlspecialchars($log['pin']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                                    <td>
                                                        <span class="badge bg-light text-dark border">
                                                            <?php echo $log['verify'] == '1' ? 'Fingerprint' : ($log['verify'] == '2' ? 'Face' : 'Other'); ?>
                                                        </span>
                                                    </td>
                                                    <td class="pe-4">
                                                        <span class="badge <?php echo $log['status_scan'] == '0' ? 'bg-success' : 'bg-warning'; ?>">
                                                            <?php echo $log['status_scan'] == '0' ? 'Clock In' : 'Clock Out'; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-5 text-muted">No attendance logs found for today.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($page == 'employees'): ?>
                    <h2 class="mb-4">Employee Management</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header"><i class="bi bi-person-plus me-2"></i> Register New Employee</div>
                                <div class="card-body">
                                    <form method="POST">
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
                                            <label for="pin" class="form-label">PIN / Employee ID</label>
                                            <input type="text" id="pin" name="pin" class="form-control" placeholder="e.g. 101" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" id="name" name="name" class="form-control" placeholder="e.g. John Doe" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Send to Machine</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header"><i class="bi bi-trash me-2"></i> Remove Employee</div>
                                <div class="card-body">
                                    <p class="text-muted small mb-4">Note: Deleting an employee here will send a command to the device to remove their data. This action is permanent on the device.</p>
                                    <form method="POST" class="row g-3">
                                        <input type="hidden" name="action" value="delete_employee">
                                        <div class="col-md-5">
                                            <label class="form-label">Device</label>
                                            <select name="cloud_id" class="form-select" required>
                                                <option value="">Select Device...</option>
                                                <?php foreach ($devices as $dev): ?>
                                                    <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Employee PIN</label>
                                            <input type="text" name="pin" class="form-control" placeholder="PIN" required>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure?')">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($page == 'devices'): ?>
                    <h2 class="mb-4">Device Management</h2>
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">Cloud ID</th>
                                            <th>Device Name</th>
                                            <th>Status</th>
                                            <th class="pe-4 text-end">Quick Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($devices)): ?>
                                            <?php foreach ($devices as $dev): ?>
                                                <tr>
                                                    <td class="ps-4"><code><?php echo htmlspecialchars($dev['cloud_id']); ?></code></td>
                                                    <td><?php echo htmlspecialchars($dev['name']); ?></td>
                                                    <td>
                                                        <span class="status-<?php echo strtolower($dev['status']); ?>">
                                                            <i class="bi bi-circle-fill small me-1"></i> <?php echo $dev['status']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="pe-4 text-end">
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="action" value="sync_time">
                                                            <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                                <i class="bi bi-clock me-1"></i> Sync Time
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-5">No devices registered or API connection failed.</td></tr>
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

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
