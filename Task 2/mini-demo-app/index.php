<?php
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
                $message = $res['message'] ?? ($res['status'] ? 'Employee added successfully' : 'Failed to add employee');
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
            case 'restart_device':
                $res = restart_device($_POST['cloud_id']);
                $message = $res['message'] ?? ($res['status'] ? 'Restart command sent' : 'Failed to restart device');
                $message_type = $res['status'] ? 'success' : 'danger';
                break;
        }
    }
}

// Fetch Data for Dashboard
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
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar bg-dark text-white min-vh-100 p-3">
                <h4 class="text-center mb-4">Fingerspot App</h4>
                <nav class="nav flex-column">
                    <a href="index.php?page=dashboard" class="nav-link <?php echo $page == 'dashboard' ? 'active text-white' : 'text-secondary'; ?>">Dashboard</a>
                    <a href="index.php?page=employees" class="nav-link <?php echo $page == 'employees' ? 'active text-white' : 'text-secondary'; ?>">Employees</a>
                    <a href="index.php?page=devices" class="nav-link <?php echo $page == 'devices' ? 'active text-white' : 'text-secondary'; ?>">Devices</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($page == 'dashboard'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Attendance Dashboard (Today)</h2>
                        <span class="text-muted"><?php echo date('l, d F Y'); ?></span>
                    </div>

                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <form method="GET" class="row g-3 align-items-center">
                                <input type="hidden" name="page" value="dashboard">
                                <div class="col-auto">
                                    <label class="col-form-label">Select Device:</label>
                                </div>
                                <div class="col-auto">
                                    <select name="device" class="form-select">
                                        <option value="">-- Choose Machine --</option>
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
                                    <button type="submit" class="btn btn-primary">Filter Logs</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Latest Attendance Scans</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">PIN</th>
                                            <th>Scan Time</th>
                                            <th>Status</th>
                                            <th>Verification</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($attendance)): ?>
                                            <?php foreach ($attendance as $log): ?>
                                                <tr>
                                                    <td class="ps-3 fw-bold"><?php echo htmlspecialchars($log['pin']); ?></td>
                                                    <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                                    <td>
                                                        <span class="badge rounded-pill <?php echo $log['status_scan'] == '0' ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                                            <?php echo $log['status_scan'] == '0' ? 'Check-In' : 'Check-Out'; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($log['verify']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-4">No logs found for today on this device.</td></tr>
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
                            <div class="card shadow-sm">
                                <div class="card-header">Add New Employee</div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="add_employee">
                                        <div class="mb-3">
                                            <label class="form-label">Target Device</label>
                                            <select name="cloud_id" class="form-select" required>
                                                <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                                    <?php foreach ($devices['data'] as $dev): ?>
                                                        <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">PIN / Employee ID</label>
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
                        <div class="col-md-7">
                            <div class="card shadow-sm border-danger">
                                <div class="card-header bg-danger text-white">Danger Zone</div>
                                <div class="card-body">
                                    <h5>Delete Employee from Machine</h5>
                                    <p class="text-muted small">Caution: This will remove the user from the selected device.</p>
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
                    <h2 class="mb-4">Device Status & Maintenance</h2>
                    <div class="card shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Cloud ID</th>
                                            <th>Device Name</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                            <?php foreach ($devices['data'] as $dev): ?>
                                                <tr>
                                                    <td class="ps-3 fw-bold text-primary"><?php echo htmlspecialchars($dev['cloud_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($dev['name']); ?></td>
                                                    <td>
                                                        <span class="badge bg-success">Online</span>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="action" value="sync_time">
                                                            <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">Sync Time</button>
                                                        </form>
                                                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to restart this device?');">
                                                            <input type="hidden" name="action" value="restart_device">
                                                            <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">Restart</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center py-4">No devices found. Ensure your API Token is correct.</td></tr>
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
