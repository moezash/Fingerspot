import {
  EmptyState,
  LoadingState,
  SectionCard,
} from "@/components/shared";
import type { Employee } from "@/services/types/employees";

import type { EmployeeFilters } from "./employee-utils";
import { EmployeesFilters } from "./employees-filters";
import { EmployeesTable } from "./employees-table";

type EmployeesDirectoryProps = {
  employees: Employee[];
  isLoading: boolean;
  isListLoading: boolean;
  error: string | null;
  filters: EmployeeFilters;
  hasActiveFilters: boolean;
  onSearchChange: (search: string) => void;
  onStatusChange: (status: EmployeeFilters["status"]) => void;
  onRetry?: () => void;
};

export function EmployeesDirectory({
  employees,
  isLoading,
  isListLoading,
  error,
  filters,
  hasActiveFilters,
  onSearchChange,
  onStatusChange,
  onRetry,
}: EmployeesDirectoryProps) {
  const showTableLoading = isLoading || isListLoading;
  const showEmptyResults = !showTableLoading && !error && employees.length === 0;

  return (
    <SectionCard
      title="All employees"
      description="Directory of registered employees across departments."
      contentClassName="space-y-4 pt-4"
    >
      <EmployeesFilters
        filters={filters}
        onSearchChange={onSearchChange}
        onStatusChange={onStatusChange}
        resultCount={employees.length}
      />

      {showTableLoading ? (
        <LoadingState variant="table" rows={6} />
      ) : error ? (
        <EmptyState
          title="Unable to load employees"
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
      ) : showEmptyResults && !hasActiveFilters ? (
        <EmptyState
          title="No employees yet"
          description="Employee records will appear here once they are added to the workspace."
        />
      ) : showEmptyResults ? (
        <EmptyState
          title="No matching employees"
          description="Try adjusting your search or status filter to find employees."
        />
      ) : (
        <EmployeesTable employees={employees} />
      )}
    </SectionCard>
  );
}
