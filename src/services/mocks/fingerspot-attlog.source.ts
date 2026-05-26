import { fingerspotClient } from "../fingerspot-client";
import { validateAttlogRequest } from "../helpers/attendance-helpers";
import { generateTransId } from "../helpers/trans-id";
import type {
  AttendanceLog,
  AttlogListResponse,
  AttlogRequestParams,
} from "../types/attendance";

// ---------------------------------------------------------------------------
// Raw Fingerspot API shapes
// ---------------------------------------------------------------------------

/**
 * Raw attendance record as returned by POST /get_attlog.
 * Field names follow the Fingerspot Cloud API convention.
 */
type FingerspotAttlogRecord = {
  /** Employee PIN / user ID on the device */
  pin: string;
  /** Timestamp of the attendance event, e.g. "2024-05-25 08:02:00" */
  check_time: string;
  /** Verification method code: 1=Fingerprint, 4=Card, 15=Face, etc. */
  verify_type: number | string;
  /** Work-code / status code: 0=check-in, 1=check-out, etc. */
  status_code?: number | string;
  /** Device serial number */
  sn?: string;
  /** Employee name (may be absent in some firmware versions) */
  name?: string;
};

/**
 * Top-level response envelope from POST /get_attlog.
 * The API wraps records inside a `data` array.
 */
type FingerspotAttlogResponse = {
  /** HTTP-level status code echoed in the body */
  statusCode?: number;
  /** Human-readable message */
  message?: string;
  /** Attendance records */
  data?: FingerspotAttlogRecord[];
};

// ---------------------------------------------------------------------------
// Normalization helpers
// ---------------------------------------------------------------------------

const VERIFY_TYPE_MAP: Record<string, string> = {
  "0": "Password",
  "1": "Fingerprint",
  "2": "Card",
  "3": "Password+Fingerprint",
  "4": "Card",
  "6": "Face",
  "10": "Face",
  "15": "Face",
};

function normalizeVerifyMode(raw: number | string | undefined): string {
  if (raw === undefined || raw === null) return "Unknown";
  const key = String(raw);
  return VERIFY_TYPE_MAP[key] ?? `Mode ${key}`;
}

/**
 * Fingerspot status_code: 0 = check-in, 1 = check-out.
 * Anything else is treated as check-in (most common default).
 */
function normalizeStatus(
  raw: number | string | undefined
): "check-in" | "check-out" {
  const code = Number(raw);
  return code === 1 ? "check-out" : "check-in";
}

/**
 * Normalise a raw Fingerspot record into the internal AttendanceLog shape.
 * Generates a stable synthetic `id` from pin + check_time.
 */
function normalizeRecord(
  record: FingerspotAttlogRecord,
  transId: string,
  cloudId: string,
  index: number
): AttendanceLog {
  const checkTime = record.check_time
    ? // Fingerspot returns "YYYY-MM-DD HH:mm:ss" — convert to ISO 8601
      new Date(record.check_time.replace(" ", "T")).toISOString()
    : new Date().toISOString();

  return {
    id: `${transId}-${record.pin}-${index}`,
    trans_id: transId,
    cloud_id: cloudId,
    pin: record.pin ?? "",
    employee_name: record.name ?? record.pin ?? "Unknown",
    verify_mode: normalizeVerifyMode(record.verify_type),
    check_time: checkTime,
    device_sn: record.sn ?? "Unknown",
    status: normalizeStatus(record.status_code),
  };
}

// ---------------------------------------------------------------------------
// Public source function
// ---------------------------------------------------------------------------

/**
 * Fetches attendance logs from the live Fingerspot POST /get_attlog endpoint.
 *
 * Validates the request, posts to the API, normalises the raw response into
 * the internal AttendanceLog shape, and returns an AttlogListResponse.
 *
 * Throws on validation failure or network/API errors — the service layer
 * and hook handle error propagation to the UI.
 */
export async function fetchAttlogFromApi(
  params: AttlogRequestParams
): Promise<AttlogListResponse> {
  const validation = validateAttlogRequest(params);
  if (!validation.valid) {
    throw new Error(validation.message);
  }

  // Generate a unique transaction ID for this request
  const transId = generateTransId();

  const response = await fingerspotClient.post<FingerspotAttlogResponse>(
    "/get_attlog",
    {
      trans_id: transId,
      cloud_id: params.cloud_id.trim(),
      start_date: params.start_date,
      end_date: params.end_date,
    }
  );

  const raw = response.data;
  const records: FingerspotAttlogRecord[] = Array.isArray(raw?.data)
    ? raw.data
    : [];

  const data = records.map((record, index) =>
    normalizeRecord(record, transId, params.cloud_id.trim(), index)
  );

  return {
    data,
    meta: {
      trans_id: transId,
      cloud_id: params.cloud_id.trim(),
      start_date: params.start_date,
      end_date: params.end_date,
      total: data.length,
    },
  };
}
