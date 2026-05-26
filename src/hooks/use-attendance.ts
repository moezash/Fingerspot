"use client";

import { useCallback, useEffect, useMemo, useRef, useState } from "react";

import type { AttendanceFilters } from "@/components/api/attendance/attendance-utils";
import { attendanceService } from "@/services/endpoints/attendance.service";
import {
  computeAttendanceStats,
  filterAttendanceLogs,
  validateAttlogRequest,
} from "@/services/helpers/attendance-helpers";
import { normalizeApiError } from "@/services/helpers";
import type {
  AttendanceLog,
  AttendanceStats,
  AttlogRequestParams,
} from "@/services/types/attendance";

function toRequestParams(filters: AttendanceFilters): AttlogRequestParams {
  return {
    trans_id: filters.trans_id.trim(),
    cloud_id: filters.cloud_id.trim(),
    start_date: filters.start_date,
    end_date: filters.end_date,
  };
}

type UseAttendanceResult = {
  logs: AttendanceLog[];
  stats: AttendanceStats | null;
  isLoading: boolean;
  isListLoading: boolean;
  error: string | null;
  validationError: string | null;
  refetch: () => void;
};

export function useAttendance(filters: AttendanceFilters): UseAttendanceResult {
  const [logs, setLogs] = useState<AttendanceLog[]>([]);
  const [stats, setStats] = useState<AttendanceStats | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isListLoading, setIsListLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [validationError, setValidationError] = useState<string | null>(null);
  const [reloadKey, setReloadKey] = useState(0);
  const hasFetchedRef = useRef(false);

  const requestParams = useMemo(() => toRequestParams(filters), [filters]);
  const searchQuery = filters.search;

  const displayedLogs = useMemo(
    () => filterAttendanceLogs(logs, searchQuery),
    [logs, searchQuery]
  );

  const refetch = useCallback(() => {
    setReloadKey((key) => key + 1);
  }, []);

  useEffect(() => {
    let cancelled = false;
    const validation = validateAttlogRequest(requestParams);

    if (!validation.valid) {
      setValidationError(validation.message ?? "Invalid request parameters.");
      setError(null);
      setLogs([]);
      setStats(null);
      setIsLoading(false);
      setIsListLoading(false);
      return;
    }

    setValidationError(null);
    const isInitialRequest = !hasFetchedRef.current;

    if (isInitialRequest) {
      setIsLoading(true);
    } else {
      setIsListLoading(true);
    }

    attendanceService
      .getAttlog(requestParams)
      .then((response) => {
        if (cancelled) return;
        setLogs(response.data);
        setStats(computeAttendanceStats(response.data));
        setError(null);
      })
      .catch((err) => {
        if (cancelled) return;
        setError(normalizeApiError(err).message);
        setLogs([]);
        setStats(null);
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
  }, [requestParams, reloadKey]);

  return {
    logs: displayedLogs,
    stats,
    isLoading,
    isListLoading,
    error,
    validationError,
    refetch,
  };
}
