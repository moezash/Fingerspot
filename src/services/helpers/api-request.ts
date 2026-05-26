import type { AxiosRequestConfig } from "axios";

import { apiClient } from "../api-client";
import type { ApiResponse, PaginatedResponse } from "../types/common";
import { assertApiConfigured } from "./assert-api-configured";
import { buildUrl, type QueryParamValue } from "./build-url";
import { unwrapApiResponse } from "./unwrap-response";

type ApiRequestOptions = {
  params?: Record<string, QueryParamValue>;
  config?: Omit<AxiosRequestConfig, "url" | "method" | "params">;
};

/**
 * Low-level request helper. Prefer apiGet / apiGetPaginated in service modules.
 */
export async function apiRequest<T>(
  method: AxiosRequestConfig["method"],
  path: string,
  options?: ApiRequestOptions & { body?: unknown }
): Promise<T> {
  assertApiConfigured();

  const response = await apiClient.request<ApiResponse<T>>({
    method,
    url: buildUrl(path, options?.params),
    data: options?.body,
    ...options?.config,
  });

  return unwrapApiResponse(response.data);
}

export async function apiGet<T>(
  path: string,
  options?: ApiRequestOptions
): Promise<T> {
  return apiRequest<T>("GET", path, options);
}

export async function apiGetPaginated<T>(
  path: string,
  options?: ApiRequestOptions
): Promise<PaginatedResponse<T>> {
  assertApiConfigured();

  const response = await apiClient.get<PaginatedResponse<T>>(
    buildUrl(path, options?.params),
    options?.config
  );

  return response.data;
}

export async function apiPost<T>(
  path: string,
  body?: unknown,
  options?: ApiRequestOptions
): Promise<T> {
  return apiRequest<T>("POST", path, { ...options, body });
}

export async function apiPut<T>(
  path: string,
  body?: unknown,
  options?: ApiRequestOptions
): Promise<T> {
  return apiRequest<T>("PUT", path, { ...options, body });
}

export async function apiPatch<T>(
  path: string,
  body?: unknown,
  options?: ApiRequestOptions
): Promise<T> {
  return apiRequest<T>("PATCH", path, { ...options, body });
}

export async function apiDelete<T>(
  path: string,
  options?: ApiRequestOptions
): Promise<T> {
  return apiRequest<T>("DELETE", path, options);
}
