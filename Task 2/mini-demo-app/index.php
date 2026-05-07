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
    <!-- Simple CSS for the dashboard -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { height: 100vh; background: #343a40; color: white; padding-top: 20px; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: white; background: #495057; }
        .main-content { padding: 20px; }
        .card { margin-bottom: 20px; border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    </style>
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
                    <h2>Attendance Dashboard (Today)</h2>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <form method="GET" class="d-flex">
                                <input type="hidden" name="page" value="dashboard">
                                <select name="device" class="form-select me-2">
                                    <option value="">Select Device</option>
                                    <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                        <?php foreach ($devices['data'] as $dev): ?>
                                            <option value="<?php echo $dev['cloud_id']; ?>" <?php echo $selected_device == $dev['cloud_id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dev['name']); ?> (<?php echo $dev['cloud_id']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>PIN</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Verify</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($attendance)): ?>
                                        <?php foreach ($attendance as $log): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($log['pin']); ?></td>
                                                <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                                <td><span class="badge bg-info"><?php echo htmlspecialchars($log['status_scan']); ?></span></td>
                                                <td><?php echo htmlspecialchars($log['verify']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center">No logs found for today.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($page == 'employees'): ?>
                    <h2>Employee Management</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">Add New Employee</div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="add_employee">
                                        <div class="mb-3">
                                            <label class="form-label">Device</label>
                                            <select name="cloud_id" class="form-select" required>
                                                <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                                    <?php foreach ($devices['data'] as $dev): ?>
                                                        <option value="<?php echo $dev['cloud_id']; ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pin" class="form-label">PIN</label>
                                            <input type="text" id="pin" name="pin" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" id="name" name="name" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">Send to Machine</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">Actions</div>
                                <div class="card-body">
                                    <p>Use the form on the left to add employees to the machine.</p>
                                    <hr>
                                    <h5>Delete Employee</h5>
                                    <form method="POST" class="row g-3">
                                        <input type="hidden" name="action" value="delete_employee">
                                        <div class="col-md-5">
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
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-danger w-100">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($page == 'devices'): ?>
                    <h2>Device Status</h2>
                    <div class="card">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cloud ID</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($devices['status']) && $devices['status'] && isset($devices['data'])): ?>
                                        <?php foreach ($devices['data'] as $dev): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($dev['cloud_id']); ?></td>
                                                <td><?php echo htmlspecialchars($dev['name']); ?></td>
                                                <td><span class="badge bg-success">Online</span></td>
                                                <td>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="action" value="sync_time">
                                                        <input type="hidden" name="cloud_id" value="<?php echo $dev['cloud_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">Sync Time</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center">No devices found or API error.</td></tr>
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
