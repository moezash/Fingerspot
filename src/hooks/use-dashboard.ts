"use client";

import { useCallback, useEffect, useRef, useState } from "react";

import { dashboardService } from "@/services/endpoints/dashboard.service";
import { normalizeApiError } from "@/services/helpers";
import type { DashboardOverview } from "@/services/types/dashboard";

type UseDashboardResult = {
  overview: DashboardOverview | null;
  isLoading: boolean;
  error: string | null;
  refetch: () => void;
};

export function useDashboard(): UseDashboardResult {
  const [overview, setOverview] = useState<DashboardOverview | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [reloadKey, setReloadKey] = useState(0);
  const hasFetchedRef = useRef(false);

  const refetch = useCallback(() => {
    hasFetchedRef.current = false;
    setReloadKey((key) => key + 1);
  }, []);

  useEffect(() => {
    let cancelled = false;

    setIsLoading(true);

    dashboardService
      .getOverview()
      .then((response) => {
        if (cancelled) return;
        setOverview(response.data);
        setError(null);
      })
      .catch((err) => {
        if (cancelled) return;
        setError(normalizeApiError(err).message);
        setOverview(null);
      })
      .finally(() => {
        if (cancelled) return;
        hasFetchedRef.current = true;
        setIsLoading(false);
      });

    return () => {
      cancelled = true;
    };
  }, [reloadKey]);

  return { overview, isLoading, error, refetch };
}
