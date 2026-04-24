# Task 2: Fingerspot Cloud API Integration & Sample Code

This repository contains the results of the Task 2 internship project, focusing on integrating the Fingerspot Cloud API using pure PHP and cURL.

## Project Structure

```text
Task 2/
├── sample-code/          # Independent, runnable sample codes for each feature
│   ├── auth/             # Authentication & Header setup
│   ├── devices/          # Device listing and status
│   ├── attendance/       # Retrieving scan logs
│   ├── users/            # CRUD operations & Online Registration
│   ├── sync/             # Machine commands (Time sync, Restart)
│   └── webhook/          # Real-time data processing template
│
├── mini-demo-app/        # Integrated Attendance Monitoring Dashboard
│   ├── index.php         # Main UI (Bootstrap-based) & Controller
│   ├── config.php        # API Credentials & App Settings
│   ├── functions.php     # Reusable API wrapper functions
│   └── assets/           # Optional styles/images
│
└── README.md             # This documentation
```

## Features Implemented

The following main features and endpoints have been implemented and documented:

1.  **Authentication**: Implementation of Bearer Token authentication required for all requests.
2.  **Get Device List**: Retrieve all registered attendance machines (`/api/get_device`).
3.  **Get Attendance Logs**: Fetch scan data with date range filtering (`/api/get_attlog`).
4.  **Add/Update User**: Upload employee info to the device (`/api/set_userinfo`).
5.  **Get User Information**: Request user data and templates from the machine (`/api/get_userinfo`).
6.  **Delete User**: Remote removal of an employee from the machine (`/api/delete_userinfo`).
7.  **Remote Registration**: Trigger online registration mode for fingers/face (`/api/reg_online`).
8.  **Sync Time**: Synchronize device timezone and time (`/api/set_time`).
9.  **Restart Machine**: Remotely reboot the attendance system (`/api/restart`).
10. **Webhook Receiver**: Template for handling real-time push data and command responses.

## Requirements

- PHP 7.4 or higher
- PHP cURL extension enabled
- Valid Fingerspot API Token and Cloud ID (obtain from [developer.fingerspot.io](https://developer.fingerspot.io))

## Getting Started

### 1. Using Sample Codes
Each file in `sample-code/` is self-contained.
1. Open the desired file (e.g., `sample-code/attendance/get-attendance.php`).
2. Replace `YOUR_API_TOKEN_HERE` and `YOUR_CLOUD_ID_HERE` with your actual credentials.
3. Run the script:
   ```bash
   php sample-code/attendance/get-attendance.php
   ```

### 2. Running the Mini Demo App
The dashboard provides a visual way to interact with the API features.
1. Navigate to `mini-demo-app/config.php` and enter your API Token.
2. Start a local PHP server:
   ```bash
   php -S localhost:8000 -t mini-demo-app/
   ```
3. Open `http://localhost:8000` in your browser.

## Important Notes
- **Asynchronous Operations**: Some commands (like `Get Userinfo`) trigger the machine to send data back via Webhook rather than returning it in the immediate API response.
- **Security**: For production use, ensure your Webhook URL is secured with HTTPS and validates incoming requests.
- **Pure PHP**: All code is written without external frameworks or dependencies, as per the internship requirements.

---
*Created as part of the Internship Project.*
