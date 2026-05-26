import { fingerspotClient } from "../fingerspot-client";
import { generateTransId } from "../helpers/trans-id";
import { applyDeviceListParams, buildDeviceListMeta } from "../helpers/device-helpers";
import type {
  Device,
  DeviceListParams,
  DeviceListResponse,
  DeviceStatus,
} from "../types/devices";

// ---------------------------------------------------------------------------
// Raw Fingerspot API shapes
// ---------------------------------------------------------------------------

/**
 * Raw device record as returned by POST /get_device.
 * Field names follow the Fingerspot Cloud API convention.
 */
type FingerspotDeviceRecord = {
  /** Device serial number */
  sn?: string;
  /** Human-readable device alias / name */
  alias?: string;
  /**
   * Online status flag.
   * Fingerspot typically returns 1 = online, 0 = offline.
   */
  status?: number | string;
  /** Firmware version string */
  firmware_ver?: string;
  /** Last heartbeat timestamp, e.g. "2024-05-27 07:45:00" */
  last_activity?: string;
  /** Cloud ID the device is registered under */
  cloud_id?: string;
};

/**
 * Top-level response envelope from POST /get_device.
 */
type FingerspotDeviceResponse = {
  statusCode?: number;
  message?: string;
  data?: FingerspotDeviceRecord[];
};

// ---------------------------------------------------------------------------
// Normalization helpers
// ---------------------------------------------------------------------------

function normalizeStatus(raw: number | string | undefined): DeviceStatus {
  const value = Number(raw ?? -1);
  if (value === 1) return "online";
  if (value === 0) return "offline";
  return "unknown";
}

/**
 * Fingerspot returns timestamps as "YYYY-MM-DD HH:mm:ss".
 * Convert to ISO 8601 for consistent handling in the UI.
 */
function normalizeTimestamp(raw: string | undefined): string | null {
  if (!raw?.trim()) return null;
  const iso = raw.trim().replace(" ", "T");
  const date = new Date(iso);
  return Number.isNaN(date.getTime()) ? null : date.toISOString();
}

/**
 * Normalize a raw Fingerspot device record into the internal Device shape.
 */
function normalizeRecord(
  record: FingerspotDeviceRecord,
  cloudId: string,
  index: number
): Device {
  const sn = record.sn?.trim() ?? `unknown-${index}`;

  return {
    id: `fp-dev-${sn}`,
    sn,
    name: record.alias?.trim() || sn,
    cloudId: record.cloud_id?.trim() || cloudId,
    status: normalizeStatus(record.status),
    firmware: record.firmware_ver?.trim() || undefined,
    lastUpdateAt: normalizeTimestamp(record.last_activity),
  };
}

// ---------------------------------------------------------------------------
// Public source function
// ---------------------------------------------------------------------------

/**
 * Fetches device records from the live Fingerspot POST /get_device endpoint.
 *
 * Posts with a unique trans_id and the configured cloud_id, normalises the
 * raw response into the internal Device shape, applies any list params
 * (search filter), and returns a DeviceListResponse.
 *
 * Throws on network or API errors — the service layer and hook handle
 * error propagation to the UI.
 */
export async function fetchDevicesFromApi(
  cloudId: string,
  params?: DeviceListParams
): Promise<DeviceListResponse> {
  const transId = generateTransId();

  const response = await fingerspotClient.post<FingerspotDeviceResponse>(
    "/get_device",
    {
      trans_id: transId,
      cloud_id: cloudId.trim(),
    }
  );

  const raw = response.data;
  const records: FingerspotDeviceRecord[] = Array.isArray(raw?.data)
    ? raw.data
    : [];

  const allDevices = records.map((record, index) =>
    normalizeRecord(record, cloudId.trim(), index)
  );

  const filtered = applyDeviceListParams(allDevices, params);

  return {
    data: filtered,
    meta: buildDeviceListMeta(filtered.length, params),
  };
}
