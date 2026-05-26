import { HelpCircle, Monitor, MonitorOff, MonitorCheck } from "lucide-react";

import { DashboardGrid, LoadingState, StatsCard } from "@/components/shared";
import type { DeviceStats } from "@/services/types/devices";

type DevicesStatsProps = {
  stats: DeviceStats | null;
  isLoading?: boolean;
};

export function DevicesStats({ stats, isLoading }: DevicesStatsProps) {
  if (isLoading || !stats) {
    return <LoadingState variant="stats" />;
  }

  return (
    <DashboardGrid variant="stats">
      <StatsCard
        label="Total devices"
        hint="Registered in cloud"
        value={stats.total}
        icon={Monitor}
      />
      <StatsCard
        label="Online"
        hint="Currently reachable"
        value={stats.online}
        icon={MonitorCheck}
      />
      <StatsCard
        label="Offline"
        hint="Not responding"
        value={stats.offline}
        icon={MonitorOff}
      />
      <StatsCard
        label="Unknown"
        hint="Status unavailable"
        value={stats.unknown}
        icon={HelpCircle}
      />
    </DashboardGrid>
  );
}
