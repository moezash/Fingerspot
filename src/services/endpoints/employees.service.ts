import { API_PATHS } from "@/config/api-paths";

import { computeEmployeeStats } from "../helpers/employee-helpers";
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
 * First API integration target module.
 *
 * Mock source remains the active data provider until endpoints are wired.
 * When ready, swap method bodies to use apiGet / apiGetPaginated from
 * `@/services/helpers/api-request` (requires NEXT_PUBLIC_API_BASE_URL).
 */
export const employeesService = {
  async list(params?: EmployeeListParams): Promise<EmployeeListResponse> {
    // Future: return apiGetPaginated<Employee>(employeesPaths.root, { params });
    return fetchEmployeesFromMock(params);
  },

  async getStats(): Promise<ApiResponse<EmployeeStats>> {
    // Future: const data = await apiGet<EmployeeStats>("/employees/stats");
    const { data } = await fetchEmployeesFromMock();
    return { data: computeEmployeeStats(data) };
  },

  async getById(id: string): Promise<ApiResponse<Employee>> {
    // Future: const data = await apiGet<Employee>(employeesPaths.byId(id));
    const { data } = await fetchEmployeesFromMock();
    const employee = data.find((item) => item.id === id);

    if (!employee) {
      throw new Error(`Employee not found: ${id}`);
    }

    return { data: employee };
  },
};
