import type { EmployeeStatus } from "@/services/types/employees";

export type EmployeeFilters = {
  search: string;
  status: EmployeeStatus | "all";
};

export function formatEmployeeDate(value: string | null): string {
  if (!value) return "—";

  return new Date(value).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
}
