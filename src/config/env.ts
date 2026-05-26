const DEFAULT_API_TIMEOUT_MS = 30_000;

function readPublicEnv(key: string): string | undefined {
  const value = process.env[key];
  return value?.trim() || undefined;
}

function normalizeBaseUrl(url: string | undefined): string | undefined {
  if (!url) return undefined;

  const trimmed = url.replace(/\/+$/, "");

  try {
    new URL(trimmed);
    return trimmed;
  } catch {
    if (process.env.NODE_ENV === "development") {
      console.warn(
        `[Eldev] Invalid NEXT_PUBLIC_API_BASE_URL: "${url}". Expected a valid absolute URL.`
      );
    }
    return undefined;
  }
}

function readTimeoutMs(): number {
  const raw = readPublicEnv("NEXT_PUBLIC_API_TIMEOUT_MS");
  if (!raw) return DEFAULT_API_TIMEOUT_MS;

  const parsed = Number(raw);
  return Number.isFinite(parsed) && parsed > 0 ? parsed : DEFAULT_API_TIMEOUT_MS;
}

const apiBaseUrl = normalizeBaseUrl(
  readPublicEnv("NEXT_PUBLIC_API_BASE_URL")
);

export const env = {
  apiBaseUrl,
  apiTimeoutMs: readTimeoutMs(),
  isApiConfigured: Boolean(apiBaseUrl),
  isDevelopment: process.env.NODE_ENV === "development",
} as const;
