import { StatusBadge, type StatusBadgeVariant } from "@/components/shared";
import type { AttendanceLog } from "@/services/types/attendance";

const statusConfig: Record<
  AttendanceLog["status"],
  { label: string; variant: StatusBadgeVariant }
> = {
  "check-in": { label: "Check-in", variant: "success" },
  "check-out": { label: "Check-out", variant: "neutral" },
};

type AttendanceStatusBadgeProps = {
  status: AttendanceLog["status"];
};

export function AttendanceStatusBadge({ status }: AttendanceStatusBadgeProps) {
  const config = statusConfig[status];
  return <StatusBadge label={config.label} variant={config.variant} />;
}
