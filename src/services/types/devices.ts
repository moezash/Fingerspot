import type { ListQueryParams, PaginatedResponse } from "./common";

export type Device = {
  id: string;
};

export type DeviceListParams = ListQueryParams;

export type DeviceListResponse = PaginatedResponse<Device>;
