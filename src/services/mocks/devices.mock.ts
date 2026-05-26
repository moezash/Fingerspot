import type { Device } from "../types/devices";

export const devicesMock: Device[] = [
  {
    id: "dev_001",
    sn: "FP-AX92-01",
    name: "Main Entrance",
    cloudId: "DEMO-CLOUD-001",
    status: "online",
    firmware: "v2.4.1",
    lastUpdateAt: "2026-05-27T07:45:00Z",
  },
  {
    id: "dev_002",
    sn: "FP-AX92-02",
    name: "Server Room",
    cloudId: "DEMO-CLOUD-001",
    status: "online",
    firmware: "v2.4.1",
    lastUpdateAt: "2026-05-27T07:44:30Z",
  },
  {
    id: "dev_003",
    sn: "FP-AX92-03",
    name: "Warehouse Gate",
    cloudId: "DEMO-CLOUD-001",
    status: "offline",
    firmware: "v2.3.8",
    lastUpdateAt: "2026-05-25T14:10:00Z",
  },
  {
    id: "dev_004",
    sn: "FP-AX92-04",
    name: "HR Office",
    cloudId: "DEMO-CLOUD-001",
    status: "online",
    firmware: "v2.4.1",
    lastUpdateAt: "2026-05-27T07:46:00Z",
  },
  {
    id: "dev_005",
    sn: "FP-BX10-01",
    name: "Parking Level B1",
    cloudId: "DEMO-CLOUD-001",
    status: "offline",
    firmware: "v2.2.0",
    lastUpdateAt: "2026-05-20T09:00:00Z",
  },
];
