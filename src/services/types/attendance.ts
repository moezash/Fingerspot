/** Request payload for Fingerspot GET /get_attlog. */
export type AttlogRequestParams = {
  trans_id: string;
  cloud_id: string;
  start_date: string;
  end_date: string;
};

export type AttendanceLog = {
  id: string;
  trans_id: string;
  cloud_id: string;
  pin: string;
  employee_name: string;
  verify_mode: string;
  check_time: string;
  device_sn: string;
  status: "check-in" | "check-out";
};

export type AttlogListResponse = {
  data: AttendanceLog[];
  meta: {
    trans_id: string;
    cloud_id: string;
    start_date: string;
    end_date: string;
    total: number;
  };
};

export type AttendanceStats = {
  totalLogs: number;
  uniqueEmployees: number;
  checkIns: number;
  checkOuts: number;
};

export type DateRangeValidationResult = {
  valid: boolean;
  message?: string;
};
