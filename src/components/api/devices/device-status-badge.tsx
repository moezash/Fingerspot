import { StatusBadge, type StatusBadgeVariant } from "@/components/shared";
import type { DeviceStatus } from "@/services/types/devices";

const statusConfig: Record<
  DeviceStatus,
  { label: string; variant: StatusBadgeVariant }
> = {
  online:  { label: "Online",  variant: "success" },
  offline: { label: "Offline", variant: "error"   },
  unknown: { label: "Unknown", variant: "neutral"  },
};

type DeviceStatusBadgeProps = {
  status: DeviceStatus;
};

export function DeviceStatusBadge({ status }: DeviceStatusBadgeProps) {
  const config = statusConfig[status];
  return <StatusBadge label={config.label} variant={config.variant} />;
}
