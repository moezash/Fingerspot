"use client";

import { useCallback, useEffect, useState } from "react";

import {
  clearSelectedCloudId,
  getSelectedCloudId,
  setSelectedCloudId,
} from "@/services/config/device-config";

type UseActiveDeviceResult = {
  activeCloudId: string | null;
  setActive: (cloudId: string) => void;
  clearActive: () => void;
};

/**
 * Manages the selected active device (cloud_id) in localStorage.
 *
 * Components that need to react to device selection changes should use
 * this hook. The state is local to the component tree — a page refresh
 * will re-read from localStorage via getSelectedCloudId().
 */
export function useActiveDevice(): UseActiveDeviceResult {
  const [activeCloudId, setActiveCloudId] = useState<string | null>(null);

  // Read from localStorage after mount (avoids SSR mismatch)
  useEffect(() => {
    setActiveCloudId(getSelectedCloudId());
  }, []);

  const setActive = useCallback((cloudId: string) => {
    setSelectedCloudId(cloudId);
    setActiveCloudId(cloudId);
  }, []);

  const clearActive = useCallback(() => {
    clearSelectedCloudId();
    setActiveCloudId(null);
  }, []);

  return { activeCloudId, setActive, clearActive };
}
