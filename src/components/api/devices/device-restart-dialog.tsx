"use client";

import { useState } from "react";
import { RotateCcw } from "lucide-react";

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

type DeviceRestartDialogProps = {
  device: Device;
};

export function DeviceRestartDialog({ device }: DeviceRestartDialogProps) {
  const [open, setOpen] = useState(false);
  const [state, setState] = useState<ActionState>("idle");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  function handleOpenChange(next: boolean) {
    // Block closing while the request is in-flight
    if (state === "pending") return;
    setOpen(next);
    // Reset feedback state when the dialog closes
    if (!next) {
      setState("idle");
      setErrorMessage(null);
    }
  }

  async function handleConfirm() {
    setState("pending");
    setErrorMessage(null);

    try {
      await devicesService.restart(device.sn, device.cloudId);
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
          aria-label={`Restart ${device.name}`}
        >
          <RotateCcw className="size-3" />
          Restart
        </Button>
      </DialogTrigger>

      <DialogContent showCloseButton={!isPending}>
        {/* ── Confirmation state ── */}
        {!isSuccess && !isError && (
          <>
            <DialogHeader>
              <DialogTitle>Restart device?</DialogTitle>
              <DialogDescription>
                This will send a restart command to{" "}
                <span className="font-medium text-foreground">
                  {device.name}
                </span>{" "}
                <span className="font-mono text-xs">({device.sn})</span>.
                The device will be temporarily unavailable while it reboots.
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
              <Button
                variant="destructive"
                onClick={handleConfirm}
                disabled={isPending}
              >
                {isPending ? (
                  <>
                    <RotateCcw className="size-3.5 animate-spin" />
                    Restarting…
                  </>
                ) : (
                  <>
                    <RotateCcw className="size-3.5" />
                    Restart device
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
              <DialogTitle>Restart command sent</DialogTitle>
              <DialogDescription>
                <span className="font-medium text-foreground">
                  {device.name}
                </span>{" "}
                has received the restart command and will reboot shortly.
              </DialogDescription>
            </DialogHeader>
            <DialogFooter showCloseButton />
          </>
        )}

        {/* ── Error state ── */}
        {isError && (
          <>
            <DialogHeader>
              <DialogTitle>Restart failed</DialogTitle>
              <DialogDescription>
                {errorMessage ?? "An unexpected error occurred. Please try again."}
              </DialogDescription>
            </DialogHeader>
            <DialogFooter>
              <Button variant="outline" onClick={() => handleOpenChange(false)}>
                Close
              </Button>
              <Button variant="outline" onClick={handleConfirm}>
                <RotateCcw className="size-3.5" />
                Try again
              </Button>
            </DialogFooter>
          </>
        )}
      </DialogContent>
    </Dialog>
  );
}
