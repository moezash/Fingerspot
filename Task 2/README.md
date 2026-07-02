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
│   └── assets/           # Optional styles/images
│
└── README.md             # Documentation
```

## Main Endpoints Documentation

Based on [developer.fingerspot.io](https://developer.fingerspot.io), the following are the core API endpoints for Fingerspot Cloud:

| Feature | Endpoint | Method | Description |
|---------|----------|--------|-------------|
| **Device List** | `/api/get_device` | POST | Retrieve a list of all attendance devices registered to your account. |
| **Attendance Logs** | `/api/get_attlog` | POST | Retrieve attendance scan logs from a specific device within a date range. |
| **Set User Info** | `/api/set_userinfo` | POST | Add or update employee information (PIN, Name, Privilege, etc.) on a device. |
| **Get User Info** | `/api/get_userinfo` | POST | Request employee information from a device (result returned via Webhook). |
| **Delete User Info** | `/api/delete_userinfo` | POST | Remove an employee from a specific attendance device. |
| **Register Online** | `/api/reg_online` | POST | Trigger remote registration mode for fingerprints, face, etc. |
| **Set Time** | `/api/set_time` | POST | Synchronize the device's date, time, and timezone. |
| **Restart Device** | `/api/restart` | POST | Remote reboot of the attendance machine. |

## Features Implemented in this Project

1.  **Authentication**: Secure Bearer token implementation for all API requests.
2.  **Get Device List**: Full implementation of device retrieval.
3.  **Get Attendance Logs**: Flexible retrieval of scan data with date filtering.
4.  **Employee Management**: Add, update, and delete employees remotely.
5.  **Remote Operations**: Syncing time and restarting machines from the cloud.
6.  **Webhook Integration**: Sample logic for receiving real-time push data.

## Requirements

- PHP 7.4 or higher
- PHP cURL extension enabled
- Valid Fingerspot API Token and Cloud ID

## How to Use

### 1. Sample Codes
Each file in the `sample-code/` directory is designed to be self-contained and beginner-friendly.
1. Open the desired PHP file.
2. Replace `YOUR_API_TOKEN_HERE` and `YOUR_CLOUD_ID_HERE` with your actual credentials.
3. Run the script via terminal or browser:
   ```bash
   php sample-code/attendance/get-attendance.php
   ```

### 2. Mini Demo App
The Attendance Monitoring Dashboard provides a visual way to interact with multiple features at once.
1. Configure your credentials in `mini-demo-app/config.php`.
2. Host the folder on a PHP-enabled server (e.g., Apache, Nginx, or PHP Built-in server).
3. Access `index.php` in your browser.

## Important Notes
- All API requests use **POST** and send data in **JSON** format.
- A `trans_id` is required in most requests to track the communication.
- Some commands are asynchronous; the result will be sent back via your configured **Webhook**.
