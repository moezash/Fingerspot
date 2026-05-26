import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { cn } from "@/lib/utils";

type SectionCardProps = {
  title: string;
  description?: string;
  action?: React.ReactNode;
  children?: React.ReactNode;
  footer?: React.ReactNode;
  className?: string;
  contentClassName?: string;
};

export function SectionCard({
  title,
  description,
  action,
  children,
  footer,
  className,
  contentClassName,
}: SectionCardProps) {
  return (
    <Card
      className={cn(
        "flex h-full flex-col shadow-none ring-1 ring-border/60",
        className
      )}
    >
      <CardHeader
        className={cn(
          "gap-3",
          (description || action) && "border-b border-border/60"
        )}
      >
        <div className="flex items-start justify-between gap-4">
          <div className="min-w-0 space-y-1">
            <CardTitle className="text-sm font-medium">{title}</CardTitle>
            {description && <CardDescription>{description}</CardDescription>}
          </div>
          {action}
        </div>
      </CardHeader>
      {children && (
        <CardContent className={cn("flex-1 pt-5", contentClassName)}>
          {children}
        </CardContent>
      )}
      {footer && <CardFooter className="border-t border-border/60">{footer}</CardFooter>}
    </Card>
  );
}
