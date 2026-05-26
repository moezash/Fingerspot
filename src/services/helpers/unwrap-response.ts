import type { ApiResponse, ApiResponseBody } from "../types/common";

function isApiEnvelope<T>(payload: ApiResponseBody<T>): payload is ApiResponse<T> {
  return (
    typeof payload === "object" &&
    payload !== null &&
    "data" in payload &&
    (payload as ApiResponse<T>).data !== undefined
  );
}

/** Unwrap `{ data: T }` envelopes or return raw payloads. */
export function unwrapApiResponse<T>(payload: ApiResponseBody<T>): T {
  if (isApiEnvelope(payload)) {
    return payload.data;
  }

  return payload as T;
}
