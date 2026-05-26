"use client";

import { useState } from "react";
import { Clock } from "lucide-react";

import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { devicesService } from "@/services/endpoints/devices.service";
import { normalizeApiError } from "@/services/helpers";
import type { Device } from "@/services/types/devices";

type ActionState = "idle" | "pending" | "success" | "error";

type DeviceSyncTimeDialogProps = {
  device: Device;
};

export function DeviceSyncTimeDialog({ device }: DeviceSyncTimeDialogProps) {
  const [open, setOpen] = useState(false);
  const [state, setState] = useState<ActionState>("idle");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [syncedAt, setSyncedAt] = useState<string | null>(null);

  function handleOpenChange(next: boolean) {
    if (state === "pending") return;
    setOpen(next);
    if (!next) {
      setState("idle");
      setErrorMessage(null);
      setSyncedAt(null);
    }
  }

  async function handleConfirm() {
    setState("pending");
    setErrorMessage(null);

    try {
      await devicesService.syncTime(device.sn, device.cloudId);
      setSyncedAt(
        new Date().toLocaleString("en-US", {
          month: "short",
          day: "numeric",
          year: "numeric",
          hour: "2-digit",
          minute: "2-digit",
          second: "2-digit",
        })
      );
      setState("success");
    } catch (err) {
      setErrorMessage(normalizeApiError(err).message);
      setState("error");
    }
  }

  const isPending = state === "pending";
  const isSuccess = state === "success";
  const isError = state === "error";

  return (
    <Dialog open={open} onOpenChange={handleOpenChange}>
      <DialogTrigger asChild>
        <Button
          variant="ghost"
          size="sm"
          className="h-7 gap-1.5 px-2 text-xs text-muted-foreground hover:text-foreground"
          aria-label={`Sync time on ${device.name}`}
        >
          <Clock className="size-3" />
          Sync time
        </Button>
      </DialogTrigger>

      <DialogContent showCloseButton={!isPending}>
        {/* ── Confirmation state ── */}
        {!isSuccess && !isError && (
          <>
            <DialogHeader>
              <DialogTitle>Sync device time?</DialogTitle>
              <DialogDescription>
                This will send the current system time to{" "}
                <span className="font-medium text-foreground">
                  {device.name}
                </span>{" "}
                <span className="font-mono text-xs">({device.sn})</span>.
                The device clock will be updated immediately.
              </DialogDescription>
            </DialogHeader>

            <DialogFooter>
              <Button
                variant="outline"
                onClick={() => handleOpenChange(false)}
                disabled={isPending}
              >
                Cancel
              </Button>
              <Button onClick={handleConfirm} disabled={isPending}>
                {isPending ? (
                  <>
                    <Clock className="size-3.5 animate-pulse" />
                    Syncing…
                  </>
                ) : (
                  <>
                    <Clock className="size-3.5" />
                    Sync time
                  </>
                )}
              </Button>
            </DialogFooter>
          </>
        )}

        {/* ── Success state ── */}
        {isSuccess && (
          <>
            <DialogHeader>
              <DialogTitle>Time synced</DialogTitle>
              <DialogDescription>
                <span className="font-medium text-foreground">
                  {device.name}
                </span>{" "}
                has been updated to{" "}
                <span className="font-medium text-foreground">{syncedAt}</span>.
              </DialogDescription>
            </DialogHeader>
            <DialogFooter showCloseButton />
          </>
        )}

        {/* ── Error state ── */}
        {isError && (
          <>
            <DialogHeader>
              <DialogTitle>Sync failed</DialogTitle>
              <DialogDescription>
                {errorMessage ?? "An unexpected error occurred. Please try again."}
              </DialogDescription>
            </DialogHeader>
            <DialogFooter>
              <Button variant="outline" onClick={() => handleOpenChange(false)}>
                Close
              </Button>
              <Button variant="outline" onClick={handleConfirm}>
                <Clock className="size-3.5" />
                Try again
              </Button>
            </DialogFooter>
          </>
        )}
      </DialogContent>
    </Dialog>
  );
}
