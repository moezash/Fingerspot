"use client";

import { useCallback, useEffect, useMemo, useRef, useState } from "react";

import type { EmployeeFilters } from "@/components/api/employees/employee-utils";
import { employeesService } from "@/services/endpoints/employees.service";
import { normalizeApiError } from "@/services/helpers";
import type {
  Employee,
  EmployeeListParams,
  EmployeeStats,
} from "@/services/types/employees";

function toListParams(filters: EmployeeFilters): EmployeeListParams {
  const search = filters.search.trim();

  return {
    search: search || undefined,
    status: filters.status,
  };
}

type UseEmployeesResult = {
  employees: Employee[];
  stats: EmployeeStats | null;
  isLoading: boolean;
  isListLoading: boolean;
  error: string | null;
  refetch: () => void;
};

export function useEmployees(filters: EmployeeFilters): UseEmployeesResult {
  const [employees, setEmployees] = useState<Employee[]>([]);
  const [stats, setStats] = useState<EmployeeStats | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isListLoading, setIsListLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [reloadKey, setReloadKey] = useState(0);
  const hasLoadedListRef = useRef(false);

  const listParams = useMemo(() => toListParams(filters), [filters]);

  const refetch = useCallback(() => {
    setReloadKey((key) => key + 1);
  }, []);

  useEffect(() => {
    let cancelled = false;

    employeesService
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

  useEffect(() => {
    let cancelled = false;
    const isInitialRequest = !hasLoadedListRef.current;

    if (isInitialRequest) {
      setIsLoading(true);
    } else {
      setIsListLoading(true);
    }

    employeesService
      .list(listParams)
      .then((response) => {
        if (cancelled) return;
        setEmployees(response.data);
        setError(null);
      })
      .catch((err) => {
        if (cancelled) return;
        setError(normalizeApiError(err).message);
        setEmployees([]);
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
    employees,
    stats,
    isLoading,
    isListLoading,
    error,
    refetch,
  };
}
