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

The following main features and endpoints have been identified and implemented based on the [Fingerspot Developer Documentation](https://developer.fingerspot.io):

| Feature | Endpoint | Method | Description |
| :--- | :--- | :--- | :--- |
| **Authentication** | - | - | Uses Bearer Token in the `Authorization` header for all requests. |
| **Get Device List** | `/api/get_device` | `POST` | Retrieves a list of all devices registered to the account. |
| **Get Attendance Logs** | `/api/get_attlog` | `POST` | Retrieves scan records from a specific device within a date range (max 2 days). |
| **Set User Info** | `/api/set_userinfo` | `POST` | Adds or updates user data (PIN, Name, Biometrics) on a device. |
| **Get User Info** | `/api/get_userinfo` | `POST` | Requests user details from a device (Asynchronous, results via Webhook). |
| **Delete User Info** | `/api/delete_userinfo` | `POST` | Remotely deletes a specific user/PIN from a device. |
| **Register Online** | `/api/reg_online` | `POST` | Triggers remote registration mode (Fingerprint, Face, Card, Password). |
| **Set Time** | `/api/set_time` | `POST` | Synchronizes device clock or changes the timezone offset. |
| **Restart Device** | `/api/restart` | `POST` | Remotely reboots the attendance machine. |
| **Get User ID List** | `/api/get_userid_list` | `POST` | Retrieves all PIN numbers registered on a specific device. |
| **Real-time Webhook** | (Custom URL) | `POST` | A listener endpoint to receive real-time scans and command responses. |

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
