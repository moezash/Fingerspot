import { Separator } from "@/components/ui/separator";
import { cn } from "@/lib/utils";

import { SidebarBrand } from "./sidebar-brand";
import { SidebarNav } from "./sidebar-nav";

type SidebarProps = {
  className?: string;
};

export function Sidebar({ className }: SidebarProps) {
  return (
    <aside
      className={cn(
        "flex h-full w-[260px] shrink-0 flex-col border-r border-sidebar-border bg-sidebar",
        className
      )}
    >
      <div className="flex h-[52px] items-center px-4">
        <SidebarBrand />
      </div>

      <Separator className="bg-sidebar-border" />

      <div className="flex flex-1 flex-col overflow-y-auto px-2 py-5">
        <SidebarNav />
      </div>
    </aside>
  );
}
