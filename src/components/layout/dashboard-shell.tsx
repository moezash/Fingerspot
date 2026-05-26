import { cn } from "@/lib/utils";

import { Navbar } from "./navbar";
import { Sidebar } from "./sidebar";

type DashboardShellProps = {
  children: React.ReactNode;
  className?: string;
};

export function DashboardShell({ children, className }: DashboardShellProps) {
  return (
    <div className="flex h-screen w-full overflow-hidden bg-background">
      <Sidebar className="hidden lg:flex" />

      <div className="flex min-w-0 flex-1 flex-col overflow-hidden">
        <Navbar />
        <main className={cn("flex-1 overflow-y-auto", className)}>{children}</main>
      </div>
    </div>
  );
}
