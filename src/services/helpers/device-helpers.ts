import type {
  Device,
  DeviceListParams,
  DeviceStats,
} from "../types/devices";

export function applyDeviceListParams(
  devices: Device[],
  params?: DeviceListParams
): Device[] {
  const search = params?.search?.trim().toLowerCase();
  if (!search) return devices;

  return devices.filter(
    (device) =>
      device.name.toLowerCase().includes(search) ||
      device.sn.toLowerCase().includes(search) ||
      device.cloudId.toLowerCase().includes(search)
  );
}

export function buildDeviceListMeta(total: number, params?: DeviceListParams) {
  return {
    page: 1,
    limit: total || 1,
    total,
    totalPages: 1,
  };
}

export function computeDeviceStats(devices: Device[]): DeviceStats {
  return devices.reduce<DeviceStats>(
    (stats, device) => {
      stats.total += 1;
      if (device.status === "online") stats.online += 1;
      else if (device.status === "offline") stats.offline += 1;
      else stats.unknown += 1;
      return stats;
    },
    { total: 0, online: 0, offline: 0, unknown: 0 }
  );
}
