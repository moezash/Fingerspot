import { cn } from "@/lib/utils";

const gridVariants = {
  stats: "grid gap-4 sm:grid-cols-2 xl:grid-cols-4",
  double: "grid gap-4 md:grid-cols-2",
  triple: "grid gap-4 md:grid-cols-2 xl:grid-cols-3",
  mainAside: "grid gap-4 lg:grid-cols-5",
  single: "grid gap-4 grid-cols-1",
} as const;

type DashboardGridVariant = keyof typeof gridVariants;

type DashboardGridProps = {
  children: React.ReactNode;
  variant?: DashboardGridVariant;
  className?: string;
};

export function DashboardGrid({
  children,
  variant = "single",
  className,
}: DashboardGridProps) {
  return (
    <div className={cn(gridVariants[variant], className)}>{children}</div>
  );
}

export function DashboardGridMain({
  children,
  className,
}: {
  children: React.ReactNode;
  className?: string;
}) {
  return <div className={cn("lg:col-span-3", className)}>{children}</div>;
}

export function DashboardGridAside({
  children,
  className,
}: {
  children: React.ReactNode;
  className?: string;
}) {
  return <div className={cn("lg:col-span-2", className)}>{children}</div>;
}
