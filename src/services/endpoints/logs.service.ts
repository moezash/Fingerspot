import { API_PATHS } from "@/config/api-paths";

import { notImplemented } from "../helpers/not-implemented";
import type { ApiResponse } from "../types/common";
import type { LogEntry, LogListParams, LogListResponse } from "../types/logs";

export const logsPaths = API_PATHS.logs;

export const logsService = {
  list(_params?: LogListParams): Promise<LogListResponse> {
    return notImplemented(`${logsPaths.root} GET`);
  },

  getById(_id: string): Promise<ApiResponse<LogEntry>> {
    return notImplemented(`${logsPaths.byId(":id")} GET`);
  },
};
