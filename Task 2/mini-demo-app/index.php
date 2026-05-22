<?php
/**
 * Main Controller & View for the Attendance Monitoring Dashboard
 */

require_once 'functions.php';

$page = $_GET['page'] ?? 'dashboard';
$message = '';
$messageType = 'info';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_employee') {
        $cloud_id = $_POST['cloud_id'];
        $pin = $_POST['pin'];
        $name = $_POST['name'];

        $result = add_employee($cloud_id, $pin, $name);
        $message = $result['message'] ?? 'Command sent';
        $messageType = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']) ? 'success' : 'danger';
    }

    if ($action === 'delete_employee') {
        $cloud_id = $_POST['cloud_id'];
        $pin = $_POST['pin'];

        $result = delete_employee($cloud_id, $pin);
        $message = $result['message'] ?? 'Delete command sent';
        $messageType = (isset($result['status']) && $result['status']) || (isset($result['success']) && $result['success']) ? 'success' : 'danger';
    }
}

// Fetch Data for Views
$devices = get_devices();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php"><?php echo APP_NAME; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'employees' ? 'active' : ''; ?>" href="?page=employees">Manage Employees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $page === 'devices' ? 'active' : ''; ?>" href="?page=devices">Devices</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($page === 'dashboard'): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Dashboard</h4>
                    <form class="d-flex gap-2" method="GET">
                        <input type="hidden" name="page" value="dashboard">
                        <select name="cloud_id" class="form-select">
                            <option value="">Select Device</option>
                            <?php foreach ($devices as $dev): ?>
                                <option value="<?php echo $dev['cloud_id']; ?>" <?php echo ($_GET['cloud_id'] ?? '') === $dev['cloud_id'] ? 'selected' : ''; ?>>
                                    <?php echo $dev['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="date" name="date" class="form-control" value="<?php echo $_GET['date'] ?? date('Y-m-d'); ?>">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">Recent Attendance Logs</div>
                    <div class="card-body">
                        <?php
                        $cloud_id = $_GET['cloud_id'] ?? '';
                        $date = $_GET['date'] ?? date('Y-m-d');

                        if ($cloud_id) {
                            $logs = get_attendance_logs($cloud_id, $date, $date);
                            if (!empty($logs)): ?>
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
                                            <?php foreach ($logs as $log): ?>
                                                <tr>
                                                    <td><?php echo $log['pin']; ?></td>
                                                    <td><?php echo $log['scan']; ?></td>
                                                    <td>
                                                        <span class="badge bg-info text-dark">
                                                            <?php echo format_scan_status($log['status_scan']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center py-4">No logs found for this date/device.</p>
                            <?php endif;
                        } else {
                            echo '<p class="text-center py-4">Please select a device to view logs.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'employees'): ?>
        <h4>Employee Management</h4>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Add New Employee</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add_employee">
                            <div class="mb-3">
                                <label for="cloud_id" class="form-label">Target Device</label>
                                <select id="cloud_id" name="cloud_id" class="form-select" required>
                                    <?php foreach ($devices as $dev): ?>
                                        <option value="<?php echo $dev['cloud_id']; ?>"><?php echo $dev['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="pin" class="form-label">Employee PIN</label>
                                <input type="text" id="pin" name="pin" class="form-control" required placeholder="e.g. 101">
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Jane Doe">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Sync to Device</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Remove Employee from Device</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="delete_employee">
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label for="del_cloud_id" class="form-label">Device</label>
                                    <select id="del_cloud_id" name="cloud_id" class="form-select" required>
                                        <?php foreach ($devices as $dev): ?>
                                            <option value="<?php echo $dev['cloud_id']; ?>"><?php echo $dev['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="del_pin" class="form-label">PIN</label>
                                    <input type="text" id="del_pin" name="pin" class="form-control" required>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-danger w-100">Delete</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'devices'): ?>
        <h4>Registered Devices</h4>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Cloud ID</th>
                                <th>Device Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($devices)): ?>
                                <?php foreach ($devices as $dev): ?>
                                    <tr>
                                        <td><code><?php echo $dev['cloud_id']; ?></code></td>
                                        <td><?php echo $dev['name']; ?></td>
                                        <td>
                                            <?php
                                            $status = strtolower($dev['status'] ?? 'unknown');
                                            $class = ($status === 'online') ? 'text-success' : 'text-danger';
                                            ?>
                                            <span class="<?php echo $class; ?> font-weight-bold">
                                                ● <?php echo ucfirst($status); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No devices found. Check your API Token.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Bootstrap 5 JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
