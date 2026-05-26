import { AlertTriangle, Monitor } from "lucide-react";
import Link from "next/link";

import { Input } from "@/components/ui/input";
import { cn } from "@/lib/utils";
import { MAX_ATTLOG_RANGE_DAYS } from "@/services/helpers/attendance-helpers";

import type { AttendanceFilters } from "./attendance-utils";

type AttendanceFiltersProps = {
  filters: AttendanceFilters;
  validationError: string | null;
  resultCount: number;
  onChange: (patch: Partial<AttendanceFilters>) => void;
  className?: string;
};

const fieldClassName = "h-9 bg-muted/40 text-sm dark:bg-input/30";

export function AttendanceFiltersBar({
  filters,
  validationError,
  resultCount,
  onChange,
  className,
}: AttendanceFiltersProps) {
  const hasDevice = Boolean(filters.cloud_id.trim());

  return (
    <div className={cn("space-y-4", className)}>
      {/* No device selected warning */}
      {!hasDevice && (
        <div className="flex items-start gap-2.5 rounded-lg border border-amber-500/20 bg-amber-500/5 px-3 py-2.5 text-sm text-amber-700 dark:text-amber-400">
          <AlertTriangle className="mt-0.5 size-4 shrink-0" aria-hidden />
          <p className="flex-1 leading-snug">
            No active device selected. Go to{" "}
            <Link
              href="/devices"
              className="font-medium underline underline-offset-3 hover:opacity-80"
            >
              Devices
            </Link>{" "}
            and set an active device to load attendance logs.
          </p>
        </div>
      )}

      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {/* Active device (read-only) */}
        <div className="space-y-1.5">
          <label className="text-xs font-medium text-muted-foreground">
            Active device
          </label>
          <div className="flex h-9 items-center gap-2 rounded-lg border border-input bg-muted/20 px-2.5 dark:bg-input/20">
            <Monitor className="size-3.5 shrink-0 text-muted-foreground/60" />
            <span
              className={cn(
                "truncate font-mono text-xs",
                hasDevice ? "text-foreground" : "text-muted-foreground"
              )}
            >
              {hasDevice ? filters.cloud_id : "No device selected"}
            </span>
          </div>
        </div>

        <div className="space-y-1.5">
          <label
            htmlFor="start_date"
            className="text-xs font-medium text-muted-foreground"
          >
            Start date
          </label>
          <Input
            id="start_date"
            type="date"
            value={filters.start_date}
            onChange={(e) => onChange({ start_date: e.target.value })}
            className={fieldClassName}
          />
        </div>

        <div className="space-y-1.5">
          <label
            htmlFor="end_date"
            className="text-xs font-medium text-muted-foreground"
          >
            End date
          </label>
          <Input
            id="end_date"
            type="date"
            value={filters.end_date}
            onChange={(e) => onChange({ end_date: e.target.value })}
            className={fieldClassName}
          />
        </div>
      </div>

      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative min-w-0 flex-1 sm:max-w-sm">
          <Input
            type="search"
            placeholder="Search logs..."
            value={filters.search}
            onChange={(e) => onChange({ search: e.target.value })}
            className="h-9 bg-muted/40 pl-3"
            aria-label="Search attendance logs"
          />
        </div>
        <p className="text-sm text-muted-foreground">
          {resultCount} {resultCount === 1 ? "log" : "logs"} · max{" "}
          {MAX_ATTLOG_RANGE_DAYS}-day range
        </p>
      </div>

      {validationError && (
        <p className="text-sm text-destructive" role="alert">
          {validationError}
        </p>
      )}
    </div>
  );
}
