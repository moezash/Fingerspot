<?php
/**
 * Configuration file for Fingerspot Integration App
 *
 * Credentials & Cloud IDs harus di-set via environment variables
 * (atau file .env di root project). Jangan hardcode secret di source.
 */

/**
 * Load simple KEY=VALUE pairs from a .env file into the process environment.
 * Existing real environment variables take precedence.
 */
function load_env_file($path) {
    if (!is_readable($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '') {
            continue;
        }

        $len = strlen($value);
        if ($len >= 2) {
            $first = $value[0];
            $last = $value[$len - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $existing = getenv($name);
        if ($existing !== false && $existing !== '') {
            continue;
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

/**
 * Read an environment variable with optional default.
 */
function env($key, $default = '') {
    $value = getenv($key);
    if ($value === false || $value === '') {
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string) $_SERVER[$key];
        }
        return $default;
    }
    return (string) $value;
}

// Project root: parent of /app
load_env_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env');

// ---------------------------------------------------------------------------
// Fingerspot credentials & integration settings (from environment)
// ---------------------------------------------------------------------------
// Compatibility: constant names API_TOKEN / DEFAULT_CLOUD_IDS / WEBHOOK_URL
// remain available for existing code, but values come from env vars.
define('API_TOKEN', env('FINGERSPOT_API_TOKEN', ''));
define('API_URL', 'https://developer.fingerspot.io/api');

// Comma-separated Cloud IDs, e.g. FTV123456,FTV789012
define('DEFAULT_CLOUD_IDS', env('FINGERSPOT_CLOUD_IDS', ''));

// Public webhook URL registered at developer.fingerspot.io
define('WEBHOOK_URL', env('FINGERSPOT_WEBHOOK_URL', ''));

// Shared secret for webhook URL query (?secret=...) — never log this value
define('WEBHOOK_SECRET', env('FINGERSPOT_WEBHOOK_SECRET', ''));

// Outbound SSL verification (default ON). Set FINGERSPOT_SSL_VERIFY=0 only for local debug.
define('SSL_VERIFY', !in_array(strtolower(env('FINGERSPOT_SSL_VERIFY', '1')), ['0', 'false', 'off', 'no'], true));

// Pending async commands older than this are failed while waiting for a webhook.
$commandPendingTimeout = filter_var(env('COMMAND_PENDING_TIMEOUT_MINUTES', '15'), FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1],
]);
define('COMMAND_PENDING_TIMEOUT_MINUTES', $commandPendingTimeout !== false ? $commandPendingTimeout : 15);

// development | production — affects private-URL validation messaging
define('APP_ENV', strtolower(env('APP_ENV', 'development')));

// Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'fingerspot_app'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

// App Settings
define('APP_NAME', 'ELDEV');
define('APP_VERSION', '1.0.0');
define('ITEMS_PER_PAGE', 20);

date_default_timezone_set('Asia/Jakarta');

// Environment-aware error display (never leak details in production)
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', APP_ENV === 'development' ? '1' : '0');

/**
 * Whether the current request appears to use HTTPS.
 */
function is_https_request() {
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
        return true;
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        return true;
    }
    return false;
}

/**
 * Configure secure session cookie flags. Call before session_start().
 * Auth/login system is a future improvement — only hardens cookie params for now.
 */
function configure_secure_session() {
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');

    $secure = is_https_request();
    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, '/; samesite=Lax', '', $secure, true);
    }
}

/**
 * Whether API token is configured (never expose the token value).
 */
function is_api_token_configured() {
    return API_TOKEN !== '';
}

/**
 * Whether webhook shared secret is configured (never expose the value).
 */
function is_webhook_secret_configured() {
    return WEBHOOK_SECRET !== '';
}

/**
 * Whether at least one Cloud ID is configured.
 */
function is_cloud_ids_configured() {
    return trim(DEFAULT_CLOUD_IDS) !== '';
}

/**
 * Detect private / local hostnames and IP addresses.
 */
function is_private_or_local_host($host) {
    $host = strtolower(trim($host));
    if ($host === '') {
        return true;
    }

    // Strip IPv6 brackets
    if ($host[0] === '[' && substr($host, -1) === ']') {
        $host = substr($host, 1, -1);
    }

    if ($host === 'localhost' || $host === '::1' || $host === '0.0.0.0') {
        return true;
    }

    if (substr($host, -6) === '.local' || substr($host, -8) === '.localhost') {
        return true;
    }

    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        if ($host === '127.0.0.1') {
            return true;
        }
        // RFC1918 + link-local
        if (preg_match('/^10\./', $host)) {
            return true;
        }
        if (preg_match('/^192\.168\./', $host)) {
            return true;
        }
        if (preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $host)) {
            return true;
        }
        if (preg_match('/^169\.254\./', $host)) {
            return true;
        }
        return false;
    }

    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        // Unique local / link-local
        if (stripos($host, 'fc') === 0 || stripos($host, 'fd') === 0 || stripos($host, 'fe80') === 0) {
            return true;
        }
        return false;
    }

    return false;
}

/**
 * Validate webhook URL configuration.
 *
 * Returns:
 * - valid: usable public URL (HTTPS), or HTTP public with warning
 * - ready: checklist "siap" — only HTTPS public URL
 * - level: ok | warning | error
 * - message: human-readable status (never includes secrets)
 */
function validate_webhook_url($url = null) {
    if ($url === null) {
        $url = WEBHOOK_URL;
    }
    $url = trim((string) $url);

    if ($url === '') {
        return [
            'valid' => false,
            'ready' => false,
            'level' => 'error',
            'message' => 'Webhook URL kosong. Set FINGERSPOT_WEBHOOK_URL ke URL publik HTTPS (mis. ngrok).',
        ];
    }

    $parts = parse_url($url);
    if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
        return [
            'valid' => false,
            'ready' => false,
            'level' => 'error',
            'message' => 'Webhook URL tidak valid. Gunakan URL absolut, contoh: https://example.com/Task2/app/webhook.php',
        ];
    }

    $scheme = strtolower($parts['scheme']);
    $host = strtolower($parts['host']);

    if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
        return [
            'valid' => false,
            'ready' => false,
            'level' => 'error',
            'message' => 'Webhook URL memakai localhost/127.0.0.1 — tidak bisa dijangkau Fingerspot. Gunakan URL publik (ngrok/domain).',
        ];
    }

    $isPrivate = is_private_or_local_host($host);
    if ($isPrivate) {
        $prodNote = (APP_ENV === 'production')
            ? ' Tidak diizinkan di production.'
            : ' Fingerspot cloud tidak bisa menjangkau alamat lokal/private.';
        return [
            'valid' => false,
            'ready' => false,
            'level' => 'error',
            'message' => 'Webhook URL mengarah ke host lokal/private.' . $prodNote,
        ];
    }

    if ($scheme === 'http') {
        return [
            'valid' => true,
            'ready' => false,
            'level' => 'warning',
            'message' => 'Webhook URL memakai HTTP. Disarankan HTTPS untuk production; status belum dianggap siap.',
        ];
    }

    if ($scheme !== 'https') {
        return [
            'valid' => false,
            'ready' => false,
            'level' => 'error',
            'message' => 'Webhook URL harus memakai skema https:// (atau http:// dengan peringatan).',
        ];
    }

    return [
        'valid' => true,
        'ready' => true,
        'level' => 'ok',
        'message' => 'Webhook URL publik HTTPS valid. Pastikan sudah didaftarkan di dashboard Fingerspot.',
    ];
}

/**
 * Configuration status for UI / CLI checks (no secret values).
 */
function get_config_status() {
    $webhook = validate_webhook_url();

    $tokenOk = is_api_token_configured();
    $cloudOk = is_cloud_ids_configured();
    $secretOk = is_webhook_secret_configured();

    $errors = [];
    $warnings = [];

    if (!$tokenOk) {
        $errors[] = 'FINGERSPOT_API_TOKEN belum di-set. API request tidak akan dijalankan.';
    }
    if (!$cloudOk) {
        $errors[] = 'FINGERSPOT_CLOUD_IDS belum di-set. Isi Cloud ID mesin (pisahkan dengan koma jika lebih dari satu).';
    }
    if ($webhook['level'] === 'error') {
        $errors[] = $webhook['message'];
    } elseif ($webhook['level'] === 'warning') {
        $warnings[] = $webhook['message'];
    }

    if (!$secretOk) {
        if (APP_ENV === 'production') {
            $errors[] = 'FINGERSPOT_WEBHOOK_SECRET wajib di production. Tambahkan ?secret=... pada URL webhook di dashboard.';
        } else {
            $warnings[] = 'FINGERSPOT_WEBHOOK_SECRET belum di-set. Development mengizinkan webhook tanpa secret; production akan menolak.';
        }
    }

    if (!SSL_VERIFY) {
        $warnings[] = 'FINGERSPOT_SSL_VERIFY dimatikan. Aktifkan kembali untuk production.';
    }

    return [
        'token_configured' => $tokenOk,
        'cloud_ids_configured' => $cloudOk,
        'webhook_secret_configured' => $secretOk,
        'ssl_verify' => SSL_VERIFY,
        'webhook' => $webhook,
        'webhook_ready' => $webhook['ready'],
        'errors' => $errors,
        'warnings' => $warnings,
        'ok' => empty($errors),
    ];
}
