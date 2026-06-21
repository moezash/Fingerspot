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
                if (is_api_success($res)) {
                    $message = "Command to add employee '{$_POST['name']}' sent successfully.";
                    $message_type = "success";
                } else {
                    $message = "Error: " . ($res['message'] ?? 'Failed to add employee');
                    $message_type = "danger";
                }
                break;
            case 'delete_employee':
                $res = delete_employee($_POST['cloud_id'], $_POST['pin']);
                if (is_api_success($res)) {
                    $message = "Command to delete employee PIN {$_POST['pin']} sent successfully.";
                    $message_type = "success";
                } else {
                    $message = "Error: " . ($res['message'] ?? 'Failed to delete employee');
                    $message_type = "danger";
                }
                break;
            case 'sync_time':
                $res = sync_device($_POST['cloud_id']);
                if (is_api_success($res)) {
                    $message = "Time synchronization command sent successfully.";
                    $message_type = "success";
                } else {
                    $message = "Error: " . ($res['message'] ?? 'Failed to sync time');
                    $message_type = "danger";
                }
                break;
            case 'restart':
                $res = restart_device($_POST['cloud_id']);
                if (is_api_success($res)) {
                    $message = "Restart command sent successfully.";
                    $message_type = "success";
                } else {
                    $message = "Error: " . ($res['message'] ?? 'Failed to restart device');
                    $message_type = "danger";
                }
                break;
        }
    }
}

// Fetch Data for Dashboard
$devices_res = get_devices();
$devices = [];
if (is_api_success($devices_res)) {
    $devices = $devices_res['data'] ?? [];
}

$attendance = [];
$selected_device = $_GET['device'] ?? '';

if ($selected_device) {
    $today = date('Y-m-d');
    // Fingerspot documentation recommends max 2 days range
    $att_res = get_attendance($selected_device, $today, $today);
    if (is_api_success($att_res)) {
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
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-center mb-4">Fingerspot App</h4>
                <a href="index.php?page=dashboard" class="<?php echo $page == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                <a href="index.php?page=employees" class="<?php echo $page == 'employees' ? 'active' : ''; ?>">Employees</a>
                <a href="index.php?page=devices" class="<?php echo $page == 'devices' ? 'active' : ''; ?>">Devices</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($page == 'dashboard'): ?>
                    <h2>Attendance Dashboard</h2>
                    <p class="text-muted">Viewing today's logs (<?php echo date('Y-m-d'); ?>)</p>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <form method="GET" class="d-flex">
                                <input type="hidden" name="page" value="dashboard">
                                <select name="device" id="device_select" class="form-select me-2" required>
                                    <option value="">Select Device</option>
                                    <?php foreach ($devices as $dev): ?>
                                        <option value="<?php echo $dev['cloud_id']; ?>" <?php echo $selected_device == $dev['cloud_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dev['name']); ?> (<?php echo $dev['cloud_id']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary">View</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>PIN</th>
                                        <th>Scan Time</th>
                                        <th>Status Scan</th>
                                        <th>Verify Mode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($attendance)): ?>
                                        <?php foreach ($attendance as $log): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($log['pin']); ?></td>
                                                <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($log['status_scan']); ?></span></td>
                                                <td><?php echo htmlspecialchars($log['verify']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-4">No logs found for the selected device today.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($page == 'employees'): ?>
                    <h2>Employee Management</h2>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white">Add New Employee</div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="add_employee">
                                        <div class="mb-3">
                                            <label for="cloud_id_add" class="form-label">Target Device</label>
                                            <select name="cloud_id" id="cloud_id_add" class="form-select" required>
                                                <option value="">Choose...</option>
                                                <?php foreach ($devices as $dev): ?>
                                                    <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pin" class="form-label">Employee PIN</label>
                                            <input type="text" name="pin" id="pin" class="form-control" placeholder="e.g. 101" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. John Doe" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">Sync to Machine</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="card shadow-sm border-danger">
                                <div class="card-header bg-danger text-white">Delete Employee</div>
                                <div class="card-body">
                                    <form method="POST" class="row g-3">
                                        <input type="hidden" name="action" value="delete_employee">
                                        <div class="col-md-12">
                                            <label for="cloud_id_del" class="form-label">Target Device</label>
                                            <select name="cloud_id" id="cloud_id_del" class="form-select" required>
                                                <option value="">Choose...</option>
                                                <?php foreach ($devices as $dev): ?>
                                                    <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="pin_del" class="form-label">Employee PIN</label>
                                            <input type="text" name="pin" id="pin_del" class="form-control" placeholder="PIN to delete" required>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-danger w-100">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($page == 'devices'): ?>
                    <h2>Device Management</h2>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cloud ID</th>
                                        <th>Device Name</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($devices)): ?>
                                        <?php foreach ($devices as $dev): ?>
                                            <tr>
                                                <td><code><?php echo htmlspecialchars($dev['cloud_id']); ?></code></td>
                                                <td><?php echo htmlspecialchars($dev['name']); ?></td>
                                                <td><span class="badge bg-success">Online</span></td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="action" value="sync_time">
                                                        <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">Sync Time</button>
                                                    </form>
                                                    <form method="POST" class="d-inline ms-1">
                                                        <input type="hidden" name="action" value="restart">
                                                        <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-warning text-dark">Restart</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-4">No devices found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ?>
