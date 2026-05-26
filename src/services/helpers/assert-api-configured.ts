import { env } from "@/config/env";

export function assertApiConfigured(): void {
  if (!env.isApiConfigured) {
    throw new Error(
      "API base URL is not configured. Set NEXT_PUBLIC_API_BASE_URL in your environment."
    );
  }
}
