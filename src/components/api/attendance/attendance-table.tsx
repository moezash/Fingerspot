import { DataTable, type DataTableColumn } from "@/components/shared";
import type { AttendanceLog } from "@/services/types/attendance";

import { formatCheckTime } from "./attendance-utils";
import { AttendanceStatusBadge } from "./attendance-status-badge";

const columns: DataTableColumn<AttendanceLog>[] = [
  {
    id: "employee",
    header: "Employee",
    cell: (row) => (
      <div>
        <p className="font-medium text-foreground">{row.employee_name}</p>
        <p className="font-mono text-xs text-muted-foreground">PIN {row.pin}</p>
      </div>
    ),
  },
  {
    id: "check_time",
    header: "Check time",
    cell: (row) => (
      <span className="text-muted-foreground">{formatCheckTime(row.check_time)}</span>
    ),
  },
  {
    id: "status",
    header: "Status",
    cell: (row) => <AttendanceStatusBadge status={row.status} />,
  },
  {
    id: "verify_mode",
    header: "Verify mode",
    cell: (row) => (
      <span className="text-muted-foreground">{row.verify_mode}</span>
    ),
  },
  {
    id: "device",
    header: "Device",
    cell: (row) => (
      <span className="font-mono text-xs text-muted-foreground">{row.device_sn}</span>
    ),
  },
];

type AttendanceTableProps = {
  logs: AttendanceLog[];
};

export function AttendanceTable({ logs }: AttendanceTableProps) {
  return (
    <DataTable columns={columns} data={logs} getRowKey={(row) => row.id} />
  );
}
