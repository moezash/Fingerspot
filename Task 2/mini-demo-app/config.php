<?php
/**
 * Configuration file for Fingerspot Mini Demo App
 */

// API Credentials
// Replace with your actual Fingerspot API Token from https://developer.fingerspot.io
define('API_TOKEN', 'YOUR_API_TOKEN_HERE');

// Base API URL
define('API_URL', 'https://developer.fingerspot.io/api');

// App Settings
define('APP_NAME', 'Fingerspot Attendance Dashboard');

// Error reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Jakarta');
?>
