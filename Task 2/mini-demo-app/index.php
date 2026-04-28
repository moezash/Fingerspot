<?php
/**
 * Main Interface for Fingerspot Attendance Dashboard
 */
require_once 'functions.php';

$page = $_GET['page'] ?? 'dashboard';
$message = '';
$message_type = 'info';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_employee':
                $res = add_employee($_POST['cloud_id'], $_POST['pin'], $_POST['name']);
                $message = $res['message'] ?? ($res['status'] ? 'Employee registration command sent to device' : 'Failed to add employee');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
            case 'delete_employee':
                $res = delete_employee($_POST['cloud_id'], $_POST['pin']);
                $message = $res['message'] ?? ($res['status'] ? 'Employee delete command sent' : 'Failed to delete employee');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
            case 'sync_time':
                $res = sync_time($_POST['cloud_id']);
                $message = $res['message'] ?? ($res['status'] ? 'Time synchronization command sent' : 'Failed to sync time');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
        }
    }
}

// Fetch Data
$devices = get_devices();
$attendance = [];
$selected_device = $_GET['device'] ?? '';

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; padding-top: 20px; position: fixed; width: 240px; }
        .sidebar a { color: #bdc3c7; text-decoration: none; display: block; padding: 12px 20px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { color: white; background: #34495e; border-left: 4px solid #3498db; }
        .main-content { margin-left: 240px; padding: 30px; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-header { background-color: #fff; border-bottom: 1px solid #eee; font-weight: bold; }
        .btn-primary { background-color: #3498db; border: none; }
        .btn-success { background-color: #2ecc71; border: none; }
        .badge-online { background-color: #2ecc71; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center mb-4">Fingerspot App</h4>
        <a href="index.php?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
        <a href="index.php?page=employees" class="<?php echo $page == 'employees' ? 'active' : ''; ?>">Employees</a>
        <a href="index.php?page=devices" class="<?php echo $page == 'devices' ? 'active' : ''; ?>">Devices</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($page == 'dashboard'): ?>
            <h2 class="mb-4">Attendance Logs (Today)</h2>
            <div class="card p-3 mb-4">
                <form method="GET" class="row g-3 align-items-center">
                    <input type="hidden" name="page" value="dashboard">
                    <div class="col-auto">
                        <select name="device" class="form-select" required>
                            <option value="">-- Select Device --</option>
                            <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                <?php foreach ($devices['data'] as $dev): ?>
                                    <option value="<?php echo $dev['cloud_id']; ?>" <?php echo $selected_device == $dev['cloud_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dev['name']); ?> (<?php echo $dev['cloud_id']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">View Logs</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Employee PIN</th>
                                <th>Scan Time</th>
                                <th>Status</th>
                                <th>Verify Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($attendance)): ?>
                                <?php foreach ($attendance as $log): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($log['pin']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                        <td><span class="badge bg-info text-dark">Mode: <?php echo htmlspecialchars($log['status_scan']); ?></span></td>
                                        <td><?php echo htmlspecialchars($log['verify']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No attendance data found for today.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($page == 'employees'): ?>
            <h2 class="mb-4">Employee Management</h2>
            <div class="row">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">Add New Employee to Machine</div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="add_employee">
                                <div class="mb-3">
                                    <label class="form-label">Target Device</label>
                                    <select name="cloud_id" class="form-select" required>
                                        <option value="">-- Select Device --</option>
                                        <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                            <?php foreach ($devices['data'] as $dev): ?>
                                                <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">PIN (ID)</label>
                                    <input type="text" name="pin" class="form-control" placeholder="e.g. 101" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                                </div>
                                <button type="submit" class="btn btn-success w-100">Sync to Machine</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">Batch Actions</div>
                        <div class="card-body">
                            <h5>Delete Employee from Device</h5>
                            <p class="text-muted small">Removes user records and templates from the selected machine.</p>
                            <form method="POST" class="row g-3">
                                <input type="hidden" name="action" value="delete_employee">
                                <div class="col-md-6">
                                    <select name="cloud_id" class="form-select" required>
                                        <option value="">Select Device</option>
                                        <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                            <?php foreach ($devices['data'] as $dev): ?>
                                                <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="pin" class="form-control" placeholder="PIN" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-danger w-100">Delete</button>
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
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Cloud ID (SN)</th>
                                <th>Device Name</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                <?php foreach ($devices['data'] as $dev): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($dev['cloud_id']); ?></code></td>
                                        <td><?php echo htmlspecialchars($dev['name']); ?></td>
                                        <td><span class="badge badge-online">Online</span></td>
                                        <td class="text-end">
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="action" value="sync_time">
                                                <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Sync Time</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-4">No devices found. Check your API configuration.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
