import { env } from "@/config/env";

/**
 * Returns true when NEXT_PUBLIC_API_BASE_URL is set and valid.
 * Use in services to gate live API calls during incremental migration.
 */
export function isLiveApiEnabled(): boolean {
  return env.isApiConfigured;
}
