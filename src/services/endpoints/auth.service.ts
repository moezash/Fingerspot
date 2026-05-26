import { API_PATHS } from "@/config/api-paths";

import { notImplemented } from "../helpers/not-implemented";
import type { ApiResponse } from "../types/common";
import type { AuthCredentials, AuthSession } from "../types/auth";

export const authPaths = API_PATHS.auth;

export const authService = {
  getSession(): Promise<ApiResponse<AuthSession>> {
    return notImplemented(`${authPaths.session} GET`);
  },

  listCredentials(): Promise<ApiResponse<AuthCredentials[]>> {
    return notImplemented(`${authPaths.credentials} GET`);
  },
};
