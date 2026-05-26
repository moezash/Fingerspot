import {
  Activity,
  BookOpen,
  KeyRound,
  Monitor,
  Plug,
  Users,
} from "lucide-react";

import { Button } from "@/components/ui/button";
import {
  APIStatusIndicator,
  ContentSection,
  DashboardGrid,
  DashboardGridAside,
  DashboardGridMain,
  LoadingState,
  PageHeader,
  SectionCard,
  StatsCard,
  StatusBadge,
} from "@/components/shared";

const quickLinks = [
  {
    title: "API credentials",
    description: "Configure keys and access tokens",
    icon: KeyRound,
  },
  {
    title: "Device registry",
    description: "Register and manage fingerprint devices",
    icon: Monitor,
  },
  {
    title: "SDK reference",
    description: "Explore endpoints in the playground",
    icon: Plug,
  },
] as const;

const gettingStartedSteps = [
  "Connect your first device",
  "Configure authentication",
  "Test an API request in the SDK Playground",
] as const;

export function DashboardOverview() {
  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        label="Workspace"
        title="Dashboard"
        description="Overview of your developer workspace. Metrics and activity will populate once integrations are connected."
      >
        <APIStatusIndicator status="checking" />
        <Button variant="outline" size="sm" className="h-8">
          <BookOpen className="size-3.5" />
          Documentation
        </Button>
      </PageHeader>

      <ContentSection
        title="Overview"
        description="High-level metrics across your connected services."
        action={<StatusBadge label="Awaiting data" variant="pending" />}
      >
        <DashboardGrid variant="stats">
          <StatsCard label="API requests" hint="Last 24 hours" icon={Activity} />
          <StatsCard label="Active devices" hint="Registered endpoints" icon={Monitor} />
          <StatsCard label="Employees" hint="Synced records" icon={Users} />
          <StatsCard label="Sync status" hint="Last successful run" icon={Plug} />
        </DashboardGrid>
      </ContentSection>

      <ContentSection title="Activity & shortcuts">
        <DashboardGrid variant="mainAside">
          <DashboardGridMain>
            <LoadingState
              variant="panel"
              title="Recent activity"
              description="API calls, device events, and sync logs will appear here."
              rows={5}
            />
          </DashboardGridMain>
          <DashboardGridAside>
            <SectionCard
              title="Quick links"
              description="Jump to common setup areas in the platform."
              contentClassName="flex flex-col gap-2 pt-4"
            >
              {quickLinks.map((link) => (
                <div
                  key={link.title}
                  className="flex items-start gap-3 rounded-lg border border-border/60 bg-muted/20 px-3 py-3 transition-colors hover:bg-muted/40"
                >
                  <link.icon className="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                  <div className="min-w-0 space-y-0.5">
                    <p className="text-sm font-medium leading-none">{link.title}</p>
                    <p className="text-xs text-muted-foreground">{link.description}</p>
                  </div>
                </div>
              ))}
            </SectionCard>
          </DashboardGridAside>
        </DashboardGrid>
      </ContentSection>

      <ContentSection>
        <SectionCard
          title="Getting started"
          description="Complete these steps to set up your Eldev workspace."
        >
          <ol className="space-y-3">
            {gettingStartedSteps.map((step, index) => (
              <li
                key={step}
                className="flex items-center gap-3 text-sm text-muted-foreground"
              >
                <span className="flex size-6 shrink-0 items-center justify-center rounded-full border border-border bg-muted/30 font-mono text-xs text-foreground">
                  {index + 1}
                </span>
                {step}
              </li>
            ))}
          </ol>
        </SectionCard>
      </ContentSection>
    </div>
  );
}
