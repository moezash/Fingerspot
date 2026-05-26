import { Search } from "lucide-react";

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

const fieldClassName =
  "h-9 bg-muted/40 text-sm dark:bg-input/30";

export function AttendanceFiltersBar({
  filters,
  validationError,
  resultCount,
  onChange,
  className,
}: AttendanceFiltersProps) {
  return (
    <div className={cn("space-y-4", className)}>
      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div className="space-y-1.5">
          <label
            htmlFor="trans_id"
            className="text-xs font-medium text-muted-foreground"
          >
            Transaction ID
          </label>
          <Input
            id="trans_id"
            value={filters.trans_id}
            onChange={(e) => onChange({ trans_id: e.target.value })}
            placeholder="trans_id"
            className={fieldClassName}
          />
        </div>
        <div className="space-y-1.5">
          <label
            htmlFor="cloud_id"
            className="text-xs font-medium text-muted-foreground"
          >
            Cloud ID
          </label>
          <Input
            id="cloud_id"
            value={filters.cloud_id}
            onChange={(e) => onChange({ cloud_id: e.target.value })}
            placeholder="cloud_id"
            className={fieldClassName}
          />
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
          <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground/70" />
          <Input
            type="search"
            placeholder="Search logs..."
            value={filters.search}
            onChange={(e) => onChange({ search: e.target.value })}
            className="h-9 bg-muted/40 pl-9"
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
