/**
 * Integration configuration — Fingerspot API credentials stored in
 * localStorage so they survive page refreshes without a backend.
 *
 * Structure is intentionally forward-compatible: when a real persistence
 * layer is added, only this file and the service need to change.
 *
 * Note: cloud_id is device-level data and does not belong here.
 * It lives on each Device record and is passed per-request by the
 * individual module services.
 */

export const INTEGRATION_STORAGE_KEY = "eldev:integration:fingerspot";

export type IntegrationConfig = {
  baseUrl: string;
  apiToken: string;
};

export type ConnectionTestResult =
  | { status: "success"; message: string }
  | { status: "error"; message: string };

/** Seed defaults from env vars so existing .env.local values are visible. */
export function getDefaultIntegrationConfig(): IntegrationConfig {
  return {
    baseUrl: process.env.NEXT_PUBLIC_FP_BASE_URL?.trim() ?? "",
    apiToken: process.env.NEXT_PUBLIC_FP_API_TOKEN?.trim() ?? "",
  };
}

export function loadIntegrationConfig(): IntegrationConfig {
  if (typeof window === "undefined") return getDefaultIntegrationConfig();

  try {
    const raw = localStorage.getItem(INTEGRATION_STORAGE_KEY);
    if (!raw) return getDefaultIntegrationConfig();

    const parsed = JSON.parse(raw) as Partial<IntegrationConfig>;
    const defaults = getDefaultIntegrationConfig();

    return {
      baseUrl: parsed.baseUrl ?? defaults.baseUrl,
      apiToken: parsed.apiToken ?? defaults.apiToken,
    };
  } catch {
    return getDefaultIntegrationConfig();
  }
}

export function saveIntegrationConfig(config: IntegrationConfig): void {
  if (typeof window === "undefined") return;
  localStorage.setItem(INTEGRATION_STORAGE_KEY, JSON.stringify(config));
}

/** Returns a trimmed, trailing-slash-free URL or null if invalid. */
export function normalizeUrl(raw: string): string | null {
  const trimmed = raw.trim().replace(/\/+$/, "");
  if (!trimmed) return null;
  try {
    new URL(trimmed);
    return trimmed;
  } catch {
    return null;
  }
}

export function validateIntegrationConfig(
  config: IntegrationConfig
): Record<keyof IntegrationConfig, string | null> {
  return {
    baseUrl:
      normalizeUrl(config.baseUrl) === null && config.baseUrl.trim()
        ? "Enter a valid absolute URL (e.g. https://developer.fingerspot.io/api)"
        : null,
    apiToken: null,
  };
}
