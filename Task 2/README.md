# Task 2: Fingerspot Cloud API Integration Samples

This folder contains the results of the Task 2 internship project, focused on studying the Fingerspot Cloud API and creating professional, runnable PHP sample code for its main features.

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
│   └── assets/           # UI Assets (CSS/JS)
│
└── README.md             # This documentation
```

## API Endpoints Overview

All requests to the Fingerspot Cloud API use the **POST** method and communicate via **JSON**.

| Feature | Endpoint | Description |
| :--- | :--- | :--- |
| **Get Device List** | `/api/get_device` | Retrieve all registered devices in your account. |
| **Get Attendance Logs** | `/api/get_attlog` | Fetch scan data with date range filtering. |
| **Set User Info** | `/api/set_userinfo` | Add or update employee data on the device. |
| **Get User Info** | `/api/get_userinfo` | Request user details (Asynchronous via Webhook). |
| **Delete User Info** | `/api/delete_userinfo` | Remote employee deletion from the device. |
| **Register Online** | `/api/reg_online` | Trigger remote template registration mode. |
| **Set Time** | `/api/set_time` | Synchronize machine time/timezone. |
| **Restart Machine** | `/api/restart` | Remote system reboot. |

## Requirements

- PHP 7.4 or higher
- PHP cURL extension enabled
- Valid Fingerspot API Token and Cloud ID

## How to Use

### 1. Sample Codes
Each file in the `sample-code/` directory is self-contained and includes example request/response blocks.
1. Open a sample file (e.g., `sample-code/devices/get-devices.php`).
2. Replace `YOUR_API_TOKEN_HERE` with your actual token.
3. Run via CLI: `php sample-code/devices/get-devices.php`

### 2. Mini Demo App
A simple "Attendance Monitoring Dashboard" integrating multiple features.
1. Configure credentials in `mini-demo-app/config.php`.
2. Host on a PHP server and access `index.php`.

## Security Notes
- `CURLOPT_SSL_VERIFYPEER` is set to `true` by default for production security.
- Setting it to `false` is only recommended for local development troubleshooting.
