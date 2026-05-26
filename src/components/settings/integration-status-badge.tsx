import { StatusBadge } from "@/components/shared";
import type { ConnectionTestResult } from "@/services/config/integration-config";

type IntegrationStatusBadgeProps = {
  result: ConnectionTestResult;
};

export function IntegrationStatusBadge({ result }: IntegrationStatusBadgeProps) {
  return (
    <StatusBadge
      label={result.status === "success" ? "Connected" : "Failed"}
      variant={result.status === "success" ? "success" : "error"}
    />
  );
}
