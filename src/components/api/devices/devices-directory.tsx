import { Search } from "lucide-react";
import { Monitor } from "lucide-react";

import { Input } from "@/components/ui/input";
import {
  EmptyState,
  LoadingState,
  SectionCard,
} from "@/components/shared";
import type { Device } from "@/services/types/devices";

import { DevicesTable } from "./devices-table";

type DevicesDirectoryProps = {
  devices: Device[];
  isLoading: boolean;
  isListLoading: boolean;
  error: string | null;
  search: string;
  onSearchChange: (value: string) => void;
  onRetry?: () => void;
};

export function DevicesDirectory({
  devices,
  isLoading,
  isListLoading,
  error,
  search,
  onSearchChange,
  onRetry,
}: DevicesDirectoryProps) {
  const showTableLoading = isLoading || isListLoading;
  const showEmptyResults = !showTableLoading && !error && devices.length === 0;
  const hasSearch = search.trim().length > 0;

  return (
    <SectionCard
      title="All devices"
      description="Fingerspot devices registered under your cloud account."
      contentClassName="space-y-4 pt-4"
    >
      {/* Search bar */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div className="relative min-w-0 flex-1 sm:max-w-sm">
          <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground/70" />
          <Input
            type="search"
            placeholder="Search by name, serial, cloud ID..."
            value={search}
            onChange={(e) => onSearchChange(e.target.value)}
            className="h-9 bg-muted/40 pl-9"
            aria-label="Search devices"
          />
        </div>
        <p className="text-sm text-muted-foreground">
          {devices.length} {devices.length === 1 ? "device" : "devices"}
        </p>
      </div>

      {/* Content */}
      {showTableLoading ? (
        <LoadingState variant="table" rows={5} />
      ) : error ? (
        <EmptyState
          icon={Monitor}
          title="Unable to load devices"
          description={error}
        >
          {onRetry && (
            <button
              type="button"
              onClick={onRetry}
              className="text-sm font-medium text-foreground underline-offset-4 hover:underline"
            >
              Try again
            </button>
          )}
        </EmptyState>
      ) : showEmptyResults && !hasSearch ? (
        <EmptyState
          icon={Monitor}
          title="No devices found"
          description="No devices are registered under this cloud account yet."
        />
      ) : showEmptyResults ? (
        <EmptyState
          icon={Monitor}
          title="No matching devices"
          description="Try adjusting your search to find devices."
        />
      ) : (
        <DevicesTable devices={devices} />
      )}
    </SectionCard>
  );
}
