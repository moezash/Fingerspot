/**
 * Device-level configuration — tracks which Fingerspot device is currently
 * active for scoped API requests (attendance, employees, logs).
 *
 * cloud_id belongs to individual devices, not the global API config.
 * This module is the single source of truth for the selected device context.
 *
 * Persisted in localStorage. Falls back to NEXT_PUBLIC_FP_CLOUD_ID (env var)
 * so existing .env.local setups continue to work without any UI interaction.
 */

export const DEVICE_CONFIG_STORAGE_KEY = "eldev:device-config:selected-cloud-id";

/**
 * Returns the currently selected cloud_id.
 *
 * Resolution order:
 *   1. localStorage (user-selected via Devices page)
 *   2. NEXT_PUBLIC_FP_CLOUD_ID env var (legacy / .env.local fallback)
 *   3. null (no device selected)
 */
export function getSelectedCloudId(): string | null {
  if (typeof window !== "undefined") {
    try {
      const stored = localStorage.getItem(DEVICE_CONFIG_STORAGE_KEY);
      if (stored?.trim()) return stored.trim();
    } catch {
      // localStorage unavailable — fall through to env fallback
    }
  }

  // Env var fallback for .env.local / server-side rendering
  const envFallback = process.env.NEXT_PUBLIC_FP_CLOUD_ID?.trim();
  return envFallback || null;
}

export function setSelectedCloudId(cloudId: string): void {
  if (typeof window === "undefined") return;
  localStorage.setItem(DEVICE_CONFIG_STORAGE_KEY, cloudId.trim());
}

export function clearSelectedCloudId(): void {
  if (typeof window === "undefined") return;
  localStorage.removeItem(DEVICE_CONFIG_STORAGE_KEY);
}

export function hasSelectedDevice(): boolean {
  return Boolean(getSelectedCloudId());
}
