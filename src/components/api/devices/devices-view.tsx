"use client";

import { useMemo, useState } from "react";
import { RefreshCw } from "lucide-react";

import { Button } from "@/components/ui/button";
import {
  APIStatusIndicator,
  ContentSection,
  PageHeader,
} from "@/components/shared";
import { env } from "@/config/env";
import { useDevices } from "@/hooks/use-devices";

import { DevicesDirectory } from "./devices-directory";
import { DevicesStats } from "./devices-stats";

export function DevicesView() {
  const [search, setSearch] = useState("");

  const { devices, stats, isLoading, isListLoading, error, refetch } =
    useDevices(search);

  const isStatsLoading = useMemo(
    () => isLoading && stats === null,
    [isLoading, stats]
  );

  const apiStatus = env.isFingerspotConfigured ? "connected" : "checking";

  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        label="Platform"
        title="Devices"
        description="Monitor Fingerspot devices registered to your cloud account. Data is fetched from the get_device API endpoint."
      >
        <APIStatusIndicator status={apiStatus} />
        <Button
          variant="outline"
          size="sm"
          className="h-8"
          onClick={refetch}
          disabled={isLoading || isListLoading}
        >
          <RefreshCw className="size-3.5" />
          Refresh
        </Button>
      </PageHeader>

      <ContentSection
        title="Overview"
        description="Summary of device connectivity across your fleet."
      >
        <DevicesStats stats={stats} isLoading={isStatsLoading} />
      </ContentSection>

      <ContentSection
        title="Device registry"
        description="All devices returned by the Fingerspot get_device endpoint."
      >
        <DevicesDirectory
          devices={devices}
          isLoading={isLoading}
          isListLoading={isListLoading}
          error={error}
          search={search}
          onSearchChange={setSearch}
          onRetry={refetch}
        />
      </ContentSection>
    </div>
  );
}
