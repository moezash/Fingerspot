import axios, { type AxiosInstance } from "axios";

import { env } from "@/config/env";

/**
 * Dedicated Axios instance for the Fingerspot Cloud API.
 *
 * - Base URL: NEXT_PUBLIC_FP_BASE_URL
 * - Auth:     Bearer token from NEXT_PUBLIC_FP_API_TOKEN (injected per-request)
 * - Separate from the generic apiClient so Fingerspot concerns stay isolated.
 */
function createFingerspotClient(): AxiosInstance {
  const client = axios.create({
    baseURL: env.fingerspotBaseUrl,
    timeout: env.apiTimeoutMs,
    headers: {
      Accept: "application/json",
      "Content-Type": "application/json",
    },
    validateStatus: (status) => status >= 200 && status < 300,
  });

  // Inject Bearer token on every request
  client.interceptors.request.use((config) => {
    if (env.fingerspotApiToken) {
      config.headers.set("Authorization", `Bearer ${env.fingerspotApiToken}`);
    }
    return config;
  });

  return client;
}

export const fingerspotClient = createFingerspotClient();
