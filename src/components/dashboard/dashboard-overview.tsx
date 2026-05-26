"use client";

import {
  Activity,
  BookOpen,
  CalendarCheck,
  Monitor,
  MonitorCheck,
  RefreshCw,
  Users,
} from "lucide-react";

import { Button } from "@/components/ui/button";
import {
  APIStatusIndicator,
  ContentSection,
  DashboardGrid,
  DashboardGridAside,
  DashboardGridMain,
  EmptyState,
  PageHeader,
  SectionCard,
  StatsCard,
} from "@/components/shared";
import { env } from "@/config/env";
import { useDashboard } from "@/hooks/use-dashboard";

import { DashboardStatsGrid } from "./dashboard-stats-grid";
import { DashboardActivityFeed } from "./dashboard-activity-feed";
import { DashboardDevicePanel } from "./dashboard-device-panel";

export function DashboardOverview() {
  const { overview, isLoading, error, refetch } = useDashboard();

  const apiStatus = env.isFingerspotConfigured ? "connected" : "checking";

  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        label="Workspace"
        title="Dashboard"
        description="Overview of your Fingerspot workspace. Metrics are aggregated from the Employees, Devices, and Attendance modules."
      >
        <APIStatusIndicator status={apiStatus} />
        <Button variant="outline" size="sm" className="h-8" onClick={refetch} disabled={isLoading}>
          <RefreshCw className="size-3.5" />
          Refresh
        </Button>
        <Button variant="outline" size="sm" className="h-8" asChild>
          <a
            href="https://developer.fingerspot.io"
            target="_blank"
            rel="noopener noreferrer"
          >
            <BookOpen className="size-3.5" />
            Documentation
          </a>
        </Button>
      </PageHeader>

      {/* Stats row */}
      <ContentSection
        title="Overview"
        description="High-level metrics across your connected services."
      >
        <DashboardStatsGrid overview={overview} isLoading={isLoading} />
      </ContentSection>

      {/* Activity + device panel */}
      <ContentSection title="Activity & devices">
        {error ? (
          <EmptyState
            icon={Activity}
            title="Unable to load dashboard data"
            description={error}
          >
            <button
              type="button"
              onClick={refetch}
              className="text-sm font-medium text-foreground underline-offset-4 hover:underline"
            >
              Try again
            </button>
          </EmptyState>
        ) : (
          <DashboardGrid variant="mainAside">
            <DashboardGridMain>
              <DashboardActivityFeed
                logs={overview?.recentActivity ?? []}
                isLoading={isLoading}
              />
            </DashboardGridMain>
            <DashboardGridAside>
              <DashboardDevicePanel
                devices={overview?.devices ?? []}
                isLoading={isLoading}
              />
            </DashboardGridAside>
          </DashboardGrid>
        )}
      </ContentSection>
    </div>
  );
}
