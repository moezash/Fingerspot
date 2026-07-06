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

Based on the official [Fingerspot Documentation](https://developer.fingerspot.io), the following endpoints are available and implemented in this project:

| Category | Endpoint | Method | Description |
| :--- | :--- | :--- | :--- |
| **Authentication** | - | - | All requests require a `Bearer [token]` in the Authorization header. |
| **Device** | `/api/get_device` | POST | Retrieve the list of registered devices and their status. |
| **Attendance** | `/api/get_attlog` | POST | Retrieve attendance logs (scan data) within a date range (max 2 days). |
| **User** | `/api/get_userinfo` | POST | Request detailed user information (Asynchronous). |
| **User** | `/api/set_userinfo` | POST | Add or update user information on a device (Asynchronous). |
| **User** | `/api/delete_userinfo`| POST | Delete a user from a device (Asynchronous). |
| **User** | `/api/reg_online` | POST | Trigger remote biometric registration (Asynchronous). |
| **User** | `/api/get_userid_list`| POST | Retrieve all registered PINs on a device (Asynchronous). |
| **System** | `/api/set_time` | POST | Synchronize device time and timezone (Asynchronous). |
| **System** | `/api/restart` | POST | Remotely reboot the device (Asynchronous). |
| **Webhook** | - | POST | Receives real-time attendance logs and asynchronous command responses. |

## Features Implemented in Mini Demo App
- Dashboard with Device Status
- Real-time Attendance Monitoring
- Employee Management (View, Add, Delete)
- Device Synchronization (Time Sync, Restart)

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
