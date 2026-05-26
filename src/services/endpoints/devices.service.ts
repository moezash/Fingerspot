import { env } from "@/config/env";
import { API_PATHS } from "@/config/api-paths";

import { computeDeviceStats } from "../helpers/device-helpers";
import { generateTransId } from "../helpers/trans-id";
import { fingerspotClient } from "../fingerspot-client";
import { getSelectedCloudId } from "../config/device-config";
import { fetchDevicesFromApi } from "../mocks/fingerspot-device.source";
import { fetchDevicesFromMock } from "../mocks/devices.source";
import type { ApiResponse } from "../types/common";
import type {
  Device,
  DeviceListParams,
  DeviceListResponse,
  DeviceStats,
} from "../types/devices";

export const devicesPaths = API_PATHS.devices;

/**
 * Devices service — POST /get_device + POST /restart_device + POST /set_time.
 *
 * Source selection:
 *   - Live API: when NEXT_PUBLIC_FP_BASE_URL + NEXT_PUBLIC_FP_API_TOKEN are set.
 *   - Mock:     fallback for local development without credentials.
 *
 * cloud_id for list operations comes from the selected device context
 * (device-config.ts). For device actions (restart/syncTime), cloud_id
 * comes from the Device record itself — never hardcoded.
 */
export const devicesService = {
  async list(params?: DeviceListParams): Promise<DeviceListResponse> {
    if (env.isFingerspotConfigured) {
      const cloudId = getSelectedCloudId() ?? "";
      return fetchDevicesFromApi(cloudId, params);
    }
    return fetchDevicesFromMock(params);
  },

  async getStats(): Promise<ApiResponse<DeviceStats>> {
    const { data } = await devicesService.list();
    return { data: computeDeviceStats(data) };
  },

  async getById(id: string): Promise<ApiResponse<Device>> {
    const { data } = await devicesService.list();
    const device = data.find((item) => item.id === id);

    if (!device) {
      throw new Error(`Device not found: ${id}`);
    }

    return { data: device };
  },

  /**
   * Sends a restart command to the specified device via POST /restart_device.
   * cloud_id comes from the Device record — not from global config.
   */
  async restart(sn: string, cloudId: string): Promise<void> {
    if (!env.isFingerspotConfigured) {
      await new Promise((resolve) => setTimeout(resolve, 800));
      return;
    }

    await fingerspotClient.post(devicesPaths.restartDevice, {
      trans_id: generateTransId(),
      cloud_id: cloudId.trim(),
      sn: sn.trim(),
    });
  },

  /**
   * Synchronises the current system time to the specified device via
   * POST /set_time. cloud_id comes from the Device record.
   */
  async syncTime(sn: string, cloudId: string): Promise<void> {
    if (!env.isFingerspotConfigured) {
      await new Promise((resolve) => setTimeout(resolve, 800));
      return;
    }

    const now = new Date();
    const pad = (n: number) => String(n).padStart(2, "0");
    const datetime =
      `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ` +
      `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

    await fingerspotClient.post(devicesPaths.setTime, {
      trans_id: generateTransId(),
      cloud_id: cloudId.trim(),
      sn: sn.trim(),
      datetime,
    });
  },
};
