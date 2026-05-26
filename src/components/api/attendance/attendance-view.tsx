"use client";

import { useMemo, useState } from "react";
import { RefreshCw } from "lucide-react";

import { Button } from "@/components/ui/button";
import { APIStatusIndicator, ContentSection, PageHeader } from "@/components/shared";
import { useAttendance } from "@/hooks/use-attendance";

import { AttendanceDirectory } from "./attendance-directory";
import { AttendanceStatsCards } from "./attendance-stats";
import {
  getDefaultAttendanceFilters,
  type AttendanceFilters,
} from "./attendance-utils";

export function AttendanceView() {
  const [filters, setFilters] = useState<AttendanceFilters>(
    getDefaultAttendanceFilters
  );

  const {
    logs,
    stats,
    isLoading,
    isListLoading,
    error,
    validationError,
    refetch,
  } = useAttendance(filters);

  const hasSearchFilter = filters.search.trim().length > 0;

  const isStatsLoading = useMemo(
    () => isLoading && stats === null && !validationError,
    [isLoading, stats, validationError]
  );

  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        label="Platform"
        title="Attendance"
        description="Review fingerprint attendance logs from the Fingerspot get_attlog API. Requests require trans_id, cloud_id, and a maximum 2-day date range."
      >
        <APIStatusIndicator status="checking" />
        <Button
          variant="outline"
          size="sm"
          className="h-8"
          onClick={refetch}
          disabled={isLoading || isListLoading || Boolean(validationError)}
        >
          <RefreshCw className="size-3.5" />
          Refresh
        </Button>
      </PageHeader>

      <ContentSection
        title="Overview"
        description="Summary for the active /get_attlog query."
      >
        <AttendanceStatsCards stats={stats} isLoading={isStatsLoading} />
      </ContentSection>

      <ContentSection
        title="Attendance logs"
        description="Data is provided by the attendance service layer. Bearer token auth will apply when the live API is connected."
      >
        <AttendanceDirectory
          logs={logs}
          isLoading={isLoading}
          isListLoading={isListLoading}
          error={error}
          validationError={validationError}
          filters={filters}
          hasSearchFilter={hasSearchFilter}
          onFiltersChange={(patch) =>
            setFilters((current) => ({ ...current, ...patch }))
          }
          onRetry={refetch}
        />
      </ContentSection>
    </div>
  );
}
