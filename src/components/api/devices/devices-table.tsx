"use client";

import { CheckCircle2, Radio } from "lucide-react";

import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import { useActiveDevice } from "@/hooks/use-active-device";
import type { Device } from "@/services/types/devices";

import { formatDeviceDate } from "./device-utils";
import { DeviceStatusBadge } from "./device-status-badge";
import { DeviceRestartDialog } from "./device-restart-dialog";
import { DeviceSyncTimeDialog } from "./device-sync-time-dialog";

type DevicesTableProps = {
  devices: Device[];
};

export function DevicesTable({ devices }: DevicesTableProps) {
  const { activeCloudId, setActive } = useActiveDevice();

  return (
    <div className="overflow-hidden rounded-xl border border-border/60">
      <table className="w-full text-sm">
        <thead>
          <tr className="hover:bg-transparent">
            {["Device", "Cloud ID", "Status", "Firmware", "Last update", ""].map(
              (header) => (
                <th
                  key={header}
                  className="h-10 bg-muted/30 px-4 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                >
                  {header}
                </th>
              )
            )}
          </tr>
        </thead>
        <tbody>
          {devices.map((device) => {
            const isActive = activeCloudId === device.cloudId;

            return (
              <tr
                key={device.id}
                className={cn(
                  "border-t border-border/60 transition-colors",
                  isActive
                    ? "bg-emerald-500/5 dark:bg-emerald-500/10"
                    : "hover:bg-muted/30"
                )}
              >
                {/* Device name + serial */}
                <td className="px-4 py-3">
                  <div className="flex items-center gap-2 min-w-[10rem]">
                    {isActive && (
                      <CheckCircle2
                        className="size-3.5 shrink-0 text-emerald-500"
                        aria-label="Active device"
                      />
                    )}
                    <div>
                      <p className="font-medium text-foreground">{device.name}</p>
                      <p className="font-mono text-xs text-muted-foreground">
                        {device.sn}
                      </p>
                    </div>
                  </div>
                </td>

                {/* Cloud ID */}
                <td className="px-4 py-3">
                  <span className="font-mono text-sm text-muted-foreground">
                    {device.cloudId}
                  </span>
                </td>

                {/* Status badge */}
                <td className="px-4 py-3">
                  <DeviceStatusBadge status={device.status} />
                </td>

                {/* Firmware */}
                <td className="px-4 py-3">
                  <span className="text-sm text-muted-foreground">
                    {device.firmware ?? "—"}
                  </span>
                </td>

                {/* Last update */}
                <td className="px-4 py-3">
                  <span className="text-sm text-muted-foreground">
                    {formatDeviceDate(device.lastUpdateAt)}
                  </span>
                </td>

                {/* Actions */}
                <td className="px-4 py-3">
                  <div className="flex items-center gap-1">
                    {!isActive && (
                      <Button
                        variant="ghost"
                        size="sm"
                        className="h-7 gap-1.5 px-2 text-xs text-muted-foreground hover:text-foreground"
                        onClick={() => setActive(device.cloudId)}
                        aria-label={`Set ${device.name} as active device`}
                      >
                        <Radio className="size-3" />
                        Set active
                      </Button>
                    )}
                    {isActive && (
                      <span className="px-2 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                        Active
                      </span>
                    )}
                    <DeviceSyncTimeDialog device={device} />
                    <DeviceRestartDialog device={device} />
                  </div>
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
}
