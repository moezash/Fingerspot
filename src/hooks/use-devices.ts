"use client";

import { useCallback, useEffect, useMemo, useRef, useState } from "react";

import { devicesService } from "@/services/endpoints/devices.service";
import { normalizeApiError } from "@/services/helpers";
import type {
  Device,
  DeviceListParams,
  DeviceStats,
} from "@/services/types/devices";

type UseDevicesResult = {
  devices: Device[];
  stats: DeviceStats | null;
  isLoading: boolean;
  isListLoading: boolean;
  error: string | null;
  refetch: () => void;
};

export function useDevices(search: string): UseDevicesResult {
  const [devices, setDevices] = useState<Device[]>([]);
  const [stats, setStats] = useState<DeviceStats | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isListLoading, setIsListLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [reloadKey, setReloadKey] = useState(0);
  const hasLoadedListRef = useRef(false);

  const listParams = useMemo<DeviceListParams>(
    () => ({ search: search.trim() || undefined }),
    [search]
  );

  const refetch = useCallback(() => {
    setReloadKey((key) => key + 1);
  }, []);

  // Stats — fetches the full unfiltered list
  useEffect(() => {
    let cancelled = false;

    devicesService
      .getStats()
      .then((response) => {
        if (!cancelled) setStats(response.data);
      })
      .catch((err) => {
        if (!cancelled) setError(normalizeApiError(err).message);
      });

    return () => {
      cancelled = true;
    };
  }, [reloadKey]);

  // Device list — respects search filter
  useEffect(() => {
    let cancelled = false;
    const isInitialRequest = !hasLoadedListRef.current;

    if (isInitialRequest) {
      setIsLoading(true);
    } else {
      setIsListLoading(true);
    }

    devicesService
      .list(listParams)
      .then((response) => {
        if (cancelled) return;
        setDevices(response.data);
        setError(null);
      })
      .catch((err) => {
        if (cancelled) return;
        setError(normalizeApiError(err).message);
        setDevices([]);
      })
      .finally(() => {
        if (cancelled) return;
        hasLoadedListRef.current = true;
        setIsLoading(false);
        setIsListLoading(false);
      });

    return () => {
      cancelled = true;
    };
  }, [listParams, reloadKey]);

  return {
    devices,
    stats,
    isLoading,
    isListLoading,
    error,
    refetch,
  };
}
