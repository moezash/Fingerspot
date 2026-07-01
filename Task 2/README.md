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

## API Endpoints

The following main endpoints have been identified and implemented from the [Fingerspot Developer Documentation](https://developer.fingerspot.io):

| Feature | Endpoint | Method | Description |
| --- | --- | --- | --- |
| **Get Device List** | `/api/get_device` | POST | Retrieve all registered attendance machines in the account. |
| **Get Attendance Logs** | `/api/get_attlog` | POST | Fetch attendance scan data with date range filtering. |
| **Get User Info** | `/api/get_userinfo` | POST | Request user information (PIN, name, templates) from a device. |
| **Set User Info** | `/api/set_userinfo` | POST | Upload or update user information on a specific device. |
| **Delete User Info** | `/api/delete_userinfo` | POST | Remote command to delete user data from a device. |
| **Remote Registration**| `/api/reg_online` | POST | Trigger a device to enter remote registration mode. |
| **Sync Time** | `/api/set_time` | POST | Synchronize the date, time, and timezone of a device. |
| **Restart Machine** | `/api/restart` | POST | Remotely reboot the attendance machine. |

## Features Implemented

1.  **Authentication**: Bearer token implementation required for all requests.
2.  **Device Management**: Listing devices and monitoring status.
3.  **Attendance Tracking**: Retrieving historical scan logs.
4.  **Employee Management**: CRUD operations for users on devices (Add, Request, Delete).
5.  **Device Control**: Remote commands for synchronization and maintenance.
6.  **Webhook Integration**: Processing real-time data push for logs and async responses.

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
