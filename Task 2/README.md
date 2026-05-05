# Task 2: Fingerspot Cloud API Integration Samples & Demo App

This repository contains professional, beginner-friendly PHP sample code for integrating with the Fingerspot Cloud API, along with a functional Attendance Monitoring Dashboard.

## 📂 Project Structure

```text
Task 2/
├── sample-code/          # Independent runnable sample codes
│   ├── auth/             # Authentication & Header setup
│   ├── devices/          # Device listing & status
│   ├── attendance/       # Retrieving scan logs
│   ├── users/            # CRUD operations & Online Registration
│   ├── sync/             # Machine commands (Time sync, Restart)
│   └── webhook/          # Real-time data processing
│
├── mini-demo-app/        # Integrated Attendance Monitoring Dashboard
│   ├── index.php         # Main UI & Controller
│   ├── config.php        # API Credentials & Settings
│   ├── functions.php     # Reusable API wrapper functions
│   └── assets/           # Optional styles/images
│
├── endpoints.txt         # Compiled list of main API endpoints
└── README.md             # Documentation
```

## 🚀 Key Features Implemented

1.  **Device Management**: List all registered cloud devices and check their online status.
2.  **Attendance Logs**: Retrieve scan data with specific date range filtering.
3.  **Employee Management**:
    *   Add/Set user information on the machine.
    *   Delete users remotely.
    *   Request full user info via Webhook.
    *   Trigger remote online registration (Fingerprint, Face, etc.).
4.  **Machine Synchronization**: Sync machine time and remote system restart.
5.  **Real-time Webhook**: A receiver template to process incoming data from the Cloud API.
6.  **Demo Mode**: The Mini Demo App includes a "Demo Mode" that uses mock data if no API token is provided.

## 🛠️ Requirements

- **PHP 7.4+**
- **PHP cURL Extension**
- Fingerspot Cloud API Account (Get your token at [developer.fingerspot.io](https://developer.fingerspot.io))

## 📖 How to Run

### 1. Independent Sample Codes
Each file in the `sample-code/` directory is designed to be self-contained.
1. Open the desired PHP file.
2. Replace `YOUR_API_TOKEN_HERE` and `YOUR_CLOUD_ID_HERE` with your actual credentials.
3. Run via CLI:
   ```bash
   php sample-code/attendance/get-attendance.php
   ```

### 2. Mini Demo App
The dashboard provides a visual interface for the main features.
1. Update credentials in `mini-demo-app/config.php`.
2. Start a local PHP server:
   ```bash
   php -S localhost:8000 -t "Task 2/mini-demo-app"
   ```
3. Open `http://localhost:8000` in your browser.

## ⚠️ Important Notes
- **SSL Verification**: For local development convenience, `CURLOPT_SSL_VERIFYPEER` is set to `false`. **Ensure this is set to `true` in production.**
- **Asynchronous Commands**: Commands like `get_userinfo` and `reg_online` are asynchronous. The API will return a success status once the command is queued; the actual result will be sent to your configured **Webhook URL**.
