# Fingerspot API Integration - Task 2

This repository contains sample code and a mini demo application for integrating with the Fingerspot Cloud API.

## Features Implemented
- Authentication (Bearer Token)
- Device Management (List devices, Sync time, Restart)
- Attendance Management (Get logs, Real-time Webhook)
- User Management (Add user, Delete user, Get user info, Remote registration)

## Folder Structure
- `sample-code/`: Individual, runnable PHP scripts for each API feature.
  - `auth/`: Authentication headers setup.
  - `devices/`: Fetching registered devices.
  - `attendance/`: Retrieving attendance scan logs.
  - `users/`: CRUD operations and remote registration.
  - `sync/`: Device commands (Time sync, Restart).
  - `webhook/`: Handling real-time push data from Fingerspot.
- `mini-demo-app/`: A unified "Attendance Monitoring Dashboard" combining multiple features.

## Requirements
- PHP 7.4+
- PHP cURL extension
- Fingerspot API Token and Cloud ID (Serial Number)

## How to Run the Mini Demo App
1. Update `mini-demo-app/config.php` with your actual API credentials.
2. Start a local PHP server:
   ```bash
   php -S localhost:8000 -t Task\ 2/mini-demo-app
   ```
3. Open `http://localhost:8000` in your browser.

## Sample Code Usage
Each script in `sample-code/` is self-contained. Replace the placeholder credentials and run them via CLI:
```bash
php Task\ 2/sample-code/devices/get-devices.php
```
