<?php
/**
 * Configuration file for Fingerspot Mini Demo App
 */

// API Credentials
// Replace with your actual Fingerspot API Token from the dashboard
define('API_TOKEN', 'YOUR_API_TOKEN_HERE');

// Base API URL
define('API_URL', 'https://developer.fingerspot.io/api');

// App Settings
define('APP_NAME', 'Attendance Monitor Dashboard');

// Error reporting - Set to 0 in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for alerts
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
