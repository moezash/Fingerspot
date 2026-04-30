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

## Main Endpoints & Features

The following primary endpoints from developer.fingerspot.io are implemented in this project:

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/get_device` | POST | Retrieve a list of all devices registered in the account. |
| `/api/get_attlog` | POST | Fetch attendance scan logs for a specific device and date range. |
| `/api/set_userinfo` | POST | Add or update employee information on the device. |
| `/api/get_userinfo` | POST | Request detailed user info from the device (returns data via Webhook). |
| `/api/delete_userinfo` | POST | Remotely delete an employee from the device. |
| `/api/reg_online` | POST | Trigger remote registration mode on the device for a specific PIN. |
| `/api/set_time` | POST | Synchronize the device's time and timezone. |
| `/api/restart` | POST | Remotely restart the device system. |

## Features Implemented

1.  **Authentication**: Bearer token implementation required for all requests.
2.  **Device Management**: Listing and checking status of all connected machines.
3.  **Attendance Monitoring**: Retrieving logs for reporting or dashboarding.
4.  **User Management**: CRUD operations for employees on the device.
5.  **Device Control**: Remote commands for syncing time and restarting machines.
6.  **Real-time Processing**: Webhook implementation for handling asynchronous data.

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
