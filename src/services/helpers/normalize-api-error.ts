import { isAxiosError } from "axios";

import { ApiError } from "../types/api-error";
import type { ApiErrorResponse } from "../types/common";

function parseResponseError(data: unknown): Partial<ApiErrorResponse> | null {
  if (!data || typeof data !== "object") return null;

  const record = data as Record<string, unknown>;
  const message =
    typeof record.message === "string"
      ? record.message
      : typeof record.error === "string"
        ? record.error
        : undefined;

  if (!message) return null;

  return {
    message,
    code: typeof record.code === "string" ? record.code : undefined,
    details:
      typeof record.details === "object" && record.details !== null
        ? (record.details as Record<string, unknown>)
        : undefined,
  };
}

export function normalizeApiError(error: unknown): ApiErrorResponse {
  if (error instanceof ApiError) {
    return error.toJSON();
  }

  if (isAxiosError(error)) {
    const parsed = parseResponseError(error.response?.data);

    if (parsed?.message) {
      return {
        message: parsed.message,
        code: parsed.code,
        status: error.response?.status,
        details: parsed.details,
      };
    }

    if (error.code === "ECONNABORTED") {
      return {
        message: "Request timed out. Please try again.",
        code: error.code,
        status: error.response?.status,
      };
    }

    if (error.code === "ERR_NETWORK") {
      return {
        message: "Network error. Check your connection and API base URL.",
        code: error.code,
      };
    }

    if (error.response?.status === 401) {
      return {
        message: "Unauthorized. Authentication will be required for this resource.",
        status: 401,
        code: "UNAUTHORIZED",
      };
    }

    if (error.response?.status === 403) {
      return {
        message: "You do not have permission to access this resource.",
        status: 403,
        code: "FORBIDDEN",
      };
    }

    if (error.response?.status === 404) {
      return {
        message: "The requested resource was not found.",
        status: 404,
        code: "NOT_FOUND",
      };
    }

    return {
      message: error.message || "Request failed",
      status: error.response?.status,
      code: error.code,
    };
  }

  if (error instanceof Error) {
    return { message: error.message };
  }

  return { message: "An unexpected error occurred" };
}

export function toApiError(error: unknown): ApiError {
  return new ApiError(normalizeApiError(error));
}
