# Task 2: Fingerspot API Integration & Sample Code

This folder contains the deliverables for Task 2 of the Fingerspot Internship project. It includes runnable sample codes for all main API features and a comprehensive mini-demo application.

## 📋 Main API Endpoints

The following endpoints from [developer.fingerspot.io](https://developer.fingerspot.io) have been identified and implemented:

| Endpoint | Description |
| :--- | :--- |
| `/api/get_device` | Retrieve all registered attendance machines. |
| `/api/get_attlog` | Fetch attendance scan logs with date range filtering. |
| `/api/set_userinfo` | Upload or update user information on the device. |
| `/api/get_userinfo` | Request specific user information from the device. |
| `/api/delete_userinfo` | Remotely delete a user from the device. |
| `/api/reg_online` | Trigger remote registration mode on the device. |
| `/api/set_time` | Synchronize device time and timezone. |
| `/api/restart` | Remotely reboot the attendance machine. |

## 📂 Folder Structure

```text
Task 2/
├── sample-code/          # Independent runnable sample codes
│   ├── auth/             # Authentication & Header setup
│   ├── devices/          # Device listing & status
│   ├── attendance/       # Retrieving scan logs
│   ├── users/            # CRUD & Registration operations
│   ├── sync/             # Machine maintenance (Time, Restart)
│   └── webhook/          # Real-time data processing template
│
├── mini-demo-app/        # Attendance Monitoring Dashboard
│   ├── index.php         # Main Dashboard UI & Controller
│   ├── config.php        # API Credentials Configuration
│   ├── functions.php     # Reusable API wrapper functions
│   └── assets/           # Dashboard Styles (CSS)
│
└── README.md             # This documentation
```

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- PHP cURL extension enabled
- A valid Fingerspot API Token and Cloud ID

### 1. Using Sample Codes
Each file in `sample-code/` is a self-contained script.
1. Open the script (e.g., `sample-code/devices/get-devices.php`).
2. Replace `YOUR_API_TOKEN_HERE` with your actual token.
3. Run via CLI: `php get-devices.php` or via browser.

### 2. Running the Mini Demo App
The dashboard provides a visual interface for the API features.
1. Configure your credentials in `mini-demo-app/config.php`.
2. Start a local server: `php -S localhost:8000 -t "mini-demo-app"`
3. Open `http://localhost:8000` in your browser.

## 🛠️ Implementation Details
- **Pure PHP & cURL**: No external frameworks or libraries were used.
- **Security**: Basic XSS protection using `htmlspecialchars()` and professional coding standards.
- **Asynchronous Logic**: Includes guidance on handling Webhooks for commands that the device processes asynchronously.

---
*Created as part of the Fingerspot Internship Project.*
