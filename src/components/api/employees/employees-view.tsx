"use client";

import { useMemo, useState } from "react";
import { UserPlus } from "lucide-react";

import { Button } from "@/components/ui/button";
import { ContentSection, PageHeader } from "@/components/shared";
import { useEmployees } from "@/hooks/use-employees";

import type { EmployeeFilters } from "./employee-utils";
import { EmployeesDirectory } from "./employees-directory";
import { EmployeesStats } from "./employees-stats";

const defaultFilters: EmployeeFilters = {
  search: "",
  status: "all",
};

export function EmployeesView() {
  const [filters, setFilters] = useState<EmployeeFilters>(defaultFilters);
  const { employees, stats, isLoading, isListLoading, error, refetch } =
    useEmployees(filters);

  const hasActiveFilters =
    filters.search.trim().length > 0 || filters.status !== "all";

  const isStatsLoading = useMemo(
    () => isLoading && stats === null,
    [isLoading, stats]
  );

  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        label="Platform"
        title="Employees"
        description="Manage employee records, device sync status, and directory access for your organization."
      >
        <Button variant="outline" size="sm" className="h-8" disabled>
          <UserPlus className="size-3.5" />
          Add employee
        </Button>
      </PageHeader>

      <ContentSection
        title="Overview"
        description="Summary metrics for the employee directory."
      >
        <EmployeesStats stats={stats} isLoading={isStatsLoading} />
      </ContentSection>

      <ContentSection
        title="Employee directory"
        description="Search and filter employees. Data is provided by the employee service layer."
      >
        <EmployeesDirectory
          employees={employees}
          isLoading={isLoading}
          isListLoading={isListLoading}
          error={error}
          filters={filters}
          hasActiveFilters={hasActiveFilters}
          onSearchChange={(search) =>
            setFilters((current) => ({ ...current, search }))
          }
          onStatusChange={(status) =>
            setFilters((current) => ({ ...current, status }))
          }
          onRetry={refetch}
        />
      </ContentSection>
    </div>
  );
}
