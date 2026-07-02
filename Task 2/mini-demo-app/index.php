<?php
/**
 * Attendance Monitoring Dashboard - Main Index
 */

require_once 'functions.php';

$page = $_GET['page'] ?? 'dashboard';
$message = '';
$messageType = 'info';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_employee') {
        $result = set_employee(FINGERSPOT_CLOUD_ID, $_POST['pin'], $_POST['name']);
        if (isset($result['success']) && $result['success']) {
            $message = "Employee added successfully! Machine will process it shortly.";
            $messageType = "success";
        } else {
            $message = "Error: " . ($result['message'] ?? 'Failed to add employee');
            $messageType = "danger";
        }
    }

    if ($action === 'delete_employee') {
        $result = delete_employee(FINGERSPOT_CLOUD_ID, $_POST['pin']);
        if (isset($result['success']) && $result['success']) {
            $message = "Delete command sent for PIN " . $_POST['pin'];
            $messageType = "warning";
        } else {
            $message = "Error: " . ($result['message'] ?? 'Failed to delete');
            $messageType = "danger";
        }
    }

    if ($action === 'sync_time') {
        $result = sync_time($_POST['cloud_id']);
        if (isset($result['success']) && $result['success']) {
            $message = "Time synchronization command sent to device.";
            $messageType = "success";
        } else {
            $message = "Error: " . ($result['message'] ?? 'Failed to sync');
            $messageType = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><?php echo APP_NAME; ?></a>
        <span class="navbar-text d-none d-md-block">
            v<?php echo APP_VERSION; ?>
        </span>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>" href="index.php?page=dashboard">
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'employees' ? 'active' : ''; ?>" href="index.php?page=employees">
                            Employees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page === 'devices' ? 'active' : ''; ?>" href="index.php?page=devices">
                            Devices
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="pt-3">
                <?php echo display_alert($message, $messageType); ?>

                <?php if ($page === 'dashboard'): ?>
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h2>Attendance Dashboard</h2>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.reload();">Refresh Data</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Latest Attendance Logs (Today)</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>PIN</th>
                                            <th>Scan Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $logs = get_attendance_logs(FINGERSPOT_CLOUD_ID, date('Y-m-d'), date('Y-m-d'));
                                        if (isset($logs['data']) && is_array($logs['data'])):
                                            foreach ($logs['data'] as $log):
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($log['pin']); ?></td>
                                                <td><?php echo htmlspecialchars($log['scan']); ?></td>
                                                <td><?php echo htmlspecialchars($log['status_scan']); ?></td>
                                            </tr>
                                        <?php
                                            endforeach;
                                        else:
                                        ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No attendance data found for today or API is not configured.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($page === 'employees'): ?>
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h2>Employee Management</h2>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">Add New Employee</div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="add_employee">
                                        <div class="mb-3">
                                            <label for="pin" class="form-label">PIN (ID)</label>
                                            <input type="text" class="form-control" id="pin" name="pin" required placeholder="e.g. 101">
                                        </div>
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. John Doe">
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Send to Device</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">Manage Existing Employees</div>
                                <div class="card-body">
                                    <p class="text-muted small">Note: To see the full employee list, the machine must be connected and data synced via API/Webhook.</p>
                                    <form method="POST" class="row g-3">
                                        <input type="hidden" name="action" value="delete_employee">
                                        <div class="col-auto">
                                            <input type="text" class="form-control" name="pin" placeholder="Enter PIN to delete" required>
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user from the device?')">Delete from Device</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php elseif ($page === 'devices'): ?>
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h2>Cloud Devices</h2>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Cloud ID</th>
                                            <th>Device Name</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $devices = get_devices();
                                        if (isset($devices['data']) && is_array($devices['data'])):
                                            foreach ($devices['data'] as $dev):
                                        ?>
                                            <tr>
                                                <td><code><?php echo htmlspecialchars($dev['cloud_id']); ?></code></td>
                                                <td><?php echo htmlspecialchars($dev['name']); ?></td>
                                                <td>
                                                    <span class="status-<?php echo strtolower($dev['status'] ?? 'offline'); ?>">
                                                        ● <?php echo htmlspecialchars($dev['status'] ?? 'Unknown'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <form method="POST" style="display:inline;">
                                                        <input type="hidden" name="action" value="sync_time">
                                                        <input type="hidden" name="cloud_id" value="<?php echo htmlspecialchars($dev['cloud_id']); ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">Sync Time</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php
                                            endforeach;
                                        else:
                                        ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No devices found or API is not configured.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<footer class="footer mt-auto py-3 bg-light text-center border-top">
    <div class="container">
        <span class="text-muted">&copy; <?php echo date('Y'); ?> Fingerspot Internship - Task 2</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ?>
