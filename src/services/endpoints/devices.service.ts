import { env } from "@/config/env";
import { API_PATHS } from "@/config/api-paths";

import { computeDeviceStats } from "../helpers/device-helpers";
import { generateTransId } from "../helpers/trans-id";
import { fingerspotClient } from "../fingerspot-client";
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
 * Devices service — POST /get_device + POST /restart_device integration.
 *
 * Source selection:
 *   - Live API: when NEXT_PUBLIC_FP_BASE_URL + NEXT_PUBLIC_FP_API_TOKEN are set.
 *   - Mock:     fallback for local development without credentials.
 *
 * The hook and UI are unaware of which source is active.
 * Bearer token auth is handled by the Fingerspot client interceptor.
 * cloud_id is read from the device record and never hardcoded.
 */
export const devicesService = {
  async list(params?: DeviceListParams): Promise<DeviceListResponse> {
    if (env.isFingerspotConfigured) {
      const cloudId = env.fingerspotCloudId ?? "";
      return fetchDevicesFromApi(cloudId, params);
    }
    return fetchDevicesFromMock(params);
  },

  async getStats(): Promise<ApiResponse<DeviceStats>> {
    // Fetch the full unfiltered list so stats always reflect the total fleet
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
   *
   * In mock mode (no Fingerspot credentials) the call is simulated so the
   * confirmation flow can be exercised locally without a live device.
   *
   * @param sn      - Device serial number
   * @param cloudId - Cloud ID the device belongs to (taken from the device record)
   */
  async restart(sn: string, cloudId: string): Promise<void> {
    if (!env.isFingerspotConfigured) {
      // Simulate network latency in mock mode
      await new Promise((resolve) => setTimeout(resolve, 800));
      return;
    }

    await fingerspotClient.post(devicesPaths.restartDevice, {
      trans_id: generateTransId(),
      cloud_id: cloudId.trim(),
      sn: sn.trim(),
    });
  },
};
