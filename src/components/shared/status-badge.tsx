import { Badge } from "@/components/ui/badge";
import { cn } from "@/lib/utils";

const statusStyles = {
  success:
    "border-border bg-muted/50 text-foreground [&_[data-status-dot]]:bg-emerald-500",
  warning:
    "border-border bg-muted/50 text-foreground [&_[data-status-dot]]:bg-amber-500",
  error:
    "border-border bg-muted/50 text-foreground [&_[data-status-dot]]:bg-red-500",
  pending:
    "border-border bg-muted/50 text-muted-foreground [&_[data-status-dot]]:bg-muted-foreground",
  neutral:
    "border-border bg-muted/50 text-muted-foreground [&_[data-status-dot]]:bg-muted-foreground/60",
} as const;

export type StatusBadgeVariant = keyof typeof statusStyles;

type StatusBadgeProps = {
  label: string;
  variant?: StatusBadgeVariant;
  className?: string;
};

export function StatusBadge({
  label,
  variant = "neutral",
  className,
}: StatusBadgeProps) {
  return (
    <Badge
      variant="outline"
      className={cn("gap-1.5 font-normal", statusStyles[variant], className)}
    >
      <span
        data-status-dot
        className="size-1.5 shrink-0 rounded-full"
        aria-hidden
      />
      {label}
    </Badge>
  );
}
