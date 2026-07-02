<?php
/**
 * Configuration for the Attendance Monitoring Dashboard
 */

// API Credentials - Replace with your actual credentials
define('FINGERSPOT_API_TOKEN', 'YOUR_API_TOKEN_HERE');
define('FINGERSPOT_CLOUD_ID', 'YOUR_CLOUD_ID_HERE');

// API Base URL
define('FINGERSPOT_API_BASE', 'https://developer.fingerspot.io/api');

// Dashboard Settings
define('APP_NAME', 'Fingerspot Attendance Dashboard');
define('APP_VERSION', '1.0.0');

// Reporting settings (default range for logs)
define('DEFAULT_DATE_RANGE', 0); // 0 means today only
?>
