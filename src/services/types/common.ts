/** Standard single-resource API envelope. */
export type ApiResponse<T> = {
  data: T;
  message?: string;
};

/** Standard API error payload (HTTP or client-level). */
export type ApiErrorResponse = {
  message: string;
  code?: string;
  status?: number;
  details?: Record<string, unknown>;
};

/** Axios response body shape before unwrapping. */
export type ApiResponseBody<T> = ApiResponse<T> | T;

export type PaginationMeta = {
  page: number;
  limit: number;
  total: number;
  totalPages: number;
};

export type PaginationParams = {
  page?: number;
  limit?: number;
};

export type PaginatedResponse<T> = {
  data: T[];
  meta: PaginationMeta;
};

export type ListQueryParams = PaginationParams & {
  search?: string;
};
