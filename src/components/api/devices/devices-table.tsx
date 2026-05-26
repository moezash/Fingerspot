import { DataTable, type DataTableColumn } from "@/components/shared";
import type { Device } from "@/services/types/devices";

import { formatDeviceDate } from "./device-utils";
import { DeviceStatusBadge } from "./device-status-badge";
import { DeviceRestartDialog } from "./device-restart-dialog";

const columns: DataTableColumn<Device>[] = [
  {
    id: "device",
    header: "Device",
    cell: (row) => (
      <div className="min-w-[10rem]">
        <p className="font-medium text-foreground">{row.name}</p>
        <p className="font-mono text-xs text-muted-foreground">{row.sn}</p>
      </div>
    ),
  },
  {
    id: "cloudId",
    header: "Cloud ID",
    cell: (row) => (
      <span className="font-mono text-sm text-muted-foreground">
        {row.cloudId}
      </span>
    ),
  },
  {
    id: "status",
    header: "Status",
    cell: (row) => <DeviceStatusBadge status={row.status} />,
  },
  {
    id: "firmware",
    header: "Firmware",
    cell: (row) => (
      <span className="text-sm text-muted-foreground">
        {row.firmware ?? "—"}
      </span>
    ),
  },
  {
    id: "lastUpdate",
    header: "Last update",
    cell: (row) => (
      <span className="text-sm text-muted-foreground">
        {formatDeviceDate(row.lastUpdateAt)}
      </span>
    ),
  },
  {
    id: "actions",
    header: "",
    headerClassName: "w-px",
    cell: (row) => <DeviceRestartDialog device={row} />,
  },
];

type DevicesTableProps = {
  devices: Device[];
};

export function DevicesTable({ devices }: DevicesTableProps) {
  return (
    <DataTable
      columns={columns}
      data={devices}
      getRowKey={(row) => row.id}
    />
  );
}
