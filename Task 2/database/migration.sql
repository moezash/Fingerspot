-- ============================================================
-- Fingerspot API Integration - Database Migration
-- Created for Task 2: Integrasi API & Webhook developer.fingerspot.io
-- ============================================================

-- Create database
CREATE DATABASE IF NOT EXISTS fingerspot_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fingerspot_app;

-- ============================================================
-- Tabel: attlogs
-- Menyimpan data absensi / scan log dari mesin
-- ============================================================
CREATE TABLE IF NOT EXISTS attlogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cloud_id VARCHAR(50) NOT NULL COMMENT 'ID mesin absensi',
    pin VARCHAR(50) NOT NULL COMMENT 'User ID / PIN karyawan',
    scan_time DATETIME NOT NULL COMMENT 'Waktu scan absensi',
    verify INT DEFAULT 0 COMMENT 'Metode verifikasi (0=finger, 1=password, dll)',
    status_scan INT DEFAULT 0 COMMENT 'Status scan (0=check-in, 1=check-out, dll)',
    raw_payload TEXT NULL COMMENT 'Raw JSON payload dari webhook',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pin (pin),
    INDEX idx_cloud_id (cloud_id),
    INDEX idx_scan_time (scan_time),
    -- Natural identity for idempotent attendance inserts (get_attlog + realtime webhook)
    UNIQUE KEY uniq_attlog_scan (cloud_id, pin, scan_time, verify, status_scan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: userinfos
-- Menyimpan data user / karyawan dari mesin
-- ============================================================
CREATE TABLE IF NOT EXISTS userinfos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cloud_id VARCHAR(50) NOT NULL COMMENT 'ID mesin absensi',
    pin VARCHAR(50) NOT NULL COMMENT 'User ID / PIN karyawan',
    name VARCHAR(100) NULL COMMENT 'Nama karyawan',
    privilege VARCHAR(10) DEFAULT '0' COMMENT 'Hak akses (0=user, 14=admin)',
    finger INT DEFAULT 0 COMMENT 'Jumlah sidik jari terdaftar',
    face INT DEFAULT 0 COMMENT 'Jumlah wajah terdaftar',
    password VARCHAR(50) NULL COMMENT 'Password user di mesin',
    rfid VARCHAR(50) NULL COMMENT 'Nomor kartu RFID',
    raw_payload TEXT NULL COMMENT 'Raw JSON payload dari webhook',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pin (pin),
    INDEX idx_cloud_id (cloud_id),
    UNIQUE KEY unique_user (cloud_id, pin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: pins
-- Menyimpan data PIN / User ID dari mesin (hasil Get All PIN)
-- ============================================================
CREATE TABLE IF NOT EXISTS pins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cloud_id VARCHAR(50) NOT NULL COMMENT 'ID mesin absensi',
    pin VARCHAR(50) NOT NULL COMMENT 'User ID / PIN',
    raw_payload TEXT NULL COMMENT 'Raw JSON payload',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_cloud_id (cloud_id),
    INDEX idx_pin (pin),
    UNIQUE KEY uniq_cloud_pin (cloud_id, pin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: api_requests
-- Log semua request API yang dikirim ke developer.fingerspot.io
-- ============================================================
CREATE TABLE IF NOT EXISTS api_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    endpoint VARCHAR(100) NOT NULL COMMENT 'API endpoint yang dipanggil',
    method VARCHAR(10) DEFAULT 'POST' COMMENT 'HTTP method',
    cloud_id VARCHAR(50) NULL COMMENT 'Target cloud ID',
    trans_id VARCHAR(50) NULL COMMENT 'Transaction ID untuk tracking',
    request_payload TEXT NULL COMMENT 'Raw request body (JSON)',
    response_payload TEXT NULL COMMENT 'Raw response body (JSON)',
    http_status INT NULL COMMENT 'HTTP response status code',
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending' COMMENT 'Status proses',
    error_message TEXT NULL COMMENT 'Pesan error jika gagal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_endpoint (endpoint),
    INDEX idx_status (status),
    INDEX idx_trans_id (trans_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: webhook_responses
-- Log semua webhook yang diterima dari developer.fingerspot.io
-- ============================================================
CREATE TABLE IF NOT EXISTS webhook_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NULL COMMENT 'Tipe webhook (attlog, get_userinfo, dll)',
    cloud_id VARCHAR(50) NULL COMMENT 'Cloud ID mesin pengirim',
    trans_id VARCHAR(50) NULL COMMENT 'Transaction ID terkait',
    raw_payload TEXT NOT NULL COMMENT 'Raw JSON payload yang diterima',
    status ENUM('received', 'processed', 'failed') DEFAULT 'received' COMMENT 'Status pemrosesan',
    error_message TEXT NULL COMMENT 'Pesan error jika gagal diproses',
    received_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL COMMENT 'Waktu selesai diproses',
    INDEX idx_type (type),
    INDEX idx_cloud_id (cloud_id),
    INDEX idx_trans_id (trans_id),
    INDEX idx_status (status),
    INDEX idx_received_at (received_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabel: command_logs
-- Log proses command / riwayat operasi (Register Online, Restart, dll)
-- ============================================================
CREATE TABLE IF NOT EXISTS command_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    command_type VARCHAR(50) NOT NULL COMMENT 'Tipe command (set_userinfo, delete_userinfo, set_time, restart, reg_online, get_allpin)',
    cloud_id VARCHAR(50) NOT NULL COMMENT 'Target cloud ID',
    trans_id VARCHAR(50) NOT NULL COMMENT 'Transaction ID untuk tracking',
    pin VARCHAR(50) NULL COMMENT 'PIN terkait (jika ada)',
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending' COMMENT 'Status proses',
    request_payload TEXT NULL COMMENT 'Raw request payload',
    response_payload TEXT NULL COMMENT 'Raw response dari webhook',
    notes TEXT NULL COMMENT 'Catatan tambahan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_command_type (command_type),
    INDEX idx_cloud_id (cloud_id),
    INDEX idx_trans_id (trans_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    -- Supports webhook matching: pending row by trans_id (+ cloud_id / type filters)
    INDEX idx_cmd_pending_match (trans_id, status, command_type, cloud_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Notes (FIX TAHAP 4)
-- ============================================================
-- * Attendance uniqueness: uniq_attlog_scan prevents duplicate identical scans
--   from get_attlog pull + realtime attlog webhook + re-pull of same range.
-- * Pins uniqueness: uniq_cloud_pin prevents duplicate PIN per device.
-- * command_logs ↔ api_requests are linked by shared trans_id (same generator).
--   Adding api_request_id FK is deferred as a future improvement (no schema
--   expansion in this stage).
-- * For already-deployed databases, run: database/migration_tahap4.sql
--   (cleans identical duplicates first, then adds constraints/indexes).
