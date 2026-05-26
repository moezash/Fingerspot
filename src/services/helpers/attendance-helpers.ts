import type {
  AttendanceLog,
  AttendanceStats,
  AttlogRequestParams,
  DateRangeValidationResult,
} from "../types/attendance";

/** Fingerspot API limit: maximum 2 days per /get_attlog request. */
export const MAX_ATTLOG_RANGE_DAYS = 2;

const MS_PER_DAY = 24 * 60 * 60 * 1000;

export function toDateInputValue(date: Date): string {
  return date.toISOString().slice(0, 10);
}

export function getDefaultAttlogDateRange(): Pick<
  AttlogRequestParams,
  "start_date" | "end_date"
> {
  const end = new Date();
  const start = new Date(end);
  start.setDate(end.getDate() - 1);

  return {
    start_date: toDateInputValue(start),
    end_date: toDateInputValue(end),
  };
}

export function parseDateInput(value: string): Date | null {
  if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) return null;

  const parsed = new Date(`${value}T00:00:00`);
  return Number.isNaN(parsed.getTime()) ? null : parsed;
}

export function validateAttlogDateRange(
  startDate: string,
  endDate: string
): DateRangeValidationResult {
  const start = parseDateInput(startDate);
  const end = parseDateInput(endDate);

  if (!start || !end) {
    return { valid: false, message: "Enter valid start and end dates." };
  }

  if (end < start) {
    return {
      valid: false,
      message: "End date must be on or after the start date.",
    };
  }

  const rangeDays = (end.getTime() - start.getTime()) / MS_PER_DAY;

  if (rangeDays > MAX_ATTLOG_RANGE_DAYS) {
    return {
      valid: false,
      message: `Date range cannot exceed ${MAX_ATTLOG_RANGE_DAYS} days per request.`,
    };
  }

  return { valid: true };
}

export function validateAttlogRequest(
  params: AttlogRequestParams
): DateRangeValidationResult {
  if (!params.trans_id.trim()) {
    return { valid: false, message: "Transaction ID is required." };
  }

  if (!params.cloud_id.trim()) {
    return { valid: false, message: "Cloud ID is required." };
  }

  return validateAttlogDateRange(params.start_date, params.end_date);
}

export function filterAttendanceLogs(
  logs: AttendanceLog[],
  search: string
): AttendanceLog[] {
  const query = search.trim().toLowerCase();
  if (!query) return logs;

  return logs.filter(
    (log) =>
      log.employee_name.toLowerCase().includes(query) ||
      log.pin.toLowerCase().includes(query) ||
      log.device_sn.toLowerCase().includes(query) ||
      log.verify_mode.toLowerCase().includes(query)
  );
}

export function filterLogsByDateRange(
  logs: AttendanceLog[],
  startDate: string,
  endDate: string
): AttendanceLog[] {
  const start = parseDateInput(startDate);
  const end = parseDateInput(endDate);
  if (!start || !end) return logs;

  const rangeEnd = new Date(end);
  rangeEnd.setHours(23, 59, 59, 999);

  return logs.filter((log) => {
    const checkTime = new Date(log.check_time).getTime();
    return checkTime >= start.getTime() && checkTime <= rangeEnd.getTime();
  });
}

export function computeAttendanceStats(logs: AttendanceLog[]): AttendanceStats {
  const uniquePins = new Set(logs.map((log) => log.pin));

  return logs.reduce<AttendanceStats>(
    (stats, log) => {
      stats.totalLogs += 1;
      if (log.status === "check-in") stats.checkIns += 1;
      if (log.status === "check-out") stats.checkOuts += 1;
      return stats;
    },
    {
      totalLogs: 0,
      uniqueEmployees: uniquePins.size,
      checkIns: 0,
      checkOuts: 0,
    }
  );
}
