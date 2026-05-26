"use client";

import { useCallback, useEffect, useRef, useState } from "react";

import { logsService } from "@/services/endpoints/logs.service";
import { normalizeApiError } from "@/services/helpers";
import type { AttendanceLog } from "@/services/types/attendance";

type UseLogsResult = {
  logs: AttendanceLog[];
  isLoading: boolean;
  isListLoading: boolean;
  error: string | null;
  refetch: () => void;
};

export function useLogs(search: string): UseLogsResult {
  const [logs, setLogs] = useState<AttendanceLog[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isListLoading, setIsListLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [reloadKey, setReloadKey] = useState(0);
  const hasFetchedRef = useRef(false);

  const refetch = useCallback(() => {
    setReloadKey((key) => key + 1);
  }, []);

  useEffect(() => {
    let cancelled = false;
    const isInitialRequest = !hasFetchedRef.current;

    if (isInitialRequest) {
      setIsLoading(true);
    } else {
      setIsListLoading(true);
    }

    logsService
      .getLogs(search)
      .then((response) => {
        if (cancelled) return;
        setLogs(response.data);
        setError(null);
      })
      .catch((err) => {
        if (cancelled) return;
        setError(normalizeApiError(err).message);
        setLogs([]);
      })
      .finally(() => {
        if (cancelled) return;
        hasFetchedRef.current = true;
        setIsLoading(false);
        setIsListLoading(false);
      });

    return () => {
      cancelled = true;
    };
  }, [search, reloadKey]);

  return { logs, isLoading, isListLoading, error, refetch };
}
