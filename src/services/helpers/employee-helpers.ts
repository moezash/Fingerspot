import type {
  Employee,
  EmployeeListParams,
  EmployeeStats,
} from "../types/employees";

export function applyEmployeeListParams(
  employees: Employee[],
  params?: EmployeeListParams
): Employee[] {
  const search = params?.search?.trim().toLowerCase();
  const status = params?.status ?? "all";

  return employees.filter((employee) => {
    const matchesStatus = status === "all" || employee.status === status;
    if (!matchesStatus) return false;
    if (!search) return true;

    return (
      employee.name.toLowerCase().includes(search) ||
      employee.employeeId.toLowerCase().includes(search) ||
      employee.department.toLowerCase().includes(search) ||
      employee.role.toLowerCase().includes(search)
    );
  });
}

export function computeEmployeeStats(employees: Employee[]): EmployeeStats {
  return employees.reduce<EmployeeStats>(
    (stats, employee) => {
      stats.total += 1;
      if (employee.status === "active") stats.active += 1;
      if (employee.status === "pending") stats.pending += 1;
      if (employee.status === "inactive") stats.inactive += 1;
      return stats;
    },
    { total: 0, active: 0, pending: 0, inactive: 0 }
  );
}

export function buildEmployeeListMeta(total: number, params?: EmployeeListParams) {
  const limit = params?.limit ?? (total || 1);
  const page = params?.page ?? 1;

  return {
    page,
    limit,
    total,
    totalPages: Math.max(1, Math.ceil(total / limit)),
  };
}
