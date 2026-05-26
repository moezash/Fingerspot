import { env } from "@/config/env";
import { API_PATHS } from "@/config/api-paths";

import { toDateInputValue } from "../helpers/attendance-helpers";
import { getSelectedCloudId } from "../config/device-config";
import { attendanceService } from "./attendance.service";
import { devicesService } from "./devices.service";
import { employeesService } from "./employees.service";
import type { ApiResponse } from "../types/common";
import type { DashboardOverview } from "../types/dashboard";

export const dashboardPaths = API_PATHS.dashboard;

/**
 * Builds AttlogRequestParams for today's attendance.
 * cloud_id comes from the selected device context, not a global env var.
 */
function getTodayAttlogParams() {
  const today = toDateInputValue(new Date());
  return {
    trans_id: env.isFingerspotConfigured ? "auto" : "DEMO-TRANS-001",
    cloud_id: getSelectedCloudId() ?? "DEMO-CLOUD-001",
    start_date: today,
    end_date: today,
  };
}

/**
 * Dashboard service — aggregates data from the three integrated modules.
 *
 * Uses Promise.allSettled so a failure in one module does not block the
 * others. Each slice degrades gracefully to zero / empty defaults.
 */
export const dashboardService = {
  async getOverview(): Promise<ApiResponse<DashboardOverview>> {
    const attlogParams = getTodayAttlogParams();

    const [employeeStatsResult, deviceListResult, attlogResult] =
      await Promise.allSettled([
        employeesService.getStats(),
        devicesService.list(),
        attendanceService.getAttlog(attlogParams),
      ]);

    // --- Employees ---
    const employeeStats =
      employeeStatsResult.status === "fulfilled"
        ? employeeStatsResult.value.data
        : null;

    // --- Devices ---
    const devices =
      deviceListResult.status === "fulfilled"
        ? deviceListResult.value.data
        : [];

    const onlineDevices = devices.filter((d) => d.status === "online").length;

    // --- Attendance ---
    const attlogs =
      attlogResult.status === "fulfilled" ? attlogResult.value.data : [];

    const checkInsToday = attlogs.filter((l) => l.status === "check-in").length;

    // Most recent 5 logs sorted by check_time descending
    const recentActivity = [...attlogs]
      .sort(
        (a, b) =>
          new Date(b.check_time).getTime() - new Date(a.check_time).getTime()
      )
      .slice(0, 5);

    const overview: DashboardOverview = {
      totalEmployees: employeeStats?.total ?? 0,
      activeEmployees: employeeStats?.active ?? 0,
      totalDevices: devices.length,
      onlineDevices,
      attendanceToday: attlogs.length,
      checkInsToday,
      recentActivity,
      devices,
    };

    return { data: overview };
  },
};
