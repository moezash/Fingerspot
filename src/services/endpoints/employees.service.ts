import { env } from "@/config/env";
import { API_PATHS } from "@/config/api-paths";

import { computeEmployeeStats } from "../helpers/employee-helpers";
import { fetchUsersFromApi } from "../mocks/fingerspot-userinfo.source";
import { fetchEmployeesFromMock } from "../mocks/employees.source";
import type { ApiResponse } from "../types/common";
import type {
  Employee,
  EmployeeListParams,
  EmployeeListResponse,
  EmployeeStats,
} from "../types/employees";

export const employeesPaths = API_PATHS.employees;

/**
 * Employees service — POST /get_userinfo integration.
 *
 * Source selection:
 *   - Live API: when NEXT_PUBLIC_FP_BASE_URL + NEXT_PUBLIC_FP_API_TOKEN are set.
 *   - Mock:     fallback for local development without credentials.
 *
 * The hook and UI are unaware of which source is active.
 * Bearer token auth is handled by the Fingerspot client interceptor.
 * cloud_id is read from NEXT_PUBLIC_FP_CLOUD_ID and never hardcoded.
 */
export const employeesService = {
  async list(params?: EmployeeListParams): Promise<EmployeeListResponse> {
    if (env.isFingerspotConfigured) {
      const cloudId = env.fingerspotCloudId ?? "";
      return fetchUsersFromApi(cloudId, params);
    }
    return fetchEmployeesFromMock(params);
  },

  async getStats(): Promise<ApiResponse<EmployeeStats>> {
    // Fetch the full unfiltered list so stats always reflect the total roster
    const { data } = await employeesService.list();
    return { data: computeEmployeeStats(data) };
  },

  async getById(id: string): Promise<ApiResponse<Employee>> {
    // Fetch full list and find by id — avoids a separate endpoint for now
    const { data } = await employeesService.list();
    const employee = data.find((item) => item.id === id);

    if (!employee) {
      throw new Error(`Employee not found: ${id}`);
    }

    return { data: employee };
  },
};
