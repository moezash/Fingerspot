import { API_PATHS } from "@/config/api-paths";

import { notImplemented } from "../helpers/not-implemented";
import type { ApiResponse } from "../types/common";
import type {
  Device,
  DeviceListParams,
  DeviceListResponse,
} from "../types/devices";

export const devicesPaths = API_PATHS.devices;

export const devicesService = {
  list(_params?: DeviceListParams): Promise<DeviceListResponse> {
    return notImplemented(`${devicesPaths.root} GET`);
  },

  getById(_id: string): Promise<ApiResponse<Device>> {
    return notImplemented(`${devicesPaths.byId(":id")} GET`);
  },
};
