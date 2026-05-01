# Task 2: Fingerspot API Integration & Sample Code

This folder contains the results of the Task 2 internship project, focused on integrating with the Fingerspot Cloud API.

## Project Structure

```text
Task 2/
├── sample-code/          # Independent runnable sample codes
│   ├── auth/             # Authentication & Header setup (auth.php)
│   ├── devices/          # Device listing & status (get-devices.php)
│   ├── attendance/       # Retrieving scan logs (get-attendance.php)
│   ├── users/            # Employee management (add-user.php, delete-user.php, etc.)
│   ├── sync/             # Machine commands (set-time.php, restart.php)
│   └── webhook/          # Real-time data processing (webhook-receiver.php)
│
├── mini-demo-app/        # Integrated Attendance Monitoring Dashboard
│   ├── index.php         # Main UI & Controller
│   ├── config.php        # API Credentials
│   ├── functions.php     # Reusable API wrapper functions
│   └── assets/           # Stylesheets and frontend assets
│
└── README.md             # This documentation
```

## Features Implemented

The following main features and endpoints have been implemented using pure PHP and cURL:

1.  **Authentication**: Bearer token implementation for all requests.
2.  **Get Device List**: Retrieve all registered attendance machines (`/api/get_device`).
3.  **Get Attendance Logs**: Fetch scan data with date range filtering (`/api/get_attlog`).
4.  **Add Employee**: Upload user info to the device (`/api/set_userinfo`).
5.  **Delete Employee**: Remote user deletion (`/api/delete_userinfo`).
6.  **Remote Registration**: Trigger online registration mode (`/api/reg_online`).
7.  **Sync Time**: Remote timezone/time synchronization (`/api/set_time`).
8.  **Restart Machine**: Remote system restart (`/api/restart`).
9.  **Webhook Receiver**: Handling real-time push data for logs and command responses.

## Requirements

- PHP 7.4 or higher
- PHP cURL extension enabled
- Valid Fingerspot API Token and Cloud ID from [developer.fingerspot.io](https://developer.fingerspot.io)

## How to Use

### 1. Sample Codes
Each file in the `sample-code/` directory is designed to be self-contained and educational.
1. Open the desired PHP file.
2. Replace `YOUR_API_TOKEN_HERE` and `YOUR_CLOUD_ID_HERE` with your actual credentials.
3. Run the script via terminal or browser:
   ```bash
   php Task\ 2/sample-code/attendance/get-attendance.php
   ```

### 2. Mini Demo App
The Attendance Monitoring Dashboard provides a visual way to interact with multiple features.
1. Configure your credentials in `Task 2/mini-demo-app/config.php`.
2. Host the folder on a PHP-enabled server.
3. Access `index.php` in your browser.

## API Notes
- All API requests use the **POST** method.
- Data must be sent in **JSON** format with `Content-Type: application/json`.
- A unique `trans_id` should be included in every request for tracking.
- Many commands are asynchronous; the actual execution result is returned via the **Webhook URL**.
