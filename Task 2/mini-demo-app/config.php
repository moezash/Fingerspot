<?php
/**
 * Configuration for the Attendance Monitoring Dashboard
 */

// API Credentials
define('FINGERSPOT_API_TOKEN', 'YOUR_API_TOKEN_HERE');

// Application Settings
define('APP_NAME', 'Fingerspot Attendance Dashboard');
define('BASE_URL', 'https://developer.fingerspot.io/api');

/**
 * Common headers for all API requests
 */
$api_headers = [
    'Authorization: Bearer ' . FINGERSPOT_API_TOKEN,
    'Content-Type: application/json',
    'Accept: application/json'
];
?>
