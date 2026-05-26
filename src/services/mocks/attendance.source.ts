import {
  filterLogsByDateRange,
  validateAttlogRequest,
} from "../helpers/attendance-helpers";
import type {
  AttlogListResponse,
  AttlogRequestParams,
} from "../types/attendance";
import { attendanceMock } from "./attendance.mock";

const MOCK_NETWORK_DELAY_MS = 600;

function delay(ms: number): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

/**
 * Temporary data source for /get_attlog.
 * Replace with apiPost/apiGet in attendance.service when API is enabled.
 */
export async function fetchAttlogFromMock(
  params: AttlogRequestParams
): Promise<AttlogListResponse> {
  const validation = validateAttlogRequest(params);
  if (!validation.valid) {
    throw new Error(validation.message);
  }

  await delay(MOCK_NETWORK_DELAY_MS);

  const data = filterLogsByDateRange(
    attendanceMock.filter(
      (log) =>
        log.trans_id === params.trans_id.trim() &&
        log.cloud_id === params.cloud_id.trim()
    ),
    params.start_date,
    params.end_date
  );

  return {
    data,
    meta: {
      trans_id: params.trans_id.trim(),
      cloud_id: params.cloud_id.trim(),
      start_date: params.start_date,
      end_date: params.end_date,
      total: data.length,
    },
  };
}
