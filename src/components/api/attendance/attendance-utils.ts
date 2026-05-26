import { env } from "@/config/env";
import { getSelectedCloudId } from "@/services/config/device-config";
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
    // trans_id is auto-generated per-request by the service layer when live.
    // The field is kept for mock compatibility.
    trans_id: env.isFingerspotConfigured ? "auto" : "",
    // cloud_id comes from the selected device context, not a global env var.
    cloud_id: getSelectedCloudId() ?? "",
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
