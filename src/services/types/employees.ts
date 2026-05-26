import type { ListQueryParams, PaginatedResponse } from "./common";

export type EmployeeStatus = "active" | "inactive" | "pending";

export type Employee = {
  id: string;
  employeeId: string;
  name: string;
  department: string;
  role: string;
  status: EmployeeStatus;
  deviceSynced: boolean;
  lastSeenAt: string | null;
  createdAt: string;
};

export type EmployeeListParams = ListQueryParams & {
  search?: string;
  status?: EmployeeStatus | "all";
};

export type EmployeeListResponse = PaginatedResponse<Employee>;

export type EmployeeStats = {
  total: number;
  active: number;
  pending: number;
  inactive: number;
};
