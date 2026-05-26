import { Skeleton } from "@/components/ui/skeleton";
import { cn } from "@/lib/utils";

import { DashboardGrid } from "./dashboard-grid";
import { SectionCard } from "./section-card";

type LoadingStateVariant = "list" | "stats" | "table" | "panel";

type LoadingStateProps = {
  variant?: LoadingStateVariant;
  rows?: number;
  title?: string;
  description?: string;
  className?: string;
};

function ListSkeleton({ rows }: { rows: number }) {
  return (
    <div className="space-y-3">
      {Array.from({ length: rows }).map((_, index) => (
        <div key={index} className="flex items-center gap-3">
          <Skeleton className="size-8 shrink-0 rounded-md" />
          <div className="flex min-w-0 flex-1 flex-col gap-1.5">
            <Skeleton className="h-3 w-full max-w-[12rem]" />
            <Skeleton className="h-2.5 w-2/3 max-w-[8rem]" />
          </div>
        </div>
      ))}
    </div>
  );
}

function TableSkeleton({ rows }: { rows: number }) {
  return (
    <div className="space-y-3">
      <Skeleton className="h-8 w-full rounded-md" />
      {Array.from({ length: rows }).map((_, index) => (
        <Skeleton key={index} className="h-10 w-full rounded-md" />
      ))}
    </div>
  );
}

function StatsSkeleton() {
  return (
    <DashboardGrid variant="stats">
      {Array.from({ length: 4 }).map((_, index) => (
        <div
          key={index}
          className="space-y-3 rounded-xl border border-border/60 p-4"
        >
          <Skeleton className="h-3 w-24" />
          <Skeleton className="h-8 w-16" />
          <Skeleton className="h-2.5 w-32" />
        </div>
      ))}
    </DashboardGrid>
  );
}

export function LoadingState({
  variant = "list",
  rows = 4,
  title = "Loading",
  description = "Content will appear here once data is available.",
  className,
}: LoadingStateProps) {
  if (variant === "stats") {
    return (
      <div className={className} aria-busy="true" aria-label={title}>
        <StatsSkeleton />
      </div>
    );
  }

  if (variant === "panel") {
    return (
      <SectionCard
        title={title}
        description={description}
        className={className}
        contentClassName="pt-5"
      >
        <ListSkeleton rows={rows} />
      </SectionCard>
    );
  }

  return (
    <div className={cn("space-y-3", className)} aria-busy="true" aria-label={title}>
      {variant === "table" ? <TableSkeleton rows={rows} /> : <ListSkeleton rows={rows} />}
    </div>
  );
}
