import { StatusBadge, type StatusBadgeVariant } from "@/components/shared";
import type { EmployeeStatus } from "@/services/types/employees";

const statusConfig: Record<
  EmployeeStatus,
  { label: string; variant: StatusBadgeVariant }
> = {
  active: { label: "Active", variant: "success" },
  pending: { label: "Pending", variant: "warning" },
  inactive: { label: "Inactive", variant: "neutral" },
};

type EmployeeStatusBadgeProps = {
  status: EmployeeStatus;
};

export function EmployeeStatusBadge({ status }: EmployeeStatusBadgeProps) {
  const config = statusConfig[status];
  return <StatusBadge label={config.label} variant={config.variant} />;
}
