<?php
/**
 * Configuration file for Fingerspot Mini Demo App
 *
 * This file contains the API credentials and application settings.
 * For internship deliverables, ensure credentials remain as placeholders
 * or instructions for the end-user.
 */

// 1. Fingerspot Cloud API Credentials
// Get your API Token from https://developer.fingerspot.io
define('API_TOKEN', 'YOUR_API_TOKEN_HERE');

// 2. Base API URL
// The endpoint for the Fingerspot Developer API
define('API_URL', 'https://developer.fingerspot.io/api');

// 3. Application Settings
define('APP_NAME', 'Attendance Monitoring Dashboard');
define('APP_VERSION', '1.0.0');

// 4. Error Reporting
// Enable during development, disable in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 5. Default Timezone
date_default_timezone_set('Asia/Jakarta');
?>
