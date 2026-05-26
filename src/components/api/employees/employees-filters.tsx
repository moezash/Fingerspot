import { Search } from "lucide-react";

import { Input } from "@/components/ui/input";
import { cn } from "@/lib/utils";
import type { EmployeeStatus } from "@/services/types/employees";

import type { EmployeeFilters } from "./employee-utils";

const statusOptions: { value: EmployeeFilters["status"]; label: string }[] = [
  { value: "all", label: "All statuses" },
  { value: "active", label: "Active" },
  { value: "pending", label: "Pending" },
  { value: "inactive", label: "Inactive" },
];

type EmployeesFiltersProps = {
  filters: EmployeeFilters;
  onSearchChange: (search: string) => void;
  onStatusChange: (status: EmployeeFilters["status"]) => void;
  resultCount: number;
  className?: string;
};

export function EmployeesFilters({
  filters,
  onSearchChange,
  onStatusChange,
  resultCount,
  className,
}: EmployeesFiltersProps) {
  return (
    <div
      className={cn(
        "flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between",
        className
      )}
    >
      <div className="flex flex-1 flex-col gap-3 sm:max-w-xl sm:flex-row">
        <div className="relative min-w-0 flex-1">
          <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground/70" />
          <Input
            type="search"
            placeholder="Search by name, ID, department..."
            value={filters.search}
            onChange={(event) => onSearchChange(event.target.value)}
            className="h-9 bg-muted/40 pl-9"
            aria-label="Search employees"
          />
        </div>
        <select
          value={filters.status}
          onChange={(event) =>
            onStatusChange(event.target.value as EmployeeStatus | "all")
          }
          aria-label="Filter by status"
          className="h-9 min-w-[10rem] rounded-lg border border-input bg-muted/40 px-2.5 text-sm text-foreground outline-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 dark:bg-input/30"
        >
          {statusOptions.map((option) => (
            <option key={option.value} value={option.value}>
              {option.label}
            </option>
          ))}
        </select>
      </div>
      <p className="text-sm text-muted-foreground">
        {resultCount} {resultCount === 1 ? "employee" : "employees"}
      </p>
    </div>
  );
}
