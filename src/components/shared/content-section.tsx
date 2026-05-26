import { cn } from "@/lib/utils";

type ContentSectionProps = {
  title?: string;
  description?: string;
  action?: React.ReactNode;
  children: React.ReactNode;
  className?: string;
  headerClassName?: string;
};

export function ContentSection({
  title,
  description,
  action,
  children,
  className,
  headerClassName,
}: ContentSectionProps) {
  const hasHeader = title || description || action;

  return (
    <section className={cn("space-y-4", className)}>
      {hasHeader && (
        <div
          className={cn(
            "flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between",
            headerClassName
          )}
        >
          <div className="space-y-1">
            {title && (
              <h2 className="text-sm font-medium tracking-tight text-foreground">
                {title}
              </h2>
            )}
            {description && (
              <p className="text-sm text-muted-foreground">{description}</p>
            )}
          </div>
          {action}
        </div>
      )}
      {children}
    </section>
  );
}
