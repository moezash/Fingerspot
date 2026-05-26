import { env } from "@/config/env";
import { API_PATHS } from "@/config/api-paths";

import { toDateInputValue } from "../helpers/attendance-helpers";
import { getSelectedCloudId } from "../config/device-config";
import { attendanceService } from "./attendance.service";
import type { AttlogListResponse } from "../types/attendance";

export const logsPaths = API_PATHS.logs;

/**
 * Logs service — surfaces today's attendance events as a read-only stream.
 *
 * cloud_id comes from the selected device context (device-config.ts).
 * Falls back to the demo ID so mock mode works without any device selected.
 */
export const logsService = {
  async getLogs(search?: string): Promise<AttlogListResponse> {
    const today = toDateInputValue(new Date());

    const params = {
      trans_id: env.isFingerspotConfigured ? "auto" : "DEMO-TRANS-001",
      cloud_id: getSelectedCloudId() ?? "DEMO-CLOUD-001",
      start_date: today,
      end_date: today,
    };

    const response = await attendanceService.getAttlog(params);

    if (!search?.trim()) return response;

    const query = search.trim().toLowerCase();
    const filtered = response.data.filter(
      (log) =>
        log.employee_name.toLowerCase().includes(query) ||
        log.pin.toLowerCase().includes(query) ||
        log.device_sn.toLowerCase().includes(query) ||
        log.verify_mode.toLowerCase().includes(query)
    );

    return {
      ...response,
      data: filtered,
      meta: { ...response.meta, total: filtered.length },
    };
  },
};
