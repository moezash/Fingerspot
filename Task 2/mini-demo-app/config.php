<?php
/**
 * Configuration file for Fingerspot Mini Demo App
 *
 * Instructions:
 * 1. Get your API Token from developer.fingerspot.io
 * 2. Replace 'YOUR_API_TOKEN_HERE' with your actual token.
 */

// API Credentials
define('API_TOKEN', 'YOUR_API_TOKEN_HERE');

// Base API URL
define('API_URL', 'https://developer.fingerspot.io/api');

// App Settings
define('APP_NAME', 'Attendance Monitor Dashboard');

// Error reporting (Disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
