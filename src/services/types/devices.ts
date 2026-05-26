import type { PaginatedResponse } from "./common";

export type DeviceStatus = "online" | "offline" | "unknown";

export type Device = {
  id: string;
  /** Device serial number */
  sn: string;
  /** Human-readable device name */
  name: string;
  /** Fingerspot cloud ID this device belongs to */
  cloudId: string;
  /** Connection / operational status */
  status: DeviceStatus;
  /** Firmware or platform version string */
  firmware?: string;
  /** Last heartbeat / sync timestamp (ISO 8601) */
  lastUpdateAt: string | null;
};

export type DeviceStats = {
  total: number;
  online: number;
  offline: number;
  unknown: number;
};

export type DeviceListParams = {
  search?: string;
};

export type DeviceListResponse = PaginatedResponse<Device>;
