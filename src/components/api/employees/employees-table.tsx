import { DataTable, type DataTableColumn } from "@/components/shared";
import type { Employee } from "@/services/types/employees";

import { formatEmployeeDate } from "./employee-utils";
import { EmployeeStatusBadge } from "./employee-status-badge";

const columns: DataTableColumn<Employee>[] = [
  {
    id: "employee",
    header: "Employee",
    cell: (row) => (
      <div className="min-w-[10rem]">
        <p className="font-medium text-foreground">{row.name}</p>
        <p className="font-mono text-xs text-muted-foreground">{row.employeeId}</p>
      </div>
    ),
  },
  {
    id: "department",
    header: "Department",
    cell: (row) => (
      <div>
        <p className="text-foreground">{row.department}</p>
        <p className="text-xs text-muted-foreground">{row.role}</p>
      </div>
    ),
  },
  {
    id: "status",
    header: "Status",
    cell: (row) => <EmployeeStatusBadge status={row.status} />,
  },
  {
    id: "device",
    header: "Device",
    cell: (row) => (
      <span className="text-muted-foreground">
        {row.deviceSynced ? "Synced" : "Not synced"}
      </span>
    ),
  },
  {
    id: "lastSeen",
    header: "Last seen",
    cell: (row) => (
      <span className="text-muted-foreground">
        {formatEmployeeDate(row.lastSeenAt)}
      </span>
    ),
  },
];

type EmployeesTableProps = {
  employees: Employee[];
};

export function EmployeesTable({ employees }: EmployeesTableProps) {
  return (
    <DataTable
      columns={columns}
      data={employees}
      getRowKey={(row) => row.id}
    />
  );
}
