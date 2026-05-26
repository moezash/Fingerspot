import { Clock, UserCheck, UserMinus, Users } from "lucide-react";

import { DashboardGrid, LoadingState, StatsCard } from "@/components/shared";
import type { EmployeeStats } from "@/services/types/employees";

type EmployeesStatsProps = {
  stats: EmployeeStats | null;
  isLoading?: boolean;
};

export function EmployeesStats({ stats, isLoading }: EmployeesStatsProps) {
  if (isLoading || !stats) {
    return <LoadingState variant="stats" />;
  }

  return (
    <DashboardGrid variant="stats">
      <StatsCard
        label="Total employees"
        hint="Registered in workspace"
        value={stats.total}
        icon={Users}
      />
      <StatsCard
        label="Active"
        hint="Currently enabled"
        value={stats.active}
        icon={UserCheck}
      />
      <StatsCard
        label="Pending sync"
        hint="Awaiting device enrollment"
        value={stats.pending}
        icon={Clock}
      />
      <StatsCard
        label="Inactive"
        hint="Disabled or archived"
        value={stats.inactive}
        icon={UserMinus}
      />
    </DashboardGrid>
  );
}
