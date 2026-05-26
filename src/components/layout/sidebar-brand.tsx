import Link from "next/link";

import { cn } from "@/lib/utils";

type SidebarBrandProps = {
  className?: string;
  onNavigate?: () => void;
};

export function SidebarBrand({ className, onNavigate }: SidebarBrandProps) {
  return (
    <Link
      href="/dashboard"
      onClick={onNavigate}
      className={cn(
        "flex items-center gap-2.5 rounded-lg outline-none transition-opacity hover:opacity-90 focus-visible:ring-2 focus-visible:ring-sidebar-ring",
        className
      )}
    >
      <span className="flex size-7 shrink-0 items-center justify-center rounded-md border border-sidebar-border bg-sidebar-accent font-mono text-xs font-semibold text-sidebar-foreground">
        E
      </span>
      <span className="text-sm font-semibold tracking-tight text-sidebar-foreground">
        Eldev
      </span>
    </Link>
  );
}
