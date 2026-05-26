/**
 * Generates a unique transaction ID for Fingerspot API requests.
 *
 * Format: EL-<timestamp-hex>-<random-hex>
 * Example: EL-019612ab3f4-a3f7
 *
 * Uses crypto.randomUUID when available (modern browsers + Node 19+),
 * falling back to a timestamp + random suffix for older environments.
 */
export function generateTransId(): string {
  if (typeof crypto !== "undefined" && typeof crypto.randomUUID === "function") {
    return `EL-${crypto.randomUUID()}`;
  }

  const timestamp = Date.now().toString(16);
  const random = Math.floor(Math.random() * 0xffff).toString(16).padStart(4, "0");
  return `EL-${timestamp}-${random}`;
}
