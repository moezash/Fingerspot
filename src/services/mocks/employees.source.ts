import {
  applyEmployeeListParams,
  buildEmployeeListMeta,
} from "../helpers/employee-helpers";
import type {
  EmployeeListParams,
  EmployeeListResponse,
} from "../types/employees";
import { employeesMock } from "./employees.mock";

const MOCK_NETWORK_DELAY_MS = 600;

function delay(ms: number): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

/**
 * Temporary data source. Replace with apiClient calls in employees.service.
 */
export async function fetchEmployeesFromMock(
  params?: EmployeeListParams
): Promise<EmployeeListResponse> {
  await delay(MOCK_NETWORK_DELAY_MS);

  const data = applyEmployeeListParams(employeesMock, params);

  return {
    data,
    meta: buildEmployeeListMeta(data.length, params),
  };
}
