import { API_PATHS } from "@/config/api-paths";

import { computeAttendanceStats } from "../helpers/attendance-helpers";
import { fetchAttlogFromMock } from "../mocks/attendance.source";
import type { ApiResponse } from "../types/common";
import type {
  AttendanceStats,
  AttlogListResponse,
  AttlogRequestParams,
} from "../types/attendance";

export const attendancePaths = API_PATHS.attendance;

/**
 * Fingerspot attendance module — first /get_attlog integration target.
 *
 * Mock source is active until live API is wired.
 * Future: apiPost<AttlogListResponse>(attendancePaths.getAttlog, params)
 * Bearer token is attached automatically via API client interceptors.
 */
export const attendanceService = {
  async getAttlog(params: AttlogRequestParams): Promise<AttlogListResponse> {
    return fetchAttlogFromMock(params);
  },

  async getStats(
    params: AttlogRequestParams
  ): Promise<ApiResponse<AttendanceStats>> {
    const { data } = await fetchAttlogFromMock(params);
    return { data: computeAttendanceStats(data) };
  },
};
