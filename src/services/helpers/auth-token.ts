/**
 * In-memory auth token store.
 * Replace with secure session storage when authentication is implemented.
 */
let authToken: string | null = null;

export function getAuthToken(): string | null {
  return authToken;
}

export function setAuthToken(token: string | null): void {
  authToken = token;
}

export function clearAuthToken(): void {
  authToken = null;
}

/** Headers ready for Axios request interceptors. */
export function getAuthHeaders(): Record<string, string> | null {
  if (!authToken) return null;

  return {
    Authorization: `Bearer ${authToken}`,
  };
}
