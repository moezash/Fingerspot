import type { ApiErrorResponse } from "./common";

export class ApiError extends Error {
  readonly status?: number;
  readonly code?: string;
  readonly details?: Record<string, unknown>;

  constructor(payload: ApiErrorResponse) {
    super(payload.message);
    this.name = "ApiError";
    this.status = payload.status;
    this.code = payload.code;
    this.details = payload.details;
  }

  toJSON(): ApiErrorResponse {
    return {
      message: this.message,
      status: this.status,
      code: this.code,
      details: this.details,
    };
  }
}
