<?php
/**
 * Configuration file for Fingerspot Mini Demo App
 */

// API Credentials
// Replace with your actual Fingerspot API Token
define('API_TOKEN', 'YOUR_API_TOKEN_HERE');

// Base API URL
define('API_URL', 'https://developer.fingerspot.io/api');

// App Settings
define('APP_NAME', 'Fingerspot Attendance Dashboard');

// Demo Mode: If API_TOKEN is the default placeholder, show mock data
define('DEMO_MODE', (API_TOKEN === 'YOUR_API_TOKEN_HERE'));

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
