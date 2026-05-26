/**
 * Global API configuration — Fingerspot developer account credentials.
 *
 * Stores only what belongs to the developer account:
 *   - baseUrl  → the API environment endpoint
 *   - apiToken → the developer Bearer token
 *
 * cloud_id is device-level data and is managed separately in device-config.ts.
 * Persisted in localStorage so settings survive page refreshes without a backend.
 */

export const API_CONFIG_STORAGE_KEY = "eldev:api-config:fingerspot";

export type ApiConfig = {
  baseUrl: string;
  apiToken: string;
};

/** Seed from env vars so existing .env.local values pre-populate the form. */
export function getDefaultApiConfig(): ApiConfig {
  return {
    baseUrl: process.env.NEXT_PUBLIC_FP_BASE_URL?.trim() ?? "",
    apiToken: process.env.NEXT_PUBLIC_FP_API_TOKEN?.trim() ?? "",
  };
}

export function getApiConfig(): ApiConfig {
  if (typeof window === "undefined") return getDefaultApiConfig();

  try {
    const raw = localStorage.getItem(API_CONFIG_STORAGE_KEY);
    if (!raw) return getDefaultApiConfig();

    const parsed = JSON.parse(raw) as Partial<ApiConfig>;
    const defaults = getDefaultApiConfig();

    return {
      baseUrl: parsed.baseUrl ?? defaults.baseUrl,
      apiToken: parsed.apiToken ?? defaults.apiToken,
    };
  } catch {
    return getDefaultApiConfig();
  }
}

export function saveApiConfig(config: ApiConfig): void {
  if (typeof window === "undefined") return;
  localStorage.setItem(API_CONFIG_STORAGE_KEY, JSON.stringify(config));
}

export function clearApiConfig(): void {
  if (typeof window === "undefined") return;
  localStorage.removeItem(API_CONFIG_STORAGE_KEY);
}

export function hasApiConfig(): boolean {
  const config = getApiConfig();
  return Boolean(config.baseUrl.trim() && config.apiToken.trim());
}
