<?php
/**
 * Main UI Controller for Fingerspot Mini Demo App
 */

require_once 'functions.php';

$page = $_GET['page'] ?? 'dashboard';
$action = $_POST['action'] ?? '';

// Handle actions
if ($action) {
    switch ($action) {
        case 'add_employee':
            $result = add_employee($_POST['cloud_id'], $_POST['pin'], $_POST['name'], $_POST['privilege']);
            if ($result['status'] || (isset($result['success']) && $result['success'])) {
                set_flash_message("Employee {$_POST['name']} successfully added to device.");
            } else {
                set_flash_message("Failed to add employee: " . ($result['message'] ?? 'Unknown error'), 'danger');
            }
            header("Location: index.php?page=employees");
            exit;

        case 'delete_employee':
            $result = delete_employee($_POST['cloud_id'], $_POST['pin']);
            if ($result['status'] || (isset($result['success']) && $result['success'])) {
                set_flash_message("Delete command sent for PIN {$_POST['pin']}.");
            } else {
                set_flash_message("Failed to delete employee: " . ($result['message'] ?? 'Unknown error'), 'danger');
            }
            header("Location: index.php?page=employees");
            exit;

        case 'sync_time':
            $result = sync_time($_POST['cloud_id']);
            if ($result['status'] || (isset($result['success']) && $result['success'])) {
                set_flash_message("Time synchronization command sent to device.");
            } else {
                set_flash_message("Failed to sync time: " . ($result['message'] ?? 'Unknown error'), 'danger');
            }
            header("Location: index.php?page=devices");
            exit;
    }
}

// Prepare data for views
$devices_response = get_devices();
$devices = ((isset($devices_response['status']) && $devices_response['status']) || (isset($devices_response['success']) && $devices_response['success'])) ? ($devices_response['data'] ?? []) : [];

$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="position-sticky">
                <h4 class="text-center mb-4">Fingerspot Fio</h4>
                <a href="index.php?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
                <a href="index.php?page=employees" class="<?php echo $page === 'employees' ? 'active' : ''; ?>">Employees</a>
                <a href="index.php?page=devices" class="<?php echo $page === 'devices' ? 'active' : ''; ?>">Devices</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Attendance Monitoring</h1>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($flash['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($page === 'dashboard'): ?>
                <h2>Attendance Dashboard</h2>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Recent Attendance Logs</span>
                                <form class="row g-2" method="GET">
                                    <input type="hidden" name="page" value="dashboard">
                                    <div class="col-auto">
                                        <select name="cloud_id" class="form-select form-select-sm" required>
                                            <option value="">Select Device</option>
                                            <?php foreach ($devices as $dev): ?>
                                                <option value="<?php echo htmlspecialchars($dev['cloud_id']); ?>" <?php echo ($_GET['cloud_id'] ?? '') === $dev['cloud_id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($dev['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>PIN</th>
                                            <th>Scan Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_GET['cloud_id'])) {
                                            $today = date('Y-m-d');
                                            $logs_response = get_attendance($_GET['cloud_id'], $today, $today);
                                            $logs = ((isset($logs_response['status']) && $logs_response['status']) || (isset($logs_response['success']) && $logs_response['success'])) ? ($logs_response['data'] ?? []) : [];

                                            if (!empty($logs)) {
                                                foreach ($logs as $log) {
                                                    echo "<tr>
                                                        <td>" . htmlspecialchars($log['pin']) . "</td>
                                                        <td>" . htmlspecialchars($log['scan']) . "</td>
                                                        <td><span class='badge bg-info'>" . htmlspecialchars($log['status_scan']) . "</span></td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3' class='text-center'>No logs found for today or device offline.</td></tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3' class='text-center'>Please select a device to view logs.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($page === 'employees'): ?>
                <h2>Employee Management</h2>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">Add New Employee</div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add_employee">
                                    <div class="mb-3">
                                        <label for="cloud_id" class="form-label">Target Device</label>
                                        <select name="cloud_id" id="cloud_id" class="form-select" required>
                                            <option value="">Select Device</option>
                                            <?php foreach ($devices as $dev): ?>
                                                <option value="<?php echo htmlspecialchars($dev['cloud_id']); ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="pin" class="form-label">PIN (ID)</label>
                                        <input type="text" name="pin" id="pin" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" name="name" id="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="privilege" class="form-label">Privilege</label>
                                        <select name="privilege" id="privilege" class="form-select">
                                            <option value="0">User</option>
                                            <option value="1">Admin</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Upload to Device</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Existing Employees (Device Commands)</div>
                            <div class="card-body text-center py-5">
                                <p class="text-muted">Employee lists are maintained on the devices. Use the form to add or update.</p>
                                <hr>
                                <h5>Remote Delete</h5>
                                <form method="POST" class="row g-3 justify-content-center">
                                    <input type="hidden" name="action" value="delete_employee">
                                    <div class="col-auto">
                                        <select name="cloud_id" class="form-select" required>
                                            <option value="">Device</option>
                                            <?php foreach ($devices as $dev): ?>
                                                <option value="<?php echo htmlspecialchars($dev['cloud_id']); ?>"><?php echo htmlspecialchars($dev['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <input type="text" name="pin" class="form-control" placeholder="PIN to delete" required>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete from Device</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($page === 'devices'): ?>
                <h2>Device List</h2>
                <div class="row mt-4">
                    <?php if (empty($devices)): ?>
                        <div class="col-12">
                            <div class="alert alert-warning">No devices found. Please check your API Token and Cloud ID in the dashboard.</div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($devices as $dev): ?>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><?php echo htmlspecialchars($dev['name']); ?></h5>
                                        <p class="card-text text-muted"><?php echo htmlspecialchars($dev['cloud_id']); ?></p>
                                        <p class="<?php echo (isset($dev['status']) && $dev['status'] === 'Online') ? 'status-online' : 'status-offline'; ?>">
                                            ● <?php echo htmlspecialchars($dev['status'] ?? 'Unknown'); ?>
                                        </p>
                                        <div class="mt-3">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="sync_time">
                                                <input type="hidden" name="cloud_id" value="<?php echo htmlspecialchars($dev['cloud_id']); ?>">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm">Sync Time</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php ?>
