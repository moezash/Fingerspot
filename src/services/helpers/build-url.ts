export type QueryParamValue = string | number | boolean | undefined | null;

export function buildUrl(
  path: string,
  params?: Record<string, QueryParamValue>
): string {
  if (!params) return path;

  const searchParams = new URLSearchParams();

  for (const [key, value] of Object.entries(params)) {
    if (value === undefined || value === null || value === "") continue;
    searchParams.set(key, String(value));
  }

  const query = searchParams.toString();
  return query ? `${path}?${query}` : path;
}
