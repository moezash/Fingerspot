import { API_PATHS } from "@/config/api-paths";

import { notImplemented } from "../helpers/not-implemented";
import type { ApiResponse } from "../types/common";
import type { DashboardOverview } from "../types/dashboard";

export const dashboardPaths = API_PATHS.dashboard;

export const dashboardService = {
  getOverview(): Promise<ApiResponse<DashboardOverview>> {
    return notImplemented(`${dashboardPaths.overview} GET`);
  },
};
