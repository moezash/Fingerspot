"use client";

import { CheckCircle2, Loader2, PlugZap, Save, XCircle } from "lucide-react";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Separator } from "@/components/ui/separator";
import { ContentSection, PageHeader, SectionCard } from "@/components/shared";
import { useIntegrationSettings } from "@/hooks/use-integration-settings";

import { IntegrationStatusBadge } from "./integration-status-badge";

// ---------------------------------------------------------------------------
// Field wrapper — label + input + optional error message
// ---------------------------------------------------------------------------

type FieldProps = {
  id: string;
  label: string;
  hint?: string;
  value: string;
  type?: string;
  placeholder?: string;
  error?: string | null;
  onChange: (value: string) => void;
};

function Field({ id, label, hint, value, type = "text", placeholder, error, onChange }: FieldProps) {
  return (
    <div className="space-y-1.5">
      <div className="space-y-0.5">
        <label htmlFor={id} className="text-xs font-medium text-foreground">
          {label}
        </label>
        {hint && <p className="text-xs text-muted-foreground">{hint}</p>}
      </div>
      <Input
        id={id}
        type={type}
        value={value}
        placeholder={placeholder}
        onChange={(e) => onChange(e.target.value)}
        aria-invalid={Boolean(error)}
        className="h-9 bg-muted/40 dark:bg-input/30"
      />
      {error && (
        <p className="text-xs text-destructive" role="alert">
          {error}
        </p>
      )}
    </div>
  );
}

// ---------------------------------------------------------------------------
// Inline feedback banner — save / test result
// ---------------------------------------------------------------------------

type FeedbackBannerProps = {
  type: "success" | "error";
  message: string;
  onDismiss: () => void;
};

function FeedbackBanner({ type, message, onDismiss }: FeedbackBannerProps) {
  const Icon = type === "success" ? CheckCircle2 : XCircle;
  const colorClass =
    type === "success"
      ? "border-emerald-500/20 bg-emerald-500/5 text-emerald-700 dark:text-emerald-400"
      : "border-destructive/20 bg-destructive/5 text-destructive";

  return (
    <div
      role="status"
      className={`flex items-start gap-2.5 rounded-lg border px-3 py-2.5 text-sm ${colorClass}`}
    >
      <Icon className="mt-0.5 size-4 shrink-0" aria-hidden />
      <p className="flex-1 leading-snug">{message}</p>
      <button
        type="button"
        onClick={onDismiss}
        className="shrink-0 opacity-60 hover:opacity-100"
        aria-label="Dismiss"
      >
        <XCircle className="size-3.5" />
      </button>
    </div>
  );
}

// ---------------------------------------------------------------------------
// Main view
// ---------------------------------------------------------------------------

export function IntegrationSettingsView() {
  const {
    config,
    fieldErrors,
    isDirty,
    isSaving,
    isTesting,
    saveResult,
    testResult,
    onChange,
    onSave,
    onTest,
    onDismissSaveResult,
    onDismissTestResult,
  } = useIntegrationSettings();

  const hasFieldErrors = Object.values(fieldErrors).some(Boolean);

  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        label="Settings"
        title="Integration"
        description="Configure your Fingerspot Cloud API credentials. Settings are saved locally and used across all modules."
      />

      {/* ── Credentials ── */}
      <ContentSection
        title="Fingerspot API"
        description="Credentials used to authenticate with the Fingerspot Cloud API."
      >
        <SectionCard
          title="API credentials"
          description="Enter your Fingerspot Cloud API details. These values are stored in your browser."
          contentClassName="pt-5"
          footer={
            <div className="flex w-full flex-col gap-3">
              {saveResult === "success" && (
                <FeedbackBanner
                  type="success"
                  message="Configuration saved successfully."
                  onDismiss={onDismissSaveResult}
                />
              )}
              {saveResult === "error" && (
                <FeedbackBanner
                  type="error"
                  message="Failed to save configuration. Please try again."
                  onDismiss={onDismissSaveResult}
                />
              )}
              <div className="flex justify-end">
                <Button
                  onClick={onSave}
                  disabled={isSaving || !isDirty || hasFieldErrors}
                  size="sm"
                >
                  {isSaving ? (
                    <Loader2 className="size-3.5 animate-spin" />
                  ) : (
                    <Save className="size-3.5" />
                  )}
                  {isSaving ? "Saving…" : "Save changes"}
                </Button>
              </div>
            </div>
          }
        >
          <div className="space-y-4">
            <Field
              id="fp-base-url"
              label="Base URL"
              hint="Absolute URL to the Fingerspot Cloud API, no trailing slash."
              placeholder="https://developer.fingerspot.io/api"
              value={config.baseUrl}
              error={fieldErrors.baseUrl}
              onChange={(v) => onChange({ baseUrl: v })}
            />
            <Field
              id="fp-api-token"
              label="API token"
              hint="Bearer token used to authenticate all API requests."
              placeholder="••••••••••••••••"
              type="password"
              value={config.apiToken}
              error={fieldErrors.apiToken}
              onChange={(v) => onChange({ apiToken: v })}
            />
          </div>
        </SectionCard>
      </ContentSection>

      <Separator />

      {/* ── Connection test ── */}
      <ContentSection
        title="Connection test"
        description="Verify that the credentials above can reach the Fingerspot API."
      >
        <SectionCard
          title="Test connection"
          description="Sends a lightweight request to POST /get_device to validate your credentials."
          contentClassName="pt-5"
        >
          <div className="space-y-4">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div className="space-y-1">
                <p className="text-sm font-medium text-foreground">
                  Fingerspot Cloud API
                </p>
                <p className="font-mono text-xs text-muted-foreground">
                  {config.baseUrl || "No base URL configured"}
                </p>
              </div>
              <div className="flex shrink-0 items-center gap-3">
                {testResult && (
                  <IntegrationStatusBadge result={testResult} />
                )}
                <Button
                  variant="outline"
                  size="sm"
                  onClick={onTest}
                  disabled={isTesting || !config.baseUrl || !config.apiToken}
                >
                  {isTesting ? (
                    <Loader2 className="size-3.5 animate-spin" />
                  ) : (
                    <PlugZap className="size-3.5" />
                  )}
                  {isTesting ? "Testing…" : "Test connection"}
                </Button>
              </div>
            </div>

            {testResult && (
              <FeedbackBanner
                type={testResult.status}
                message={testResult.message}
                onDismiss={onDismissTestResult}
              />
            )}
          </div>
        </SectionCard>
      </ContentSection>
    </div>
  );
}
