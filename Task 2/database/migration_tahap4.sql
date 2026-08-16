-- ============================================================
-- FIX TAHAP 4 — Safe incremental migration for existing DBs
-- ============================================================
-- Changes:
--   1. Remove identical duplicate attlogs (keep lowest id)
--   2. Add UNIQUE attlogs (cloud_id, pin, scan_time, verify, status_scan)
--   3. Remove identical duplicate pins (keep lowest id)
--   4. Add UNIQUE pins (cloud_id, pin)
--   5. Add composite index for command_logs webhook matching
--
-- Safe to re-run: uses IF NOT EXISTS / information_schema guards where needed.
-- Does NOT drop tables or truncate data.
-- ============================================================

USE fingerspot_app;

-- ------------------------------------------------------------
-- 1) Cleanup identical duplicate attendance rows
-- Natural identity: cloud_id + pin + scan_time + verify + status_scan
-- Keeps the earliest row (MIN(id)); deletes only exact duplicates.
-- ------------------------------------------------------------
DELETE a FROM attlogs a
INNER JOIN attlogs b
  ON a.cloud_id = b.cloud_id
 AND a.pin = b.pin
 AND a.scan_time = b.scan_time
 AND a.verify = b.verify
 AND a.status_scan = b.status_scan
 AND a.id > b.id;

-- ------------------------------------------------------------
-- 2) Unique constraint for attendance dedupe
-- ------------------------------------------------------------
SET @idx_exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'attlogs'
      AND INDEX_NAME = 'uniq_attlog_scan'
);
SET @sql := IF(
    @idx_exists = 0,
    'ALTER TABLE attlogs ADD UNIQUE KEY uniq_attlog_scan (cloud_id, pin, scan_time, verify, status_scan)',
    'SELECT ''uniq_attlog_scan already exists'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ------------------------------------------------------------
-- 3) Cleanup identical duplicate PIN rows
-- ------------------------------------------------------------
DELETE a FROM pins a
INNER JOIN pins b
  ON a.cloud_id = b.cloud_id
 AND a.pin = b.pin
 AND a.id > b.id;

-- ------------------------------------------------------------
-- 4) Unique constraint for pins per device
-- ------------------------------------------------------------
SET @idx_exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'pins'
      AND INDEX_NAME = 'uniq_cloud_pin'
);
SET @sql := IF(
    @idx_exists = 0,
    'ALTER TABLE pins ADD UNIQUE KEY uniq_cloud_pin (cloud_id, pin)',
    'SELECT ''uniq_cloud_pin already exists'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ------------------------------------------------------------
-- 5) Composite index for async command matching (idempotent add)
-- ------------------------------------------------------------
SET @idx_exists := (
    SELECT COUNT(*) FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'command_logs'
      AND INDEX_NAME = 'idx_cmd_pending_match'
);
SET @sql := IF(
    @idx_exists = 0,
    'ALTER TABLE command_logs ADD INDEX idx_cmd_pending_match (trans_id, status, command_type, cloud_id)',
    'SELECT ''idx_cmd_pending_match already exists'' AS info'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Deferred (future improvement, not applied here):
--   ALTER TABLE command_logs ADD COLUMN api_request_id INT NULL;
--   FK command_logs.api_request_id → api_requests.id
-- Current linkage remains shared trans_id generated once per request lifecycle.
