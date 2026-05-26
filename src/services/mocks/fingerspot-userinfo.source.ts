import { fingerspotClient } from "../fingerspot-client";
import { generateTransId } from "../helpers/trans-id";
import { applyEmployeeListParams, buildEmployeeListMeta } from "../helpers/employee-helpers";
import type {
  Employee,
  EmployeeListParams,
  EmployeeListResponse,
  EmployeeStatus,
} from "../types/employees";

// ---------------------------------------------------------------------------
// Raw Fingerspot API shapes
// ---------------------------------------------------------------------------

/**
 * Raw user record as returned by POST /get_userinfo.
 * Field names follow the Fingerspot Cloud API convention.
 */
type FingerspotUserRecord = {
  /** Employee PIN / user ID registered on the device */
  pin: string;
  /** Full name of the employee */
  name?: string;
  /**
   * Privilege level:
   *   0 = Normal user
   *   1 = Enroller
   *   2 = Manager
   *   3 = Super admin
   *   14 = Device admin
   */
  privilege?: number | string;
  /**
   * Password (presence indicates device enrollment).
   * Not used for display — only checked for truthiness.
   */
  password?: string;
  /** Card number registered on the device (presence = card enrolled) */
  card?: string;
  /**
   * Verify mode bitmask:
   *   0 = Only fingerprint
   *   1 = Only password
   *   3 = Fingerprint or password
   *   etc.
   * Presence of a non-zero value indicates biometric enrollment.
   */
  verify?: number | string;
  /** Group ID the user belongs to */
  group?: string;
  /** Time zone string */
  tz?: string;
};

/**
 * Top-level response envelope from POST /get_userinfo.
 */
type FingerspotUserinfoResponse = {
  statusCode?: number;
  message?: string;
  /** Array of user records */
  data?: FingerspotUserRecord[];
};

// ---------------------------------------------------------------------------
// Normalization helpers
// ---------------------------------------------------------------------------

/**
 * Derive EmployeeStatus from Fingerspot privilege level.
 *
 * Fingerspot doesn't have an explicit active/inactive flag, so we infer:
 *   - privilege 0–3 with a name = active
 *   - no name or empty pin = pending (incomplete enrollment)
 *
 * This is a best-effort mapping; adjust if the API returns richer status data.
 */
function normalizeStatus(record: FingerspotUserRecord): EmployeeStatus {
  if (!record.name?.trim()) return "pending";
  return "active";
}

/**
 * A user is considered device-synced when they have at least one biometric
 * credential registered: fingerprint (verify != 0), card, or password.
 */
function normalizeDeviceSynced(record: FingerspotUserRecord): boolean {
  const hasCard = Boolean(record.card?.trim());
  const hasPassword = Boolean(record.password?.trim());
  const hasVerify = Number(record.verify ?? 0) !== 0;
  return hasCard || hasPassword || hasVerify;
}

/**
 * Map Fingerspot privilege level to a human-readable role label.
 */
function normalizeRole(privilege: number | string | undefined): string {
  const level = Number(privilege ?? 0);
  switch (level) {
    case 14:
      return "Device Admin";
    case 3:
      return "Super Admin";
    case 2:
      return "Manager";
    case 1:
      return "Enroller";
    default:
      return "Employee";
  }
}

/**
 * Normalize a raw Fingerspot user record into the internal Employee shape.
 *
 * Fields with no Fingerspot equivalent (department, lastSeenAt, createdAt)
 * receive safe defaults so the UI renders without errors.
 */
function normalizeRecord(record: FingerspotUserRecord): Employee {
  const pin = record.pin?.trim() ?? "";

  return {
    id: `fp-${pin}`,
    employeeId: pin,
    name: record.name?.trim() || `User ${pin}`,
    department: record.group?.trim() || "—",
    role: normalizeRole(record.privilege),
    status: normalizeStatus(record),
    deviceSynced: normalizeDeviceSynced(record),
    lastSeenAt: null,   // not provided by /get_userinfo
    createdAt: new Date().toISOString(), // not provided; use fetch time as fallback
  };
}

// ---------------------------------------------------------------------------
// Public source function
// ---------------------------------------------------------------------------

/**
 * Fetches employee records from the live Fingerspot POST /get_userinfo endpoint.
 *
 * Posts with a unique trans_id and the configured cloud_id, normalises the
 * raw response into the internal Employee shape, applies any list params
 * (search / status filter), and returns an EmployeeListResponse.
 *
 * Throws on network or API errors — the service layer and hook handle
 * error propagation to the UI.
 */
export async function fetchUsersFromApi(
  cloudId: string,
  params?: EmployeeListParams
): Promise<EmployeeListResponse> {
  const transId = generateTransId();

  const response = await fingerspotClient.post<FingerspotUserinfoResponse>(
    "/get_userinfo",
    {
      trans_id: transId,
      cloud_id: cloudId.trim(),
    }
  );

  const raw = response.data;
  const records: FingerspotUserRecord[] = Array.isArray(raw?.data)
    ? raw.data
    : [];

  const allEmployees = records.map(normalizeRecord);

  // Apply search / status filtering and pagination via the shared helper
  const filtered = applyEmployeeListParams(allEmployees, params);

  return {
    data: filtered,
    meta: buildEmployeeListMeta(filtered.length, params),
  };
}
