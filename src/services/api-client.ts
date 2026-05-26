import axios, {
  type AxiosInstance,
  type CreateAxiosDefaults,
  isAxiosError,
} from "axios";

import { env } from "@/config/env";

import { registerApiClientInterceptors } from "./helpers/register-interceptors";

const defaultHeaders: CreateAxiosDefaults["headers"] = {
  Accept: "application/json",
  "Content-Type": "application/json",
};

/**
 * Creates a configured Axios instance for the Eldev API.
 * Base URL is read from NEXT_PUBLIC_API_BASE_URL (see .env.local).
 */
export function createApiClient(
  overrides?: CreateAxiosDefaults
): AxiosInstance {
  const client = axios.create({
    baseURL: env.apiBaseUrl,
    timeout: env.apiTimeoutMs,
    headers: defaultHeaders,
    validateStatus: (status) => status >= 200 && status < 300,
    ...overrides,
  });

  registerApiClientInterceptors(client);
  return client;
}

/** Shared Axios instance for real API module integrations. */
export const apiClient = createApiClient();

export { isAxiosError };
