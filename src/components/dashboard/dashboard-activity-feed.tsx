import { Activity, LogIn, LogOut } from "lucide-react";

import { LoadingState, SectionCard } from "@/components/shared";
import type { AttendanceLog } from "@/services/types/attendance";

type DashboardActivityFeedProps = {
  logs: AttendanceLog[];
  isLoading: boolean;
};

function formatTime(iso: string): string {
  return new Date(iso).toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
}

function formatDate(iso: string): string {
  return new Date(iso).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
  });
}

export function DashboardActivityFeed({
  logs,
  isLoading,
}: DashboardActivityFeedProps) {
  return (
    <SectionCard
      title="Recent activity"
      description="Latest attendance events from today."
      contentClassName="pt-4"
    >
      {isLoading ? (
        <LoadingState variant="list" rows={5} />
      ) : logs.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-lg border border-dashed border-border/80 bg-muted/20 py-10 text-center">
          <Activity className="size-5 text-muted-foreground/50" />
          <p className="mt-3 text-sm font-medium text-foreground">
            No activity today
          </p>
          <p className="mt-1 text-xs text-muted-foreground">
            Attendance events will appear here as they are recorded.
          </p>
        </div>
      ) : (
        <ol className="space-y-1" aria-label="Recent attendance activity">
          {logs.map((log) => {
            const isCheckIn = log.status === "check-in";
            const Icon = isCheckIn ? LogIn : LogOut;

            return (
              <li
                key={log.id}
                className="flex items-center gap-3 rounded-lg px-2 py-2.5 transition-colors hover:bg-muted/30"
              >
                <span
                  className="flex size-7 shrink-0 items-center justify-center rounded-md border border-border/60 bg-muted/30"
                  aria-hidden
                >
                  <Icon className="size-3.5 text-muted-foreground" />
                </span>
                <div className="min-w-0 flex-1">
                  <p className="truncate text-sm font-medium text-foreground">
                    {log.employee_name}
                  </p>
                  <p className="text-xs text-muted-foreground">
                    {isCheckIn ? "Checked in" : "Checked out"} ·{" "}
                    {log.device_sn}
                  </p>
                </div>
                <div className="shrink-0 text-right">
                  <p className="text-xs font-medium tabular-nums text-foreground">
                    {formatTime(log.check_time)}
                  </p>
                  <p className="text-xs text-muted-foreground">
                    {formatDate(log.check_time)}
                  </p>
                </div>
              </li>
            );
          })}
        </ol>
      )}
    </SectionCard>
  );
}
