import { Clock, LogIn, LogOut, Users } from "lucide-react";

import { DashboardGrid, LoadingState, StatsCard } from "@/components/shared";
import type { AttendanceStats } from "@/services/types/attendance";

type AttendanceStatsProps = {
  stats: AttendanceStats | null;
  isLoading?: boolean;
};

export function AttendanceStatsCards({ stats, isLoading }: AttendanceStatsProps) {
  if (isLoading || !stats) {
    return <LoadingState variant="stats" />;
  }

  return (
    <DashboardGrid variant="stats">
      <StatsCard
        label="Total logs"
        hint="Records in selected range"
        value={stats.totalLogs}
        icon={Clock}
      />
      <StatsCard
        label="Employees"
        hint="Unique PINs"
        value={stats.uniqueEmployees}
        icon={Users}
      />
      <StatsCard
        label="Check-ins"
        hint="Inbound events"
        value={stats.checkIns}
        icon={LogIn}
      />
      <StatsCard
        label="Check-outs"
        hint="Outbound events"
        value={stats.checkOuts}
        icon={LogOut}
      />
    </DashboardGrid>
  );
}
