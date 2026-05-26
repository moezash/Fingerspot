export type DeviceFilters = {
  search: string;
};

export function formatDeviceDate(value: string | null): string {
  if (!value) return "—";

  return new Date(value).toLocaleString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}
