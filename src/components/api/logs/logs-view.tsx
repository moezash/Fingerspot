"use client";

import { useState } from "react";
import { RefreshCw } from "lucide-react";

import { Button } from "@/components/ui/button";
import { APIStatusIndicator, PageHeader } from "@/components/shared";
import { env } from "@/config/env";
import { useLogs } from "@/hooks/use-logs";

import { LogsDirectory } from "./logs-directory";

export function LogsView() {
  const [search, setSearch] = useState("");
  const { logs, isLoading, isListLoading, error, refetch } = useLogs(search);

  const apiStatus = env.isFingerspotConfigured ? "connected" : "checking";

  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        label="Platform"
        title="Logs"
        description="Real-time attendance event stream for today. Data is fetched from the Fingerspot get_attlog endpoint and refreshes on demand."
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

      <LogsDirectory
        logs={logs}
        isLoading={isLoading}
        isListLoading={isListLoading}
        error={error}
        search={search}
        onSearchChange={setSearch}
        onRetry={refetch}
      />
    </div>
  );
}
