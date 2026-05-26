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
    /** Fingerspot device list endpoint (Bearer token required). */
    getDevice: "/get_device",
    /** Fingerspot device restart endpoint (Bearer token required). */
    restartDevice: "/restart_device",
    /** Fingerspot device time sync endpoint (Bearer token required). */
    setTime: "/set_time",
  },
  logs: {
    root: "/logs",
    byId: (id: string) => `/logs/${id}`,
  },
  dashboard: {
    overview: "/dashboard/overview",
  },
} as const;
