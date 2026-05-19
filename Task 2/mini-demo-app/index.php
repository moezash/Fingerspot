<?php
/**
 * Main Controller & View for Fingerspot Mini Demo App
 *
 * Features implemented:
 * - Device Listing
 * - Today's Attendance Monitoring
 * - Employee Management (Add/Delete)
 * - Device Synchronization (Time Sync)
 */
require_once 'functions.php';

$page = $_GET['page'] ?? 'dashboard';
$message = '';
$message_type = 'info';

// Handle Form Submissions (Actions)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_employee':
            $res = add_employee($_POST['cloud_id'], $_POST['pin'], $_POST['name']);
            $message = $res['message'] ?? (is_success($res) ? 'Add user command sent to machine.' : 'Failed to send command.');
            $message_type = is_success($res) ? 'success' : 'danger';
            break;

        case 'delete_employee':
            $res = delete_employee($_POST['cloud_id'], $_POST['pin']);
            $message = $res['message'] ?? (is_success($res) ? 'Delete user command sent to machine.' : 'Failed to send command.');
            $message_type = is_success($res) ? 'success' : 'danger';
            break;

        case 'sync_time':
            $res = sync_time($_POST['cloud_id']);
            $message = $res['message'] ?? (is_success($res) ? 'Time sync command sent to machine.' : 'Failed to send command.');
            $message_type = is_success($res) ? 'success' : 'danger';
            break;
    }
}

// Fetch Global Data
$devices_res = get_devices();
$devices = is_success($devices_res) ? $devices_res['data'] : [];

// Fetch Dashboard Data
$attendance = [];
$selected_device = $_GET['device'] ?? '';

if ($page == 'dashboard' && $selected_device) {
    $today = date('Y-m-d');
    $att_res = get_attendance($selected_device, $today, $today);
    if (is_success($att_res)) {
        $attendance = $att_res['data'] ?? [];
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
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar bg-dark text-white min-vh-100">
                <div class="p-3 text-center border-bottom border-secondary">
                    <h5 class="m-0">Fingerspot Dashboard</h5>
                    <small class="text-muted">Internship Task 2</small>
                </div>
                <nav class="mt-3">
                    <a href="index.php?page=dashboard" class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                    <a href="index.php?page=employees" class="nav-link <?php echo $page == 'employees' ? 'active' : ''; ?>">Employees</a>
                    <a href="index.php?page=devices" class="nav-link <?php echo $page == 'devices' ? 'active' : ''; ?>">Devices</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content bg-light">
                <header class="bg-white p-3 shadow-sm mb-4">
                    <div class="container-fluid">
                        <h4 class="m-0"><?php echo ucwords($page); ?></h4>
                    </div>
                </header>

                <div class="container-fluid px-4">
                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($page == 'dashboard'): ?>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <form method="GET" class="row g-3 align-items-center">
                                            <input type="hidden" name="page" value="dashboard">
                                            <div class="col-auto">
                                                <label for="deviceSelect" class="form-label mb-0">Select Device:</label>
                                            </div>
                                            <div class="col-md-4">
                                                <select name="device" id="deviceSelect" class="form-select">
                                                    <option value="">-- Choose Machine --</option>
                                                    <?php foreach ($devices as $dev): ?>
                                                        <option value="<?php echo $dev['cloud_id']; ?>" <?php echo $selected_device == $dev['cloud_id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($dev['name']); ?> (<?php echo $dev['cloud_id']; ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-primary">Refresh Logs</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Today's Attendance Logs</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">PIN</th>
                                                <th>Scan Time</th>
                                                <th>Status</th>
                                                <th class="pe-4 text-end">Verify</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($attendance)): ?>
                                                <?php foreach ($attendance as $log): ?>
                                                    <tr>
                                                        <td class="ps-4 font-weight-bold"><?php echo htmlspecialchars($log['pin']); ?></td>
                                                        <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                                        <td>
                                                            <span class="badge rounded-pill <?php echo $log['status_scan'] == '0' ? 'bg-success' : 'bg-warning'; ?>">
                                                                <?php echo $log['status_scan'] == '0' ? 'Check In' : 'Check Out'; ?>
                                                            </span>
                                                        </td>
                                                        <td class="pe-4 text-end text-muted"><?php echo htmlspecialchars($log['verify']); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="4" class="text-center py-5 text-muted">
                                                        <?php echo $selected_device ? 'No logs found for today.' : 'Please select a device to view logs.'; ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($page == 'employees'): ?>
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Add New Employee</h6>
                                    </div>
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="add_employee">
                                            <div class="mb-3">
                                                <label for="cloud_id" class="form-label">Target Device</label>
                                                <select name="cloud_id" id="cloud_id" class="form-select" required>
                                                    <option value="">-- Select Machine --</option>
                                                    <?php foreach ($devices as $dev): ?>
                                                        <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="pin" class="form-label">PIN / Employee ID</label>
                                                <input type="text" name="pin" id="pin" class="form-control" placeholder="e.g. 101" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Full Name</label>
                                                <input type="text" name="name" id="name" class="form-control" placeholder="e.g. John Doe" required>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100">Send to Machine</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="m-0 font-weight-bold text-danger">Delete Employee</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted mb-4">Warning: This action will remotely remove the user from the attendance machine.</p>
                                        <form method="POST" class="row g-3">
                                            <input type="hidden" name="action" value="delete_employee">
                                            <div class="col-md-6">
                                                <label for="del_cloud_id" class="form-label">Device</label>
                                                <select name="cloud_id" id="del_cloud_id" class="form-select" required>
                                                    <option value="">-- Select Machine --</option>
                                                    <?php foreach ($devices as $dev): ?>
                                                        <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="del_pin" class="form-label">PIN</label>
                                                <input type="text" name="pin" id="del_pin" class="form-control" placeholder="PIN" required>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="submit" class="btn btn-danger w-100">Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($page == 'devices'): ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Connected Devices</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Cloud ID</th>
                                                <th>Machine Name</th>
                                                <th>Connection Status</th>
                                                <th class="pe-4 text-end">Remote Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($devices)): ?>
                                                <?php foreach ($devices as $dev): ?>
                                                    <tr>
                                                        <td class="ps-4 font-monospace"><?php echo htmlspecialchars($dev['cloud_id']); ?></td>
                                                        <td><?php echo htmlspecialchars($dev['name']); ?></td>
                                                        <td>
                                                            <span class="badge <?php echo $dev['status'] == 'Online' ? 'bg-success' : 'bg-secondary'; ?>">
                                                                <?php echo htmlspecialchars($dev['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td class="pe-4 text-end">
                                                            <form method="POST" class="d-inline">
                                                                <input type="hidden" name="action" value="sync_time">
                                                                <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-primary">Sync Time</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center py-5">No devices found. Ensure your API Token is correct.</td></tr>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
