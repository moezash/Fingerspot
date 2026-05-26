import { cn } from "@/lib/utils";

import { StatusBadge, type StatusBadgeVariant } from "./status-badge";

export type ApiStatus = "connected" | "disconnected" | "checking" | "degraded";

const statusConfig: Record<
  ApiStatus,
  { label: string; variant: StatusBadgeVariant }
> = {
  connected: { label: "API connected", variant: "success" },
  disconnected: { label: "API disconnected", variant: "error" },
  checking: { label: "Checking connection", variant: "pending" },
  degraded: { label: "API degraded", variant: "warning" },
};

type APIStatusIndicatorProps = {
  status?: ApiStatus;
  label?: string;
  className?: string;
};

export function APIStatusIndicator({
  status = "checking",
  label,
  className,
}: APIStatusIndicatorProps) {
  const config = statusConfig[status];

  return (
    <div
      className={cn(
        "inline-flex items-center gap-2 text-xs text-muted-foreground",
        className
      )}
    >
      <StatusBadge label={label ?? config.label} variant={config.variant} />
    </div>
  );
}
