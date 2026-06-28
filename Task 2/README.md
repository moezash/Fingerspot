# Task 2: Fingerspot API Integration & Sample Code

This folder contains the results of the Task 2 internship project, focused on integrating with the Fingerspot Cloud API.

## Project Structure

```text
Task 2/
├── sample-code/          # Independent runnable sample codes
│   ├── auth/             # Authentication & Header setup
│   ├── devices/          # Device listing & status
│   ├── attendance/       # Retrieving scan logs
│   ├── users/            # CRUD operations for employees
│   ├── sync/             # Machine commands (Time sync, Restart)
│   └── webhook/          # Real-time data processing
│
├── mini-demo-app/        # Integrated Attendance Monitoring Dashboard
│   ├── index.php         # Main UI & Controller
│   ├── config.php        # API Credentials
│   ├── functions.php     # Reusable API wrapper functions
│   └── assets/           # Application assets (CSS, etc.)
│       └── style.css     # Centralized dashboard styling
│
└── README.md             # Documentation
```

## Features Implemented

The following main features and endpoints have been implemented based on the [Fingerspot Developer Documentation](https://developer.fingerspot.io):

1.  **Authentication**: Bearer token implementation required for all requests (`POST`).
2.  **Get Device List**: Retrieve all registered attendance machines (`POST /api/get_device`).
3.  **Get Attendance Logs**: Fetch scan data with date range filtering (`POST /api/get_attlog`).
    *   *Note: Recommended maximum range is 2 days per request.*
4.  **Add/Update Employee**: Upload user info to the device (`POST /api/set_userinfo`).
5.  **Get Employee Info**: Request user data from the device (`POST /api/get_userinfo`).
    *   *Note: This is an asynchronous request; results are sent via Webhook.*
6.  **Delete Employee**: Remote user deletion (`POST /api/delete_userinfo`).
7.  **Remote Registration**: Trigger online registration mode (`POST /api/reg_online`).
8.  **Sync Time**: Remote timezone/time synchronization (`POST /api/set_time`).
9.  **Restart Machine**: Remote system restart (`POST /api/restart`).
10. **Webhook Receiver**: Handling real-time push data for logs and command responses.

## Requirements

- PHP 7.4 or higher
- PHP cURL extension enabled
- Valid Fingerspot API Token and Cloud ID

## Best Practices Followed

- **Production Security**: SSL verification (`CURLOPT_SSL_VERIFYPEER`) is enabled by default.
- **Robust Integration**: Unique transaction IDs (`trans_id`) are generated using `uniqid()` for every request to avoid collisions and improve tracking.
- **Error Handling**: Comprehensive checks for cURL errors and API status keys (`status` or `success`).
- **Professional UI**: Dashboard styles are centralized in `assets/style.css` for better maintainability.

## How to Use

### 1. Sample Codes
Each file in the `sample-code/` directory is designed to be self-contained and beginner-friendly.
1. Open the desired PHP file.
2. Replace `YOUR_API_TOKEN_HERE` and `YOUR_CLOUD_ID_HERE` with your actual credentials.
3. Run the script via terminal:
   ```bash
   php Task\ 2/sample-code/attendance/get-attendance.php
   ```

### 2. Mini Demo App
The Attendance Monitoring Dashboard provides a visual way to interact with multiple features at once.
1. Configure your credentials in `Task 2/mini-demo-app/config.php`.
2. Host the folder on a PHP-enabled server.
3. Access `index.php` in your browser.

## Important Notes
- All API requests use **POST** and send data in **JSON** format.
- A `trans_id` is required in most requests to track communication.
- Some commands are asynchronous; results will be sent back via your configured **Webhook**.
