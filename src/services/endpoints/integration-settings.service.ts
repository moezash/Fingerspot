import axios from "axios";

import { generateTransId } from "../helpers/trans-id";
import {
  loadIntegrationConfig,
  normalizeUrl,
  saveIntegrationConfig,
  type ConnectionTestResult,
  type IntegrationConfig,
} from "../config/integration-config";

/**
 * Integration settings service.
 *
 * Handles persistence (localStorage) and connection testing.
 * All Fingerspot credential logic is centralised here — UI never
 * touches axios or storage directly.
 */
export const integrationSettingsService = {
  load(): IntegrationConfig {
    return loadIntegrationConfig();
  },

  save(config: IntegrationConfig): void {
    saveIntegrationConfig(config);
  },

  /**
   * Tests connectivity with the supplied credentials by calling
   * POST /get_device — the lightest read endpoint available.
   *
   * Uses a one-off axios instance built from the form values so the
   * test reflects what the user has typed, not what's in process.env.
   *
   * cloud_id is device-level data and not part of the integration config,
   * so an empty string is sent — the API validates auth before the payload.
   */
  async testConnection(config: IntegrationConfig): Promise<ConnectionTestResult> {
    const baseUrl = normalizeUrl(config.baseUrl);
    const token = config.apiToken.trim();

    if (!baseUrl) {
      return { status: "error", message: "Base URL is required and must be a valid URL." };
    }
    if (!token) {
      return { status: "error", message: "API token is required." };
    }

    try {
      await axios.post(
        `${baseUrl}/get_device`,
        { trans_id: generateTransId(), cloud_id: "" },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          timeout: 10_000,
          validateStatus: (status) => status >= 200 && status < 300,
        }
      );

      return {
        status: "success",
        message: "Connection successful. Fingerspot API is reachable.",
      };
    } catch (err) {
      if (axios.isAxiosError(err)) {
        if (err.code === "ECONNABORTED") {
          return { status: "error", message: "Connection timed out. Check the base URL." };
        }
        if (err.code === "ERR_NETWORK") {
          return { status: "error", message: "Network error. Check the base URL and your connection." };
        }
        if (err.response?.status === 401) {
          return { status: "error", message: "Unauthorized. Check your API token." };
        }
        if (err.response?.status === 403) {
          return { status: "error", message: "Forbidden. The token does not have access to this resource." };
        }
        return {
          status: "error",
          message: err.message || "Request failed. Check your credentials.",
        };
      }

      return { status: "error", message: "An unexpected error occurred." };
    }
  },
};
