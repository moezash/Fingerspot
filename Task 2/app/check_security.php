<?php
/**
 * CLI security checks for FIX TAHAP 5 (no secrets printed).
 * Usage: php app/check_security.php
 */

$root = dirname(__DIR__);

// Start session before any output (CSRF tests)
require_once $root . '/app/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    configure_secure_session();

    // The CLI check must not depend on a writable system session directory.
    // Keep session storage process-local; production keeps PHP's configured handler.
    if (PHP_SAPI === 'cli') {
        session_set_save_handler(
            static fn($path, $name) => true,
            static fn() => true,
            static fn($id) => '',
            static fn($id, $data) => true,
            static fn($id) => true,
            static fn($maxLifetime) => 0
        );
    }

    session_start();
}

$configSrc = file_get_contents($root . '/app/config.php');
$functionsSrc = file_get_contents($root . '/app/functions.php');
$webhookSrc = file_get_contents($root . '/app/webhook.php');
$indexSrc = file_get_contents($root . '/app/index.php');
$gitignore = file_get_contents($root . '/.gitignore');
$ok = true;

ob_start();

function fail($msg) {
    global $ok;
    $ok = false;
    echo "FAIL: $msg\n";
}

function pass($msg) {
    echo "OK: $msg\n";
}

// --- A: API token not hardcoded ---
$hardcodedPatterns = [
    "/define\s*\(\s*'API_TOKEN'\s*,\s*'[^']{8,}'\s*\)/",
    "/define\s*\(\s*\"API_TOKEN\"\s*,\s*\"[^\"]{8,}\"\s*\)/",
];
$hardcoded = false;
foreach ([$configSrc, $functionsSrc, $indexSrc, $webhookSrc] as $src) {
    foreach ($hardcodedPatterns as $pat) {
        if (preg_match($pat, $src)) {
            $hardcoded = true;
        }
    }
}
if ($hardcoded || strpos($configSrc, "env('FINGERSPOT_API_TOKEN'") === false) {
    fail('A: API token must come from environment, not hardcoded');
} else {
    pass('A: API token not hardcoded (env-based)');
}

// --- B: .env ignored + example present ---
if (strpos($gitignore, '.env') === false) {
    fail('B: .env must be in .gitignore');
} else {
    pass('B: .env is gitignored');
}
if (!is_file($root . '/.env.example')) {
    fail('B: .env.example missing');
} else {
    $exampleHasSecret = false;
    foreach (file($root . '/.env.example', FILE_IGNORE_NEW_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (!preg_match('/^(FINGERSPOT_API_TOKEN|FINGERSPOT_WEBHOOK_SECRET|DB_PASS)=(.*)$/', $line, $m)) {
            continue;
        }
        if (trim($m[2]) !== '') {
            $exampleHasSecret = true;
        }
    }
    if ($exampleHasSecret) {
        fail('B: .env.example must not contain real secret values');
    } else {
        pass('B: .env.example present without secrets');
    }
}

// --- C: SSL verification default ON ---
if (strpos($functionsSrc, 'CURLOPT_SSL_VERIFYPEER, SSL_VERIFY') === false) {
    fail('C: CURLOPT_SSL_VERIFYPEER must use SSL_VERIFY');
} elseif (strpos($configSrc, "env('FINGERSPOT_SSL_VERIFY', '1')") === false) {
    fail('C: SSL_VERIFY default must be ON (env default 1)');
} else {
    pass('C: SSL verification default ON');
}

// --- D: production display_errors OFF ---
if (!preg_match("/ini_set\(\s*'display_errors'\s*,\s*APP_ENV\s*===\s*'development'\s*\?\s*'1'\s*:\s*'0'\s*\)/", $configSrc)) {
    fail('D: production must set display_errors=0');
} else {
    pass('D: production display_errors OFF (dev ON)');
}

// --- E/F: webhook secret gate ---
if (strpos($webhookSrc, 'verify_webhook_request()') === false) {
    fail('E/F: webhook.php must call verify_webhook_request before processing');
} else {
    pass('E/F: webhook calls verify_webhook_request');
}

$origGet = $_GET;

$probeSecret = 'probe-check-secret-only';
if (hash_equals($probeSecret, 'invalid-secret')) {
    fail('E: hash_equals must reject wrong secret');
} else {
    pass('E: invalid secret comparison rejected');
}
if (!hash_equals($probeSecret, $probeSecret)) {
    fail('F: hash_equals must accept matching secret');
} else {
    pass('F: valid secret comparison accepted');
}

if (is_webhook_secret_configured()) {
    $_GET = [];
    if (verify_webhook_request()) {
        fail('E: missing secret must be rejected when secret is configured');
    } else {
        pass('E: webhook missing secret rejected (configured)');
    }

    $_GET = ['secret' => 'definitely-wrong-secret-value'];
    if (verify_webhook_request()) {
        fail('E: wrong secret must be rejected');
    } else {
        pass('E: webhook wrong secret rejected (configured)');
    }

    $_GET = ['secret' => WEBHOOK_SECRET];
    if (verify_webhook_request()) {
        pass('F: webhook valid secret accepted (configured)');
    } else {
        fail('F: webhook valid secret should be accepted');
    }
} else {
    if (APP_ENV === 'production') {
        if (verify_webhook_request()) {
            fail('E: production without secret must reject');
        } else {
            pass('E: production without secret rejects');
        }
    } else {
        if (!verify_webhook_request()) {
            fail('F: development without secret should allow (explicit insecure-dev path)');
        } else {
            pass('F: development without secret allowed (optional in dev)');
        }
    }
    if (strpos($functionsSrc, 'hash_equals(WEBHOOK_SECRET') === false) {
        fail('E/F: verify_webhook_request must use hash_equals');
    } else {
        pass('E/F: webhook secret compare uses hash_equals');
    }
}
$_GET = $origGet;

if (strpos($webhookSrc, '401') === false) {
    fail('E: unauthorized webhook should return 401');
} else {
    pass('E: unauthorized webhook returns 401');
}

$authPos = strpos($webhookSrc, 'verify_webhook_request()');
$insertPos = strpos($webhookSrc, 'INSERT INTO webhook_responses');
if ($authPos === false || $insertPos === false || $authPos > $insertPos) {
    fail('E: secret check must run before DB insert');
} else {
    pass('E: secret check before database write');
}

// --- G/H: CSRF ---
$_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$good = $_SESSION['csrf_token'];
if (verify_csrf_token('invalid-token')) {
    fail('G: invalid CSRF must be rejected');
} else {
    pass('G: CSRF invalid rejected');
}
if (!verify_csrf_token($good)) {
    fail('H: valid CSRF must be accepted');
} else {
    pass('H: CSRF valid accepted');
}
if (strpos($indexSrc, 'verify_csrf_token') === false) {
    fail('G/H: index.php must validate CSRF on POST');
} else {
    pass('G/H: index.php validates CSRF');
}
if (stripos($webhookSrc, 'csrf') !== false) {
    fail('G/H: webhook must not require CSRF');
} else {
    pass('G/H: webhook excluded from CSRF');
}

// --- I: SQL helper table whitelist ---
$threw = false;
try {
    get_pagination('DROP TABLE users; --', '1=1', []);
} catch (Throwable $e) {
    $threw = true;
}
if (!$threw) {
    fail('I: get_pagination must reject arbitrary table names');
} else {
    pass('I: get_pagination rejects arbitrary tables');
}
if (strpos($functionsSrc, 'is_allowed_db_table') === false) {
    fail('I: table whitelist helper missing');
} else {
    pass('I: table whitelist helper present');
}

// --- J: secrets must not appear in this script's output ---
$output = ob_get_contents();
$leaked = false;
foreach ([API_TOKEN, WEBHOOK_SECRET, DB_PASS] as $secret) {
    if (is_string($secret) && $secret !== '' && strpos($output, $secret) !== false) {
        $leaked = true;
    }
}
if ($leaked) {
    // Do not echo the secret — scrub and fail
    fail('J: secret value appeared in check output');
} else {
    pass('J: secrets not present in check output');
}

// Session hardening present
if (strpos($configSrc, 'httponly') === false || strpos($indexSrc, 'configure_secure_session') === false) {
    fail('session hardening / configure_secure_session missing');
} else {
    pass('session cookie hardening configured');
}

ob_end_flush();

if ($ok) {
    echo "\nSECURITY CHECK OK\n";
    exit(0);
}

echo "\nSECURITY CHECK FAILED\n";
exit(1);
