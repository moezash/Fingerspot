import type { DeviceListParams, DeviceListResponse } from "../types/devices";
import { applyDeviceListParams, buildDeviceListMeta } from "../helpers/device-helpers";
import { devicesMock } from "./devices.mock";

const MOCK_NETWORK_DELAY_MS = 600;

function delay(ms: number): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

/**
 * Temporary mock source for /get_device.
 * Replace with fetchDevicesFromApi in devices.service when API is enabled.
 */
export async function fetchDevicesFromMock(
  params?: DeviceListParams
): Promise<DeviceListResponse> {
  await delay(MOCK_NETWORK_DELAY_MS);

  const data = applyDeviceListParams(devicesMock, params);

  return {
    data,
    meta: buildDeviceListMeta(data.length, params),
  };
}
