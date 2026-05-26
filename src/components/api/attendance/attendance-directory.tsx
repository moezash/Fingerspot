import {
  EmptyState,
  LoadingState,
  SectionCard,
} from "@/components/shared";
import type { AttendanceLog } from "@/services/types/attendance";

import type { AttendanceFilters } from "./attendance-utils";
import { AttendanceFiltersBar } from "./attendance-filters";
import { AttendanceTable } from "./attendance-table";

type AttendanceDirectoryProps = {
  logs: AttendanceLog[];
  isLoading: boolean;
  isListLoading: boolean;
  error: string | null;
  validationError: string | null;
  filters: AttendanceFilters;
  hasSearchFilter: boolean;
  onFiltersChange: (patch: Partial<AttendanceFilters>) => void;
  onRetry?: () => void;
};

export function AttendanceDirectory({
  logs,
  isLoading,
  isListLoading,
  error,
  validationError,
  filters,
  hasSearchFilter,
  onFiltersChange,
  onRetry,
}: AttendanceDirectoryProps) {
  const showTableLoading =
    !validationError && (isLoading || isListLoading);
  const showEmptyResults =
    !showTableLoading && !error && !validationError && logs.length === 0;

  return (
    <SectionCard
      title="Attendance logs"
      description="Fingerspot /get_attlog records for the selected transaction, cloud, and date range."
      contentClassName="space-y-4 pt-4"
    >
      <AttendanceFiltersBar
        filters={filters}
        validationError={validationError}
        resultCount={logs.length}
        onChange={onFiltersChange}
      />

      {validationError ? (
        <EmptyState
          title="Invalid query parameters"
          description={validationError}
        />
      ) : showTableLoading ? (
        <LoadingState variant="table" rows={6} />
      ) : error ? (
        <EmptyState title="Unable to load attendance logs" description={error}>
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
      ) : showEmptyResults && !hasSearchFilter ? (
        <EmptyState
          title="No attendance logs"
          description="No records were returned for this date range and device scope."
        />
      ) : showEmptyResults ? (
        <EmptyState
          title="No matching logs"
          description="Try adjusting your search or date range filters."
        />
      ) : (
        <AttendanceTable logs={logs} />
      )}
    </SectionCard>
  );
}
