import type { ListQueryParams, PaginatedResponse } from "./common";

export type LogEntry = {
  id: string;
};

export type LogListParams = ListQueryParams;

export type LogListResponse = PaginatedResponse<LogEntry>;
