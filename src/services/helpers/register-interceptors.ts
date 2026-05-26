import { AxiosHeaders, type AxiosInstance } from "axios";

import { getAuthHeaders } from "./auth-token";
import { toApiError } from "./normalize-api-error";

export function registerApiClientInterceptors(client: AxiosInstance): void {
  client.interceptors.request.use((config) => {
    const authHeaders = getAuthHeaders();

    if (authHeaders?.Authorization) {
      const headers = AxiosHeaders.from(config.headers);
      headers.set("Authorization", authHeaders.Authorization);
      config.headers = headers;
    }

    return config;
  });

  client.interceptors.response.use(
    (response) => response,
    (error) => Promise.reject(toApiError(error))
  );
}
