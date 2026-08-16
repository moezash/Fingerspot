<?php
/**
 * Main application entry point.
 */
require_once __DIR__ . '/functions.php';

configure_secure_session();
session_start();

// Handle POST actions (CSRF-protected; webhook is separate and excluded)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    handle_action($_POST);
}

$page = $_GET['page'] ?? 'dashboard';
$validPages = allowed_redirect_pages();
if (!in_array($page, $validPages, true)) {
    $page = 'dashboard';
}

function handle_action($post) {
    $message = '';
    $messageType = 'success';
    $result = ['status' => false, 'message' => 'Aksi tidak dikenali'];

    if (!verify_csrf_token($post['csrf_token'] ?? null)) {
        $_SESSION['flash_message'] = 'Permintaan ditolak: CSRF token tidak valid.';
        $_SESSION['flash_type'] = 'danger';
        $redirectPage = in_array($post['redirect_page'] ?? '', allowed_redirect_pages(), true)
            ? $post['redirect_page']
            : 'dashboard';
        header('Location: index.php?page=' . urlencode($redirectPage));
        exit;
    }

    $action = isset($post['action']) ? (string) $post['action'] : '';
    if (!in_array($action, allowed_post_actions(), true)) {
        $_SESSION['flash_message'] = 'Aksi tidak dikenali.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: index.php?page=dashboard');
        exit;
    }

    $cloudId = validate_cloud_id($post['cloud_id'] ?? '');
    if ($cloudId === null) {
        $_SESSION['flash_message'] = 'Cloud ID tidak valid.';
        $_SESSION['flash_type'] = 'danger';
        $redirectPage = in_array($post['redirect_page'] ?? '', allowed_redirect_pages(), true)
            ? $post['redirect_page']
            : 'dashboard';
        header('Location: index.php?page=' . urlencode($redirectPage));
        exit;
    }

    switch ($action) {
        case 'get_attendance':
            $start = validate_date_ymd($post['start_date'] ?? '');
            $end = validate_date_ymd($post['end_date'] ?? '');
            if ($start === null || $end === null) {
                $result = ['status' => false, 'message' => 'Rentang tanggal tidak valid (YYYY-MM-DD).'];
            } else {
                $result = get_attendance($cloudId, $start, $end);
            }
            if ($result['status']) {
                $saved = (int) ($result['saved_count'] ?? 0);
                $message = $saved > 0
                    ? "Get Attlog berhasil. $saved data absensi disimpan ke database."
                    : 'Get Attlog berhasil dikirim. Data akan diterima via webhook jika mesin mengirim balik.';
            } else {
                $message = 'Gagal: ' . ($result['message'] ?? 'Unknown error');
            }
            break;

        case 'get_userinfo':
            $pin = validate_pin($post['pin'] ?? '');
            if ($pin === null) {
                $result = ['status' => false, 'message' => 'PIN tidak valid.'];
            } else {
                $result = get_userinfo($cloudId, $pin);
            }
            $message = $result['status']
                ? 'Request Get Userinfo berhasil. Data user akan diterima via webhook.'
                : 'Gagal: ' . ($result['message'] ?? 'Unknown error');
            break;

        case 'set_userinfo':
            $pin = validate_pin($post['pin'] ?? '');
            $name = validate_name($post['name'] ?? '');
            $privilege = in_array((string) ($post['privilege'] ?? '0'), ['0', '14'], true)
                ? (string) $post['privilege']
                : '0';
            if ($pin === null || $name === null) {
                $result = ['status' => false, 'message' => 'PIN atau nama tidak valid.'];
            } else {
                $result = set_userinfo($cloudId, $pin, $name, $privilege);
            }
            $message = $result['status']
                ? 'Request Set Userinfo berhasil dikirim ke mesin.'
                : 'Gagal: ' . ($result['message'] ?? 'Unknown error');
            break;

        case 'delete_userinfo':
            $pin = validate_pin($post['pin'] ?? '');
            if ($pin === null) {
                $result = ['status' => false, 'message' => 'PIN tidak valid.'];
            } else {
                $result = delete_userinfo($cloudId, $pin);
            }
            $message = $result['status']
                ? 'Request Delete Userinfo berhasil dikirim ke mesin.'
                : 'Gagal: ' . ($result['message'] ?? 'Unknown error');
            break;

        case 'get_allpin':
            $result = get_allpin($cloudId);
            $message = $result['status']
                ? 'Request Get All PIN berhasil. Daftar PIN akan diterima via webhook.'
                : 'Gagal: ' . ($result['message'] ?? 'Unknown error');
            break;

        case 'set_time':
            $result = set_time($cloudId);
            $message = $result['status']
                ? 'Request Set Time berhasil dikirim ke mesin.'
                : 'Gagal: ' . ($result['message'] ?? 'Unknown error');
            break;

        case 'register_online':
            $pin = validate_pin($post['pin'] ?? '');
            $verification = (string) ($post['verification'] ?? '0');
            if (!in_array($verification, ['0', '1', '2', '12', '13'], true)) {
                $verification = '0';
            }
            if ($pin === null) {
                $result = ['status' => false, 'message' => 'PIN tidak valid.'];
            } else {
                $result = register_online($cloudId, $pin, $verification);
            }
            $message = $result['status']
                ? 'Request Register Online berhasil. Mesin masuk mode registrasi.'
                : 'Gagal: ' . ($result['message'] ?? 'Unknown error');
            break;

        case 'restart':
            $result = restart_machine($cloudId);
            $message = $result['status']
                ? 'Request Restart berhasil dikirim ke mesin.'
                : 'Gagal: ' . ($result['message'] ?? 'Unknown error');
            break;
    }

    if (!$result['status']) {
        $messageType = 'danger';
    }

    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $messageType;

    $redirectPage = $post['redirect_page'] ?? 'dashboard';
    if (!in_array($redirectPage, allowed_redirect_pages(), true)) {
        $redirectPage = 'dashboard';
    }
    header('Location: index.php?page=' . urlencode($redirectPage));
    exit;
}

$flashMessage = $_SESSION['flash_message'] ?? null;
$flashType = $_SESSION['flash_type'] ?? 'info';
unset($_SESSION['flash_message'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/svg+xml" sizes="any" href="assets/favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/app.css?v=<?php echo urlencode(APP_VERSION); ?>" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <div class="ambient-scene" aria-hidden="true">
            <span class="chrome-sphere sphere-small"></span>
            <span class="chrome-sphere sphere-large"></span>
            <svg class="ambient-lines" viewBox="0 0 1600 900" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="ribbon-a" x1="0" x2="1"><stop stop-color="#C8F560"/><stop offset=".5" stop-color="#55DDE0"/><stop offset="1" stop-color="#5B8CFF"/></linearGradient>
                    <filter id="ribbon-blur"><feGaussianBlur stdDeviation="12"/></filter>
                </defs>
                <path class="ambient-ribbon" d="M-100 790 C310 555 570 835 900 665 S1320 500 1710 610" stroke="url(#ribbon-a)" filter="url(#ribbon-blur)"/>
                <g class="biometric-contour">
                    <path d="M1080 820 C1025 650 1060 470 1230 365 C1340 298 1450 310 1555 380"/>
                    <path d="M1140 850 C1078 685 1115 525 1260 430 C1350 371 1448 375 1530 430"/>
                    <path d="M1205 865 C1155 725 1182 588 1290 510 C1360 460 1434 460 1500 500"/>
                    <path d="M1280 875 C1245 770 1265 670 1332 614 C1380 575 1430 570 1470 590"/>
                </g>
            </svg>
        </div>
        <div class="sidebar-backdrop" data-sidebar-close></div>
            <!-- Sidebar -->
            <nav class="sidebar" id="appSidebar" aria-label="Navigasi utama">
                <div class="brand">
                    <span class="brand-mark"><i class="bi bi-fingerprint"></i></span>
                    <span><strong><?php echo APP_NAME; ?></strong><small>Fingerspot Console</small></span>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-label">Overview</li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-label">Data</li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'attlog' ? 'active' : ''; ?>" href="?page=attlog">
                            <i class="bi bi-clock-history"></i> Data Absensi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'userinfo' ? 'active' : ''; ?>" href="?page=userinfo">
                            <i class="bi bi-people"></i> Data User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'pins' ? 'active' : ''; ?>" href="?page=pins">
                            <i class="bi bi-key"></i> Data PIN
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'devices' ? 'active' : ''; ?>" href="?page=devices">
                            <i class="bi bi-hdd-network"></i> Devices
                        </a>
                    </li>
                    <li class="nav-label">Operations</li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'commands' ? 'active' : ''; ?>" href="?page=commands">
                            <i class="bi bi-terminal"></i> Kirim Command
                        </a>
                    </li>
                    <li class="nav-label">Logs</li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'api_logs' ? 'active' : ''; ?>" href="?page=api_logs">
                            <i class="bi bi-arrow-up-circle"></i> Riwayat API
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $page == 'webhook_logs' ? 'active' : ''; ?>" href="?page=webhook_logs">
                            <i class="bi bi-arrow-down-circle"></i> Riwayat Webhook
                        </a>
                    </li>
                </ul>
                <div class="sidebar-footer"><span class="status-dot ready"></span> Console ready <small>v<?php echo APP_VERSION; ?></small></div>
            </nav>

            <!-- Main Content -->
            <section class="workspace">
                <header class="topbar">
                    <button class="icon-btn menu-toggle" type="button" aria-label="Buka navigasi" aria-controls="appSidebar" aria-expanded="false"><i class="bi bi-list"></i></button>
                    <div class="topbar-title"><span>Integration Console</span><small>Monitor &amp; control center</small></div>
                    <div class="system-indicators">
                        <span><i class="status-dot <?php echo is_api_token_configured() ? 'ready' : 'warning'; ?>"></i> API</span>
                        <span><i class="status-dot <?php echo get_config_status()['webhook_ready'] ? 'ready' : 'warning'; ?>"></i> Webhook</span>
                        <span><i class="status-dot ready"></i> DB</span>
                    </div>
                </header>
                <main class="content-area">
                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?php echo htmlspecialchars($flashType); ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($flashMessage); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup pesan"></button>
                    </div>
                <?php endif; ?>

                <?php include __DIR__ . '/pages/' . $page . '.php'; ?>
                </main>
            </section>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/app.js?v=<?php echo urlencode(APP_VERSION); ?>"></script>
</body>
</html>
