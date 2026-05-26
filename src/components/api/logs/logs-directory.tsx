import { Search, ScrollText } from "lucide-react";

import { Input } from "@/components/ui/input";
import { EmptyState, LoadingState, SectionCard } from "@/components/shared";
import type { AttendanceLog } from "@/services/types/attendance";

// Reuse the attendance table — identical columns, identical data shape
import { AttendanceTable } from "@/components/api/attendance/attendance-table";

type LogsDirectoryProps = {
  logs: AttendanceLog[];
  isLoading: boolean;
  isListLoading: boolean;
  error: string | null;
  search: string;
  onSearchChange: (value: string) => void;
  onRetry?: () => void;
};

export function LogsDirectory({
  logs,
  isLoading,
  isListLoading,
  error,
  search,
  onSearchChange,
  onRetry,
}: LogsDirectoryProps) {
  const showTableLoading = isLoading || isListLoading;
  const showEmpty = !showTableLoading && !error && logs.length === 0;
  const hasSearch = search.trim().length > 0;

  return (
    <SectionCard
      title="Event log"
      description="Fingerspot attendance events recorded today via get_attlog."
      contentClassName="space-y-4 pt-4"
    >
      {/* Search bar */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative min-w-0 flex-1 sm:max-w-sm">
          <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground/70" />
          <Input
            type="search"
            placeholder="Search by name, PIN, device..."
            value={search}
            onChange={(e) => onSearchChange(e.target.value)}
            className="h-9 bg-muted/40 pl-9"
            aria-label="Search logs"
          />
        </div>
        <p className="text-sm text-muted-foreground">
          {logs.length} {logs.length === 1 ? "event" : "events"}
        </p>
      </div>

      {/* Content */}
      {showTableLoading ? (
        <LoadingState variant="table" rows={6} />
      ) : error ? (
        <EmptyState
          icon={ScrollText}
          title="Unable to load logs"
          description={error}
        >
          {onRetry && (
            <button
              type="button"
              onClick={onRetry}
              className="text-sm font-medium text-foreground underline-offset-4 hover:underline"
            >
              Try again
            </button>
          )}
        </EmptyState>
      ) : showEmpty && !hasSearch ? (
        <EmptyState
          icon={ScrollText}
          title="No events today"
          description="Attendance events will appear here as they are recorded by connected devices."
        />
      ) : showEmpty ? (
        <EmptyState
          icon={ScrollText}
          title="No matching events"
          description="Try adjusting your search to find log entries."
        />
      ) : (
        <AttendanceTable logs={logs} />
      )}
    </SectionCard>
  );
}
