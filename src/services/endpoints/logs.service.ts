import { env } from "@/config/env";
import { API_PATHS } from "@/config/api-paths";

import { toDateInputValue } from "../helpers/attendance-helpers";
import { attendanceService } from "./attendance.service";
import type { AttlogListResponse } from "../types/attendance";

export const logsPaths = API_PATHS.logs;

/**
 * Logs service — surfaces the most recent attendance events as a read-only
 * event stream, without exposing date-range or trans_id controls to the UI.
 *
 * Delegates entirely to attendanceService.getAttlog so all source routing
 * (live API vs mock) and Bearer auth are handled in one place.
 *
 * Date range: today only (satisfies the ≤2-day API constraint).
 * cloud_id:   NEXT_PUBLIC_FP_CLOUD_ID when configured, demo ID otherwise.
 */
export const logsService = {
  async getLogs(search?: string): Promise<AttlogListResponse> {
    const today = toDateInputValue(new Date());

    const params = {
      trans_id: env.isFingerspotConfigured ? "auto" : "DEMO-TRANS-001",
      cloud_id: env.fingerspotCloudId ?? "DEMO-CLOUD-001",
      start_date: today,
      end_date: today,
    };

    const response = await attendanceService.getAttlog(params);

    // Apply search filter at the service boundary so the hook stays simple
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
