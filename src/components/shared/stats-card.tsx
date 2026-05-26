import type { LucideIcon } from "lucide-react";

import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { cn } from "@/lib/utils";

type StatsCardProps = {
  label: string;
  hint?: string;
  value?: React.ReactNode;
  icon?: LucideIcon;
  className?: string;
};

export function StatsCard({
  label,
  hint,
  value = "—",
  icon: Icon,
  className,
}: StatsCardProps) {
  return (
    <Card
      size="sm"
      className={cn(
        "shadow-none ring-1 ring-border/60 transition-colors hover:ring-border",
        className
      )}
    >
      <CardHeader className="pb-0">
        <div className="flex items-start justify-between gap-3">
          <CardDescription className="text-xs font-medium tracking-wide uppercase">
            {label}
          </CardDescription>
          {Icon && (
            <Icon className="size-4 shrink-0 text-muted-foreground/60" />
          )}
        </div>
        <CardTitle className="pt-2 font-mono text-2xl font-semibold tracking-tight tabular-nums">
          {value}
        </CardTitle>
      </CardHeader>
      {hint && (
        <CardContent>
          <p className="text-xs text-muted-foreground">{hint}</p>
        </CardContent>
      )}
    </Card>
  );
}
