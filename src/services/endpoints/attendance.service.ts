import { env } from "@/config/env";
import { API_PATHS } from "@/config/api-paths";

import { computeAttendanceStats } from "../helpers/attendance-helpers";
import { fetchAttlogFromApi } from "../mocks/fingerspot-attlog.source";
import { fetchAttlogFromMock } from "../mocks/attendance.source";
import type { ApiResponse } from "../types/common";
import type {
  AttendanceStats,
  AttlogListResponse,
  AttlogRequestParams,
} from "../types/attendance";

export const attendancePaths = API_PATHS.attendance;

/**
 * Attendance service — POST /get_attlog integration.
 *
 * Source selection:
 *   - Live API: when NEXT_PUBLIC_FP_BASE_URL + NEXT_PUBLIC_FP_API_TOKEN are set.
 *   - Mock:     fallback for local development without credentials.
 *
 * The hook and UI are unaware of which source is active.
 * Bearer token auth is handled by the Fingerspot client interceptor.
 */
export const attendanceService = {
  async getAttlog(params: AttlogRequestParams): Promise<AttlogListResponse> {
    if (env.isFingerspotConfigured) {
      return fetchAttlogFromApi(params);
    }
    return fetchAttlogFromMock(params);
  },

  async getStats(
    params: AttlogRequestParams
  ): Promise<ApiResponse<AttendanceStats>> {
    const { data } = await attendanceService.getAttlog(params);
    return { data: computeAttendanceStats(data) };
  },
};
