import { env } from "@/config/env";
import { API_PATHS } from "@/config/api-paths";

import { computeEmployeeStats } from "../helpers/employee-helpers";
import { getSelectedCloudId } from "../config/device-config";
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
 * cloud_id comes from the selected device context (device-config.ts),
 * not from a global env var. This scopes employee records to the active device.
 */
export const employeesService = {
  async list(params?: EmployeeListParams): Promise<EmployeeListResponse> {
    if (env.isFingerspotConfigured) {
      const cloudId = getSelectedCloudId() ?? "";
      return fetchUsersFromApi(cloudId, params);
    }
    return fetchEmployeesFromMock(params);
  },

  async getStats(): Promise<ApiResponse<EmployeeStats>> {
    const { data } = await employeesService.list();
    return { data: computeEmployeeStats(data) };
  },

  async getById(id: string): Promise<ApiResponse<Employee>> {
    const { data } = await employeesService.list();
    const employee = data.find((item) => item.id === id);

    if (!employee) {
      throw new Error(`Employee not found: ${id}`);
    }

    return { data: employee };
  },
};
