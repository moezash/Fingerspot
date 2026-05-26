/**
 * Relative API path segments. Combined with `env.apiBaseUrl` by the API client.
 */
export const API_PATHS = {
  auth: {
    session: "/auth/session",
    credentials: "/auth/credentials",
  },
  employees: {
    root: "/employees",
    byId: (id: string) => `/employees/${id}`,
  },
  attendance: {
    /** Fingerspot attendance log endpoint (Bearer token required). */
    getAttlog: "/get_attlog",
  },
  devices: {
    root: "/devices",
    byId: (id: string) => `/devices/${id}`,
  },
  logs: {
    root: "/logs",
    byId: (id: string) => `/logs/${id}`,
  },
  dashboard: {
    overview: "/dashboard/overview",
  },
} as const;
