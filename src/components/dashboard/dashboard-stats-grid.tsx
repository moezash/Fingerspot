import { CalendarCheck, Monitor, MonitorCheck, Users } from "lucide-react";

import { DashboardGrid, LoadingState, StatsCard } from "@/components/shared";
import type { DashboardOverview } from "@/services/types/dashboard";

type DashboardStatsGridProps = {
  overview: DashboardOverview | null;
  isLoading: boolean;
};

export function DashboardStatsGrid({
  overview,
  isLoading,
}: DashboardStatsGridProps) {
  if (isLoading || !overview) {
    return <LoadingState variant="stats" />;
  }

  return (
    <DashboardGrid variant="stats">
      <StatsCard
        label="Employees"
        hint={`${overview.activeEmployees} active`}
        value={overview.totalEmployees}
        icon={Users}
      />
      <StatsCard
        label="Devices"
        hint={`${overview.onlineDevices} online`}
        value={overview.totalDevices}
        icon={Monitor}
      />
      <StatsCard
        label="Online devices"
        hint="Currently reachable"
        value={overview.onlineDevices}
        icon={MonitorCheck}
      />
      <StatsCard
        label="Attendance today"
        hint={`${overview.checkInsToday} check-ins`}
        value={overview.attendanceToday}
        icon={CalendarCheck}
      />
    </DashboardGrid>
  );
}
