import { Monitor } from "lucide-react";

import { LoadingState, SectionCard } from "@/components/shared";
import type { Device } from "@/services/types/devices";

import { DeviceStatusBadge } from "@/components/api/devices/device-status-badge";

type DashboardDevicePanelProps = {
  devices: Device[];
  isLoading: boolean;
};

export function DashboardDevicePanel({
  devices,
  isLoading,
}: DashboardDevicePanelProps) {
  return (
    <SectionCard
      title="Device status"
      description="Current connectivity of registered devices."
      contentClassName="pt-4"
    >
      {isLoading ? (
        <LoadingState variant="list" rows={4} />
      ) : devices.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-lg border border-dashed border-border/80 bg-muted/20 py-8 text-center">
          <Monitor className="size-5 text-muted-foreground/50" />
          <p className="mt-3 text-sm font-medium text-foreground">
            No devices registered
          </p>
          <p className="mt-1 text-xs text-muted-foreground">
            Devices will appear here once enrolled.
          </p>
        </div>
      ) : (
        <ul className="space-y-1" aria-label="Device status list">
          {devices.map((device) => (
            <li
              key={device.id}
              className="flex items-center justify-between gap-3 rounded-lg px-2 py-2.5 transition-colors hover:bg-muted/30"
            >
              <div className="flex min-w-0 items-center gap-2.5">
                <Monitor className="size-3.5 shrink-0 text-muted-foreground/60" />
                <div className="min-w-0">
                  <p className="truncate text-sm font-medium text-foreground">
                    {device.name}
                  </p>
                  <p className="font-mono text-xs text-muted-foreground">
                    {device.sn}
                  </p>
                </div>
              </div>
              <DeviceStatusBadge status={device.status} />
            </li>
          ))}
        </ul>
      )}
    </SectionCard>
  );
}
