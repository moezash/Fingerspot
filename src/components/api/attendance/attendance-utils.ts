import { getDefaultAttlogDateRange } from "@/services/helpers/attendance-helpers";

export type AttendanceFilters = {
  trans_id: string;
  cloud_id: string;
  start_date: string;
  end_date: string;
  search: string;
};

export function getDefaultAttendanceFilters(): AttendanceFilters {
  const { start_date, end_date } = getDefaultAttlogDateRange();

  return {
    trans_id: "DEMO-TRANS-001",
    cloud_id: "DEMO-CLOUD-001",
    start_date,
    end_date,
    search: "",
  };
}

export function formatCheckTime(value: string): string {
  return new Date(value).toLocaleString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}
