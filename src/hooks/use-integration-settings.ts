"use client";

import { useCallback, useEffect, useRef, useState } from "react";

import { integrationSettingsService } from "@/services/endpoints/integration-settings.service";
import { validateIntegrationConfig, type IntegrationConfig } from "@/services/config/integration-config";
import type { ConnectionTestResult } from "@/services/config/integration-config";

type ActionState = "idle" | "pending" | "done";

type UseIntegrationSettingsResult = {
  config: IntegrationConfig;
  fieldErrors: Record<keyof IntegrationConfig, string | null>;
  isDirty: boolean;
  isSaving: boolean;
  isTesting: boolean;
  saveResult: "success" | "error" | null;
  testResult: ConnectionTestResult | null;
  onChange: (patch: Partial<IntegrationConfig>) => void;
  onSave: () => void;
  onTest: () => Promise<void>;
  onDismissSaveResult: () => void;
  onDismissTestResult: () => void;
};

export function useIntegrationSettings(): UseIntegrationSettingsResult {
  const [config, setConfig] = useState<IntegrationConfig>(() =>
    integrationSettingsService.load()
  );
  const savedConfigRef = useRef<IntegrationConfig>(config);

  const [saveState, setSaveState] = useState<ActionState>("idle");
  const [testState, setTestState] = useState<ActionState>("idle");
  const [saveResult, setSaveResult] = useState<"success" | "error" | null>(null);
  const [testResult, setTestResult] = useState<ConnectionTestResult | null>(null);

  // Re-load from storage on mount (handles SSR hydration)
  useEffect(() => {
    const loaded = integrationSettingsService.load();
    setConfig(loaded);
    savedConfigRef.current = loaded;
  }, []);

  const fieldErrors = validateIntegrationConfig(config);

  const isDirty =
    config.baseUrl !== savedConfigRef.current.baseUrl ||
    config.apiToken !== savedConfigRef.current.apiToken;

  const onChange = useCallback((patch: Partial<IntegrationConfig>) => {
    setConfig((prev) => ({ ...prev, ...patch }));
    setSaveResult(null);
  }, []);

  const onSave = useCallback(() => {
    if (saveState === "pending") return;
    setSaveState("pending");
    setSaveResult(null);

    try {
      integrationSettingsService.save(config);
      savedConfigRef.current = config;
      setSaveResult("success");
    } catch {
      setSaveResult("error");
    } finally {
      setSaveState("done");
    }
  }, [config, saveState]);

  const onTest = useCallback(async () => {
    if (testState === "pending") return;
    setTestState("pending");
    setTestResult(null);

    const result = await integrationSettingsService.testConnection(config);
    setTestResult(result);
    setTestState("done");
  }, [config, testState]);

  const onDismissSaveResult = useCallback(() => setSaveResult(null), []);
  const onDismissTestResult = useCallback(() => setTestResult(null), []);

  return {
    config,
    fieldErrors,
    isDirty,
    isSaving: saveState === "pending",
    isTesting: testState === "pending",
    saveResult,
    testResult,
    onChange,
    onSave,
    onTest,
    onDismissSaveResult,
    onDismissTestResult,
  };
}
