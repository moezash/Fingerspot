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

## API Endpoints & Features

The following main endpoints and features from [developer.fingerspot.io](https://developer.fingerspot.io) have been identified and implemented:

### Core API Endpoints
| Feature | Endpoint | Description |
| :--- | :--- | :--- |
| **Authentication** | N/A | Uses Bearer Token in the HTTP `Authorization` header for all requests. |
| **Get Device List** | `/api/get_device` | Retrieves a list of all devices registered to the account. |
| **Get Attendance Logs**| `/api/get_attlog` | Fetches scan logs from a specific device within a defined date range. |
| **Add/Update User** | `/api/set_userinfo` | Sends user information (PIN, name, etc.) to the attendance machine. |
| **Request User Info**| `/api/get_userinfo` | Requests user data from the device (result is returned via Webhook). |
| **Delete User** | `/api/delete_userinfo`| Removes a specific user's information from the attendance machine. |
| **Remote Registration**| `/api/reg_online` | Triggers the device to enter remote registration mode for various biometric types. |
| **Sync Machine Time** | `/api/set_time` | Synchronizes the device's date, time, and timezone. |
| **Restart Machine** | `/api/restart` | Commands the attendance machine to perform a system reboot. |

### Webhook Features
The Fingerspot Cloud API utilizes Webhooks to push real-time data and asynchronous command results to your server:
- **Real-time Scan Data**: Automatically receives scan logs as they happen on the device.
- **Asynchronous Results**: Receives confirmation and data (e.g., from `get_userinfo`) for commands sent to the machine.

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
