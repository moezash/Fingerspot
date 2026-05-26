import type { AttendanceLog } from "./attendance";
import type { Device } from "./devices";

export type DashboardOverview = {
  /** Total registered employees */
  totalEmployees: number;
  /** Active employees */
  activeEmployees: number;
  /** Total registered devices */
  totalDevices: number;
  /** Devices currently online */
  onlineDevices: number;
  /** Attendance log count for today's date range */
  attendanceToday: number;
  /** Check-ins recorded today */
  checkInsToday: number;
  /** Most recent attendance logs (up to 5) for the activity feed */
  recentActivity: AttendanceLog[];
  /** All devices for the status overview panel */
  devices: Device[];
};
