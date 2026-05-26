"use client";

import { useEffect, useState } from "react";
import { AlertTriangle, CheckCircle2, Monitor, Wifi, WifiOff } from "lucide-react";
import Link from "next/link";

import { cn } from "@/lib/utils";
import { env } from "@/config/env";
import { getSelectedCloudId } from "@/services/config/device-config";

type IntegrationStatusProps = {
  className?: string;
};

type StatusLine = {
  icon: React.ReactNode;
  label: string;
  variant: "ok" | "warn" | "info";
};

/**
 * Reusable integration status banner.
 *
 * Shows three runtime signals:
 *   1. API configured (baseUrl + token present) vs missing
 *   2. Active device selected vs missing
 *   3. Live mode vs mock mode
 *
 * Reads from env (build-time) and device-config (localStorage, client-only).
 * Renders nothing on the server — mounts after hydration to avoid mismatch.
 */
export function IntegrationStatus({ className }: IntegrationStatusProps) {
  const [cloudId, setCloudId] = useState<string | null>(null);
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setCloudId(getSelectedCloudId());
    setMounted(true);
  }, []);

  // Don't render until client-side to avoid SSR/hydration mismatch
  if (!mounted) return null;

  const isApiConfigured = env.isFingerspotConfigured;
  const hasDevice = Boolean(cloudId);
  const isLive = isApiConfigured;

  const lines: StatusLine[] = [
    {
      icon: isApiConfigured
        ? <CheckCircle2 className="size-3.5 text-emerald-500" />
        : <WifiOff className="size-3.5 text-amber-500" />,
      label: isApiConfigured
        ? "API configured"
        : "API not configured — go to Settings → Integration",
      variant: isApiConfigured ? "ok" : "warn",
    },
    {
      icon: hasDevice
        ? <Monitor className="size-3.5 text-emerald-500" />
        : <AlertTriangle className="size-3.5 text-amber-500" />,
      label: hasDevice
        ? `Active device: ${cloudId}`
        : "No active device — select one from the Devices page",
      variant: hasDevice ? "ok" : "warn",
    },
    {
      icon: isLive
        ? <Wifi className="size-3.5 text-emerald-500" />
        : <Wifi className="size-3.5 text-muted-foreground/50" />,
      label: isLive ? "Live mode" : "Mock mode — using demo data",
      variant: isLive ? "ok" : "info",
    },
  ];

  const hasWarning = lines.some((l) => l.variant === "warn");

  return (
    <div
      className={cn(
        "rounded-lg border px-3 py-2.5",
        hasWarning
          ? "border-amber-500/20 bg-amber-500/5"
          : "border-border/60 bg-muted/20",
        className
      )}
      role="status"
      aria-label="Integration status"
    >
      <div className="flex flex-wrap gap-x-5 gap-y-1.5">
        {lines.map((line) => (
          <div key={line.label} className="flex items-center gap-1.5">
            {line.icon}
            <span
              className={cn(
                "text-xs",
                line.variant === "warn"
                  ? "text-amber-700 dark:text-amber-400"
                  : line.variant === "ok"
                    ? "text-foreground"
                    : "text-muted-foreground"
              )}
            >
              {line.label}
            </span>
          </div>
        ))}
        {!isApiConfigured && (
          <Link
            href="/settings/integration"
            className="text-xs font-medium text-foreground underline underline-offset-3 hover:opacity-80"
          >
            Configure →
          </Link>
        )}
      </div>
    </div>
  );
}
