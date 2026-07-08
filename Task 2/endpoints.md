# Fingerspot Cloud API Endpoints Reference

This document lists the main endpoints available in the Fingerspot Cloud API as per the official documentation at https://developer.fingerspot.io.

## Base URL
`https://developer.fingerspot.io`

## Authentication
All requests must include the following headers:
- `Authorization: Bearer [YOUR_API_TOKEN]`
- `Content-Type: application/json`
- `Accept: application/json`

---

## 1. Device Management

### Get Device List (Synchronous)
- **Endpoint**: `/api/get_device`
- **Method**: POST
- **Description**: Retrieves a list of all registered devices in your account.
- **Parameters**: `trans_id`

### Set Device Time (Asynchronous)
- **Endpoint**: `/api/set_time`
- **Method**: POST
- **Description**: Synchronizes the time and timezone of the device.
- **Parameters**: `trans_id`, `cloud_id`, `timezone`

### Restart Device (Asynchronous)
- **Endpoint**: `/api/restart`
- **Method**: POST
- **Description**: Reboots the device remotely.
- **Parameters**: `trans_id`, `cloud_id`

---

## 2. Attendance Data

### Get Attendance Logs (Synchronous)
- **Endpoint**: `/api/get_attlog`
- **Method**: POST
- **Description**: Retrieves historical scan logs from the server for a specific device.
- **Parameters**: `trans_id`, `cloud_id`, `start_date`, `end_date`

### Real-time Scan Webhook
- **Method**: POST (sent from Fingerspot to your server)
- **Description**: Spontaneous push notification whenever a user scans at the device.

---

## 3. User Management

### Get User Info (Asynchronous)
- **Endpoint**: `/api/get_userinfo`
- **Method**: POST
- **Description**: Requests specific user details (name, templates) from the device. Result sent via Webhook.
- **Parameters**: `trans_id`, `cloud_id`, `pin`

### Set User Info (Asynchronous)
- **Endpoint**: `/api/set_userinfo`
- **Method**: POST
- **Description**: Uploads user details and templates to the device.
- **Parameters**: `trans_id`, `cloud_id`, `pin`, `name`, `privilege`, `password`, `card`, `template`

### Register Online (Asynchronous)
- **Endpoint**: `/api/reg_online`
- **Method**: POST
- **Description**: Triggers the device to enter registration mode for a specific user.
- **Parameters**: `trans_id`, `cloud_id`, `pin`, `type_data` (0: Fingerprint, 1: Face, 2: Card, 3: Password)

### Delete User Info (Asynchronous)
- **Endpoint**: `/api/delete_userinfo`
- **Method**: POST
- **Description**: Removes a user from the device.
- **Parameters**: `trans_id`, `cloud_id`, `pin`

### Get All PINs (Asynchronous)
- **Endpoint**: `/api/get_userid_list`
- **Method**: POST
- **Description**: Requests a list of all registered user IDs (PINs) from the device.
- **Parameters**: `trans_id`, `cloud_id`
