import Link from "next/link";

import { Button } from "@/components/ui/button";
import { EmptyState, PageHeader } from "@/components/shared";

export default function DashboardNotFound() {
  return (
    <div className="flex flex-col gap-10">
      <PageHeader
        title="Page not found"
        description="The page you requested does not exist in this workspace."
      />
      <EmptyState
        title="Nothing here yet"
        description="Check the URL or return to the dashboard to continue."
      >
        <Button variant="outline" size="sm" className="h-8" asChild>
          <Link href="/dashboard">Back to dashboard</Link>
        </Button>
      </EmptyState>
    </div>
  );
}
